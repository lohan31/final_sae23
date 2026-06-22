<?php
// compte_rendu.php
// Start secure session for stateful user authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include global header with navigation menu and CSS style linkage
include 'header.php';
?>

<main class="report-container">
    <header class="report-header">
        <h1 class="report-main-title">Compte-rendu Technique Global — GTC Blagnac</h1>
        <p class="report-meta-info">
            Dossier d'architecture, de planification et de rétro-ingénierie — Projet SAÉ 23
        </p>
    </header>

    <section class="report-section" id="chapitre-0">
        <header class="section-header">
            <h2 class="section-title">Introduction : Gestion de projet, Planification et Diagramme de GANTT</h2>
            <span class="section-badge">Project Management & Planning</span>
        </header>
        
        <section class="section-content">
            <p class="content-paragraph">
                La réussite de la SAÉ 23 reposait avant tout sur une gestion de projet rigoureuse et une anticipation des contraintes techniques. Afin de respecter le volume horaire imparti (17h de séances encadrées et 12h de travail en autonomie) ainsi que les échéances strictes imposées par le cahier des charges, nous avons structuré notre démarche autour d'un Diagramme de GANTT.
            </p>
            <p class="content-paragraph">
                Cet outil de pilotage a été fondamental pour cartographier le cycle de vie du projet et organiser notre travail en plusieurs jalons critiques (livrables) :
            </p>
            <ul class="content-list">
                <li><strong>Livrable 1 (L1) :</strong> Phase de conception initiale, incluant l'élaboration du planning GANTT et la modélisation du schéma de la base de données (Modèle Entité-Association).</li>
                <li><strong>Livrables 2 et 3 (L2 & L3) :</strong> Phase de traitement IoT et de visualisation, comprenant la configuration du flux Node-RED pour le routage des données MQTT, ainsi que la création du Dashboard interactif sous Grafana.</li>
                <li><strong>Livrable 4 (L4) :</strong> Phase de consolidation et de livraison finale, englobant la finalisation du développement Web dynamique, la traduction des commentaires de code en anglais (compétence R210), et le versioning complet du projet sur GitHub.</li>
            </ul>
        </section>
    </section>

         <p class="content-paragraph">
    Le diagramme de GANTT a agi comme un véritable fil conducteur. Il nous a permis d'identifier rapidement les dépendances entre les tâches (par exemple, la nécessité absolue de finaliser la structure MariaDB avant de pouvoir coder les requêtes PHP ou configurer l'insertion via Node-RED). Grâce à ce suivi continu, la charge de travail a été répartie de manière équilibrée entre l'administration système GNU/Linux, le développement réseau (flux IoT) et la programmation web, garantissant ainsi une livraison de la version finale dans les délais impartis.
</p>

<figure class="report-figure" id="fig-gantt">
    <img src="img/gantt.png" alt="Diagramme de GANTT du projet SAÉ 23" class="report-img">
    <figcaption class="figure-caption">Figure 1 : Diagramme de GANTT — Planification temporelle et jalons du projet</figcaption>
</figure>
        </section> </section>

    <section class="report-section" id="chapitre-1">
        <header class="section-header">
            <h2 class="section-title">Chapitre 1 : Déploiement du serveur LAMPP, création de la base de données et automatisation via recup.sh</h2>
            <span class="section-badge">Infrastructure & Backend Ingestion</span>
        </header>
        
        <section class="section-content">
            <p class="content-paragraph">
                La première phase de notre projet de Gestion Technique Centralisée (GTC) a consisté à préparer l'environnement d'hébergement sur notre machine virtuelle GNU/Linux. L'objectif était de mettre en place une infrastructure autonome, capable de stocker et de traiter les futures données IoT du campus.
            </p>
            
            <section class="step-block">
                <h3 class="step-title">1.1 Installation et Configuration du Serveur LAMPP</h3>
                <p class="content-paragraph">
                    Pour héberger notre application Web et notre base de données, nous avons opté pour la pile logicielle LAMPP (Linux, Apache, MySQL/MariaDB, PHP). Cette solution centralisée garantit une compatibilité parfaite entre le serveur Web et le moteur de base de données. Le déploiement a nécessité l'extraction de l'archive dans le répertoire système <code>/opt/lampp/</code> et l'attribution des permissions d'exécution nécessaires via la commande <code>chmod</code> pour sécuriser l'accès aux binaires. Une fois configurés, nous avons démarré les démons Apache (port 80 pour le flux HTTP) et MariaDB (port 3306 pour les requêtes SQL) via l'interface de contrôle XAMPP, assurant ainsi la disponibilité locale de nos services.
                </p>
            </section>

            <section class="step-block">
    <h3 class="step-title">1.2 Modélisation et Déploiement de la Base de Données (MariaDB)</h3>
    <p class="content-paragraph">
        La persistance des données environnementales nécessite une structure relationnelle robuste. Via l'interface d'administration phpMyAdmin, nous avons conçu un Modèle Entité-Association (MEA) normalisé. L'enjeu était d'éviter la redondance des données tout en garantissant l'intégrité référentielle du campus. Notre schéma s'articule autour de quatre tables principales liées par des contraintes de clés étrangères :
    </p>
    <ul class="content-list">
        <li><strong>batiment :</strong> Référence les structures physiques du campus (Bâtiment B, Bâtiment E) et leur assigne un identifiant de gestionnaire.</li>
        <li><strong>salle :</strong> Entité enfant reliée à un bâtiment spécifique.</li>
        <li><strong>capteur :</strong> Équipement physique déployé dans une salle. Conformément au cahier des charges, nous avons déclaré 4 capteurs (Température et Humidité) répartis équitablement sur 2 bâtiments.</li>
        <li><strong>mesure :</strong> Table de faits volumétrique enregistrant les remontées IoT avec une valeur numérique, un horonatage (timestamp) et l'identifiant du capteur émetteur.</li>
    </ul>
</section>

<figure class="report-figure" id="fig-database">
    <img src="img/database_schema.png" alt="Schéma de la base de données MariaDB" class="report-img">
    <figcaption class="figure-caption">Figure 2 : Modèle relationnel de la base de données sous phpMyAdmin</figcaption>
</figure>

<section class="step-block">
    <h3 class="step-title">1.3 Création et automatisation du script recup.sh</h3>
    <p class="content-paragraph">
        Pour assurer l'alimentation continue de notre base de données, nous avons développé un script système nommé <code>recup.sh</code>. Ce script Bash agit comme un pont essentiel entre les données brutes circulant sur le réseau et notre stockage structuré. Son rôle est d'intercepter les trames de données, de parser leur contenu, puis d'exécuter dynamiquement les requêtes SQL d'insertion dans la table <code>mesure</code>. Afin de rendre ce processus totalement autonome, nous avons rendu ce fichier exécutable (<code>chmod +x recup.sh</code>) et l'avons intégré au planificateur de tâches de Linux (Cron). La commande suivante a été ajoutée à notre Crontab :
    </p>
    <pre class="code-block"><code>* * * * * /bin/bash /home/tcolin/recup.sh >> /home/tcolin/logs_recup.log 2>&1</code></pre>
    <p class="content-paragraph">
        Cette instruction garantit l'exécution du script d'acquisition chaque minute. Nous y avons également intégré une journalisation complète des flux de sortie et d'erreur, permettant d'auditer silencieusement le comportement du script en tâche de fond.
    </p>
</section>
        </section> </section>

    <section class="report-section" id="chapitre-2">
        <header class="section-header">
            <h2 class="section-title">Chapitre 2 : Traitement des flux IoT (Node-RED) et Visualisation des données (Grafana)</h2>
            <span class="section-badge">Data Orchestration & Supervision</span>
        </header>
        
        <section class="section-content"> <p class="content-paragraph">
                Une fois l'infrastructure de base et le stockage opérationnels, l'étape suivante consistait à mettre en place le pipeline de données. Il s'agissait de capturer les trames IoT transitant sur le réseau local, de les traiter, puis de les restituer sous forme d'une interface graphique interactive et compréhensible pour les gestionnaires.
            </p>
            
            <section class="step-block"> <h3 class="step-title">2.1 Orchestration et traitement des données avec Node-RED</h3>
                <p class="content-paragraph">
                    Pour faire le pont entre notre réseau de capteurs et la base de données, nous avons déployé Node-RED, un puissant outil de programmation visuelle agissant comme un Middleware (intergiciel). Fonctionnant sur le port réseau 1880, ce service a été configuré pour s'exécuter en tâche de fond de manière de manière persistante, résistant aux déconnexions du terminal. Notre chaîne de traitement (Flow) sous Node-RED a été construite pour interagir directement avec le broker MQTT (Mosquitto). L'outil s'abonne en temps réel aux canaux (Topics) sur lesquels les 4 capteurs du campus publient leurs mesures de température et d'humidité. Lorsqu'une trame est interceptée, Node-RED extrait la charge utile (Payload), analyse la structure des données (parsing JSON ou extraction de chaînes), et formate ces valeurs pour les rendre compatibles avec nos requêtes d'insertion SQL vers MariaDB. Cette architecture garantit un découplage parfait entre la couche matérielle (les capteurs) et la couche de stockage.
                </p>
            </section>

            <figure class="report-figure" id="fig-nodered">
                <img src="img/nodered_flow.png" alt="Flux logique Node-RED" class="report-img">
                <figcaption class="figure-caption">Figure 3 : Chaîne de traitement et routage des événements IoT dans Node-RED</figcaption>
            </figure>

            <section class="step-block">
                <h3 class="step-title">2.2 Dataviz : Création du Dashboard Grafana</h3>
                <p class="content-paragraph">
                    La supervision de la GTC nécessitant une lecture immédiate et analytique des données historiques, nous avons intégré Grafana en tant que solution de Data Visualisation (Dataviz). Après avoir configuré MariaDB comme source de données native dans Grafana, nous avons élaboré un Dashboard complet. Conformément au cahier des charges, ce tableau de bord centralise et trace de manière dynamique les courbes d'évolution des 4 capteurs répartis sur les 2 bâtiments gérés. L'utilisation de requêtes temporelles adaptées permet aux opérateurs d'observer facilement les pics de chaleur, les chutes d'humidité et les tendances globales du campus sur différentes fenêtres de temps.
                </p>
            </section>

            <section class="step-block">
                <h3 class="step-title">2.3 Intégration transparente et levée des verrous de sécurité (Mode Kiosk)</h3>
                <p class="content-paragraph">
                    L'objectif final était de ne pas forcer l'utilisateur à naviguer entre deux sites différents, mais d'intégrer directement les graphiques Grafana au cœur de notre portail Web PHP. Cependant, par défaut, Grafana bloque son intégration dans des balises <code>&lt;iframe&gt;</code> externes afin de prévenir les attaques par détournement de clic (Clickjacking, en-tête <code>X-Frame-Options: deny</code>). Pour contourner cette restriction de manière maîtrisée, nous avons modifié la configuration profonde du serveur via le fichier <code>grafana.ini</code>. Nous avons activé la directive <code>allow_embedding = true</code> pour autoriser l'encapsulation, et activé l'authentification anonyme (rôle Viewer) pour éviter à l'utilisateur de devoir s'identifier une seconde fois. Enfin, pour parfaire l'expérience utilisateur (UX) et l'intégration visuelle, nous avons injecté le paramètre d'URL <code>&amp;kiosk</code> lors de l'appel de l'iframe dans notre page PHP. Cette commande masque instantanément l'interface native d'administration de Grafana (menus latéraux, barres de recherche), ne laissant apparaître que les courbes et les jauges. Les graphiques semblent ainsi avoir été développés nativement au sein de notre interface Web.
                </p>
            </section>
        </section> </section>

    <section class="report-section" id="chapitre-3">
        <header class="section-header">
            <h2 class="section-title">Chapitre 3 : Développement du Portail Web Dynamique et Sécurité</h2>
            <span class="section-badge">Dynamic Web Application & Cybersecurity</span>
        </header>
        
        <section class="section-content">
            <p class="content-paragraph">
                Cette phase a consisté à concevoir l'interface utilisateur à l'aide de PHP procédural, HTML5 et CSS3, en mettant l'accent sur la modularité, l'ergonomie et la cybersécurité.
            </p>
            
            <section class="step-block">
                <h3 class="step-title">3.1 Architecture Modulaire & UI/UX (Front-End)</h3>
                <p class="content-paragraph">
                    Pour éviter la duplication de code (principe DRY), l'interface a été factorisée. Le menu et l'en-tête sont centralisés dans <code>header.php</code> et le pied de page dans <code>footer.php</code>, les vues (<code>gestion.php</code>, <code>admin.php</code>) s'injectant dynamiquement entre les deux. Tout le style est déporté dans <code>style.css</code> (aucun CSS inline) avec un système CSS Grid 100 % responsive. Conformément au cahier des charges, la page de supervision présente les données sous deux formes : des cartes visuelles et un tableau HTML sémantique strict.
                </p>
            </section>

            <section class="step-block">
                <h3 class="step-title">3.2 Gestion des Sessions & Privilèges (Back-End)</h3>
                <p class="content-paragraph">
                    Le contrôle d'accès repose sur les sessions PHP (<code>session_start()</code>). Après connexion (<code>login.php</code>), l'utilisateur est routé selon son rôle. L'administrateur dispose des pleins droits CRUD sur le panneau <code>admin.php</code>. À l'inverse, le gestionnaire est restreint à sa zone de responsabilité : une variable mémorise l'ID de son bâtiment en session et filtre hermétiquement toutes les requêtes SQL via une clause <code>WHERE id_batiment = ...</code>.
                </p>
            </section>

            <section class="step-block">
                <h3 class="step-title">3.3 Sécurité Applicative & OWASP</h3>
                <p class="content-paragraph">
                    Pour éradiquer la faille critique des Injections SQL, la concaténation de variables a été bannie au profit de requêtes préparées (<code>mysqli_prepare</code> et <code>mysqli_stmt_bind_param</code>). Les données saisies sont ainsi traitées comme du texte littéral inoffensif. Les failles XSS sont neutralisées par la fonction <code>htmlspecialchars()</code>, tandis que la sécurité des flux est assurée par une règle de redirection automatique HTTP vers HTTPS dans le fichier <code>.htaccess</code>.
                </p>
            </section>

            <section class="step-block">
                <h3 class="step-title">3.4 Internationalisation (Norme R210)</h3>
                <p class="content-paragraph">
                    Afin de valider cette compétence technique, l'intégralité des commentaires au sein des scripts PHP a été rédigée en anglais technique, répondant ainsi aux standards de l'industrie logicielle.
                </p>
            </section>
        </section> </section>
</main>
<?php 
// Include global site footer to terminate DOM structure
if (file_exists('footer.php')) {
    include 'footer.php'; 
}
?>

