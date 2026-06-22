<?php
// login.php

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect already logged-in users to home page
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require 'db.php'; 

$error = "";

// Handle authentication request on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login = htmlspecialchars($_POST['login']);
    $password = $_POST['password'];

    // Fetch user details using a prepared statement
    $sql = "SELECT * FROM utilisateur WHERE login = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verify user credentials
    if ($user && $password === $user['mot_de_passe']) {
        
        // Initialize user session variables
        $_SESSION['user'] = $user['login'];
        $_SESSION['role'] = $user['role'];

        // Retrieve linked building details for manager accounts
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

        // Redirect to homepage if no configuration errors occurred
        if (empty($error)) {
            header("Location: index.php");
            exit;
        }

    } else {
        $error = "Identifiants incorrects. Veuillez réessayer.";
    }
}

// Load page layout header
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
        <legend> Authentification</legend>
        
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
// Load page layout footer if available
if (file_exists('footer.php')) {
    include 'footer.php'; 
}
?>
