<?php
require_once __DIR__ . '/../../services/session.service.php';
demarrer_session();

// Vérifier si l'apprenant est connecté
if (!isset($_SESSION['apprenant_connecte']) || $_SESSION['apprenant_connecte'] !== true) {
    header('Location: index.php?page=login_apprenant');
    exit;
}

// Récupérer les informations de l'apprenant
global $model_tab;
require_once __DIR__ . '/../../enums/chemin_page.php';
require_once __DIR__ . '/../../enums/model.enum.php';

use App\Enums\CheminPage;
use App\Models\JSONMETHODE;

$chemin = CheminPage::DATA_JSON->value;
$contenu = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
$utilisateurs = $contenu['utilisateurs'] ?? [];

// Rechercher l'apprenant par ID
$apprenant = null;
$apprenantId = $_SESSION['apprenant_id'] ?? 0;

foreach ($utilisateurs as $utilisateur) {
    if (($utilisateur['id'] ?? 0) == $apprenantId && ($utilisateur['profil'] ?? '') === 'Apprenant') {
        $apprenant = $utilisateur;
        break;
    }
}

// Si l'apprenant n'est pas trouvé, rediriger vers la page de connexion
if (!$apprenant) {
    header('Location: index.php?page=login_apprenant');
    exit;
}

// Récupérer le référentiel de l'apprenant
$referenciels = $contenu['referenciel'] ?? [];
$referenciel = null;

foreach ($referenciels as $ref) {
    if (($ref['id'] ?? 0) == ($apprenant['referenciel'] ?? 0)) {
        $referenciel = $ref;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - <?= htmlspecialchars($apprenant['nom_complet'] ?? 'Apprenant') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f1f5f9;
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Container principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .app-header {
            background: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .logo {
            height: 40px;
            width: auto;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f1f5f9;
        }

        .user-name {
            font-weight: 500;
        }

        .logout-btn {
            background: #fee2e2;
            color: #b91c1c;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: #fecaca;
        }

        /* Style des cartes */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* En-tête profil */
        .profile-card {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-name {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .profile-job {
            color: #ff7900;
            margin-bottom: 10px;
        }

        .profile-contact {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .contact-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #64748b;
        }

        /* Grille des statistiques */
        .card-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Titre des cartes */
        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: #1e293b;
            font-weight: 500;
        }

        .orange-icon {
            color: #ff7900;
        }

        /* Stats de présence */
        .presence-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
        }

        .present-bg { background: #e8f5e9; }
        .retard-bg { background: #fff3e0; }
        .absent-bg { background: #ffebee; }

        .stat-number {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .present { color: #2e7d32; }
        .retard { color: #f57c00; }
        .absent { color: #d32f2f; }

        /* QR Code section */
        .qr-section {
            text-align: center;
        }

        .qr-title {
            font-weight: 500;
            margin-bottom: 15px;
            color: #1e293b;
        }

        .qr-code {
            width: 150px;
            height: 150px;
            margin: 15px 0;
        }

        .qr-subtitle {
            color: #64748b;
            margin-bottom: 5px;
        }

        .qr-id {
            color: #ff7900;
            font-weight: 500;
        }

        /* Historique et recherche */
        .search-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .filter-button {
            padding: 8px 16px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #64748b;
            cursor: pointer;
        }

        /* Table d'historique */
        .presence-history table {
            width: 100%;
            border-collapse: collapse;
        }

        .presence-history th,
        .presence-history td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .presence-history th {
            color: #64748b;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            background: #e8f5e9;
            color: #2e7d32;
            font-size: 12px;
            font-weight: 500;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .pagination-info {
            color: #64748b;
        }

        .pagination-controls {
            display: flex;
            gap: 5px;
        }

        .pagination-button {
            padding: 6px 12px;
            border: none;
            background: none;
            color: #64748b;
            cursor: pointer;
        }

        .pagination-button.active {
            background: #ff7900;
            color: white;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .app-header {
                padding: 0.5rem 1rem;
                flex-direction: column;
                gap: 10px;
            }

            .header-right {
                width: 100%;
                justify-content: space-between;
            }

            .profile-card {
                flex-direction: column;
                text-align: center;
            }

            .profile-contact {
                align-items: center;
            }

            .card-row {
                grid-template-columns: 1fr;
            }

            .presence-stats {
                grid-template-columns: 1fr;
            }

            .search-filters {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <div class="header-left">
            <img src="assets/images/login/logo_odc.png" alt="Logo" class="logo">
        </div>
        <div class="header-right">
            <div class="user-profile">
                <img src="<?= !empty($apprenant['photo']) ? htmlspecialchars($apprenant['photo']) : 'assets/images/default-avatar.png' ?>" alt="Avatar" class="avatar">
                <span class="user-name"><?= htmlspecialchars($apprenant['nom_complet']) ?></span>
            </div>
            <a href="index.php?page=logout_apprenant" class="logout-btn">Déconnexion</a>
        </div>
    </header>

    <div class="container">
        <!-- Carte de profil -->
        <div class="card profile-card">
            <img src="<?= !empty($apprenant['photo']) ? htmlspecialchars($apprenant['photo']) : 'assets/images/default-avatar.png' ?>" alt="Photo de profil" class="profile-image">
            <div class="profile-info">
                <h1 class="profile-name"><?= htmlspecialchars($apprenant['nom_complet']) ?></h1>
                <div class="profile-job"><?= htmlspecialchars($referenciel['nom'] ?? 'Référentiel non spécifié') ?></div>
                <div class="profile-contact">
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($apprenant['login']) ?></span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <span><?= htmlspecialchars($apprenant['telephone']) ?></span>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($apprenant['adresse']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grille des statistiques -->
        <div class="card-row">
            <!-- Carte des présences -->
            <div class="card">
                <div class="card-title">
                    <div class="orange-icon">📊</div>
                    <span>Présences</span>
                </div>
                <div class="presence-stats">
                    <div class="stat-item present-bg">
                        <div class="stat-number present">40</div>
                        <div class="stat-label">Présent</div>
                    </div>
                    <div class="stat-item retard-bg">
                        <div class="stat-number retard">7</div>
                        <div class="stat-label">Retard</div>
                    </div>
                    <div class="stat-item absent-bg">
                        <div class="stat-number absent">0</div>
                        <div class="stat-label">Absent</div>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="card">
                <div class="qr-section">
                    <div class="qr-title">Scanner pour la présence</div>
                    <img src="assets/codeqr.jpeg" alt="QR Code" class="qr-code">
                    <div class="qr-subtitle">Code de présence personnel</div>
                    <div class="qr-id"><?= htmlspecialchars($apprenant['matricule'] ?? '') ?></div>
                </div>
            </div>
        </div>

        <!-- Historique des présences -->
        <div class="card">
            <div class="card-title">
                <div class="orange-icon">🕒</div>
                <span>Historique de présence</span>
            </div>

            <div class="search-filters">
                <input type="text" class="search-input" placeholder="Rechercher...">
                <div class="filter-dropdown">
                    <button class="filter-button">Tous les statuts</button>
                </div>
            </div>

            <div class="presence-history">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--
                        <!-- Exemple d'entrées d'historique -->
                        <tr>
                            <td>28/04/2025 08:15:00</td>
                            <td><span class="status-badge" style="background: #e8f5e9; color: #2e7d32;">Présent</span></td>
                        </tr>
                        <tr>
                            <td>27/04/2025 08:25:30</td>
                            <td><span class="status-badge" style="background: #fff3e0; color: #f57c00;">Retard</span></td>
                        </tr>
                        <tr>
                            <td>26/04/2025 08:00:12</td>
                            <td><span class="status-badge" style="background: #e8f5e9; color: #2e7d32;">Présent</span></td>
                        </tr>
                        <tr>
                            <td>25/04/2025 08:05:45</td>
                            <td><span class="status-badge" style="background: #e8f5e9; color: #2e7d32;">Présent</span></td>
                        </tr>
                        <tr>
                            <td>24/04/2025 08:30:22</td>
                            <td><span class="status-badge" style="background: #fff3e0; color: #f57c00;">Retard</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <div class="pagination-info">
                    Affichage de 1 à 5 sur 40 entrées
                </div>
                <div class="pagination-controls">
                    <button class="pagination-button active">1</button>
                    <button class="pagination-button">2</button>
                    <button class="pagination-button">3</button>
                    <button class="pagination-button">4</button>
                    <button class="pagination-button">5</button>
                    <button class="pagination-button">›</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pour la pagination et le filtrage
        document.addEventListener('DOMContentLoaded', function() {
            // Exemple de code pour gérer la pagination
            const paginationButtons = document.querySelectorAll('.pagination-button');
            paginationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Retirer la classe active de tous les boutons
                    paginationButtons.forEach(btn => btn.classList.remove('active'));
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');
                    
                    // Ici, vous pourriez charger les données correspondant à la page
                    // via AJAX ou autre méthode
                });
            });
            
            // Exemple de code pour gérer le filtrage
            const filterButton = document.querySelector('.filter-button');
            if (filterButton) {
                filterButton.addEventListener('click', function() {
                    // Ici, vous pourriez afficher un menu déroulant avec les options de filtre
                    const options = ['Tous les statuts', 'Présent', 'Retard', 'Absent'];
                    // Puis gérer la sélection et le filtrage des données
                });
            }
            
            // Exemple de code pour la recherche
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    // Ici, vous pourriez filtrer les données en fonction du texte saisi
                    const searchText = this.value.toLowerCase();
                    // Puis mettre à jour l'affichage en conséquence
                });
            }
        });
    </script>
</body>
</html>
