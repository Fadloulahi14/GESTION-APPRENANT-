<?php
require_once __DIR__ . '/../enums/chemin_page.php';
use App\Enums\CheminPage;
require_once CheminPage::CONTROLLER->value;



require_once CheminPage::SESSION_SERVICE->value;
require_once __DIR__ . '/../services/export.php';



function uploadPhoto(array $file, string $uploadDir, string $defaultPath = "assets/images/promo/default.jpg"): ?string {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $photoName = basename($file['na<?phpme']);
        $photoPath = rtrim($uploadDir, '/') . '/' . $photoName;

        if (move_uploaded_file($file['tmp_name'], $photoPath)) {
            return "assets/images/promo/" . $photoName;
        }

        return null;
    }

    return $defaultPath;
}


/**
 * Gère l'upload d'une photo
 * @param array $photo Données du fichier uploadé
 * @return string|null Chemin de la photo ou null en cas d'échec
 */
function gerer_upload_photo(array $photo): ?string {
    // Vérifier si le fichier a été correctement uploadé
    if (!isset($photo) || $photo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Créer le dossier de destination s'il n'existe pas
    $dossierDestination = __DIR__ . '/../../public/assets/images/promo/';
    if (!is_dir($dossierDestination)) {
        mkdir($dossierDestination, 0777, true);
    }
    
    // Générer un nom de fichier unique pour éviter les conflits
    $nomFichier = basename($photo['name']);
    $cheminComplet = $dossierDestination . $nomFichier;
    
    // Déplacer le fichier uploadé
    if (move_uploaded_file($photo['tmp_name'], $cheminComplet)) {
        // Retourner le chemin relatif pour stockage en BDD
        return 'assets/images/promo/' . $nomFichier;
    }
    
    return null;
}


/**
 * Calcule le prochain ID pour une nouvelle promotion.
 */
function getNextPromoId(array $promotions): int {
    $lastId = 0;
    foreach ($promotions as $promo) {
        if ($promo['id'] > $lastId) {
            $lastId = $promo['id'];
        }
    }
    return $lastId + 1;
}


function get_promo_active_name(): ?string {
    $chemin = App\Enums\CheminPage::DATA_JSON->value;
    $data = json_decode(file_get_contents($chemin), true);

    if (!empty($data['promotions'])) {
        foreach ($data['promotions'] as $promo) {
            if ($promo['statut'] === 'Active') {
                return $promo['nom'];
            }
        }
    }
    return null; 
}


function render(string $vue, array $donnees = [], ?string $layout = 'base.layout'): void {
    $baseViewPath = dirname(__DIR__) . '/views/';
    $baseLayoutPath = $baseViewPath . 'layout/';

    // Gestion du chemin absolu ou relatif
    $cheminVue = str_ends_with($vue, '.php') ? $vue : $baseViewPath . trim($vue, '/') . '.view.php';

    if (!file_exists($cheminVue)) {
        throw new Exception("Vue '$cheminVue' introuvable.");
    }

    extract($donnees);
    ob_start();
    require $cheminVue;
    $contenu = ob_get_clean();

    if ($layout !== null) {
        $cheminLayout = $baseLayoutPath . trim($layout, '/') . '.php';

        if (!file_exists($cheminLayout)) {
            throw new Exception("Layout '$layout' introuvable.");
        }
        $promoActiveName = get_promo_active_name(); 
        require $cheminLayout;
    } else {
        echo $contenu;
    }
}

function redirect_to_route(string $route, array $params = []): void {
    // Si on a des paramètres à passer dans l'URL
    if (!empty($params)) {
        $query = http_build_query($params);
        $route .= (strpos($route, '?') === false ? '?' : '&') . $query;
    }

    header("Location: $route");
    exit(); // Toujours arrêter l'exécution après une redirection
}

/**
 * Vérifie si la requête est une requête POST et si un champ spécifique est défini.
 */
function is_post_request_with_field(string $field): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$field]);
}


?>