<?php
require_once __DIR__ . '/config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit(); }

// Requête SQL corrigée : on demande explicitement les technos ET les deux colonnes de liens
$req = $pdo->prepare('
    SELECT p.id, p.titre, p.image, p.description, p.lien_site, p.lien_figma, GROUP_CONCAT(t.nom SEPARATOR ",") as technologies
    FROM projets p
    LEFT JOIN projet_technologies pt ON p.id = pt.projet_id
    LEFT JOIN technologies t ON pt.techno_id = t.id
    WHERE p.id = ?
    GROUP BY p.id
');
$req->execute([$id]);
$projet = $req->fetch(PDO::FETCH_ASSOC);

$isLogo = (stripos($projet['titre'], 'logo') !== false) || (stripos($projet['technologies'] ?? '', 'illustrator') !== false);
$classeMedia = $isLogo ? 'theme-clair-media' : '';

if (!$projet) { header('Location: index.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($projet['titre']) ?> - Portfolio</title>
    <link rel="stylesheet" href="build/css/style.css">

    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #130b15 !important;
            height: 100vh !important;
            overflow: hidden !important; /* Empêche tout scroll global sur la page */
        }

        /* Conteneur Slide verrouillé à la taille de l'écran */
        .slide {
            height: 100vh !important;
            width: 100% !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }

        .page-unique-projet {
            background-color: #130b15 !important;
            height: 100vh !important;
            color: white;
            font-family: sans-serif;
            padding: 40px 8% !important;
            box-sizing: border-box !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .btn-retour {
            color: #a855f7 !important;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .vue-projet-flex {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 60px !important;
            max-width: 1400px !important;
            width: 100% !important;
            margin: auto auto !important;
        }

        .colonne-PROJET-infos {
            flex: 1 1 50% !important;
            background: transparent !important;
            padding: 0 !important;
        }

        .colonne-PROJET-infos h1 {
            font-size: 42px !important;
            margin: 0 0 15px 0 !important;
            color: #ffffff !important;
        }

        .colonne-PROJET-infos h2 {
            font-size: 18px !important;
            color: #ccaed1 !important;
            line-height: 1.6 !important;
            margin: 0 0 30px 0 !important;
            font-weight: normal !important;
        }

        .colonne-PROJET-media {
            flex: 1 1 45% !important;
            background: transparent !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        /* CORRECTION DE L'IMAGE (Modèle image_d172a3.png corrigé) */
        .colonne-PROJET-media img {
            max-width: 100% !important;     /* S'adapte proprement à la largeur de sa colonne */
            max-height: 60vh !important;     /* Limite la hauteur pour ne jamais faire déborder la page */
            width: auto !important;          /* Conserve le ratio d'origine */
            height: auto !important;         /* Conserve le ratio d'origine */
            object-fit: contain !important;  /* FORCE l'affichage complet de l'image (sans aucun rognage) */
            border-radius: 16px !important;
            display: block !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6) !important;
        }

        .liste-techs {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            margin-top: 25px !important;
            margin-bottom: 35px !important;
        }

        .badge-tech {
            background: #2d2030 !important;
            color: #a855f7 !important;
            padding: 8px 18px !important;
            border-radius: 8px !important;
            font-size: 14px !important;
            font-weight: bold !important;
            border: 1px solid rgba(168, 85, 247, 0.2) !important;
        }

        .liste-liens-externes {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 15px !important;
            margin-top: 25px !important;
        }

        .btn-lien-externe {
            background: #2d2030 !important;
            color: #ffffff !important;
            border: 1px solid #a855f7 !important;
            padding: 12px 24px !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            font-weight: bold !important;
            font-size: 14px !important;
            transition: all 0.2s ease !important;
        }

        .btn-lien-externe:hover {
            background: #a855f7 !important;
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.4) !important;
        }
        /* Le conteneur qui force l'alignement strict de haut en bas */
        .conteneur-central-projet {
            max-width: 1300px !important; /* Même taille max pour le bouton ET le flex */
            width: 100% !important;
            margin: auto auto !important; /* Centre tout le bloc sur les écrans TV/4K */
            display: flex !important;
            flex-direction: column !important;
        }

        .page-unique-projet {
            background-color: #130b15 !important;
            height: 100vh !important;
            color: white;
            font-family: sans-serif;
            padding: 60px 5% !important; /* Un peu plus de marge sur les côtés */
            box-sizing: border-box !important;
            display: flex !important;
        }

        .zone-retour {
            width: 100% !important;
            text-align: left !important;
            margin-bottom: 30px !important;
        }

        .btn-retour {
            color: #a855f7 !important;
            text-decoration: none;
            font-weight: bold;
            display: inline-block !important;
            font-size: 16px;
        }

        .vue-projet-flex {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 60px !important;
            width: 100% !important;
        }
        .colonne-PROJET-media.theme-clair-media {
            background-color: #ffffff !important;
            padding: 60px !important;       /* Ajoute un espace blanc généreux autour du logo */
            border-radius: 28px !important; /* Arrondi pour matcher ton design */
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        /* On s'assure que le logo reste bien dans son cadre */
        .colonne-PROJET-media.theme-clair-media img {
            box-shadow: none !important;    /* Pas d'ombre sur le logo lui-même s'il est déjà sur fond blanc */
            max-height: 50vh !important;
        }
    </style>
</head>
<body>

<section class="slide">
    <div class="page-unique-projet">

        <!-- Nouveau conteneur global qui verrouille l'alignement vertical parfait -->
        <div class="conteneur-central-projet">

            <!-- LE BOUTON RETOUR (Parfaitement aligné avec le titre) -->
            <div class="zone-retour">
                <a href="index.php" class="btn-retour">← Retour aux projets</a>
            </div>

            <!-- LE CONTENU EN FLEX -->
            <div class="vue-projet-flex">
                <!-- GAUCHE : LES TEXTES -->
                <div class="colonne-PROJET-infos">
                    <h1><?= htmlspecialchars($projet['titre']) ?></h1>
                    <h2><?= htmlspecialchars($projet['description']) ?></h2>

                    <!-- COMPOSANT 1 : LES TECHNOLOGIES -->
                    <div class="liste-techs">
                        <?php
                        if (!empty($projet['technologies'])) {
                            $tags = explode(',', $projet['technologies']);
                            foreach($tags as $tag): if(!empty(trim($tag))): ?>
                                <span class="badge-tech"><?= htmlspecialchars(strtoupper(trim($tag))) ?></span>
                            <?php endif; endforeach;
                        } ?>
                    </div>

                    <!-- COMPOSANT 2 : LES BOUTONS DE LIENS -->
                    <div class="liste-liens-externes">
                        <?php if(!empty($projet['lien_site'])): ?>
                            <a href="<?= htmlspecialchars($projet['lien_site']) ?>" target="_blank" class="btn-lien-externe">
                                Sites
                            </a>
                        <?php endif; ?>

                        <?php if(!empty($projet['lien_figma'])): ?>
                            <a href="<?= htmlspecialchars($projet['lien_figma']) ?>" target="_blank" class="btn-lien-externe">
                                Figma
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- DROITE : L'IMAGE -->
                <div class="colonne-PROJET-media <?= $classeMedia ?>">
                    <?php if(!empty($projet['image'])): ?>
                        <img src="<?= htmlspecialchars($projet['image']) ?>" alt="<?= htmlspecialchars($projet['titre']) ?>">
                    <?php endif; ?>
                </div>
            </div>

        </div> <!-- Fin du conteneur-central-projet -->
    </div>
</section>

</body>
</html>