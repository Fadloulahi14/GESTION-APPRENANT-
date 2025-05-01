<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';

use App\Enums\CheminPage;

$url = "http://" . $_SERVER["HTTP_HOST"];
$css_ref = '/assets/css/referenciel/all_referenciel.css';
require_once __DIR__ . '/../../services/session.service.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_inputs'] ?? [];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Référentiels</title>
    <link rel="stylesheet" href="">
</head>

<body>
    <div class="ref-container">
        <div class="ref-header">
            <a href="?page=referenciel" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Retour aux référentiels actifs
            </a>
            <h1>Tous les Référentiels</h1>
            <p>Liste complète des référentiels de formation</p>
        </div>

        <div class="search-bar">
            <form method="GET" action="">
                <input type="hidden" name="page" value="all_referenciel">
                <input

                    type="text"
                    name="search"
                    placeholder="Rechercher un référentiel..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </form>
            <div class="actions">
            
                <a  href="index.php?page=add_referentiel" class="btn btn-green" onclick="location.href='#popup-add'">+ Ajouter un référentiel</a>
            </div>
        </div>

        <div class="ref-grid">
    <?php foreach ($referentiels as $ref): ?>
        <div class="ref-card">
            <img src="<?= htmlspecialchars($ref['photo']) ?>" alt="<?= htmlspecialchars($ref['nom']) ?>">
            <div class="ref-content">
                <h3><?= htmlspecialchars($ref['nom']) ?></h3>
                <p><?= htmlspecialchars($ref['description'] ?? '') ?></p>
                <div class="ref-info">
                    <span>Capacité: <?= $ref['capacite'] ?> places</span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (isset($total) && isset($page) && $total > 1): ?>
    <div class="custom-pagination">
        <!-- Flèche gauche -->
        <a href="?page=all_referenciel&p=<?= max(1, $page - 1) ?>&search=<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" 
           class="arrow <?= $page === 1 ? 'disabled' : '' ?>">
            &#10094;
        </a>
        
        <!-- Pages -->
        <?php for ($i = 1; $i <= $total; $i++): ?>
            <a href="?page=all_referenciel&p=<?= $i ?>&search=<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" 
               class="page-number <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <!-- Flèche droite -->
        <a href="?page=all_referenciel&p=<?= min($total, $page + 1) ?>&search=<?= htmlspecialchars((string)($_GET['search'] ?? '')) ?>" 
           class="arrow <?= $page === $total ? 'disabled' : '' ?>">
            &#10095;
        </a>
    </div>

    <?php if (isset($debut) && isset($parPage) && isset($totalElements)): ?>
        <div class="pagination-info">
            <?= $debut + 1 ?> à <?= min($debut + $parPage, $totalElements) ?> sur <?= $totalElements ?> résultats
        </div>
    <?php endif; ?>
<?php endif; ?>



    
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
.ref-header {
    padding: 2rem 0;
}

.back-link {
    color: var(--gray);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.back-link:hover {
    color: var(--primary-color);
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
    width: 400%;
    max-width: 1200px;
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

.ref-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 2rem;
}

.ref-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ref-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.ref-content {
    padding: 1.5rem;
}

.ref-content h3 {
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.ref-info {
    margin-top: 1rem;
    color: var(--gray);
    font-size: 0.9rem;
}
/* Styles de pagination */
.custom-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 30px;
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
    background: var(--primary-color);
    color: white;
}

.arrow.disabled {
    pointer-events: none;
    opacity: 0.4;
}

.pagination-info {
    text-align: center;
    margin-top: 15px;
    margin-bottom: 30px;
    font-size: 14px;
    color: var(--gray);
}

/* Ajustement pour le responsive */
@media (max-width: 768px) {
    .custom-pagination {
        flex-wrap: wrap;
    }

    .page-number, .arrow {
        padding: 6px 10px;
        font-size: 14px;
    }

    .pagination-info {
        font-size: 13px;
    }
}
</style>