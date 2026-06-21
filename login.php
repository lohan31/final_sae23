<?php
// login.php

// 1. Démarrage de session sécurisé (évite les conflits si header.php le fait aussi)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur est déjà connecté, on l'envoie direct sur l'accueil
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db.php'; 

$error = "";

// ==========================================
// 2. TRAITEMENT DU FORMULAIRE DE CONNEXION
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login = htmlspecialchars($_POST['login']);
    $password = $_POST['password'];

    // Recherche de l'utilisateur (Requête préparée)
    $sql = "SELECT * FROM utilisateur WHERE login = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Comparaison du mot de passe
    if ($user && $password === $user['mot_de_passe']) {
        
        $_SESSION['user'] = $user['login'];
        $_SESSION['role'] = $user['role'];

        // Recherche du bâtiment pour les gestionnaires
        if ($user['role'] === 'gestionnaire') {
            $sql_bat = "SELECT id_batiment, nom_batiment FROM batiment WHERE login_gestionnaire = ?";
            $stmt_bat = mysqli_prepare($conn, $sql_bat);
            mysqli_stmt_bind_param($stmt_bat, "s", $user['login']);
            mysqli_stmt_execute($stmt_bat);
            $res_bat = mysqli_stmt_get_result($stmt_bat);
            
            if ($batiment = mysqli_fetch_assoc($res_bat)) {
                $_SESSION['id_batiment_gere'] = $batiment['id_batiment'];
                $_SESSION['nom_batiment_gere'] = $batiment['nom_batiment'];
            } else {
                $error = "Votre compte n'est lié à aucun bâtiment.";
                session_destroy();
            }
        }

        // Si tout est bon, on redirige !
        if (empty($error)) {
            header("Location: index.php");
            exit;
        }

    } else {
        $error = "Identifiants incorrects. Veuillez réessayer.";
    }
}

// ==========================================
// 3. AFFICHAGE DE LA PAGE (INCLUSIONS)
// ==========================================
include 'header.php'; 
?>

<section style="max-width: 500px; margin: 4rem auto;">
    
    <header>
        <h2>Portail GTC Blagnac</h2>
        <p>Veuillez vous identifier pour accéder au système.</p>
    </header>

    <?php if (!empty($error)): ?>
        <div class='msg-error'><?= $error ?></div>
    <?php endif; ?>

    <fieldset>
        <legend>🔐 Authentification</legend>
        
        <form action="login.php" method="POST" style="margin-bottom: 0;">
            <label for="login">Identifiant :</label>
            <input type="text" id="login" name="login" placeholder="Ex: admin_sae" required>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" placeholder="Votre mot de passe" required 
                   style="width: 100%; padding: 0.6rem 1rem; border: 1px solid var(--bordure); border-radius: 4px; background-color: var(--bg-carte); color: var(--texte-sombre); margin-bottom: 1rem;">
            
            <button type="submit" style="width: 100%;">Se connecter au portail</button>
        </form>
    </fieldset>

</section>

<?php 
if (file_exists('footer.php')) {
    include 'footer.php'; 
}
?>
