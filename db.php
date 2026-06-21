<?php
$host = "localhost";
$user = "root"; // ou ton utilisateur
$pass = "";     // ou ton mot de passe
$dbname = "sae23";

// Connexion procédurale
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Vérification de la connexion
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Optionnel : Forcer l'encodage en UTF-8
mysqli_set_charset($conn, "utf8");
?>
