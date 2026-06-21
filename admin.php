<?php
// admin.php
require 'db.php';

// Inclusion de ton menu global
include 'header.php';

// Si la personne n'est pas connectée, on la vire vers login
if (!isset($_SESSION['role'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$role = $_SESSION['role'];
$id_batiment_gestionnaire = $_SESSION['id_batiment_gere'] ?? null;
$message = "";

// ==========================================
// 1. SÉCURITÉ : TRAITEMENT DES ACTIONS
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // ACTIONS RÉSERVÉES À L'ADMINISTRATEUR SUPRÊME
    if ($role === 'admin') {
        if ($_POST['action'] === 'add_batiment') {
            $nom_batiment = htmlspecialchars($_POST['nom_batiment']);
            $suffixe = strtolower(str_replace(' ', '', $nom_batiment));
            $login_gest = "gestionnaire_" . $suffixe;
            $mdp = "rt"; 

            // Création user + bâtiment
            $sql_user = "INSERT INTO utilisateur (login, mot_de_passe, role) VALUES (?, ?, 'gestionnaire')";
            $stmt = mysqli_prepare($conn, $sql_user);
            mysqli_stmt_bind_param($stmt, "ss", $login_gest, $mdp);
            
            if (mysqli_stmt_execute($stmt)) {
                $sql_bat = "INSERT INTO batiment (nom_batiment, login_gestionnaire) VALUES (?, ?)";
                $stmt_bat = mysqli_prepare($conn, $sql_bat);
                mysqli_stmt_bind_param($stmt_bat, "ss", $nom_batiment, $login_gest);
                mysqli_stmt_execute($stmt_bat);
                $message = "<div class='msg-success'>✅ Bâtiment et compte <b>$login_gest</b> créés avec succès.</div>";
            } else {
                $message = "<div class='msg-error'>❌ Erreur : Ce gestionnaire existe déjà.</div>";
            }
        }
        elseif ($_POST['action'] === 'del_batiment') {
            $id_bat = (int)$_POST['id_batiment'];
            $res = mysqli_query($conn, "SELECT login_gestionnaire FROM batiment WHERE id_batiment = $id_bat");
            if ($bat = mysqli_fetch_assoc($res)) {
                mysqli_query($conn, "DELETE FROM batiment WHERE id_batiment = $id_bat");
                mysqli_query($conn, "DELETE FROM utilisateur WHERE login = '{$bat['login_gestionnaire']}'");
                $message = "<div class='msg-success'>🗑️ Bâtiment et son gestionnaire supprimés.</div>";
            }
        }
    }

    // ACTIONS AUTORISÉES POUR ADMIN ET GESTIONNAIRES
    if ($_POST['action'] === 'add_capteur') {
        $nom_cap = htmlspecialchars($_POST['nom_capteur']);
        $id_salle = (int)$_POST['id_salle'];
        $sql = "INSERT INTO capteur (nom_capteur, id_salle) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nom_cap, $id_salle);
        mysqli_stmt_execute($stmt);
        $message = "<div class='msg-success'>✅ Nouveau capteur déployé.</div>";
    }
    elseif ($_POST['action'] === 'del_capteur') {
        $id_capteur = (int)$_POST['id_capteur'];
        mysqli_query($conn, "DELETE FROM capteur WHERE id_capteur = $id_capteur");
        $message = "<div class='msg-success'>🗑️ Capteur déconnecté et supprimé.</div>";
    }
}

// ==========================================
// 2. REQUÊTES DYNAMIQUES (LE SECRET DU SYSTÈME)
// ==========================================
if ($role === 'admin') {
    $salles_sql = "SELECT s.id_salle, s.nom_salle, b.nom_batiment FROM salle s JOIN batiment b ON s.id_batiment = b.id_batiment";
    $capteurs_sql = "SELECT c.id_capteur, c.nom_capteur, s.nom_salle, b.nom_batiment FROM capteur c JOIN salle s ON c.id_salle = s.id_salle JOIN batiment b ON s.id_batiment = b.id_batiment";
} else {
    $salles_sql = "SELECT s.id_salle, s.nom_salle FROM salle s WHERE s.id_batiment = $id_batiment_gestionnaire";
    $capteurs_sql = "SELECT c.id_capteur, c.nom_capteur, s.nom_salle FROM capteur c JOIN salle s ON c.id_salle = s.id_salle WHERE s.id_batiment = $id_batiment_gestionnaire";
}

$salles = mysqli_query($conn, $salles_sql);
$capteurs = mysqli_query($conn, $capteurs_sql);
$batiments = mysqli_query($conn, "SELECT * FROM batiment"); 
?>

<h2 class="page-title page-title-flex">
    Panneau d'Action
    <span class="badge online">Mode : <?= strtoupper($role) ?></span>
</h2>

<?= $message ?>

<div class="admin-forms-grid">
    
    <?php if ($role === 'admin'): ?>
    <fieldset>
        <legend>🏢 Gestion des Bâtiments</legend>
        <p class="fieldset-desc">Réservé à l'administrateur système.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="add_batiment">
            <label>Déclarer un nouveau bâtiment :</label>
            <input type="text" name="nom_batiment" placeholder="Ex: Batiment D" required>
            <button type="submit" class="btn-admin">Créer Bâtiment + Compte Gestionnaire</button>
        </form>
        
        <hr class="divider-dashed">
        
        <form method="POST">
            <input type="hidden" name="action" value="del_batiment">
            <label>Supprimer un bâtiment :</label>
            <select name="id_batiment" required>
                <option value="">-- Choisir un bâtiment --</option>
                <?php mysqli_data_seek($batiments, 0); while($b = mysqli_fetch_assoc($batiments)): ?>
                    <option value="<?= $b['id_batiment'] ?>"><?= htmlspecialchars($b['nom_batiment']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn-danger">Supprimer le Bâtiment</button>
        </form>
    </fieldset>
    <?php endif; ?>

    <fieldset style="grid-column: 1 / -1;">
        <legend>📡 Gestion des Capteurs</legend>
        <p class="fieldset-desc">
            <?= $role === 'admin' ? "🛡️ Vous avez les droits totaux sur l'ensemble des capteurs du campus." : "🏢 Vous gérez uniquement les capteurs de votre zone (<b>" . htmlspecialchars($_SESSION['nom_batiment_gere']) . "</b>)." ?>
        </p>
        
        <div class="admin-forms-grid">
            <form method="POST">
                <h3 class="form-heading">➕ Déployer un capteur</h3>
                <input type="hidden" name="action" value="add_capteur">
                
                <label>Nom de l'équipement :</label>
                <input type="text" name="nom_capteur" placeholder="Ex: Temp_Salle102" required>
                
                <label>Salle de destination :</label>
                <select name="id_salle" required>
                    <option value="">-- Affecter à une salle --</option>
                    <?php 
                    mysqli_data_seek($salles, 0); 
                    while($s = mysqli_fetch_assoc($salles)): 
                    ?>
                        <option value="<?= $s['id_salle'] ?>">
                            <?= htmlspecialchars($s['nom_salle']) ?> 
                            <?= isset($s['nom_batiment']) ? "({$s['nom_batiment']})" : "" ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn-admin">Enregistrer le capteur</button>
            </form>
            
            <form method="POST">
                <h3 class="form-heading text-danger">❌ Retirer un capteur</h3>
                <input type="hidden" name="action" value="del_capteur">
                
                <label>Sélectionner l'équipement :</label>
                <select name="id_capteur" required>
                    <option value="">-- Cible à détruire --</option>
                    <?php 
                    mysqli_data_seek($capteurs, 0); 
                    while($c = mysqli_fetch_assoc($capteurs)): 
                    ?>
                        <option value="<?= $c['id_capteur'] ?>">
                            <?= htmlspecialchars($c['nom_capteur']) ?> — [Salle: <?= htmlspecialchars($c['nom_salle']) ?>]
                        </option>
                    <?php endwhile; ?>
                </select>
                <p class="help-text-danger">⚠️ Attention : Action irréversible. L'historique des données sera perdu.</p>
                <button type="submit" class="btn-danger">Supprimer définitivement</button>
            </form>
        </div>
    </fieldset>

</div>

<?php 
if (file_exists('footer.php')) {
    include 'footer.php'; 
}
?>
