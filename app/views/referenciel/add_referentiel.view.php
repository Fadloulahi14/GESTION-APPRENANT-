<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';

use App\Enums\CheminPage;

$url = "http://" . $_SERVER["HTTP_HOST"];
$css_ref = '/assets/css/referenciel/add_referentiel.css';
require_once __DIR__ . '/../../services/session.service.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_inputs'] ?? [];
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <div id="popup-add" class="modal">
        <div class="modal-content">
            <a href="#" class="close-btn">&times;</a>
            <h2>Créer un nouveau référentiel</h2>

            <form method="POST" action="index.php?page=add_referentiel" enctype="multipart/form-data">
    <input type="hidden" name="action" value="ajouter">
    
    <!-- Zone d'upload photo -->
    <div class="upload-wrapper">
        <label for="photo" class="upload-label <?= isset($errors['photo']) ? 'alert' : '' ?>">
            <span class="upload-text">Cliquez pour ajouter une photo</span>
            <input
                type="file"
                id="photo"
                name="photo"
                accept="image/*"
                class="file-input">
        </label>
        <?php if (isset($errors['photo'])): ?>
            <p class="error-message"><?= $errors['photo'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Nom -->
    <div class="form-group">
        <label for="nom">Nom*</label>
        <input
            type="text"
            id="nom"
            name="nom"
            value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
            class="<?= isset($errors['nom']) ? 'alert' : '' ?>"
            placeholder="Ex: Développement Web">
        <?php if (isset($errors['nom'])): ?>
            <p class="error-message"><?= $errors['nom'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Description -->
    <div class="form-group">
        <label for="description">Description</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="<?= isset($errors['description']) ? 'alert' : '' ?>"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <p class="error-message"><?= $errors['description'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Capacité et sessions -->
    <div class="form-group" style="display: flex; gap: 1rem;">
        <div style="flex:1;">
            <label for="capacite">Capacité*</label>
            <input
                type="number"
                id="capacite"
                name="capacite"
                value="<?= htmlspecialchars($old['capacite'] ?? '') ?>"
                class="<?= isset($errors['capacite']) ? 'alert' : '' ?>"
                placeholder="Ex: 30">
            <?php if (isset($errors['capacite'])): ?>
                <p class="error-message"><?= $errors['capacite'] ?></p>
            <?php endif; ?>
        </div>

        <div style="flex:1;">
            <label for="sessions">Nombre de sessions*</label>
            <select id="sessions" name="sessions" class="<?= isset($errors['sessions']) ? 'alert' : '' ?>">
                <option value="">Choisir</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= ($old['sessions'] ?? '') == $i ? 'selected' : '' ?>>
                        <?= $i ?> session<?= $i > 1 ? 's' : '' ?>
                    </option>
                <?php endfor; ?>
            </select>
            <?php if (isset($errors['sessions'])): ?>
                <p class="error-message"><?= $errors['sessions'] ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div class="form-actions">
        <a href="index.php?page=all_referenciel" class="cancel-btn">Annuler</a>
        <button type="submit" class="submit-btn">Créer</button>
    </div>
</form></div>
    </div>
</body>
</html>



<style>
    :root {
    --primary-color: #00857c;
    --secondary-color: #ff7900;
    --background-color: #f5f7fa;
    --white: #ffffff;
    --gray-light: #f0f2f5;
    --gray: #707070;
    --border-radius: 8px;
}

/* Modal styles améliorés */
.modal {
    display: flex;;
    position: fixed;
    inset: 0;
    background:white;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal:target {
    display: flex;
}

.modal-content {
    max-width: 1000px;
    width: 90%;
    background: var(--white);
    padding: 2rem;
    border-radius: var(--border-radius);
    position: relative;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.modal-content h2 {
    color: var(--primary-color);
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.5rem;
    color: var(--gray);
    text-decoration: none;
    transition: color 0.2s;
}

.close-btn:hover {
    color: var(--primary-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--gray);
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid var(--gray-light);
    border-radius: var(--border-radius);
    font-size: 0.9rem;
    transition: border-color 0.2s;
}

.form-group input:focus {
    border-color: var(--primary-color);
    outline: none;
}

.form-group input[type="file"] {
    padding: 0.5rem;
    border: 2px dashed var(--gray-light);
    background: var(--gray-light);
    cursor: pointer;
}

.form-group input[type="text"],
.form-group input[type="number"] {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid var(--gray-light);
    border-radius: var(--border-radius);
    font-size: 0.9rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
}

.cancel-btn {
    padding: 0.8rem 1.5rem;
    color: var(--gray);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: background-color 0.2s;
}

.cancel-btn:hover {
    background-color: var(--gray-light);
}

.submit-btn {
    background-color: var(--primary-color);
    color: var(--white);
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: opacity 0.2s;
}

.submit-btn:hover {
    opacity: 0.9;
}

/* Style pour les champs requis */
.form-group label[for]::after {
    content: '*';
    color: var(--secondary-color);
    margin-left: 4px;
}

.upload-zone {
    border: 2px dashed #ddd;
    padding: 2rem;
    text-align: center;
    border-radius: var(--border-radius);
    cursor: pointer;
}

/* Styles pour la prévisualisation de l'image */
.preview-zone {
    width: 100%;
    height: 200px;
    border: 2px dashed var(--gray-light);
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

.preview-zone img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.preview-zone.empty {
    background: var(--gray-light);
}

/* Styles pour l'upload de fichier */
.upload-wrapper {
    width: 100%;
    margin-bottom: 1rem;
}

.upload-label {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 150px;
    border: 2px dashed var(--gray-light);
    border-radius: var(--border-radius);
    cursor: pointer;
    background-color: var(--gray-light);
    transition: all 0.3s ease;
}

.upload-label:hover {
    border-color: var(--primary-color);
    background-color: var(--gray-light);
}

.upload-text {
    color: var(--gray);
    font-size: 0.9rem;
    text-align: center;
}

.file-input {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}

</style>