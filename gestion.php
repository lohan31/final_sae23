<?php
// gestion.php

// Include database configuration
require 'db.php';

// Include global header layout
include 'header.php';

// Check if user session exists, redirect to login if unauthorized
if (!isset($_SESSION['user'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$id_batiment = null;
$titre_page = "Supervision Globale";

// Restrict scope to a single building if the user is a manager
if ($_SESSION['role'] === 'gestionnaire') {
    $id_batiment = $_SESSION['id_batiment_gere'];
    $titre_page = "Supervision : " . htmlspecialchars($_SESSION['nom_batiment_gere']);
}

// Prepare query to fetch sensor information and statistics
$sql = "SELECT c.id_capteur, c.nom_capteur, s.nom_salle, 
        MIN(m.valeur) as val_min, 
        MAX(m.valeur) as val_max,
        (SELECT valeur FROM mesure m2 WHERE m2.id_capteur = c.id_capteur ORDER BY id_mesure DESC LIMIT 1) as val_actuelle
        FROM capteur c
        JOIN salle s ON c.id_salle = s.id_salle
        LEFT JOIN mesure m ON c.id_capteur = m.id_capteur";

// Apply building filter if user role is restricted
if ($id_batiment) {
    $sql .= " WHERE s.id_batiment = $id_batiment";
}

// Group metrics by unique sensor identifier
$sql .= " GROUP BY c.id_capteur";

// Execute statement against the database
$result = mysqli_query($conn, $sql);
?>

<h2 class="page-title"><?= $titre_page ?></h2>

<div class="grid-capteurs">
    <?php // Iterate through each sensor record to display statistical data ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card-capteur">
            <h3><?= htmlspecialchars($row['nom_capteur']) ?> <span class="salle-badge"><?= htmlspecialchars($row['nom_salle']) ?></span></h3>
            <div class="stats">
                <div class="stat-box">
                    <span class="stat-val" style="color:#f59e0b;"><?= $row['val_min'] ?? '-' ?></span>
                    <span class="stat-label">Min</span>
                </div>
                <div class="stat-box actuelle">
                    <span class="stat-val" style="color:#3b82f6;"><?= $row['val_actuelle'] ?? '-' ?></span>
                    <span class="stat-label">Actuel</span>
                </div>
                <div class="stat-box">
                    <span class="stat-val" style="color:#10b981;"><?= $row['val_max'] ?? '-' ?></span>
                    <span class="stat-label">Max</span>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div class="grafana-container">
    <h2> Historique des mesures</h2>
    <?php // Embed external Grafana visualization dashboard ?>
    <iframe 
        src="http://localhost:3000/d/adr8fgt/mesures-plus-visuelles?orgId=1&from=now-30d&to=now&timezone=browser&kiosk" 
        style="width: 100%; height: 600px; border: none; border-radius: 8px; background: #181b1f;" 
        title="Dashboard Grafana">
    </iframe>
</div>

<?php 
// Load layout footer if file exists
if (file_exists('footer.php')) {
    include 'footer.php'; 
}
?>
