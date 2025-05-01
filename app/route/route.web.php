<?php
require_once __DIR__ . '/../enums/chemin_page.php';

use App\Enums\CheminPage;
require_once CheminPage::CONTROLLER->value;
require_once CheminPage::MODEL->value;

// Définir la page par défaut
if (isset($_GET['page']) && $_GET['page'] === 'apprenant') {
    if (isset($_POST['action']) && $_POST['action'] === 'import') {
        // traiter_import();
        exit;
    } elseif (isset($_GET['action']) && $_GET['action'] === 'template') {
        // generer_modele_excel();
        exit;
    } elseif (isset($_GET['export'])) {
        switch ($_GET['export']) {
            case 'pdf':
                // exportToPDF();
                break;
            case 'excel':if (isset($_GET['page']) && $_GET['page'] === 'apprenant') {
                if (isset($_POST['action']) && $_POST['action'] === 'import') {
                    // traiter_import();
                    exit;
                } elseif (isset($_GET['action']) && $_GET['action'] === 'template') {
                    // generer_modele_excel();
                    exit;
                } elseif (isset($_GET['export'])) {
                    switch ($_GET['export']) {
                        case 'pdf':
                            // exportToPDF();
                            break;
                        case 'excel':
                            // exportToCSV();
                            break;
                    }
                    exit;
                } else {
                    // afficher_apprenants();
                    exit;
                }
            } elseif (isset($_GET['page']) && $_GET['page'] === 'export_apprenants') {
                require_once __DIR__ . '/../controllers/apprenant.controller.php';
                exporter_apprenants();
                exit;
            }
                // exportToCSV();
                break;
        }
        exit;
    } else {
        // afficher_apprenants();
        exit;
    }
} elseif (isset($_GET['page']) && $_GET['page'] === 'export_apprenants') {
    require_once __DIR__ . '/../controllers/apprenant.controller.php';
    exporter_apprenants();
    exit;
}
$page = $_GET['page'] ?? 'login';
// Résolution des routes
match ($page) {
    'login', 'logout' => (function () {
        require_once CheminPage::AUTH_CONTROLLER->value;
        voir_page_login();
    })(),
    'resetPassword' => (function () {
        require_once CheminPage::AUTH_CONTROLLER->value;
    })(),

    'liste_promo', => (function () {
        require_once CheminPage::PROMO_CONTROLLER->value;
    })(),
    'liste_table_promo' => (function () {
    require_once CheminPage::PROMO_CONTROLLER->value;
    })(),
    'add_promo' => (function () {
    require_once CheminPage::PROMO_CONTROLLER->value;
    })(),
    'layout' => (function () {
        require_once CheminPage::LAYOUT_CONTROLLER->value;
    })(),
    'referenciel' => (function() {
    require_once CheminPage::REFERENCIEL_CONTROLLER->value;
    })(),
    'all_referenciel' => (function() {
        require_once CheminPage::REFERENCIEL_CONTROLLER->value;
    })(),
    'add_referentiel' => (function() {
        require_once CheminPage::REFERENCIEL_CONTROLLER->value;
    })(),
    'activer_promo' => (function () {
    require_once CheminPage::PROMO_CONTROLLER->value;
    exit;
    })(),
    'affecter_referentiel' => (function() {
        require_once CheminPage::REFERENCIEL_CONTROLLER->value;
    })(),

    'liste_apprenant' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        lister_apprenant();
    })(),
    'traiter_ajout_apprenant' => function() {
        traiter_ajout_apprenant();
    },

    

   'ajouter_apprenant' => (function () {
    require_once CheminPage::APPRENANT_CONTROLLER->value;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        traiter_ajout_apprenant();
    } else {
        ajout_apprenant_vue();
    }
})(),

    'detail_apprenant' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        afficher_detail_apprenant();
    })(),

    'import_apprenants' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        importer_apprenants();
    })(),
    'activer_promo_liste' => (function () {
        require_once CheminPage::PROMO_CONTROLLER->value;
        exit;
        })(),
        'apprenant' => (function() {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        // afficher_apprenants();
    })(),
    

    'add_apprenant' => (function() {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
 
    })(),
    default => (function () {
        require_once CheminPage::ERROR_CONTROLLER->value;
    })()
  
};






