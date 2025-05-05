<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';

use App\Enums\CheminPage;

$url = "http://" . $_SERVER["HTTP_HOST"];
$css_promo = CheminPage::CSS_PROMO->value;

$errors = recuperer_session('errors', []);
$old = recuperer_session('old_inputs', []);

?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Promotions</title>
    <!-- <link rel="stylesheet" href="/assets/css/promo/promo.css" /> -->
</head>



<body>
    <div class="promo-container">
        <header class="header">
            <h2>Promotion</h2>
            <p>Gérer les promotions de l'école</p>
        </header>
        <div class="stats">
    <div class="stat orange">
        <div class="stat-content">
            <strong class="stat-value"><?= $stats['totalApprenants'] ?></strong>
            <span class="stat-label">Apprenants</span>
        </div>
        <div class="icon"><img src="/assets/icone/icone1.png" alt=""></div>
    </div>
    <div class="stat orange">
        <div class="stat-content">
            <strong class="stat-value"><?= $stats['totalReferentiels'] ?></strong>
            <span class="stat-label">Référentiels</span>
        </div>
        <div class="icon"><img src="/assets/icone/ICONE2.png" alt=""></div>
    </div>
    <div class="stat orange">
        <div class="stat-content">
            <strong class="stat-value"><?= $stats['promotionActive'] ?></strong>
            <span class="stat-label">Promotions actives</span>
        </div>
        <div class="icon"><img src="/assets/icone/ICONE3.png" alt=""></div>
    </div>
    <div class="stat orange">
        <div class="stat-content">
            <strong class="stat-value"><?= $stats['totalPromotions'] ?></strong>
            <span class="stat-label">Total promotions</span>
        </div>
        <div class="icon"><img src="/assets/icone/ICONE4.png" alt=""></div>
    </div>
    <a class="add-btn" href="index.php?page=add_promo">+ Ajouter une promotion</a>
</div>
     
        <div class="search-filter">
    <form method="GET" action="" style="display: flex; flex: 1; gap: 10px;">
        <input type="hidden" name="page" value="liste_promo" />
        <input type="text" name="search" placeholder="Rechercher une promotion..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />

        <select name="filtre" id="filtre-select">
            <option value="tous">Tous</option>
            <option value="active" <?= ($_GET['filtre'] ?? '') === 'active' ? 'selected' : '' ?>>Actives</option>
            <option value="inactive" <?= ($_GET['filtre'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactives</option>
        </select>
        <button type="submit" class="submit-btn">Rechercher</button>
    </form>

    <div class="view-toggle">
        <form method="GET" action="">
            <button class="active">Grille</button>
            <input type="hidden" name="page" value="liste_table_promo" />
            <button type="submit">Liste</button>
        </form>
    </div>
</div>

<!-- Liste des promotions -->
<div class="card-grid">
<?php
// Récupérer le filtre depuis l'URL
$filtreStatut = $_GET['filtre'] ?? 'tous';

// Filtrer les promotions selon le critère sélectionné
$filteredPromotions = $promotions;

// Appliquer le filtre de statut (actif/inactif)
if ($filtreStatut && $filtreStatut !== 'tous') {
    $statutFiltre = $filtreStatut === 'active' ? 'Active' : 'Inactive';
    $filteredPromotions = array_filter($promotions, function($promo) use ($statutFiltre) {
        return $promo['statut'] === $statutFiltre;
    });
}

// Appliquer la recherche par nom si spécifiée
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = strtolower($_GET['search']);
    $filteredPromotions = array_filter($filteredPromotions, function($promo) use ($search) {
        return strpos(strtolower($promo['nom']), $search) !== false;
    });
}

// Si aucune promotion ne correspond aux critères
if (empty($filteredPromotions)) {
    echo '<div class="no-results">Aucune promotion ne correspond à vos critères de recherche.</div>';
} else {
    // Trier les promotions pour la page courante
    $currentPagePromos = $filteredPromotions;
    
    // Séparer les promotions actives et inactives de la page courante
    $activePromos = [];
    $inactivePromos = [];
    
    foreach ($currentPagePromos as $promo) {
        if ($promo['statut'] === 'Active') {
            $activePromos[] = $promo;
        } else {
            $inactivePromos[] = $promo;
        }
    }
    
    // Fusionner les promotions avec les actives en premier pour cette page
    $orderedPromos = array_merge($activePromos, $inactivePromos);
    
    // Afficher les promotions triées
    foreach ($orderedPromos as $promo):
?>
<div class="promo-card">
    <!-- Le reste de votre code pour afficher une carte de promotion reste inchangé -->
    <div class="toggle-container">
        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="activer_promo">
            <input type="hidden" name="activer_promo" value="<?= $promo['id'] ?>">
            <button type="submit" class="toggle-label <?= $promo["statut"] === "Active" ? "active" : "" ?>">
                <div class="status-pill <?= $promo["statut"] === "Active" ? "active" : "inactive" ?>">
                    <?= $promo["statut"] === "Active" ? "Active" : "Inactive" ?>
                </div>
                <div class="power-button">
                    <svg class="power-icon" viewBox="0 0 24 24">
                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                        <line x1="12" y1="2" x2="12" y2="12"></line>
                    </svg>
                </div>
            </button>
        </form>
    </div>
    <div class="promo-body">
        <div class="promo-image">
            <img src="<?= $promo['photo'] ?>" alt="<?= $promo['nom'] ?>">
        </div>
        <div class="promo-details">
            <h3><?= htmlspecialchars($promo['nom']) ?></h3>
            <p class="promo-date"><?= date("d/m/Y", strtotime($promo['dateDebut'])) ?> - <?= date("d/m/Y", strtotime($promo['dateFin'])) ?></p>
        </div>
    </div>
    <div class="student">
        <div class="promo-students">
            <p class="p"><?= $promo['nbrApprenant'] ?> apprenant<?= $promo['nbrApprenant'] > 1 ? "s" : "" ?></p>
        </div>
    </div>
    <div class="promo-footer">
        <button class="details-btn">Voir détails ></button>
    </div>
</div>
<?php 
    endforeach; 
}
?>
</div>
<!-- Pagination -->


    <?php if ($total > 1): ?>
<div class="custom-pagination">
    <!-- Flèche gauche -->
    <a href="?page=liste_promo&p=<?= max(1, $page - 1) ?>&filtre=<?= htmlspecialchars((string)($filtreStatut ?? '')) ?>&search=<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" 
       class="arrow <?= $page === 1 ? 'disabled' : '' ?>">
        &#10094;
    </a>
    
    <!-- Pages -->
    <?php for ($i = 1; $i <= $total; $i++): ?>
    <a href="?page=liste_promo&p=<?= $i ?>&filtre=<?= htmlspecialchars((string)($filtreStatut ?? '')) ?>&search=<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" 
       class="page-number <?= $i === $page ? 'active' : '' ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
    
    <!-- Flèche droite -->
     
    <a href="?page=liste_promo&p=<?= min($total, $page + 1) ?>&filtre=<?= htmlspecialchars((string)($filtreStatut ?? '')) ?>&search=<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" 
       class="arrow <?= $page === $total ? 'disabled' : '' ?>">
        &#10095;
    </a>
</div>

<!-- Affichage du nombre d'éléments -->
<!-- <div class="pagination-info">
    <?= $debut + 1 ?> à <?= min($debut + $parPage, $totalElements) ?> sur résultats
</div> -->
<?php endif; ?>


</body>

</html>



<style>

:root {
    --primary-bg: #f5f5f5;
    --primary-font: 'Segoe UI', sans-serif;

    /* Couleurs principales */
    --color-orange: #f37021;
    --color-white: #ffffff;
    --color-green: #00b87b;
    --color-gray: #ccc;
    --color-text: #333;
    --color-text-light: #666;
    --color-border: #e0e0e0;

    /* Couleurs spécifiques */
    --color-inactive: #f5c6cb;
    --color-active: #d4edda;
    --color-btn: #0a8754;

    /* Styles composants */
    --border-radius: 8px;
    --card-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    --pill-padding: 8px 16px;
    --pill-radius: 20px;
    --transition: all 0.3s ease;
}

/* Conteneur du bouton ON/OFF */
.toggle-container {
    display: flex;
    justify-content: end;
}

/* Cacher la checkbox tout en gardant l’accessibilité */
.toggle-input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
    
}

/* Label avec texte et bouton */
.toggle-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    border: none;
    background-color: #fff;
}

.toggle-label.active {
    background-color: white;
    border: none;
}

/* Style du "status" (pill) */
.status-pill {
    background-color: rgba(255, 200, 200, 0.8);
    color: #d63031;
    padding: var(--pill-padding);
    border: none;
    border-radius: var(--pill-radius);
    font-size: 14px;
    font-weight: 500;
    color: white;
    transition: var(--transition);
   
}
.status-pill.active {
    background-color: green;
    
}
.status-pill.inactive {
    background-color: rgba(255, 200, 200, 0.8);
    color: #d63031;
}


/* Bouton rond type power */
.power-button {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #2ecc71;
    border: none;
    display: flex;
    justify-content: center;
    align-items: center;
  
    
}

/* Icône SVG à l'intérieur du bouton */
.power-icon {
    width: 16px;
    height: 16px;
    stroke: var(--color-white);
    stroke-width: 2;
    fill: none;
}
.icon{
    width: 40px;
    height: 40px;
    background-color: #fff;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
 
}

/* Styles lorsque le bouton est activé */
.toggle-input:checked + .toggle-label .status-pill {
    background-color: rgba(200, 255, 200, 0.8);
    color: #27ae60;
}




/* Effet de focus */
.toggle-input:focus + .toggle-label .power-button {
    box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.3);
}

/* Style général du body */
body {
    margin: 0;
    font-family: var(--primary-font);
    background: var(--primary-bg);
    color: var(--color-text);
}

/* Conteneur principal */
.promo-container {
    width: 100%;
    height: 100%;
}

/* En-tête */
.header h2 {
    margin: 0;
    color: teal;
    font-size: 24px;
}


/* Conteneur principal */
.promo-container {
    width: 100%;
    height: 100%;
}

/* En-tête */
.header h2 {
    margin: 0;
    color: teal;
    font-size: 24px;
}

/*PAGINATION*/

.custom-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    gap: 10px;
}

.page-number, .arrow {
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    background: #f2f2f2;
    color: #333;
    font-weight: bold;
    transition: background 0.3s, color 0.3s;
}

.page-number.active {
    background: var(--color-orange);
    color: white;
}

.arrow.disabled {
    pointer-events: none;
    opacity: 0.4;
}

.pagination-info {
    text-align: center;
    margin-top: 10px;
    font-size: 14px;
    color: #666;
}


.header p {
    margin: 5px 0 0;
    color: var(--color-text-light);
    font-size: 14px;
}

/* Zone des statistiques */
.stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin: 20px 0;
    align-items: center;
}

.stat {
    background: var(--color-orange);
    color: var(--color-white);
    padding: 15px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 1 1 160px;
    min-height: 70px;
}

/* Contenu à l'intérieur des stats */
.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 12px;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
}

.icon {
    font-size: 24px;
    margin-left: 10px;
}

/* Bouton d'ajout */
.add-btn {
    margin-left: auto;
    background: var(--color-btn);
    color: var(--color-white);
    border: none;
    padding: 12px 20px;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-weight: bold;
    white-space: nowrap;
}

/* Barre de recherche et filtre */
.search-filter {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.search-filter input,
.search-filter select {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid var(--color-border);
    flex: 1;
    min-width: 200px;
}

/* Boutons de changement de vue */
.view-toggle {
    display: flex;
    border-radius: 5px;
    overflow: hidden;
}

.view-toggle button {
    padding: 10px 15px;
    border: none;
    background: var(--color-white);
    cursor: pointer;
    font-size: 14px;
}

.view-toggle .active {
    background: var(--color-orange);
    color: var(--color-white);
}

/* Grille des cartes */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(370px, 1fr));
    gap: 20px;
}

/* Carte promo */
.promo-card {
    background: var(--color-white);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    display: flex;
    flex-direction: column;
}

.promo-header {
    display: flex;
    justify-content: end;
    align-items: center;
    padding: 15px;
    position: relative;
}

.promo-image {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
}

.promo-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Statut actif/inactif */
.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status.active {
    background: var(--color-active);
    color: #155724;
}

.status.inactive {
    background: var(--color-inactive);
    color: #721c24;
}

.promo-body {
    padding: 0 15px 15px;
    display: flex;
    flex-grow: 1;
    gap: 10px;
}

.promo-details {
    font-size: 14px;
    color: var(--color-text-light);
}

.promo-body h3 {
    margin: 0 0 10px;
    font-size: 18px;
}

.promo-date,
.promo-students {
    margin: 5px 0;
    display: flex;
    align-items: center;
    white-space: nowrap;
}

/* Icônes dynamiques */
.promo-date::before {
    content: "📅";
    margin-right: 5px;
}

.promo-students::before {
    content: "👥";
    margin-right: 5px;
    margin-left: 5%;
}

.promo-footer {
    padding: 15px;
    border-top: 1px solid var(--color-border);
    display: flex;
    justify-content: end;
}

.details-btn {
    background: none;
    border: none;
    color: var(--color-orange);
    font-weight: bold;
    cursor: pointer;
    padding: 0;
    font-size: 14px;
}

.details-btn:hover {
    text-decoration: underline;
}

/* Switch (autre style ON/OFF) */
.switch-container {
    display: flex;
    align-items: center;
}

.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 22px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--color-gray);
    transition: 0.4s;
    border-radius: 22px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: var(--color-white);
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: var(--color-green);
}

input:checked + .slider:before {
    transform: translateX(18px);
}

/* Bloc des étudiants */
.promo-students {
    margin-left: 5%;
    background-color: #ecf3f1;
    width: 90%;
    border-radius: 10px;
}

.student {
    height: 7vh;
    display: flex;
}

/* Media queries pour mobile */
@media (max-width: 650px) {
    .stats {
        flex-direction: column;
        align-items: stretch;
    }

    .stat {
        width: 100%;
        margin-left: -3%;
    }

    .add-btn {
        width: 100%;
        margin-left: -3%;
        margin-top: 10px;
    }

    .search-filter {
        flex-direction: column;
        align-items: stretch;
    }

    .search-filter input,
    .search-filter select {
        width: 100%;
        margin-left: -5%;
    }

    .view-toggle {
        justify-content: space-around;
        width: 100%;
    }

    .card-grid {
        grid-template-columns: 1fr;
    }

    .promo-body {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .promo-details {
        text-align: center;
    }

    .promo-date,
    .promo-students {
        justify-content: center;
    }

    .promo-footer {
        justify-content: center;
    }

    .student {
        justify-content: center;
    }

    .promo-students {
        width: auto;
        padding: 5px 10px;
    }
}

@media (max-width: 650px) {
    .promo-card {
        padding: 10px;
        margin-left:-2%;
        width: 95%;
    }

    .promo-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-end;
    }

    .promo-image {
        width: 50px;
        height: 50px;
    }

    .header h2 {
        font-size: 20px;
    }

    .header p {
        font-size: 13px;
    }
}


/* pop */

/* fin */



    * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
    body { background: #f5f7fa; padding: 20px; }
    
    /* En-tête avec titre et nombre d'apprenants */
    .header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    .header h1 {
      color: #00857c;
      font-size: 24px;
      margin-right: 10px;
    }
    .header .count {
      background-color: #f8f9fa;
      color: #ff8c00;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 14px;
    }
    
    /* Barre d'outils */
    .toolbar {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      align-items: center;
    }
    .search-box {
      flex: 1;
      position: relative;
    }
    .search-box input {
      width: 100%;
      padding: 10px 10px 10px 35px;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      font-size: 14px;
    }
    .search-box i {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
    }
    .filter-dropdown {
      position: relative;
      width: 180px;
    }
    .filter-dropdown select {
      width: 100%;
      padding: 10px;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      appearance: none;
      background-color: white;
      font-size: 14px;
      color: #777;
    }
    .filter-dropdown::after {
      content: '\f107';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #aaa;
      pointer-events: none;
    }
    .add-button {
      background-color: #00857c;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    /* Cartes d'information */
    .cards {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }
    .card {
      flex: 1;
      background: #ff8c00;
      color: #fff;
      border-radius: 8px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .card .icon {
      width: 40px;
      height: 40px;
      background-color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card .icon i {
      color: #ff8c00;
      font-size: 20px;
    }
    .card .info .number {
      font-size: 24px;
      font-weight: bold;
      line-height: 1;
    }
    .card .info .label {
      font-size: 14px;
      opacity: 0.9;
    }
    
    /* Tableau */
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }
    thead {
      background: #ff8c00;
      color: #fff;
    }
    th, td {
      padding: 15px;
      text-align: left;
    }
    tbody tr {
      border-bottom: 1px solid #f0f0f0;
    }
    tbody tr:last-child {
      border-bottom: none;
    }
    .photo-cell img {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      object-fit: cover;
    }
    .promo-cell {
      font-weight: 500;
    }
    .date-cell {
      color: #777;
      font-size: 14px;
    }
    .tags {
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
    }
    .tag {
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 12px;
    }
    .tag.dev-web {
      background: #d6f5d6;
      color: #009933;
    }
    .tag.ref-dig {
      background: #e6f2ff;
      color: #0066cc;
    }
    .tag.dev-data {
      background: #e6f9ff;
      color: #0099cc;
    }
    .tag.aws {
      background: #fff0e6;
      color: #ff7733;
    }
    .tag.hackeuse {
      background: #ffe6f0;
      color: #ff3377;
    }
    .status {
      display: inline-flex;
      align-items: center;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 12px;
    }
    .status.active {
      background-color: #e6f9f0;
      color: #00cc66;
    }
    .status.inactive {
      background-color: #fce6e6;
      color: #ff3333;
    }
    .status::before {
      content: '';
      display: inline-block;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      margin-right: 5px;
    }
    .status.active::before {
      background-color: #00cc66;
    }
    .status.inactive::before {
      background-color: #ff3333;
    }
    .action-cell {
      text-align: center;
    }
    .action-cell .dots {
      color: #aaa;
    }
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
    }
    .page-size {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .page-size span {
      color: #777;
      font-size: 14px;
    }
    .page-size select {
      padding: 5px 10px;
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      appearance: none;
      background-color: white;
    }
    .page-info {
      color: #777;
      font-size: 14px;
    }
    .page-controls {
      display: flex;
      gap: 5px;
    }
    .page-controls button {
      width: 30px;
      height: 30px;
      border: none;
      border-radius: 4px;
      background-color: #fff;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .page-controls button.active {
      background-color: #ff8c00;
      color: white;
    }
    .error-message{
        color: #d63031;
        font-size: 12px;
        margin-top: 5px;
    }
 
</style>