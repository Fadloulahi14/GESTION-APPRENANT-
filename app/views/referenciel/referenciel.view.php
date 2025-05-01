<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';

use App\Enums\CheminPage;

$url = "http://" . $_SERVER["HTTP_HOST"];
$css_ref = CheminPage::CSS_REFERENCIEL->value;
require_once __DIR__ . '/../../services/session.service.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_inputs'] ?? [];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Référentiels</title>
    <link rel="stylesheet" href="<?= $url  ?>">
</head>

<body>
    <div class="ref-container">
        <header>
            <h1>Référentiels</h1>
            <p>Gérer les référentiels de la promotion</p>
        </header>

        <div class="search-bar">
            <form method="GET" action="">
                <input type="hidden" name="page" value="referenciel">
                <input

                    type="text"
                    name="search"
                    placeholder="Rechercher un référentiel..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </form>
            <div class="actions">
    <button class="btn btn-orange" onclick="location.href='?page=all_referenciel'">
        📋 Tous les référentiels
    </button>
    <a href="index.php?page=referenciel&action=show_popup" class="btn btn-green">
        + Ajouter à la promotion
    </a>
</div>
        </div>

        <div class="ref-grid">
            <?php foreach ($referentiels as $ref): ?>
                <div class="ref-card">
                    <div class="ref-image">
                        <img src="<?= htmlspecialchars($ref['photo']) ?>" alt="<?= htmlspecialchars($ref['nom']) ?>">
                    </div>
                    <div class="ref-content">
                        <h3><?= htmlspecialchars($ref['nom']) ?></h3>
                        <p class="description">
                            <?= htmlspecialchars($ref['description'] ?? 'Aucune description disponible') ?>
                        </p>
                        <div class="ref-stats">
                            <span><?= $ref['modules'] ?? 0 ?> modules</span>
                            <span><?= $ref['apprenants'] ?? 0 ?> apprenants</span>
                        </div>
                        <div class="ref-capacity">
                            <span>Capacité: <?= $ref['capacite'] ?> places</span>
                        </div>
                        <div class="apprenant-icons">
                            <?php
                            $totalApprenants = min(($ref['apprenants'] ?? 0), 3);
                            for ($i = 0; $i < $totalApprenants; $i++):
                            ?>
                                <div class="apprenant-icon"></div>
                            <?php endfor; ?>
                            <?php if (($ref['apprenants'] ?? 0) > 3): ?>
                                <div class="remaining-count">+<?= ($ref['apprenants'] - 3) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Popup d'ajout de référentiel -->
   

    <?php if (!empty($errors)): ?>
       
    <?php endif; ?>



 <!-- Popup d'ajout de référentiel à la promotion -->
<?php 
$showPopup = isset($_GET['action']) && $_GET['action'] === 'show_popup';
$searchTerm = $_GET['search_ref'] ?? '';
?>
<!-- Popup d'ajout de référentiel à la promotion -->
<div class="popup" style="display: <?= $showPopup ? 'block' : 'none' ?>">
    <div class="popup-content">
        <div class="popup-header">
            <h2>Gérer les référentiels de la promotion</h2>
            <a href="index.php?page=referenciel" class="close">&times;</a>
        </div>

        <form method="POST" action="index.php?page=affecter_referentiel">
            <input type="hidden" name="promo_id" value="<?= $promo_active['id'] ?? '' ?>">
            <div class="ref-container-flex">
    <!-- Liste des référentiels non affectés -->
    <div class="ref-box">
        <h3>Référentiels disponibles</h3>
        <div class="search-box">
            <input type="text" 
                   name="search_non_affectes" 
                   placeholder="Rechercher..."
                   value="<?= htmlspecialchars($_GET['search_non_affectes'] ?? '') ?>">
            <button type="submit" name="action" value="search">🔍</button>
        </div>
        <div class="ref-list">
            <?php
            $peut_modifier = est_promo_en_cours($promo_active);
            $referentiels_non_affectes = array_filter($tous_referentiels, function($ref) use ($promo_active) {
                return !isset($promo_active['referenciels']) || 
                       !in_array($ref['id'], $promo_active['referenciels']);
            });
            foreach ($referentiels_non_affectes as $ref):
            ?>
                <div class="ref-item">
                    <div class="ref-content">
                        <img src="<?= htmlspecialchars($ref['photo']) ?>" alt="" class="ref-mini-img">
                        <span class="ref-name"><?= htmlspecialchars($ref['nom']) ?></span>
                    </div>
                    <?php if ($peut_modifier): ?>
                        <button type="submit" 
                                name="ajouter_ref" 
                                value="<?= $ref['id'] ?>" 
                                class="btn-action add">
                            ➡️
                        </button>
                    <?php else: ?>
                        <button type="button" 
                                class="btn-action add disabled" 
                                title="Seule la promotion en cours peut être modifiée"
                                disabled>
                            ➡️
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Liste des référentiels affectés -->
    <div class="ref-box">
        <h3>Référentiels affectés</h3>
        <div class="search-box">
            <input type="text" 
                   name="search_affectes" 
                   placeholder="Rechercher..."
                   value="<?= htmlspecialchars($_GET['search_affectes'] ?? '') ?>">
            <button type="submit" name="action" value="search">🔍</button>
        </div>
        <div class="ref-list">
            <?php
            $referentiels_affectes = array_filter($tous_referentiels, function($ref) use ($promo_active) {
                return isset($promo_active['referenciels']) && 
                       in_array($ref['id'], $promo_active['referenciels']);
            });
            foreach ($referentiels_affectes as $ref):
            ?>
                <div class="ref-item">
                    <?php if ($peut_modifier && ($ref['apprenants'] ?? 0) === 0): ?>
                        <button type="submit" 
                                name="retirer_ref" 
                                value="<?= $ref['id'] ?>" 
                                class="btn-action remove">
                            ⬅️
                        </button>
                    <?php else: ?>
                        <button type="button" 
                                class="btn-action remove disabled" 
                                title="<?= ($ref['apprenants'] ?? 0) > 0 ? 'Ce référentiel contient des apprenants' : 'Seule la promotion en cours peut être modifiée' ?>"
                                disabled>
                            ⬅️
                        </button>
                    <?php endif; ?>
                    <div class="ref-content">
                        <img src="<?= htmlspecialchars($ref['photo']) ?>" alt="" class="ref-mini-img">
                        <span class="ref-name"><?= htmlspecialchars($ref['nom']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

            <div class="popup-footer">
                <a href="index.php?page=referenciel" class="btn-annuler">Fermer</a>
            </div>
        </form>
    </div>
</div>
<!-- Bouton pour ouvrir le popup -->

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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background-color: var(--background-color);
}

.ref-container {
   
    margin-top: 80px; /* Ajout d'une marge en haut */
}

header {
    margin-bottom: 2rem;
}

header h1 {
    color: var(--primary-color);
    font-size: 1.8rem;
}

header p {
    color: var(--gray);
    margin-top: 0.5rem;
}

.search-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1rem;
}

.search-bar input {
    flex: 1;
    padding: 0.8rem 1rem;
    border: 1px solid var(--gray-light);
    border-radius: 8px;
    font-size: 0.9rem;
    background-color: var(--white);
    color: var(--gray);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.actions {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn:hover {
    opacity: 0.9;
}

.btn-orange {
    background-color: var(--secondary-color);
    color: var(--white);
}

.btn-green {
    background-color: var(--primary-color);
    color: var(--white);
}

/* Ajout des styles pour les icônes dans les boutons */
.btn i {
    font-size: 1.2rem;
}

.ref-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Réduit de 300px à 250px */
    gap: 1.2rem; /* Réduit l'espacement */
    padding: 1rem;
}

.ref-card {
    background: var(--white);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.ref-card:hover {
    transform: translateY(-5px);
}

.ref-image {
    height: 140px; /* Réduit la hauteur de l'image */
    overflow: hidden;
    background-color: var(--gray-light);
}

.ref-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.ref-content {
    padding: 1rem; /* Réduit le padding */
}

.ref-content h3 {
    color: var(--primary-color);
    font-size: 1rem; /* Réduit la taille du titre */
    margin-bottom: 0.3rem;
    font-weight: 600;
}

.description {
    color: #666;
    font-size: 0.8rem; /* Réduit la taille du texte */
    margin-bottom: 1rem;
    line-height: 1.5;
    height: 32px; /* Réduit la hauteur */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    
    -webkit-box-orient: vertical;
}

.ref-stats {
    display: flex;
    justify-content: flex-start;
    gap: 1rem; /* Réduit l'espacement */
    margin-bottom: 0.8rem;
    padding-bottom: 0.8rem;
    border-bottom: 1px solid var(--gray-light);
}

.ref-stats span {
    color: #666;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ref-stats span::before {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: var(--secondary-color);
    border-radius: 50%;
}

.ref-capacity {
    font-size: 0.9rem;
    color: var(--secondary-color);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ref-capacity::before {
    content: '👥';
}

/* Styles pour les apprenants */
.apprenant-icons {
    display: flex;
    align-items: center;
    margin-top: 1rem;
}

.apprenant-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: var(--gray-light);
    margin-right: -10px;
    border: 2px solid var(--white);
}

/* Style pour le nombre d'apprenants restants */
.remaining-count {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    margin-left: 5px;
}


/* Style pour le conteneur de la popup */
    .alert {
        border: 2px solid red !important;
    }

    .error-message {
        color: red;
        font-size: 0.8rem;
        margin-top: 0.5rem;
    }
    .popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.popup-content {
    position: relative;
    background: white;
    width: 100%;
    max-width: 700px;
    top: 5%;
    margin: 50px auto;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 24px;
    text-decoration: none;
    color: #666;
}

.btn-search {
    padding: 8px 15px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-left: 10px;
}



.ref-item {
    display: flex;
    align-items: center;
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.ref-item:last-child {
    border-bottom: none;
}

.btn-terminer {
    background: #00857c;
    color: white;
}

.btn-annuler {
    background: #ccc;
    color: #333;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 4px;
    margin-right: 10px;
}

.popup-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.message {
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
}

.message.success {
    background: #d4edda;
    color: #155724;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
}

    
.ref-list p {
    text-align: center;
    padding: 20px;
    color: #666;
}

.popup-content {
    min-height: 200px;
}
.message {
    padding: 15px;
    margin: 15px 0;
    border-radius: 4px;
    text-align: center;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.ref-container-flex {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}

.ref-box {
    flex: 1;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.ref-box h3 {
    margin: 0 0 15px;
    color: #333;
    font-size: 1.1em;
    text-align: center;
}

.search-box {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.search-box input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}


.ref-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.2s;
}

.ref-item:hover {
    background-color: #f5f5f5;
}

/* .ref-content {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
} */

.ref-mini-img {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    object-fit: cover;
}

.ref-name {
    font-size: 0.9em;
    color: #444;
}

.btn-action {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-action.add {
    background-color: #28a745;
    color: white;
    margin-left: 10px;
}

.btn-action.remove {
    background-color: #dc3545;
    color: white;
    margin-right: 10px;
}

.btn-action:hover {
    opacity: 0.9;
}
.btn-action.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-action.disabled:hover {
    opacity: 0.5;
}
</style>


<script>
function filterReferentiels(search) {
    search = search.toLowerCase();
    document.querySelectorAll('.ref-item').forEach(item => {
        const text = item.querySelector('label').textContent.toLowerCase();
        item.style.display = text.includes(search) ? '' : 'none';
    });
}

// Retirer la validation qui empêche l'envoi du formulaire sans sélection
// pour permettre la désaffectation complète
document.getElementById('form-affecter').addEventListener('submit', function(e) {
    // La validation n'est plus nécessaire car nous voulons permettre
    // l'envoi du formulaire même sans sélection pour la désaffectation
});
</script>