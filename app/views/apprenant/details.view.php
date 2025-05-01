<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
require_once CheminPage::ERROR_FR->value;
require_once CheminPage::ERREUR_ENUM->value;

$errors = recuperer_session('errors', []);
$success = recuperer_session('success');
?>

<?php if (!empty($errors)): ?>
    <div class="alert-error" style="background-color: #ffdddd; color: #d8000c; padding: 1rem; margin: 1rem 0; border: 1px solid #d8000c; border-radius: 8px;">
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
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert-success" style="background-color: #ddffdd; color: #270; padding: 1rem; margin: 1rem 0; border: 1px solid #270; border-radius: 8px;">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Détails de l'Apprenant</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    body {
      background: #f5f6fa;
      width: 100%;
      padding: 0;
      margin: 0;
    }

    /* Topbar */
    .topbar {
      background: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .topbar-left {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .search-container {
      position: relative;
    }
    .search-container input {
      padding: 10px 35px 10px 15px;
      border: none;
      background: #f1f1f1;
      border-radius: 10px;
      width: 250px;
    }
    .search-container::before {
      content: "🔍";
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 18px;
      color: #999;
    }
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .notif-icon {
      font-size: 22px;
      cursor: pointer;
    }
    .user-profile {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .user-profile img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }

    /* Container principal */
    .containerapp {
      padding: 30px;
    }
    .title {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 25px;
    }
    .title h2 {
      font-size: 2rem;
      color: #009688;
    }
    .badge-count {
      background: #ffe0b2;
      color: #ff7a00;
      padding: 5px 12px;
      border-radius: 30px;
      font-size: 0.9rem;
      font-weight: bold;
    }

    /* Boutons d'action */
    .actions {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    .btn {
      padding: 12px 20px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      font-weight: bold;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-primary {
      background: #009688;
      color: white;
    }
    .btn-secondary {
      background: #f0f0f0;
      color: #333;
    }
    .btn-danger {
      background: #f44336;
      color: white;
    }

    /* Carte de profil */
    .profile-card {
      display: flex;
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    .profile-image {
      width: 300px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      background: #f9f9f9;
    }
    .profile-image img {
      width: 200px;
      height: 200px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
      border: 5px solid white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .status-badge {
      padding: 8px 15px;
      border-radius: 30px;
      font-weight: bold;
      font-size: 0.9rem;
      margin-top: 10px;
    }
    .status-retained {
      background: #d0f0c0;
      color: #2e7d32;
    }
    .status-waiting {
      background: #ffeeba;
      color: #ff7a00;
    }
    .status-replaced {
      background: #ffcdd2;
      color: #c62828;
    }
    .profile-info {
      flex: 1;
      padding: 30px;
    }
    .info-group {
      margin-bottom: 25px;
    }
    .info-group h3 {
      font-size: 1.2rem;
      color: #009688;
      margin-bottom: 15px;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 8px;
    }
    .info-row {
      display: flex;
      margin-bottom: 15px;
    }
    .info-label {
      width: 180px;
      font-weight: bold;
      color: #555;
    }
    .info-value {
      flex: 1;
      color: #333;
    }
    .badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: bold;
    }
    .badge.ref {
      background: #e0f7fa;
      color: #00796b;
    }

    /* Tabs */
    .tabs {
      display: flex;
      background: white;
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .tab {
      flex: 1;
      padding: 15px;
      text-align: center;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s;
      border-bottom: 3px solid transparent;
    }
    .tab.active {
      border-bottom: 3px solid #009688;
      color: #009688;
    }
    .tab-content {
      display: none;
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .tab-content.active {
      display: block;
    }

    /* Formulaire de modification */
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #555;
    }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 12px 15px;
      border-radius: 10px;
      border: 1px solid #ddd;
      background: white;
    }
    .form-row {
      display: flex;
      gap: 20px;
    }
    .form-row .form-group {
      flex: 1;
    }
    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }
  </style>
</head>

<body>

<!-- Topbar -->
<div class="topbar">
  <div class="topbar-left">
    <div class="search-container">
      <input type="text" placeholder="Search">
    </div>
  </div>
  <div class="topbar-right">
    <span class="notif-icon">🔔</span>
    <div class="user-profile">
      <img src="default_avatar.jpg" alt="Profil">
      <div>
        <div>Awa Niang</div>
        <small>Admin</small>
      </div>
    </div>
  </div>
</div>

<!-- Main Container -->
<div class="containerapp">

  <!-- Titre et actions -->
  <div class="title">
    <h2>Détails de l'Apprenant</h2>
  </div>

  <div class="actions">
    <a href="index.php?page=liste_apprenant" class="btn btn-secondary">← Retour à la liste</a>
    <a href="index.php?page=modifier_apprenant&id=<?= htmlspecialchars($apprenant['id']) ?>" class="btn btn-primary">✏️ Modifier</a>
    <button type="button" class="btn btn-danger" onclick="confirmerSuppression()">🗑️ Supprimer</button>
  </div>

  <!-- Carte de profil -->
  <div class="profile-card">
    <div class="profile-image">
      <?php if (!empty($apprenant['photo'])): ?>
        <img src="<?= htmlspecialchars($apprenant['photo']) ?>" alt="Photo de l'apprenant">
      <?php else: ?>
        <img src="assets/images/default_avatar.jpg" alt="Photo par défaut">
      <?php endif; ?>
      
      <h3><?= htmlspecialchars($apprenant['nom_complet'] ?? 'Nom non défini') ?></h3>
      <div class="status-badge <?= ($apprenant['statut'] ?? '') === 'Retenu' ? 'status-retained' : (($apprenant['statut'] ?? '') === 'Remplacer' ? 'status-replaced' : 'status-waiting') ?>">
        <?= htmlspecialchars($apprenant['statut'] ?? 'En attente') ?>
      </div>
    </div>
    
    <div class="profile-info">
      <div class="info-group">
        <h3>Informations personnelles</h3>
        <div class="info-row">
          <div class="info-label">Matricule</div>
          <div class="info-value"><?= htmlspecialchars($apprenant['matricule'] ?? 'Non défini') ?></div>
        </div>
        <div class="info-row">
          <div class="info-label">Nom complet</div>
          <div class="info-value"><?= htmlspecialchars($apprenant['nom_complet'] ?? 'Non défini') ?></div>
        </div>
        <div class="info-row">
          <div class="info-label">Email</div>
          <div class="info-value"><?= htmlspecialchars($apprenant['email'] ?? 'Non défini') ?></div>
        </div>
        <div class="info-row">
          <div class="info-label">Téléphone</div>
          <div class="info-value"><?= htmlspecialchars($apprenant['telephone'] ?? 'Non défini') ?></div>
        </div>
        <div class="info-row">
          <div class="info-label">Adresse</div>
          <div class="info-value"><?= htmlspecialchars($apprenant['adresse'] ?? 'Non défini') ?></div>
        </div>
        <div class="info-row">
          <div class="info-label">Date de naissance</div>
          <div class="info-value"><?= htmlspecialchars($apprenant['date_naissance'] ?? 'Non défini') ?></div>
        </div>
      </div>
      
      <div class="info-group">
        <h3>Informations académiques</h3>
        <div class="info-row">
          <div class="info-label">Référentiel</div>
          <div class="info-value">
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
          </div>
        </div>
        <div class="info-row">
          <div class="info-label">Date d'inscription</div>
          <div class="info-value"><?= htmlspecialchars($apprenant['date_inscription'] ?? 'Non défini') ?></div>
        </div>
        <div class="info-row">
          <div class="info-label">Statut</div>
          <div class="info-value">
            <span class="status-badge <?= ($apprenant['statut'] ?? '') === 'Retenu' ? 'status-retained' : (($apprenant['statut'] ?? '') === 'Remplacer' ? 'status-replaced' : 'status-waiting') ?>