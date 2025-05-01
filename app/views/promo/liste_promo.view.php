<?php
// Configuration de la pagination
$perPage = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;

// Filtrer les promotions
$filteredPromotions = $promotions;

// Filtre par statut
if (isset($_GET['filtre']) && $_GET['filtre'] !== 'tous') {
    $statutFiltre = $_GET['filtre'] === 'active' ? 'Active' : 'Inactive';
    $filteredPromotions = array_filter($filteredPromotions, function($promo) use ($statutFiltre) {
        return $promo['statut'] === $statutFiltre;
    });
}

// Filtre par référentiel
if (isset($_GET['ref_filter']) && !empty($_GET['ref_filter'])) {
    $refId = (int)$_GET['ref_filter'];
    $filteredPromotions = array_filter($filteredPromotions, function($promo) use ($refId) {
        return isset($promo['referenciels']) && 
               is_array($promo['referenciels']) && 
               in_array($refId, $promo['referenciels']);
    });
}

// Filtre par recherche
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = strtolower($_GET['search']);
    $filteredPromotions = array_filter($filteredPromotions, function($promo) use ($search) {
        return strpos(strtolower($promo['nom']), $search) !== false;
    });
}

// Recalculer la pagination après les filtres
$total = count($filteredPromotions);
$pages = ceil($total / $perPage);
$currentPage = min($currentPage, max(1, $pages));
$start = ($currentPage - 1) * $perPage;

// Paginer les résultats filtrés
$paginatedPromos = array_slice($filteredPromotions, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="fr">

<head>

  <?php
  require_once __DIR__ . '/../../enums/chemin_page.php';

  use App\Enums\CheminPage;

  $url = "http://" . $_SERVER["HTTP_HOST"];
  $css_promo = CheminPage::CSS_PROMO->value;
  ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des promotions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $url . $css_promo ?>">
</head>
<!-- Ajouter ceci juste après la balise header -->
<?php if (isset($success)): ?>
    <div class="message success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="message error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<body>
  <!-- En-tête -->
  <div class="header">
    <h1>Promotion</h1>
    <span class="count">180 apprenants</span>
  </div>
  <!-- Barre d'outils -->
  <div class="toolbar">
    <form method="GET" action="index.php" style="display: flex; flex: 1;">
        <div class="search-box">
            <i class="fa fa-search"></i>
            <input type="hidden" name="page" value="liste_table_promo">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        
        <div class="filter-dropdown">
            <select name="ref_filter">
                <option value="">Tous les référentiels</option>
                <?php foreach ($referentiels as $ref): ?>
                    <option value="<?= $ref['id'] ?>" <?= ($_GET['ref_filter'] ?? '') == $ref['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ref['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-dropdown">
            <select name="filtre">
                <option value="tous">Tous les statuts</option>
                <option value="active" <?= ($_GET['filtre'] ?? '') === 'active' ? 'selected' : '' ?>>Actives</option>
                <option value="inactive" <?= ($_GET['filtre'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactives</option>
            </select>
        </div>
        <button type="submit" class="add-button">🔍 Rechercher</button>
    </form>

    <button>
        <a href="#popup" class="add-btn">+ Ajouter une promotion</a>
    </button>
</div>

  <!-- Cartes d'information -->
<!-- Cartes d'information -->
<div class="cards">
    <div class="card">
        <div class="icon">
            <i class="fa fa-graduation-cap"></i>
        </div>
        <div class="info">
            <div class="number"><?= $stats['totalApprenants'] ?></div>
            <div class="label">Apprenants</div>
        </div>
    </div>
    <div class="card">
        <div class="icon">
            <i class="fa fa-folder"></i>
        </div>
        <div class="info">
            <div class="number"><?= $stats['totalReferentiels'] ?></div>
            <div class="label">Référentiels</div>
        </div>
    </div>
    <div class="card">
        <div class="icon">
            <i class="fa fa-user-graduate"></i>
        </div>
        <div class="info">
            <div class="number">1</div>
            <div class="label">Promotions actives</div>
        </div>
    </div>
    <div class="card">
        <div class="icon">
            <i class="fa fa-users"></i>
        </div>
        <div class="info">
            <div class="number"><?= $stats['totalPromotions'] ?></div>
            <div class="label">Total promotions</div>
        </div>
    </div>
</div>

  <!-- Tableau -->
  <table>
    <thead>
      <tr>
        <th>Photo</th>
        <th>Promotion</th>
        <th>Date de début</th>
        <th>Date de fin</th>
        <th>Référentiel</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!empty($paginatedPromos)): ?>
        <?php foreach ($paginatedPromos as $promo): ?>
            <?php
            $statut = $promo["statut"] === "Active" ? "active" : "inactive";
            $label = ucfirst(strtolower($statut));
            ?>
             <tr class="<?= $promo["statut"] === "Active" ? 'active-row' : '' ?>">
                <td class='photo-cell'><img src='<?= htmlspecialchars($promo["photo"]) ?>' alt='photo' width='50'></td>
                <td class='promo-cell'><?= htmlspecialchars($promo["nom"]) ?></td>
                <td class='date-cell'><?= htmlspecialchars($promo["dateDebut"]) ?></td>
                <td class='date-cell'><?= htmlspecialchars($promo["dateFin"]) ?></td>
                <<td>
    <div class='tags'>
        <?php 
        // Récupérer les référentiels de la promotion
        if (isset($promo['referenciels']) && is_array($promo['referenciels'])):
            foreach ($promo['referenciels'] as $refId):
                // Chercher le référentiel correspondant
                $ref = array_filter($referentiels, function($r) use ($refId) {
                    return $r['id'] === $refId;
                });
                
                if ($ref = reset($ref)): // Prendre le premier élément du tableau filtré
                    $className = strtolower(str_replace([' ', '/'], '-', $ref['nom']));
        ?>
                    <span class='tag <?= $className ?>'><?= htmlspecialchars($ref['nom']) ?></span>
        <?php 
                endif;
            endforeach;
        endif;
        ?>
    </div>
</td>
                <td>
                    <form method='GET' action='index.php'>
                        <input type='hidden' name='page' value='activer_promo_liste'>
                        <input type='hidden' name='activer_promo_liste' value='<?= $promo["id"] ?>'>
                        <button type='submit' class='status <?= $statut ?>'><?= $label ?></button>
                    </form>
                </td>
                <td class='action-cell'><span class='dots'>•••</span></td>
            </tr>
            <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="no-data">Aucune promotion trouvée</td>
        </tr>
    <?php endif; ?>
</tbody>
  </table>

 <!-- Pagination -->
<?php if ($total > 0): ?>
  <div class="pagination">
    <div class="page-size">
        <span>Afficher</span>
        <form method="get" style="display: inline;">
            <input type="hidden" name="page" value="liste_table_promo">
            <input type="hidden" name="search" value="<?= htmlspecialchars((string)($nomRecherche ?? '')) ?>">
            <input type="hidden" name="filtre" value="<?= htmlspecialchars((string)($filtreStatut ?? '')) ?>">
            <select name="limit" onchange="this.form.submit()">
                <?php foreach ([5, 10, 20] as $limit): ?>
                    <option value="<?= $limit ?>" <?= $perPage == $limit ? 'selected' : '' ?>><?= $limit ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="page-info">
        <?= $start + 1 ?> à <?= min($start + $perPage, $total) ?> sur <?= $total ?> résultats
    </div>

    <div class="page-controls">
    <?php if ($page > 1): ?>
        <a href="?page=liste_table_promo&p=<?= $page - 1 ?>&limit=<?= $perPage ?>&filtre=<?= urlencode((string)($filtreStatut ?? '')) ?>&ref_filter=<?= urlencode((string)($_GET['ref_filter'] ?? '')) ?>&search=<?= urlencode((string)($nomRecherche ?? '')) ?>">
            <button><i class="fa fa-angle-left"></i></button>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=liste_table_promo&p=<?= $i ?>&limit=<?= $perPage ?>&filtre=<?= urlencode((string)($filtreStatut ?? '')) ?>&ref_filter=<?= urlencode((string)($_GET['ref_filter'] ?? '')) ?>&search=<?= urlencode((string)($nomRecherche ?? '')) ?>">
            <button class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></button>
        </a>
    <?php endfor; ?>

    <?php if ($page < $pages): ?>
        <a href="?page=liste_table_promo&p=<?= $page + 1 ?>&limit=<?= $perPage ?>&filtre=<?= urlencode((string)($filtreStatut ?? '')) ?>&ref_filter=<?= urlencode((string)($_GET['ref_filter'] ?? '')) ?>&search=<?= urlencode((string)($nomRecherche ?? '')) ?>">
            <button><i class="fa fa-angle-right"></i></button>
        </a>
    <?php endif; ?>
</div>
</div>
<?php endif; ?>


</body>

</html>

<style>
  .pagination-container {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 20px;
}

.items-per-page {
    display: flex;
    align-items: center;
    gap: 10px;
}

.items-per-page select {
    padding: 5px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.custom-pagination {
    display: flex;
    gap: 5px;
    align-items: center;
}

.custom-pagination .arrow,
.custom-pagination .page-number {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    background: white;
    transition: all 0.3s ease;
}

.custom-pagination .arrow.disabled {
    opacity: 0.5;
    pointer-events: none;
}

.custom-pagination .page-number.active {
    background: var(--color-orange);
    color: white;
    border-color: var(--color-orange);
}

.pagination-info {
    color: #666;
    font-size: 14px;
}

@media (max-width: 768px) {
    .pagination-controls {
        flex-direction: column;
        gap: 10px;
    }
}

/* Ajouter un style distinct pour les lignes des promotions actives */
tr.active-row {
    background-color: rgba(0, 200, 81, 0.05);
}

/* Effet hover sur les lignes */
tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.filter-dropdown select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    min-width: 200px;
    margin-right: 10px;
}

.filter-dropdown select:focus {
    border-color: #ff7900;
    outline: none;
}

.add-button {
    padding: 8px 16px;
    background-color: #00857c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.add-button:hover {
    opacity: 0.9;
}

.tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.tag {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    white-space: nowrap;
}

/* Styles pour les différents types de référentiels */
.dev-web-mobile {
    background-color: #d6f5d6;
    color: #009933;
}

.ref-dig {
    background-color: #e6f2ff;
    color: #0066cc;
}

.dev-data {
    background-color: #e6f9ff;
    color: #0099cc;
}

.aws {
    background-color: #fff0e6;
    color: #ff7733;
}

.hackeuse {
    background-color: #ffe6f0;
    color: #ff3377;
}
</style>