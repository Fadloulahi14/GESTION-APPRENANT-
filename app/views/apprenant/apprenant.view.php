<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
require_once CheminPage::ERROR_FR->value;
require_once CheminPage::ERREUR_ENUM->value;

$errors = recuperer_session('errors', []);
$success = recuperer_session('success');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste des Apprenants</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  
</head>

<body>

<!-- Topbar -->


<!-- Main Container -->
<div class="containerapp">

  <!-- Alertes -->
  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
      <div>
        <ul>
          <?php foreach ($errors as $cleErreur): ?>
            <?php
              $parts = explode('ligne', $cleErreur);
              $cle_sans_ligne = $parts[0];
              $ligne = $parts[1] ?? null;
              $message = $error_messages[$cle_sans_ligne] ?? $cleErreur;
            ?>
            <li><?= htmlspecialchars($message . ($ligne ? " (ligne $ligne)" : '')) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <button class="close-btn">&times;</button>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success">
      <div><?= htmlspecialchars($success) ?></div>
      <button class="close-btn">&times;</button>
    </div>
  <?php endif; ?>

  <!-- Titre -->
  <div class="title">
    <h2>Apprenants</h2>
    <span class="badge-count"><?= count($apprenants) ?> apprenants</span>
  </div>

  <!-- Filtres et Actions -->
  <div class="filters-actions">
    <form method="GET" action="index.php" class="filters">
      <input type="hidden" name="page" value="liste_apprenant">

      <input 
        type="text" 
        name="search" 
        placeholder="Rechercher par nom complet..." 
        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
      >

      <select name="referenciel">
        <option value="">Filtrer par classe</option>
        <?php foreach ($referenciels as $referenciel): ?>
          <option value="<?= htmlspecialchars($referenciel['id']) ?>" 
            <?= (isset($_GET['referenciel']) && $_GET['referenciel'] == $referenciel['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($referenciel['nom']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="statut">
        <option value="">Filtrer par statut</option>
        <option value="Retenu" <?= (($_GET['statut'] ?? '') == 'Retenu') ? 'selected' : '' ?>>Retenu</option>
        <option value="Remplacer" <?= (($_GET['statut'] ?? '') == 'Remplacer') ? 'selected' : '' ?>>Remplacer</option>
        <option value="En attente" <?= (($_GET['statut'] ?? '') == 'En attente') ? 'selected' : '' ?>>En attente</option>
      </select>

      <button type="submit" class="search-btn"><i class="fas fa-search"></i> Rechercher</button>
    </form>

    <div class="actions">
      <form class="export" action="index.php?page=import_apprenants" method="POST" enctype="multipart/form-data">
        <div class="export-btn">
          <i class="fas fa-file-export"></i> Importer / Exporter <i class="fas fa-chevron-down"></i>
          <div class="export-menu">
            <a href="index.php?page=export_apprenants&format=csv" class="btn-download"><i class="fas fa-file-csv"></i> Exporter CSV</a>
            <a href="index.php?page=export_apprenants&format=pdf" class="btn-download"><i class="fas fa-file-pdf"></i> Exporter PDF</a>
            <a href="index.php?page=export_apprenants&format=excel" class="btn-download"><i class="fas fa-file-excel"></i> Exporter Excel</a>
            <hr>
            <input type="file" name="import_excel" accept=".csv,.xlsx" required>
            <button type="submit" class="add-btn"><i class="fas fa-file-import"></i> Importer Excel</button>
          </div>
        </div>
      </form>
      <a href="index.php?page=ajouter_apprenant" class="add-btn"><i class="fas fa-user-plus"></i> Ajouter Apprenant</a>
    </div>
  </div>

  <!-- Tableau des apprenants -->
  <div class="table-wrapper">
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Photo</th>
            <th>Matricule</th>
            <th>Nom Complet</th>
            <th>Adresse</th>
            <th>Téléphone</th>
            <th>Référentiel</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($apprenants)): ?>
            <?php foreach ($apprenants as $apprenant): ?>
              <tr>
                <td>
                  <?php if (!empty($apprenant['photo'])): ?>
                    <img src="<?= htmlspecialchars($apprenant['photo']) ?>" alt="Photo" class="photo">
                  <?php else: ?>
                    <img src="assets/images/default_avatar.jpg" alt="Photo par défaut" class="photo">
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($apprenant['matricule'] ?? '') ?></td>
                <td><?= htmlspecialchars($apprenant['nom_complet'] ?? '') ?></td>
                <td><?= htmlspecialchars($apprenant['adresse'] ?? '') ?></td>
                <td><?= htmlspecialchars($apprenant['telephone'] ?? '') ?></td>
                <td>
                  <?php
                    $nomReferenciel = 'Non défini';
                    foreach ($referenciels as $ref) {
                        if (isset($apprenant['referenciel']) && $ref['id'] == $apprenant['referenciel']) {
                            $nomReferenciel = $ref['nom'];
                            break;
                        }
                    }
                  ?>
                  <span class="badge ref"><?= htmlspecialchars($nomReferenciel) ?></span>
                </td>
                <td>
                  <?php 
                    $status = $apprenant['statut'] ?? 'En attente';
                    $statusClass = '';
                    
                    if ($status === 'Retenu') {
                      $statusClass = 'status-success';
                    } elseif ($status === 'Remplacer') {
                      $statusClass = 'status-danger';
                    } else {
                      $statusClass = 'status-warning';
                    }
                  ?>
                  <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span>
                </td>
                <td>
                  <a href="index.php?page=detail_apprenant&id=<?= htmlspecialchars($apprenant['id'] ?? '') ?>" class="detail-btn">
                    <i class="fas fa-eye"></i> Détails
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align:center; padding: 30px;">
                <i class="fas fa-search" style="font-size: 2rem; color: #ddd; margin-bottom: 15px;"></i>
                <p>Aucun apprenant trouvé</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($pagination['pages'] > 1): ?>
    <div class="pagination">
      <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
        <a href="index.php?page=liste_apprenant&pageCourante=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&referenciel=<?= urlencode($_GET['referenciel'] ?? '') ?>"
           class="<?= $i === $pagination['pageCourante'] ? 'active' : '' ?>">
           <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>

<script>
  // Script pour fermer les alertes
  document.addEventListener('DOMContentLoaded', function() {
    const closeButtons = document.querySelectorAll('.close-btn');
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        const alert = this.closest('.alert');
        alert.style.opacity = '0';
        setTimeout(() => alert.style.display = 'none', 300);
      });
    });
  });
</script>

</body>
</html>

<style>
    /* Base & Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    
    body {
      background: #f8f9fa;
      width: 100%;
      padding: 0;
      margin: 0;
      color: #333;
    }
    
    /* Topbar */
    .topbar {
      background: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .search-container {
      margin-top: 5%;

      position: relative;
      transition: all 0.3s ease;
    }
    
    .search-container input {
      padding: 12px 40px 12px 15px;
      border: none;
      background: #f5f5f5;
      border-radius: 50px;
      width: 280px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .search-container input:focus {
      background: white;
      box-shadow: 0 0 0 2px rgba(255, 122, 0, 0.2), inset 0 2px 5px rgba(0,0,0,0.05);
      outline: none;
    }
    
    .search-container::before {
      content: "\f002";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      color: #ff7a00;
    }
    
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 25px;
    }
    
    .notif-icon {
      font-size: 22px;
      cursor: pointer;
      width: 40px;
      height: 40px;
      background: #fff6ec;
      color: #ff7a00;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      position: relative;
      transition: all 0.2s ease;
    }
    
    .notif-icon:hover {
      background: #ffe0b2;
    }
    
    .notif-icon::after {
      content: "3";
      position: absolute;
      top: -5px;
      right: -5px;
      background: #ff7a00;
      color: white;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      font-size: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }
    
    .user-profile {
      display: flex;
      align-items: center;
      gap: 12px;
      cursor: pointer;
      padding: 8px 10px;
      border-radius: 50px;
      transition: all 0.2s ease;
    }
    
    .user-profile:hover {
      background: #f5f5f5;
    }
    
    .user-profile img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ffe0b2;
    }
    
    .user-profile .user-info {
      line-height: 1.3;
    }
    
    .user-profile .user-name {
      font-weight: 600;
      color: #333;
    }
    
    .user-profile .user-role {
      color: #ff7a00;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    /* Container principal */
    .containerapp {
     
      max-width: 1200px;
      
    }
    
    .title {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 30px;
    }
    
    .title h2 {
      font-size: 2.2rem;
      color: #333;
      font-weight: 600;
    }
    
    .badge-count {
      background: #ffe0b2;
      color: #ff7a00;
      padding: 6px 15px;
      border-radius: 50px;
      font-size: 0.95rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .badge-count::before {
      content: "\f007";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
    }
    
    /* Filtres + Actions */
    .filters-actions {
      
      display: flex;
      align-items: center;
      flex-direction: row;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 30px;
      align-items: center;
    }
    
    .filters {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    
    .filters input, 
    .filters select {
      padding: 12px 18px;
      border-radius: 10px;
      border: none;
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
      min-width: 180px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }
    
    .filters input:focus, 
    .filters select:focus {
      box-shadow: 0 0 0 2px rgba(255, 122, 0, 0.2), 0 2px 10px rgba(0,0,0,0.06);
      outline: none;
    }
    
    .filters select {
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ff7a00' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 15px;
      padding-right: 40px;
    }
    
    .actions {
      display: flex;
      gap: 15px;
      align-items: center;
    }
    
    .btn-download, 
    .add-btn,
    .search-btn {
      padding: 12px 24px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }
    
    .search-btn {
      background: #ff7a00;
      color: white;
    }
    
    .search-btn:hover {
      background: #e56e00;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(255, 122, 0, 0.3);
    }
    
    .btn-download {
      background: #333;
      color: white;
    }
    
    .btn-download:hover {
      background: #222;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    
    .add-btn {
      background: #ff7a00;
      color: white;
      box-shadow: 0 4px 15px rgba(255, 122, 0, 0.25);
    }
    
    .add-btn:hover {
      background: #e56e00;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(255, 122, 0, 0.35);
    }
    
    .export {
      position: relative;
    }
    
    .export-btn {
      background: #333;
      color: white;
      padding: 12px 24px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 600;
      transition: all 0.2s ease;
    }
    
    .export-btn:hover {
      background: #222;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    
    .export-menu {
      display: none;
      position: absolute;
      background: white;
      box-shadow: 0 5px 25px rgba(0,0,0,0.15);
      margin-top: 12px;
      right: 0;
      border-radius: 12px;
      overflow: hidden;
      min-width: 250px;
      z-index: 10;
    }
    
    .export:hover .export-menu {
      display: block;
      animation: fadeInDown 0.3s ease;
    }
    
    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .export-menu a, 
    .export-menu input, 
    .export-menu button {
      display: block;
      width: 100%;
      padding: 12px 20px;
      text-decoration: none;
      color: #333;
      font-size: 0.95rem;
      background: white;
      border: none;
      text-align: left;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
      font-weight: 500;
    }
    
    .export-menu a:hover,
    .export-menu button:hover {
      background: #f8f8f8;
    }
    
    .export-menu hr {
      margin: 8px 0;
      border: none;
      border-top: 1px solid #eee;
    }
    
    .export-menu input[type="file"] {
      padding: 15px 20px;
      cursor: pointer;
      color: #666;
    }
    
    .export-menu input[type="file"]::file-selector-button {
      display: none;
    }
    
    /* Table */
    .table-wrapper {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
      overflow: hidden;
      margin-bottom: 30px;
    }
    
    .table-container {
      overflow-x: auto;
      padding: 0;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    thead {
      background: #ff7a00;
      color: white;
    }
    
    th {
      padding: 18px 20px;
      text-align: left;
      font-size: 1rem;
      font-weight: 600;
      white-space: nowrap;
    }
    
    td {
      padding: 18px 20px;
      text-align: left;
      font-size: 0.95rem;
      vertical-align: middle;
    }
    
    tbody tr {
      background: white;
      border-bottom: 1px solid #eee;
      transition: all 0.2s ease;
    }
    
    tbody tr:hover {
      background: #fff8f0;
    }
    
    tbody tr:last-child {
      border-bottom: none;
    }
    
    .photo {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ffe0b2;
    }
    
    .badge {
      display: inline-flex;
      padding: 6px 12px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 600;
      align-items: center;
      gap: 5px;
    }
    
    .badge.ref {
      background: #e0f7fa;
      color: #00796b;
    }
    
    .badge.ref::before {
      content: "\f19d";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
    }
    
    .badge.status-success {
      background: #d0f0c0;
      color: #2e7d32;
    }
    
    .badge.status-warning {
      background: #ffeeba;
      color: #ff7a00;
    }
    
    .badge.status-danger {
      background: #ffcdd2;
      color: #c62828;
    }
    
    .badge.status-success::before,
    .badge.status-warning::before,
    .badge.status-danger::before {
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
    }
    
    .badge.status-success::before {
      content: "\f058";
    }
    
    .badge.status-warning::before {
      content: "\f071";
    }
    
    .badge.status-danger::before {
      content: "\f057";
    }
    
    .detail-btn {
      padding: 8px 15px;
      background: #fff6ec;
      color: #ff7a00;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.85rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s ease;
      border: 1px solid #ffe0b2;
    }
    
    .detail-btn:hover {
      background: #ffe0b2;
      transform: translateY(-2px);
    }
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      margin-top: 30px;
      gap: 8px;
    }
    
    .pagination a {
      padding: 10px 15px;
      text-decoration: none;
      color: #555;
      background: white;
      border-radius: 10px;
      transition: all 0.2s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.08);
      font-weight: 500;
    }
    
    .pagination a.active {
      background: #ff7a00;
      color: white;
      box-shadow: 0 4px 10px rgba(255, 122, 0, 0.25);
    }
    
    .pagination a:hover:not(.active) {
      background: #fff6ec;
      color: #ff7a00;
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    /* Alertes */
    .alert {
      padding: 18px 25px;
      margin-bottom: 25px;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      animation: fadeIn 0.5s ease-in-out;
    }
    
    .alert-error {
      background-color: #fff5f5;
      color: #c62828;
      border-left: 5px solid #ff5252;
    }
    
    .alert-success {
      background-color: #f0fff0;
      color: #2e7d32;
      border-left: 5px solid #4caf50;
    }
    
    .alert ul {
      margin: 10px 0 0 20px;
    }
    
    .alert li {
      margin: 5px 0;
    }
    
    .alert p {
      margin: 0;
      padding: 0;
    }
    
    .alert .close-btn {
      background: none;
      border: none;
      font-size: 20px;
      font-weight: bold;
      color: inherit;
      cursor: pointer;
    }
    
    .alert-error::before,
    .alert-success::before {
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      font-size: 1.5rem;
      margin-right: 15px;
    }
    
    .alert-error::before {
      content: "\f071";
    }
    
    .alert-success::before {
      content: "\f058";
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
      .containerapp {
        padding: 20px;
      }
      
      .filters-actions {
        flex-direction: column;
        align-items: stretch;
      }
      
      .filters {
        width: 100%;
      }
      
      .actions {
        justify-content: space-between;
      }
    }
    
    @media (max-width: 768px) {
      .topbar {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
      }
      
      .topbar-left, .topbar-right {
        width: 100%;
        justify-content: space-between;
      }
      
      .search-container input {
        width: 100%;
      }
      
      .title {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .filters {
        flex-direction: column;
      }
      
      .actions {
        flex-wrap: wrap;
        gap: 10px;
      }
      
      .export-btn, .add-btn {
        width: 100%;
        justify-content: center;
      }
      
      .pagination {
        flex-wrap: wrap;
      }
    }
  </style>