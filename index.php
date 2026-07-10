<?php
require __DIR__ . '/config/database.php';

// 1. Récupérer toutes les technos pour alimenter le menu déroulant (avec leur ID de la BDD)
$toutes_les_technos = $pdo->query('SELECT * FROM technologies ORDER BY nom ASC')->fetchAll(PDO::FETCH_ASSOC);
$types = $pdo->query('SELECT * FROM project_types ORDER BY nom ASC')->fetchAll(PDO::FETCH_ASSOC);
// 2. Récupérer les projets avec les IDs de leurs technos concaténés (ex: "1,3")
$projets = $pdo->query('
    SELECT p.*, GROUP_CONCAT(pt.techno_id SEPARATOR ",") as list_tech_ids 
    FROM projets p 
    LEFT JOIN projet_technologies pt ON p.id = pt.projet_id
    GROUP BY p.id
    ORDER BY p.ordre ASC
')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portfolio d'une dev créa</title>
    <meta name="description" content="Une description accrocheuse pour les résultats de recherche Google (max 155 caractères).">
    <link rel="canonical" href="https://www.tonsite.com/ma-page">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.tonsite.com/ma-page">
    <meta property="og:title" content="Titre de ta page">
    <meta property="og:description" content="Une description accrocheuse pour les réseaux sociaux.">
    <meta property="og:image" content="https://www.tonsite.com/img/og-image.jpg">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Titre de ta page">
    <meta name="twitter:description" content="Une description accrocheuse.">
    <meta name="twitter:image" content="https://www.tonsite.com/img/og-image.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./build/css/style.css">
    <script src="./assets/js/nav.js"></script>
    <style>
        /* BASE DE LA SECTION PROJETS */
        #projets {
            color: #ffffff !important;
            font-family: sans-serif !important;
            text-align: left !important;
            padding: 40px 20px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        /* ==========================================
           BARRE DE RECHERCHE & FILTRE (RÉPARÉ !)
           ========================================== */
        #projets .filter-section {
            margin: 20px 0 30px 0 !important;
            display: block !important;
            text-align: left !important;
        }
        #projets .search-wrapper {
            display: inline-flex !important;
            align-items: center !important;
            background-color: #231625 !important;
            border: 1px solid #3d2943 !important;
            border-radius: 6px !important;
            width: 380px !important;
            max-width: 100% !important;
            padding: 8px 14px !important;
            position: relative !important;
            cursor: pointer !important;
        }
        #projets .search-wrapper .arrow-btn {
            color: #ffffff !important;
            font-size: 10px !important;
            margin-right: 12px !important;
        }
        #projets .search-wrapper input {
            width: 100% !important;
            background: transparent !important;
            border: none !important;
            color: #ffffff !important;
            font-size: 14px !important;
            outline: none !important;
        }
        #projets .suggestions-list {
            position: absolute !important;
            top: calc(100% + 5px) !important;
            left: 0 !important;
            width: 100% !important;
            background-color: #231625 !important;
            border: 1px solid #3d2943 !important;
            border-radius: 6px !important;
            margin: 0 !important;
            padding: 5px 0 !important;
            list-style: none !important;
            z-index: 9999 !important;
            display: none; /* Géré par le JS */
        }
        #projets .suggestion-item {
            padding: 12px 18px !important;
            color: #ffffff !important;
            font-size: 14px !important;
            cursor: pointer !important;
            text-align: left !important;
        }
        #projets .suggestion-item:hover {
            background-color: #3d2943 !important;
            color: #a855f7 !important;
        }

        /* ==========================================
           GRILLE & CARTES (NET, SANS DOUBLE CADRE + OPTION CLAIRE)
           ========================================== */
        #projets .grille-projets {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)) !important;
            gap: 25px !important;
            margin-top: 25px !important;
            width: 100% !important;
        }

        #projets .carte-projet {
            display: flex !important;
            position: relative !important;
            background-color: transparent !important; /* Pas de fond par défaut pour fusionner avec l'image */
            border-radius: 28px !important;
            width: 100% !important;
            aspect-ratio: 1 / 1 !important;
            overflow: hidden !important;
            text-decoration: none !important;
            box-sizing: border-box !important;
            padding: 24px !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        /* MODIFICATION : Le fond passe au blanc pur si c'est un Logo/Illustrator */
        #projets .carte-projet.theme-clair {
            background-color: #ffffff !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
        }

        /* Image d'arrière-plan - Très nette et ajustée */
        #projets .carte-background-img {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            opacity: 0.90 !important; /* Image de base bien nette */
            z-index: 1 !important;
            pointer-events: none !important;
            transition: opacity 0.2s ease !important;
        }


        /* Contenu transparent superposé */
        #projets .carte-corps {
            position: relative !important;
            z-index: 2 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            width: 100% !important;
            height: 100% !important;
            background: transparent !important;
        }

        /* Titre standard (Blanc) */
        #projets .carte-titre {
            color: #ffffff !important;
            font-size: 22px !important;
            font-weight: 600 !important;
            text-align: left !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5) !important;
        }

        /* MODIFICATION : Le titre passe en noir profond sur les cartes de logos blanches */
        #projets .carte-projet.theme-clair .carte-titre {
            color: #140d16 !important;
            text-shadow: none !important;
        }

        #projets .carte-pills-container {
            text-align: left !important;
        }

        /* Badge sombre du bas */
        #projets .carte-badge {
            display: inline-block !important;
            background-color: #140d16 !important;
            color: #ffffff !important;
            padding: 6px 14px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-weight: bold !important;
        }
    </style>
    <style>
        /* Le conteneur qui aligne tout */
        #etudes {
            /* Ajoute cette ligne pour forcer le parent à contenir tous ses enfants */
            display: flow-root;

            /* OU essaie cela si le flow-root ne suffit pas */
            overflow: hidden;

            /* Assure-toi de ne pas avoir de hauteur fixe */
            height: auto;
            min-height: 100vh;
            padding-bottom: 50px; /* Ajoute de l'espace en bas de section */
        }
        /* 1. Élargir la grille globale */
        .timeline-row, .etudes-timeline-header {
            display: grid;
            /* On passe à 200px pour la date, 40px pour la ligne, et on laisse tout le reste pour le contenu */
            grid-template-columns: 200px 40px 1fr;
            align-items: start;
            margin-bottom: 50px; /* Plus d'espace entre les éléments */
        }

        /* La ligne verticale */
        .line {
            position: relative;
            height: 100%;
            width: 2px;
            background-color: #38293b; /* Couleur de ta ligne */
            margin: 0 auto;
        }

        /* Optionnel : ajouter un petit point sur la ligne */
        .line::before {
            content: '';
            position: absolute;
            top: 0;
            left: -4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #a855f7; /* Couleur violette */
        }

        /* Style de tes cartes */
        .card {
            background-color: #231625;
            border: 1px solid #3d2943;
            padding: 30px; /* Plus de padding interne */
            border-radius: 15px;
            margin-top: 15px;
            width: 100%;       /* Force la largeur à 100% de la colonne */
            min-height: 250px; /* Augmente la hauteur pour tes images */
            max-width: 800px;  /* Tu peux retirer cette ligne si tu veux que ça prenne TOUT l'espace */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    </style>
    <style>
        /* Utilisation d'une grille symétrique : 1fr | 40px | 1fr */
        .exp-row, .exp-header {
            display: grid;
            /* La colonne de gauche (carte) et de droite (texte) ont la même taille (1fr) */
            grid-template-columns: 1fr 40px 1fr;
            align-items: start;
            margin-bottom: 50px;
            width: 100%;
        }

        /* Aligner la carte à DROITE de sa colonne (vers la ligne) */
        .content-col {
            display: flex;
            justify-content: flex-end;
            padding-right: 20px; /* Petit espace entre la carte et la ligne */
        }

        /* Aligner le texte à GAUCHE de sa colonne (vers la ligne) */
        .date-col {
            text-align: left;
            padding-left: 20px; /* Petit espace entre le texte et la ligne */
            color: #fff;
        }

        /* Optionnel : Ajuste la carte pour qu'elle ne soit pas trop large */
        .exp-row .card {
            width: 100%;
            max-width: 450px; /* Empêche la carte de devenir immense */
        }



        /* Positionnement du menu déroulant */
.search-wrapper {
    position: relative; /* Indispensable pour positionner le menu en dessous */
}

.suggestions-list {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: #1e1520;
    border: 1px solid #3d2943;
    border-radius: 6px;
    list-style: none;
    padding: 0;
    margin: 5px 0 0 0;
    display: none; /* Caché par défaut */
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
}

.suggestions-list.active {
    display: block; /* Affiché quand on clique */
}

.suggestion-item {
    padding: 10px;
    cursor: pointer;
    color: white;
}

.suggestion-item:hover {
    background: #3d2943;
}
</style>
</head>
<body style="background-color:#131016">
<header class="sidebar-nav">
    <nav>
        <ul class="nav-list">
            <li>
                <a href="#presentation" class="nav-link active" aria-label="Présentation">
                    <i class="fa-solid fa-house"></i>
                </a>
            </li>
            <li>
                <a href="#aPropos" class="nav-link" aria-label="À propos">
                    <i class="fa-solid fa-box"></i>
                </a>
            </li>
            <li>
                <a href="#projets" class="nav-link" aria-label="Projets">
                    <i class="fa-solid fa-book"></i>
                </a>
            </li>
            <li>
                <a href="#etudes" class="nav-link" aria-label="Études">
                    <i class="fa-solid fa-briefcase"></i>
                </a>
            </li>
            <li>
                <a href="#contact" class="nav-link" aria-label="Contact">
                    <i class="fa-solid fa-comments"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>
<main>
    <section class="slide" id="presentation">
        <div class="conteneur">
            <div class="pres-right">
                <h1>Laurence Barthélémy | Étudiante</h1>
                <h2>Web Développeuse</h2>
            </div>
            <div class="pres-left">
                <img src="./build/images/portrait.png" alt="portrait">
            </div>
        </div>
    </section>

    <section class="slide" id="aPropos">
        <h2>À propos</h2>
        <p>Je suis intéressée par le commerce, le développement web et j'ai pu déjà faire quelque site depuis mes 3 années d'étude supérieur</p>
        <p>Mes passions quand a eux c'est la philosophie, les technologies VR/XR, la 3D et la musique</p>
        <img src="./build/images/logiciel/html.svg" alt="HTML">
        <img src="./build/images/logiciel/css.svg" alt="CSS">
        <img src="./build/images/logiciel/illustrator.svg" alt="illustrator">
        <img src="./build/images/logiciel/premierpro.svg" alt="premierpro">
        <img src="./build/images/logiciel/photoshop.svg" alt="photoshop">
    </section>
    <section class="slide" id="projets">
        <h2 style="font-size: 36px; font-weight: 300; letter-spacing: 2px; margin-bottom: 20px;">PROJETS</h2>

        <!-- SECTION DES FILTRES (Container Flex) -->
        <div class="filter-controls" style="display: flex; gap: 40px; align-items: flex-start; margin-bottom: 30px; flex-wrap: wrap;">

            <!-- 1. Filtre Technologie (à gauche) -->
            <div class="search-wrapper" id="filter-dropdown-toggle">
                <span class="arrow-btn">▲</span>
                <input type="text" id="filter-input" placeholder="Filtrer par technologie..." autocomplete="off"
                       style="padding: 10px; border-radius: 6px; border: 1px solid #3d2943; background: #1e1520; color: white;">
                <ul class="suggestions-list" id="filter-options">
                    <li class="suggestion-item" data-techid="all">Toutes les technologies</li>
                    <?php foreach ($toutes_les_technos as $tech): ?>
                        <li class="suggestion-item" data-techid="<?= $tech['id'] ?>">
                            <?= htmlspecialchars($tech['nom']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- 2. Filtre Types (à droite du précédent) -->
            <div class="type-filter-wrapper" style="display: grid; grid-template-columns: repeat(3, auto); gap: 10px 20px; color: white; font-size: 14px;">
                <?php foreach($types as $t): ?>
                    <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="type_filter" value="<?= $t['id'] ?>" class="type-checkbox">
                        <?= htmlspecialchars($t['nom']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- GRILLE DE PROJETS (Séparée des filtres) -->
        <div class="grille-projets">
            <?php foreach ($projets as $projet) :
                $isLogo = (stripos($projet['titre'], 'logo') !== false) || (stripos($projet['list_tech_names'] ?? '', 'illustrator') !== false);
                $classeTheme = $isLogo ? 'theme-clair' : '';
                ?>

                <a href="projet.php?id=<?= $projet['id'] ?>"
                   class="carte-projet <?= $classeTheme ?>"
                   data-techids="<?= htmlspecialchars($projet['list_tech_ids'] ?? '') ?>"
                   data-type-id="<?= htmlspecialchars($projet['type_id'] ?? '') ?>">

                    <?php if (!empty($projet['image'])) : ?>
                        <img src="<?= htmlspecialchars($projet['image']) ?>" class="carte-background-img" alt="">
                    <?php endif; ?>

                    <div class="carte-corps">
                        <?php if (!empty($projet['type_nom'])): ?>
                            <span class="carte-type-badge"><?= htmlspecialchars($projet['type_nom']) ?></span>
                        <?php endif; ?>

                        <!-- <div class="carte-titre">
                            <?= htmlspecialchars($projet['titre']) ?>
                        </div> -->

                        <div class="carte-pills-container">
                            <?php
                            if (!empty($projet['list_tech_names'])) {
                                $techs = explode(', ', $projet['list_tech_names']);
                                foreach ($techs as $t) {
                                    echo '<span class="carte-badge">' . htmlspecialchars($t) . '</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <br><br>
        <a href="admin/admin.php" style="color: #a855f7; text-decoration: none; font-weight: bold; font-size: 14px;">➡️ Accéder à l'interface d'administration</a>
    </section>
    <section class="slide" id="etudes">
        <h2>Mes Etudes</h2>

        <!-- Le header est HORS de la boucle des items -->
        <div class="etudes-timeline-header">
            <span>Date</span>
            <span></span> <!-- Espace pour la ligne -->
            <span>Ecole</span>
        </div>

        <!-- Conteneur des items -->
        <div class="timeline-items">
            <!-- Item 1 -->
            <div class="timeline-row">
                <div class="date">2024 - 2026</div>
                <div class="line"></div>
                <div class="school">
                    <h3>Nom de l'Ecole</h3>
                    <div class="card">
                        <p>Images de réalisation lié aux études ou durant...</p>
                    </div>
                </div>
            </div>

            <!-- Item 2 (Copie juste ce bloc) -->
            <div class="timeline-row">
                <div class="date">2022 - 2024</div>
                <div class="line"></div>
                <div class="school">
                    <h3>Nom de l'Ecole</h3>
                    <div class="card"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="slide" id="experiences">
        <h2>Mes Expériences</h2>

        <!-- Header inversé -->
        <div class="exp-header">
            <span></span> <!-- Espace vide à gauche -->
            <span></span> <!-- Espace pour la ligne -->
            <span class="header-right">Date | Ecole</span>
        </div>

        <div class="timeline-items">
            <!-- Item Expérience -->
            <div class="exp-row">
                <div class="content-col">
                    <div class="card">
                        <p>Images de réalisation lié au job, travail...</p>
                    </div>
                </div>
                <div class="line"></div> <!-- La ligne centrale -->
                <div class="date-col">
                    <p>2024 - 2026</p>
                    <p>Nom de l'Ecole/Entreprise</p>
                </div>
            </div>
        </div>
    </section>

    <section class="slide contact-section" id="contact">
        <div class="contact-container">
            <div class="contact-right-col">
                <h2 class="contact-main-title">Prenez contact !</h2>
                <form class="contact-form">
                    <div class="form-row-double">
                        <div class="form-group">
                            <label for="nom">Nom<span class="pink-star">*</span></label>
                            <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom<span class="pink-star">*</span></label>
                            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email<span class="pink-star">*</span></label>
                        <input type="email" id="email" name="email" placeholder="exemple@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="1" oninput="autoExpand(this)" placeholder="Écrivez votre message..."></textarea>
                    </div>

                    <div class="form-submit-block">
                        <button type="submit" class="btn-submit">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<footer>
</footer>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- 1. Sélection ---
    const filterInput = document.getElementById('filter-input');
    const filterOptions = document.getElementById('filter-options');
    const typeCheckboxes = document.querySelectorAll('.type-checkbox');
    const cards = document.querySelectorAll('.carte-projet');
    const suggestionItems = document.querySelectorAll('.suggestion-item');

  function appliquerFiltres() {
    const searchTerm = filterInput.value.trim().toLowerCase();
    const selectedTypes = Array.from(typeCheckboxes).filter(c => c.checked).map(c => c.value);
    const activeTechId = filterInput.dataset.activeTechId || '';

    cards.forEach(card => {
        // 1. Récupération des données de la carte
        const cardTechIds = card.getAttribute('data-techids') ? card.getAttribute('data-techids').split(',') : [];
        const cardTypeId = card.getAttribute('data-type-id');
        
        // On récupère tous les badges de cette carte spécifique
        const badges = card.querySelectorAll('.carte-badge');
        
        // 2. LOGIQUE TECH (Le cœur du problème)
        let matchesTech = false;

        if (searchTerm === '') {
            matchesTech = true; // Rien de tapé = tout afficher
        } else if (activeTechId !== '') {
            // Si on a cliqué sur une suggestion (ID précis)
            matchesTech = cardTechIds.includes(activeTechId);
        } else {
            // RECHERCHE TEXTUELLE : On vérifie si un des badges contient le mot tapé
            badges.forEach(badge => {
                if (badge.textContent.toLowerCase().includes(searchTerm)) {
                    matchesTech = true;
                }
            });
            // Facultatif : chercher aussi dans le titre si tu en as un
            // const title = card.querySelector('.carte-titre');
            // if (title && title.textContent.toLowerCase().includes(searchTerm)) matchesTech = true;
        }

        // 3. LOGIQUE TYPE
        const matchesType = (selectedTypes.length === 0) || (selectedTypes.includes(cardTypeId));

        // 4. AFFICHAGE
        if (matchesTech && matchesType) {
            card.style.setProperty('display', 'flex', 'important');
        } else {
            card.style.setProperty('display', 'none', 'important');
        }
    });
}
    // --- 3. Écouteurs ---

    // Quand on tape : on montre le menu, on filtre la liste, et on filtre les projets
    filterInput.addEventListener('input', (e) => {
        console.log("Texte tapé : ", e.target.value); // Vérifie si ça s'affiche quand tu tapes

        filterInput.dataset.activeTechId = '';
        filterOptions.style.display = 'block';

        // Test : est-ce que le filtrage se lance ?
        appliquerFiltres();
    });

    // Quand on clique sur l'input : ouvrir le menu
    filterInput.addEventListener('click', (e) => {
        e.stopPropagation();
        filterOptions.style.display = 'block';
    });

    // Quand on coche un type
    typeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', appliquerFiltres);
    });

    // Quand on clique sur une suggestion
    suggestionItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            const idSelectionne = item.getAttribute('data-techid');

            if (idSelectionne === 'all') {
                filterInput.value = '';
                filterInput.dataset.activeTechId = '';
            } else {
                filterInput.value = item.textContent.trim();
                filterInput.dataset.activeTechId = idSelectionne;
            }

            filterOptions.style.display = 'none';
            appliquerFiltres();
        });
    });

    // Fermer le dropdown si on clique ailleurs
    document.addEventListener('click', () => {
        if (filterOptions) filterOptions.style.display = 'none';
    });
});


</script>
</body>
</html>
