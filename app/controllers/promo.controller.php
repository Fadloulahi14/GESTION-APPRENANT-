<?php

require_once __DIR__ . '/../enums/chemin_page.php';
use App\Enums\CheminPage;

require_once CheminPage::MODEL->value;
require_once CheminPage::MESSAGE_ENUM->value;
require_once CheminPage::ERREUR_ENUM->value;
require_once CheminPage::SESSION_SERVICE->value;
require_once CheminPage::VALIDATOR_SERVICE->value;
require_once CheminPage::REF_MODEL->value;
require_once CheminPage::MODEL_ENUM->value;

use App\ENUM\ERREUR\ErreurEnum;
use App\Models\PROMOMETHODE;
use App\Models\JSONMETHODE;
use App\ENUM\MESSAGE\MSGENUM;
use App\ENUM\VALIDATOR\VALIDATORMETHODE;
use App\Models\REFMETHODE;

require_once CheminPage::PROMO_MODEL->value;

if (isset($_GET['page'])) {
    match ($_GET['page']) {
       'liste_promo' => afficher_promotions(),

        'liste_table_promo' => afficher_promotions_en_table(),
        'activer_promo' => traiter_activation_promotion(),
        'activer_promo_liste' => traiter_activation_promotion_liste(),
        'add_promo'=>($_SERVER['REQUEST_METHOD'] === 'POST') 
        ? traiter_creation_promotion() :
        afficher_page_add_promo(),

        default => null,
    };
}

function afficher_page_add_promo():void{
    demarrer_session();
    global $ref_model;
    $referentiels = $ref_model[REFMETHODE::GET_ALL->value]();

    render('promo/add_promoop', [
        'referentiels' => $referentiels
    ]);
   
}


function calculer_statistiques(array $promotions, array $referentiels): array {
    $totalPromotions = count($promotions);
    $promoActive = null;

    foreach ($promotions as $promo) {
        if (($promo['statut'] ?? '') === 'Active') {
            $promoActive = $promo;
            break;
        }
    }

    $totalReferentiels = 0;
    $totalApprenants = 0;

    if ($promoActive) {
        $idsRefs = $promoActive['referenciels'] ?? [];
        $totalReferentiels = count(array_filter($referentiels, fn($ref) => in_array($ref['id'], $idsRefs)));
        $totalApprenants = $promoActive['nbrApprenant'] ?? 0;
    }

    return [
        'totalPromotions' => $totalPromotions,
        'promotionActive' => $promoActive ? 1 : 0,
        'totalReferentiels' => $totalReferentiels,
        'totalApprenants' => $totalApprenants,
    ];
}



function afficher_promotions($message = null, $errors = []): void {
    global $promos, $ref_model;

    $nomRecherche = $_GET['search'] ?? null;
    $filtreStatut = $_GET['filtre'] ?? null;
    $liste_promos = $promos[PROMOMETHODE::GET_ALL->value]($nomRecherche, $filtreStatut);
    $referentiels = $ref_model[REFMETHODE::GET_ALL->value]();

    // Calcul des statistiques
    $stats = calculer_statistiques($liste_promos, $referentiels);


    // Séparer la promo active
    $promo_active = array_filter($liste_promos, fn($p) => $p['statut'] === 'Active');
    $promos_inactives = array_filter($liste_promos, fn($p) => $p['statut'] !== 'Active');

    // Pagination uniquement sur les inactives
    $parPage = 4;
    $total = count($promos_inactives);
    $pageCourante = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
    $pageCourante = max(1, min($pageCourante, ceil($total / $parPage)));

    $debut = ($pageCourante - 1) * $parPage;
    $promos_pagination = array_slice($promos_inactives, $debut, $parPage);

    // Ajouter la promo active tout en haut
    $liste_affichage = array_merge($promo_active, $promos_pagination);
    render("promo/promo", [
        "promotions" => $liste_affichage,
        "page" => $pageCourante,
        "total" => ceil($total / $parPage),
        "referentiels" => $referentiels,
        "debut" => $debut,
        "parPage" => $parPage,
        "message" => $message,
        "errors" => $errors,
        "stats" => $stats
    ]);
}
/**
 * Validation du nom de la promotion
 * @param string|null $nom Le nom à valider
 * @return string|null Code d'erreur si invalide, null si valide
 */
function valider_nom_promotion(?string $nom): ?string {
    return empty($nom) ? ErreurEnum::PROMO_NAME_REQUIRED->value : null;
}

/**
 * Validation des dates de début et fin
 * @param array $post Données du formulaire
 * @return array Tableau des erreurs de date
 */
function valider_dates(array $post): array {
    $erreurs = [];
    
    // Vérification de la date de début
    if (empty($post['date_debut'])) {
        $erreurs['date_debut'] = ErreurEnum::PROMO_DATE_REQUIRED->value;
    }
    
    // Vérification de la date de fin
    if (empty($post['date_fin'])) {
        $erreurs['date_fin'] = ErreurEnum::PROMO_DATE_REQUIRED->value;
    }
    
    // Vérification de la cohérence des dates
    if (!empty($post['date_debut']) && !empty($post['date_fin'])) {
        $debut = strtotime($post['date_debut']);
        $fin = strtotime($post['date_fin']);
        if ($fin < $debut) {
            $erreurs['date_fin'] = ErreurEnum::PROMO_DATE_INVALID->value;
        }
    }
    
    return $erreurs;
}

/**
 * Validation de la photo uploadée
 * @param array|null $photo Données du fichier uploadé
 * @return string|null Code d'erreur si invalide, null si valide
 */
function valider_photo(?array $photo): ?string {
    if (!isset($photo) || $photo['error'] === UPLOAD_ERR_NO_FILE) {
        return ErreurEnum::REF_PHOTO_REQUIRED->value;
    }
    return $photo['error'] !== UPLOAD_ERR_OK ? ErreurEnum::REF_PHOTO_INVALID->value : null;
}

/**
 * Validation des référentiels sélectionnés
 * @param array|null $referentiels Liste des référentiels
 * @return string|null Code d'erreur si invalide, null si valide
 */
function valider_referentiels(?array $referentiels): ?string {
    return (!isset($referentiels) || empty($referentiels)) ? 
           ErreurEnum::REF_SESSIONS_REQUIRED->value : null;
}

/**
 * Collection de toutes les erreurs de validation
 * @param array $post Données du formulaire
 * @param array $files Fichiers uploadés
 * @return array Tableau des erreurs
 */
function collecter_erreurs_validation(array $post, array $files): array {
    $erreurs = [];
    
    $erreurNom = valider_nom_promotion($post['nom_promo'] ?? null);
    if ($erreurNom) $erreurs['nom_promo'] = $erreurNom;
    
    // Validation des dates
    $erreursDates = valider_dates($post);
    if (!empty($erreursDates)) {
        $erreurs = array_merge($erreurs, $erreursDates);
    }
    
    $erreurPhoto = valider_photo($files['photo'] ?? null);
    if ($erreurPhoto) $erreurs['photo'] = $erreurPhoto;
    
    $erreurReferentiels = valider_referentiels($post['referenciels'] ?? null);
    if ($erreurReferentiels) $erreurs['referenciels'] = $erreurReferentiels;
    
    return $erreurs;
}

/**
 * Gestion des erreurs et redirection
 * @param array $erreurs Tableau des erreurs
 * @param array $post Données du formulaire
 */
function gerer_erreurs(array $erreurs, array $post): void {
    stocker_session('errors', $erreurs);
    stocker_session('old_inputs', $post);
    redirect_to_route('index.php', ['page' => 'add_promo']);
}

/**
 * Fonction principale de traitement de la création d'une promotion
 */
function traiter_creation_promotion(): void {
    global $model_tab, $validator, $promos;
    
    
    // 1. Validation
    $erreurs = collecter_erreurs_validation($_POST, $_FILES);
    if (!empty($erreurs)) {
        gerer_erreurs($erreurs, $_POST);
        return;
    }
    
    try {
        // 2. Traitement de la photo
        $cheminPhoto = gerer_upload_photo($_FILES['photo']);
        
        // 3. Chargement des données existantes et création
        $donneesExistantes = charger_promotions_existantes(CheminPage::DATA_JSON->value);
        $nouvellePromotion = creer_donnees_promotion($_POST, $donneesExistantes, $cheminPhoto);
        
        // 4. Sauvegarde et redirection
        $promos[PROMOMETHODE::AJOUTER_PROMO->value]($nouvellePromotion, CheminPage::DATA_JSON->value);
        stocker_session('success', 'La promotion a été créée avec succès');
        redirect_to_route('index.php', ['page' => 'liste_promo']);
        
    } catch (Exception $e) {
        stocker_session('errors', ['general' => ErreurEnum::PROMO_ADD_FAILED->value]);
        stocker_session('old_inputs', $_POST);
        redirect_to_route('index.php', ['page' => 'add_promo']);
    }
}

function charger_promotions_existantes(string $chemin): array {
    global $model_tab;
    return $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
}

function valider_donnees_promotion(array $donnees): array {
    global $validator;
    return $validator[VALIDATORMETHODE::VALID_GENERAL->value]($donnees);
}

function creer_donnees_promotion(array $post, array $donneesExistantes, string $cheminPhoto): array {
    $promotions = $donneesExistantes['promotions'] ?? [];
    $nouvelId = getNextPromoId($promotions);

    $referenciels = isset($post['referenciels']) ? array_map('intval', $post['referenciels']) : [];

    // S'assurer que les dates sont au bon format
    $dateDebut = date('Y-m-d', strtotime($post['date_debut']));
    $dateFin = date('Y-m-d', strtotime($post['date_fin']));

    return [
        "id" => $nouvelId,
        "nom" => $post['nom_promo'],
        "dateDebut" => $dateDebut,
        "dateFin" => $dateFin,
        "referenciels" => $referenciels,
        "photo" => $cheminPhoto,
        "statut" => "Inactive",
        "nbrApprenant" => 0
    ];
}


function afficher_promotions_en_table(): void {
    global $promos, $ref_model;

    // Récupération des paramètres de l'URL
    $nomRecherche = $_GET['search'] ?? null;
    $filtreStatut = $_GET['filtre'] ?? null;
    $perPage = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    
    // Récupération de toutes les promotions
    $liste_promos = $promos[PROMOMETHODE::GET_ALL->value]($nomRecherche, $filtreStatut);
    $referentiels = $ref_model[REFMETHODE::GET_ALL->value]();


    $stats = [
        'totalApprenants' => 0,
        'totalReferentiels' => count($referentiels),
        'promotionsActives' => 0,
        'totalPromotions' => count($liste_promos)
    ];
    // Filtrage des promotions
    if ($filtreStatut && $filtreStatut !== '') {
        $statutFiltre = $filtreStatut === 'active' ? 'Active' : 'Inactive';
        $liste_promos = array_filter($liste_promos, function($promo) use ($statutFiltre) {
            return $promo['statut'] === $statutFiltre;
        });
    }
  


    if ($nomRecherche) {
        $search = strtolower($nomRecherche);
        $liste_promos = array_filter($liste_promos, function($promo) use ($search) {
            return strpos(strtolower($promo['nom']), $search) !== false;
        });
    }

    // Trier les promotions (Actives en premier)
    usort($liste_promos, function($a, $b) {
        // Si les statuts sont différents
        if ($a['statut'] !== $b['statut']) {
            // Mettre les actives en premier
            return $a['statut'] === 'Active' ? -1 : 1;
        }
        // Si même statut, trier par nom
        return strcmp($a['nom'], $b['nom']);
    });

    // Calcul de la pagination
    $total = count($liste_promos);
    $pages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $pages)); // Assure que la page est dans les limites
    $start = ($currentPage - 1) * $perPage;
    
    // Découpage des résultats pour la page courante
    $paginatedPromos = array_slice(array_values($liste_promos), $start, $perPage);

    // Rendu de la vue avec toutes les données nécessaires
    render("promo/liste_promo", [
        "promotions" => $liste_promos,
        "paginatedPromos" => $paginatedPromos,
        "page" => $currentPage,
        "pages" => $pages,
        "perPage" => $perPage,
        "total" => $total,
        "start" => $start,
        "filtreStatut" => $filtreStatut,
        "nomRecherche" => $nomRecherche,
        "stats" => $stats,
        "referentiels" => $referentiels
    ]);
}

function calculer_statistiques_table(array $promotions, array $referentiels): array {
    $totalApprenants = 0;
    $totalPromotions = count($promotions);
    $promotionsActives = 0;
    $totalReferentiels = count($referentiels);

    foreach ($promotions as $promo) {
        $totalApprenants += $promo['nbrApprenant'] ?? 0;
        if (($promo['statut'] ?? '') === 'Active') {
            $promotionsActives++;
        }
    }

    return [
        'totalApprenants' => $totalApprenants,
        'totalPromotions' => $totalPromotions,
        'promotionsActives' => $promotionsActives,
        'totalReferentiels' => $totalReferentiels
    ];
}


function traiter_activation_promotion(): void {
    global $promos;

    if (isset($_GET['activer_promo'])) {
        $idPromo = (int) $_GET['activer_promo'];
        $cheminFichier = CheminPage::DATA_JSON->value;

        $promos[PROMOMETHODE::ACTIVER_PROMO->value]($idPromo, $cheminFichier);
    }
    redirect_to_route('index.php', ['page' => 'liste_promo']);

}
function traiter_activation_promotion_liste(): void {
    global $promos;

    if (isset($_GET['activer_promo_liste'])) {
        $idPromo = (int) $_GET['activer_promo_liste'];
        $cheminFichier = CheminPage::DATA_JSON->value;

        $promos[PROMOMETHODE::ACTIVER_PROMO->value]($idPromo, $cheminFichier);
    }
    redirect_to_route('index.php', ['page' => 'liste_table_promo']);

}




