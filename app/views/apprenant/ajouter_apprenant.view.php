<?php
require_once __DIR__ . '/../../services/session.service.php';
demarrer_session();

$errors = recuperer_session_flash('errors', []);
$old = recuperer_session_flash('old_inputs', []);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajout Apprenant</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
      font-family: 'Poppins', sans-serif; 
    }
    
    body { 
      background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
      padding: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .containerajour_apprenant {
      margin-top: 5%;
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
      max-width: 1200px;
      width: 90%;
      margin: 40px auto;
      overflow: hidden;
    }
    
    .header {
    background: #ff7a00;
      padding: 25px;
      color: white;
      text-align: center;
      border-radius: 20px 20px 0 0;
    }
    
    .header h1 {
      margin: 0;
      font-weight: 600;
      letter-spacing: 1px;
    }
    
    .form-container {
      padding: 30px;
    }
    
    .section {
      background: #fff;
      border-radius: 15px;
      margin-bottom: 30px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: transform 0.3s ease;
    }
    
    .section:hover {
      transform: translateY(-5px);
    }
    
    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f1f1f1;
    }
    
    .section-header h2 {
      font-size: 1.4rem;
      color: #333;
      display: flex;
      align-items: center;
    }
    
    .section-header h2 i {
      margin-right: 10px;
      color: #009688;
    }
    
    .edit-icon {
      cursor: pointer;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f1f8f7;
      border-radius: 50%;
      color: #ff7a00;
      transition: all 0.3s ease;
    }
    
    .edit-icon:hover {
      background: #ff7a00;
      color: white;
    }
    
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 25px;
    }
    
    .form-group {
      position: relative;
      margin-bottom: 5px;
    }
    
    .form-group label {
      display: block;
      font-size: 0.9rem;
      margin-bottom: 8px;
      color: #555;
      font-weight: 500;
    }
    
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background-color: #f9f9f9;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      border-color: "";
      box-shadow: 0 0 0 3px #ff7a00;
      outline: none;
      background-color: #fff;
    }
    
    .form-group input::placeholder,
    .form-group select::placeholder {
      color: #aaa;
    }
    
    .form-group input[readonly] {
      background-color: #f0f0f0;
      cursor: not-allowed;
    }
    
    .alert {
      border-color: #ff5252 !important;
      background-color: #fff8f8 !important;
    }
    
    .error-message {
      color: #ff5252;
      font-size: 0.8rem;
      margin-top: 5px;
      display: flex;
      align-items: center;
    }
    
    .error-message:before {
      content: "\f071";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      margin-right: 5px;
      font-size: 0.7rem;
    }
    
    .file-upload .upload-box {
      border: 2px dashed #ff7a00;
      padding: 30px 20px;
      text-align: center;
      border-radius: 10px;
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
      background-color: #f9f9f9;
    }
    
    .file-upload .upload-box:hover {
      background-color: #f0fffd;
      border-color: #00796b;
    }
    
    .file-upload input[type="file"] {
      opacity: 0;
      position: absolute;
      width: 100%;
      height: 100%;
      left: 0;
      top: 0;
      cursor: pointer;
    }
    
    .file-upload p {
      margin: 10px 0 0;
      font-size: 0.95rem;
      color: #ff7a00;
    }
    
    .file-upload i {
      font-size: 2rem;
      color: #ff7a00;
      margin-bottom: 10px;
    }
    
    .buttons {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 30px;
      padding: 0 10px;
    }
    
    .cancel {
      background: transparent;
      color: #555;
      border: none;
      padding: 12px 25px;
      font-size: 1rem;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
      border-radius: 10px;
    }
    
    .cancel:hover {
      background: #f1f1f1;
    }
    
    .submit {
      background: #ff7a00;
      color: white;
      border: none;
      padding: 12px 30px;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 150, 136, 0.2);
      display: flex;
      align-items: center;
    }
    
    .submit i {
      margin-right: 8px;
    }
    
    .submit:hover {
      background: #ff7a00;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px #ff7a00;
    }
    
    @media (max-width: 768px) {
      .container {
        width: 95%;
        margin: 20px auto;
      }
      
      .form-container {
        padding: 20px;
      }
      
      .grid {
        grid-template-columns: 1fr;
      }
      
      .buttons {
        flex-direction: column;
      }
      
      .button {
        width: 100%;
      }
    }
  </style>
</head>

<body>

<div class="containerajour_apprenant">
  <div class="header">
    <h1>Ajout d'un Apprenant</h1>
  </div>

  <div class="form-container">
    <form action="index.php?page=ajouter_apprenant" method="POST" enctype="multipart/form-data" class="form-ajout">

      <!-- Section Apprenant -->
      <section class="section">
        <div class="section-header">
          <h2><i class="fas fa-user-graduate"></i> Informations de l'Apprenant</h2>
          <div class="edit-icon">
            <i class="fas fa-pencil-alt"></i>
          </div>
        </div>

        <div class="grid">
          <!-- Matricule -->
          <div class="form-group">
            <label>Matricule</label>
            <input type="text" name="matricule" value="<?= htmlspecialchars($matricule ?? '') ?>" readonly class="<?= !empty($errors['matricule']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['matricule'])): ?><p class="error-message"><?= htmlspecialchars($errors['matricule']) ?></p><?php endif; ?>
          </div>

          <!-- Nom complet -->
          <div class="form-group">
            <label>Nom Complet</label>
            <input type="text" name="nom_complet" placeholder="Ex: Seydina Diop" value="<?= htmlspecialchars($old['nom_complet'] ?? '') ?>" class="<?= !empty($errors['nom_complet']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['nom_complet'])): ?><p class="error-message"><?= htmlspecialchars($errors['nom_complet']) ?></p><?php endif; ?>
          </div>

          <!-- Date de naissance -->
          <div class="form-group">
            <label>Date de naissance</label>
            <input type="text" name="date_naissance" placeholder="Ex: 2000-01-01" value="<?= htmlspecialchars($old['date_naissance'] ?? '') ?>" class="<?= !empty($errors['date_naissance']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['date_naissance'])): ?><p class="error-message"><?= htmlspecialchars($errors['date_naissance']) ?></p><?php endif; ?>
          </div>

          <!-- Lieu de naissance -->
          <div class="form-group">
            <label>Lieu de naissance</label>
            <input type="text" name="lieu_naissance" placeholder="Ex: Dakar" value="<?= htmlspecialchars($old['lieu_naissance'] ?? '') ?>" class="<?= !empty($errors['lieu_naissance']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['lieu_naissance'])): ?><p class="error-message"><?= htmlspecialchars($errors['lieu_naissance']) ?></p><?php endif; ?>
          </div>

          <!-- Adresse -->
          <div class="form-group">
            <label>Adresse</label>
            <input type="text" name="adresse" placeholder="Ex: Liberté 6" value="<?= htmlspecialchars($old['adresse'] ?? '') ?>" class="<?= !empty($errors['adresse']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['adresse'])): ?><p class="error-message"><?= htmlspecialchars($errors['adresse']) ?></p><?php endif; ?>
          </div>

          <!-- Email (Login) -->
          <div class="form-group">
            <label>Email</label>
            <input type="text" name="login" placeholder="Ex: email@exemple.com" value="<?= htmlspecialchars($old['login'] ?? '') ?>" class="<?= !empty($errors['login']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['login'])): ?><p class="error-message"><?= htmlspecialchars($errors['login']) ?></p><?php endif; ?>
          </div>

          <!-- Téléphone -->
          <div class="form-group">
            <label>Téléphone</label>
            <input type="text" name="telephone" placeholder="Ex: 770000000" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>" class="<?= !empty($errors['telephone']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['telephone'])): ?><p class="error-message"><?= htmlspecialchars($errors['telephone']) ?></p><?php endif; ?>
          </div>

          <!-- Référentiel -->
          <div class="form-group">
            <label>Référentiel</label>
            <select name="referenciel" class="<?= !empty($errors['referenciel']) ? 'alert' : '' ?>">
              <option value="">-- Sélectionner un référentiel --</option>
              <?php foreach ($referenciels as $ref): ?>
                <option value="<?= $ref['id'] ?>" <?= (!empty($old['referenciel']) && $old['referenciel'] == $ref['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ref['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['referenciel'])): ?><p class="error-message"><?= htmlspecialchars($errors['referenciel']) ?></p><?php endif; ?>
          </div>

          <!-- Upload Photo -->
          <div class="form-group file-upload">
            <label>Photo</label>
            <div class="upload-box">
              <input type="file" name="document">
              <i class="fas fa-cloud-upload-alt"></i>
              <p>Cliquez pour ajouter une image</p>
            </div>
            <?php if (!empty($errors['photo'])): ?><p class="error-message"><?= htmlspecialchars($errors['photo']) ?></p><?php endif; ?>
          </div>

        </div>
      </section>

      <!-- Section Tuteur -->
      <section class="section">
        <div class="section-header">
          <h2><i class="fas fa-user-friends"></i> Informations du Tuteur</h2>
          <div class="edit-icon">
            <i class="fas fa-pencil-alt"></i>
          </div>
        </div>

        <div class="grid">

          <!-- Nom du Tuteur -->
          <div class="form-group">
            <label>Nom du Tuteur</label>
            <input type="text" name="tuteur_nom" placeholder="Ex: Nathan Allen" value="<?= htmlspecialchars($old['tuteur_nom'] ?? '') ?>" class="<?= !empty($errors['tuteur_nom']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['tuteur_nom'])): ?><p class="error-message"><?= htmlspecialchars($errors['tuteur_nom']) ?></p><?php endif; ?>
          </div>

          <!-- Lien de Parenté -->
          <div class="form-group">
            <label>Lien de Parenté</label>
            <input type="text" name="lien_parente" placeholder="Ex: Mère" value="<?= htmlspecialchars($old['lien_parente'] ?? '') ?>" class="<?= !empty($errors['lien_parente']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['lien_parente'])): ?><p class="error-message"><?= htmlspecialchars($errors['lien_parente']) ?></p><?php endif; ?>
          </div>

          <!-- Adresse du Tuteur -->
          <div class="form-group">
            <label>Adresse du Tuteur</label>
            <input type="text" name="tuteur_adresse" placeholder="Ex: Dakar" value="<?= htmlspecialchars($old['tuteur_adresse'] ?? '') ?>" class="<?= !empty($errors['tuteur_adresse']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['tuteur_adresse'])): ?><p class="error-message"><?= htmlspecialchars($errors['tuteur_adresse']) ?></p><?php endif; ?>
          </div>

          <!-- Téléphone du Tuteur -->
          <div class="form-group">
            <label>Téléphone du Tuteur</label>
            <input type="text" name="tuteur_telephone" placeholder="Ex: 770000001" value="<?= htmlspecialchars($old['tuteur_telephone'] ?? '') ?>" class="<?= !empty($errors['tuteur_telephone']) ? 'alert' : '' ?>">
            <?php if (!empty($errors['tuteur_telephone'])): ?><p class="error-message"><?= htmlspecialchars($errors['tuteur_telephone']) ?></p><?php endif; ?>
          </div>

        </div>
      </section>

      <div class="buttons">
        <a href="index.php?page=liste_apprenant" class="cancel">Annuler</a>
        <button type="submit" class="submit"><i class="fas fa-save"></i> Enregistrer</button>
      </div>

    </form>
  </div>
</div>

</body>
</html>