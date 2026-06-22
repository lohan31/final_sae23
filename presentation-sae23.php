<?php 
// Start user session management
session_start();

// Load global navigation layout, metadata configurations, and stylesheet links
include 'header.php'; 
?>

<section id="presentation" style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
    <header style="margin-bottom: 2rem; border-bottom: 1px solid var(--bordure); padding-bottom: 1rem;">
        <h2 style="font-size: 2rem; color: var(--accent-teal);">Présentation de la SAÉ 23</h2>
        <p style="font-style: italic; color: var(--texte-clair);">Mettre en place une solution informatique pour l'entreprise — IoT & Monitoring</p>
    </header>

    <div style="display: grid; gap: 2rem;">

        <article style="background: var(--bg-secondaire); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--bordure);">
            <h3 style="color: var(--accent-bleu); margin-top: 0;">Qu'est-ce que la SAÉ 23 ?</h3>
            <p>La <strong>SAÉ 23</strong> est une Situation d'Apprentissage et d'Évaluation portant sur le développement d'une solution informatique complète pour répondre à un besoin professionnel concret. Elle mobilise des compétences en programmation, bases de données, administration système Linux et travail collaboratif.</p>
            <p>Dans le cadre de l'IUT de Blagnac, la problématique proposée est une <strong>situation professionnelle IoT</strong> : exploiter des données provenant de capteurs des bâtiments de l'IUT en proposant une interface de visualisation.</p>
        </article>

        <article style="background: var(--bg-secondaire); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--bordure);">
            <h3 style="color: var(--accent-bleu); margin-top: 0;">Objectifs du projet</h3>
            <p>Le projet repose sur deux axes principaux :</p>
            <ul style="margin-left: 1.5rem; color: var(--texte-clair);">
                <li style="margin-bottom: 0.5rem;"><strong>Chaîne de traitement via des conteneurs Docker</strong> — pipeline complet Mosquitto (broker MQTT) → Node-RED (traitement événementiel) → InfluxDB (base de données temporelle) → Grafana (visualisation des métriques).</li>
                <li><strong>Site web dynamique</strong> — hébergé sur un serveur LAMPP, avec base de données MySQL, gestion de comptes (administrateur, gestionnaire, utilisateur), et affichage des données capteurs sous forme de tableaux et graphiques.</li>
            </ul>
        </article>

        <article style="background: var(--bg-secondaire); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--bordure);">
            <h3 style="color: var(--accent-bleu); margin-top: 0;">Contexte et mise en situation</h3>
            <p>Les bâtiments de l'IUT sont équipés de capteurs (température, humidité…) qui publient leurs mesures sur un <strong>broker MQTT</strong>. Notre groupe a eu pour mission de concevoir une interface conviviale permettant aux gestionnaires de chaque bâtiment de consulter et d'analyser ces données en temps réel.</p>
            <p>Le projet a été réalisé en trinôme, avec utilisation obligatoire de <strong>Git et GitHub</strong> pour la gestion de versions, sur une machine virtuelle GNU/Linux (Lubuntu).</p>
        </article>

        <article style="background: var(--bg-secondaire); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--bordure);">
            <h3 style="color: var(--accent-bleu); margin-top: 0;">Fonctionnalités développées & Contraintes</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div>
                    <h4 style="margin-bottom: 0.5rem;">Fonctionnalités :</h4>
                    <ul style="margin-left: 1.5rem; color: var(--texte-clair);">
                        <li>2 bâtiments avec gestionnaires dédiés</li>
                        <li>4 capteurs par bâtiment</li>
                        <li>Dashboard Grafana global</li>
                        <li>Flow Node-RED de routage</li>
                        <li>Base de données MySQL</li>
                        <li>Site web dynamique sécurisé (PHP)</li>
                        <li>Script Bash de récupération MQTT</li>
                        <li>Automatisation via Crontab</li>
                    </ul>
                </div>
                <div>
                    <h4 style="margin-bottom: 0.5rem;">Environnement Technique :</h4>
                    <ul style="margin-left: 1.5rem; color: var(--texte-clair);">
                        <li><strong>OS :</strong> GNU/Linux (Lubuntu)</li>
                        <li><strong>Serveur :</strong> LAMPP (Apache)</li>
                        <li><strong>BDD :</strong> MySQL & InfluxDB</li>
                        <li><strong>Middleware :</strong> Mosquitto & Node-RED</li>
                        <li><strong>Langages :</strong> HTML, CSS, PHP, Bash, JS</li>
                        <li><strong>Versioning :</strong> Git + GitHub</li>
                    </ul>
                </div>
            </div>
        </article>

        <article style="background: var(--bg-secondaire); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--bordure);">
            <h3 style="color: var(--accent-bleu); margin-top: 0;">Notre équipe</h3>
            <ul style="margin-left: 1.5rem; color: var(--texte-clair); margin-bottom: 1rem;">
                <li><strong>Lohan Taho-Taza</strong> — Chef de projet, coordination et gestion des livrables</li>
                <li><strong>Théo Colin</strong> — Développement site web, Node-RED</li>
                <li><strong>Fuad Zeynalov</strong> — Développement site web, gestion des données</li>
            </ul>
            <p style="text-align: center; margin-top: 1.5rem;">
                <a href="https://fr.vecteezy.com/photo/9301678-mignon-otarie-avec-sa-bouche-large" target="_blank" style="background: var(--accent-teal); color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 6px; font-weight: bold;">Consulter notre dépôt GitHub</a>
            </p>
        </article>

    </div>
</section>

</main> 

<footer>
    <p><strong>SAÉ 23</strong> - Créateurs du projet : Theo Colin, Fuad Zeynalov et Lohan Taho-Taza.</p>
    <p>© 2026 - Département Réseaux & Télécommunications • IUT de Blagnac</p>
</footer>

</body>
</html>
