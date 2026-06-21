<?php include 'header.php'; ?>

<section id="accueil">
    <header>
        <h2>Portail de Supervision Environnementale</h2>
        <p>Déploiement et centralisation des données IoT pour l'optimisation énergétique des bâtiments de l'IUT.</p>
    </header>

    <article class="hero-text">
        <h3>Objectif de la plateforme</h3>
        <p>Ce portail web sémantique constitue le point d'orgue de la <strong>SAÉ 23</strong>. Il permet d'intercepter, de structurer, de stocker et de visualiser dynamiquement les flux d'informations capturés par les modules <strong>AM107</strong> via le protocole sécurisé <strong>MQTT (TLS)</strong>, injectés en continu dans une base de données temporelle <strong>InfluxDB</strong> et restitués ici même et via <strong>Grafana</strong>.</p>
    </article>

    <section class="grid-infrastructure">
        <article>
            <h3>Bâtiments Gérés</h3>
            <p>Supervision active de <strong>2 complexes académiques</strong> majeurs de l'IUT de Blagnac, analysant les îlots de chaleur et l'efficacité des systèmes de ventilation.</p>
        </article>
        <article>
            <h3>Salles Équipées</h3>
            <p>Déploiement de capteurs multi-paramétriques (Température, Humidité) au sein de <strong>4 salles tests</strong> stratégiques : Salles de cours, laboratoires réseaux et bureaux informatiques.</p>
        </article>
    </section>

    <footer class="legal-mentions">
        <h3>Mentions Légales & Conformité</h3>
        <p>Ce système de Gestion Technique Centralisée (GTC) est développé à des fins pédagogiques dans le cadre du département Réseaux et Télécommunications (R&T). Les données collectées respectent le RGPD, aucune donnée à caractère personnel n'est capturée. Hébergement : Serveur local LAMPP VM-sae23colin.</p>
    </footer>
</section>

<?php include 'footer.php'; ?>
