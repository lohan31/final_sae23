<?php 
// header.php
// Security check: only start the session if it hasn't already been started by the main page.
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTC - Supervision SAÉ 23</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1><span>GTC</span> Blagnac</h1>
        <p>Gestion Technique Centralisée • SAÉ 23</p>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="presentation-sae23.php">Présentation Projet</a></li>
                
                <li><a href="compte_rendu.php">Compte-rendu Technique</a></li>
                
                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['gestionnaire', 'admin'])): ?>
                    <li><a href="gestion.php">Gestion</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin.php">Administration Globale</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire'): ?>
                    <li><a href="admin.php">Gérer mes capteurs</a></li>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="logout.php" style="color: #fca5a5;">Déconnexion (<?= htmlspecialchars($_SESSION['user']) ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php" style="color: #6ee7b7;">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
