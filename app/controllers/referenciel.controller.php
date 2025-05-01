<?php
require_once __DIR__ . '/../enums/chemin_page.php';
require_once __DIR__ . '/../models/ref.model.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/validator.service.php';

use App\ENUM\VALIDATOR\VALIDATORMETHODE;



use App\Enums\CheminPage;
use App\Models\REFMETHODE;

demarrer_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'ajouter') {
        ajouter_referenciel(); 
        exit;
    }
}

if (isset($_GET['page'])) {
    match ($_GET['page']) {
        'referenciel' => afficher_referentiels(),
        'all_referenciel' => afficher_tous_referentiels(),
        'add_referentiel' => afficher_page_add_ref(),
        'affecter_referentiel' => traiter_affectation_referentiel(),
        default => null,
    };
}


function est_promo_en_cours(array $promo): bool {
    $anneeActuelle = date('Y');
    $dateDebut = date('Y', strtotime($promo['dateDebut']));
    $dateFin = date('Y', strtotime($promo['dateFin']));
    
    return $dateDebut <= $anneeActuelle && $dateFin >= $anneeActuelle;
}

function peut_modifier_referentiels(array $promo, ?array $ref = null): array {
    if (!est_promo_en_cours($promo)) {
        return [
            'peut' => false,
            'message' => "Seule la promotion de l'année en cours peut être modifiée."
        ];
    }

    if ($ref !== null && ($ref['apprenants'] ?? 0) > 0) {
        return [
            'peut' => false,
            'message' => "Impossible de désaffecter un référentiel contenant des apprenants."
        ];
    }

    return ['peut' => true, 'message' => ''];
}
function traiter_affectation_referentiel(): void {
    global $ref_model;
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect_to_route('index.php', ['page' => 'referenciel']);
        return;
    }

    $data = get_data_json(CheminPage::DATA_JSON->value);
    $promo_active = get_promo_active($data['promotions'] ?? []);

    if (!$promo_active) {
        stocker_session('error', 'Aucune promotion active trouvée.');
        redirect_to_route('index.php', ['page' => 'referenciel']);
        return;
    }

    // Vérifier si la promotion est en cours
    $validation = peut_modifier_referentiels($promo_active);
    if (!$validation['peut']) {
        stocker_session('error', $validation['message']);
        redirect_to_route('index.php', ['page' => 'referenciel', 'action' => 'show_popup']);
        return;
    }

    $referenciels_actuels = $promo_active['referenciels'] ?? [];
    $tous_referentiels = $ref_model[REFMETHODE::GET_ALL->value]();

    // Gérer l'ajout d'un référentiel
    if (isset($_POST['ajouter_ref'])) {
        $ref_id = (int)$_POST['ajouter_ref'];
        if (!in_array($ref_id, $referenciels_actuels)) {
            $referenciels_actuels[] = $ref_id;
        }
    }

    // Gérer le retrait d'un référentiel
    if (isset($_POST['retirer_ref'])) {
        $ref_id = (int)$_POST['retirer_ref'];
        
        // Vérifier si le référentiel peut être désaffecté
        $ref_a_retirer = trouver_referenciel_par_id($tous_referentiels, $ref_id);
        $validation_retrait = peut_modifier_referentiels($promo_active, $ref_a_retirer);
        
        if (!$validation_retrait['peut']) {
            stocker_session('error', $validation_retrait['message']);
            redirect_to_route('index.php', ['page' => 'referenciel', 'action' => 'show_popup']);
            return;
        }
        
        $referenciels_actuels = array_diff($referenciels_actuels, [$ref_id]);
    }

    // Mettre à jour la liste
    $resultat = $ref_model[REFMETHODE::AFFECTER_MULTIPLE->value](
        array_values($referenciels_actuels), 
        $promo_active['id']
    );

    if ($resultat) {
        stocker_session('success', 'Les référentiels ont été mis à jour avec succès.');
    } else {
        stocker_session('error', 'Une erreur est survenue lors de la mise à jour des référentiels.');
    }

    redirect_to_route('index.php', ['page' => 'referenciel', 'action' => 'show_popup']);
}

function trouver_referenciel_par_id(array $referentiels, int $ref_id): ?array {
    foreach ($referentiels as $ref) {
        if ($ref['id'] === $ref_id) {
            return $ref;
        }
    }
    return null;
}

function afficher_page_add_ref(){
    render('referenciel/add_referentiel');
}

function afficher_referentiels(): void {
    global $ref_model;

    $tous_referentiels = $ref_model[REFMETHODE::GET_ALL->value]();
    $data = get_data_json(CheminPage::DATA_JSON->value);
    $promo_active = get_promo_active($data['promotions'] ?? []);

    // Filtrer les référentiels actifs
    $referentiels_actifs = $promo_active && !empty($promo_active['referenciels']) 
        ? get_referentiels_actifs($tous_referentiels, $promo_active['referenciels'])
        : [];

    // Pagination
    $parPage = 6;
    $total = count($referentiels_actifs);
    $pageCourante = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
    $pageCourante = max(1, min($pageCourante, ceil($total / $parPage)));
    $debut = ($pageCourante - 1) * $parPage;

    // Messages de session
    $success = recuperer_session('success');
    $error = recuperer_session('error');

    // Rendu
    render('referenciel/referenciel', [
        'referentiels' => array_slice($referentiels_actifs, $debut, $parPage),
        'tous_referentiels' => $tous_referentiels,
        'promo_active' => $promo_active,
        'page' => $pageCourante,
        'total' => ceil($total / $parPage),
        'debut' => $debut,
        'parPage' => $parPage,
        'success' => $success,
        'error' => $error
    ]);

    // Nettoyer les messages
    supprimer_session('success');
    supprimer_session('error');
}





function get_data_json(string $chemin): array {
    global $model_tab;
    return $model_tab[\App\Models\JSONMETHODE::JSONTOARRAY->value]($chemin);
}

function get_promo_active(array $promotions): ?array {
    foreach ($promotions as $promo) {
        if (($promo['statut'] ?? '') === 'Active') {
            return $promo;
        }
    }
    return null;
}



function get_referentiels_actifs(array $referentiels, array $ids_referenciels): array {
    return array_filter($referentiels, function($ref) use ($ids_referenciels) {
        return in_array($ref['id'], $ids_referenciels);
    });
}



function filtrer_referentiels_par_nom(array $referentiels, string $searchTerm): array {
    return array_filter($referentiels, function($ref) use ($searchTerm) {
        return stripos($ref['nom'], $searchTerm) !== false;
    });
}


function afficher_tous_referentiels(): void {
    global $ref_model;
    
    // Récupérer tous les référentiels
    $referentiels = $ref_model[REFMETHODE::GET_ALL->value]();
    
    // Appliquer la recherche si nécessaire
    $searchTerm = $_GET['search'] ?? '';
    if (!empty($searchTerm)) {
        $referentiels = array_filter($referentiels, function($ref) use ($searchTerm) {
            return stripos($ref['nom'], $searchTerm) !== false;
        });
    }

    $totalElements = count($referentiels); // Nombre total de référentiels
    
    // Pagination
    $parPage = 4; // Nombre de référentiels par page
    $total = ceil($totalElements / $parPage);
    $page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
    $page = max(1, min($page, max(1, $total)));
    $debut = ($page - 1) * $parPage;
    
    // Extraire les référentiels pour la page courante
    $referentiels_page = array_slice($referentiels, $debut, $parPage);
    
    // Rendu avec toutes les variables nécessaires
    render('referenciel/all_referenciel', [
        'referentiels' => $referentiels_page,
        'page' => $page,
        'total' => $total,
        'debut' => $debut,
        'parPage' => $parPage,
        'totalElements' => $totalElements,
        'searchTerm' => $searchTerm
    ]);
}



function ajouter_referenciel(): void {
    global $ref_model;
    $errors = [];
    $old_inputs = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ajouter') {
        $old_inputs = [
            'nom' => trim($_POST['nom'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'capacite' => trim($_POST['capacite'] ?? ''),
            'sessions' => trim($_POST['sessions'] ?? ''),
        ];

        // Validation du nom
        if (empty($old_inputs['nom'])) {
            $errors['nom'] = 'Le nom est requis';
        } else {
            $referentielsExistants = $ref_model[REFMETHODE::GET_ALL->value]();
            foreach ($referentielsExistants as $ref) {
                if (strtolower($ref['nom']) === strtolower($old_inputs['nom'])) {
                    $errors['nom'] = 'Ce nom de référentiel existe déjà';
                    break;
                }
            }
        }

        // Validation de la capacité
        if (empty($old_inputs['capacite'])) {
            $errors['capacite'] = 'La capacité est requise';
        } elseif (!is_numeric($old_inputs['capacite']) || $old_inputs['capacite'] <= 0) {
            $errors['capacite'] = 'La capacité doit être un nombre positif';
        }

        // Validation des sessions
        if (empty($old_inputs['sessions'])) {
            $errors['sessions'] = 'Le nombre de sessions est requis';
        }

        // Validation de la photo si présente
        if (!empty($_FILES['photo']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                $errors['photo'] = 'Format de fichier non autorisé. Utilisez JPG, JPEG ou PNG';
            } elseif ($_FILES['photo']['size'] > 5000000) {
                $errors['photo'] = 'La taille du fichier ne doit pas dépasser 5Mo';
            }
        }

        // S'il n'y a pas d'erreurs, procéder à l'ajout
        if (empty($errors)) {
            $cheminPhoto = !empty($_FILES['photo']['name']) ? 
                          gerer_upload_photo($_FILES['photo']) : 
                          'assets/images/referentiel/default.png';

            $nouveau_ref = [
                'id' => time(),
                'nom' => $old_inputs['nom'],
                'description' => $old_inputs['description'],
                'capacite' => (int) $old_inputs['capacite'],
                'sessions' => (int) $old_inputs['sessions'],
                'photo' => $cheminPhoto,
                'modules' => 0,
                'apprenants' => 0
            ];

            if ($ref_model[REFMETHODE::AJOUTER->value]($nouveau_ref)) {
                stocker_session('success', 'Référentiel ajouté avec succès.');
                redirect_to_route('index.php', ['page' => 'all_referenciel']);
                return;
            } else {
                $errors['general'] = "Une erreur est survenue lors de l'ajout du référentiel";
            }
        }

        // S'il y a des erreurs, les stocker et rester sur le formulaire
        stocker_session('errors', $errors);
        stocker_session('old_inputs', $old_inputs);
        render('referenciel/add_referentiel', [
            'errors' => $errors,
            'old' => $old_inputs
        ]);
        return;
    }

    // Si ce n'est pas un POST, afficher le formulaire vide
    render('referenciel/add_referentiel');
}