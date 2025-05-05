<?php
require_once __DIR__ . '/../../vendor/autoload.php'; 
require_once __DIR__ . '/../services/export.php';
require_once __DIR__ . '/../enums/chemin_page.php';
require_once __DIR__ . '/../enums/model.enum.php';
require_once __DIR__ . '/../services/pagination.service.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/mail.service.php';

use App\Enums\CheminPage;
use App\Models\APPMETHODE;
use App\Models\JSONMETHODE;
use App\Models\REFMETHODE;
use App\ENUM\VALIDATOR\VALIDATORMETHODE;
use PhpOffice\PhpSpreadsheet\IOFactory;
require_once CheminPage::MODEL->value;
require_once CheminPage::SESSION_SERVICE->value;
require_once CheminPage::APPRENANT_MODEL->value;
require_once CheminPage::REF_MODEL->value; 
require_once CheminPage::VALIDATOR_SERVICE->value;


global $apprenants, $ref_model;



function filtrer_apprenants(array $apprenants, ?string $nomRecherche, ?int $referencielId, ?string $statut = null): array {
    return array_filter($apprenants, function ($apprenant) use ($nomRecherche, $referencielId, $statut) {
        $matchReferenciel = !$referencielId || ($apprenant['referenciel'] ?? null) == $referencielId;
        $matchNom = !$nomRecherche || stripos($apprenant['nom_complet'] ?? '', $nomRecherche) !== false;
        $matchStatut = !$statut || ($apprenant['statut'] ?? '') === $statut;

        return $matchReferenciel && $matchNom && $matchStatut;
    });
}



function lister_apprenant(): void {
    global $apprenants;

    $nomRecherche = $_GET['search'] ?? null;
    $referencielId = isset($_GET['referenciel']) ? (int)$_GET['referenciel'] : null;
    $statut = $_GET['statut'] ?? null;
    $pageCourante = isset($_GET['pageCourante']) ? (int)$_GET['pageCourante'] : 1;
    $parPage = 5;

    $apprenantsFiltres = filtrer_apprenants(
        $apprenants[APPMETHODE::GET_ALL->value]($nomRecherche, null),
        $nomRecherche,
        $referencielId,
        $statut
    );

    $pagination = paginer($apprenantsFiltres, $pageCourante, $parPage);

    $referenciels = charger_referenciels();

    // Utiliser apprenant.view.php pour afficher la liste
    render('apprenant/apprenant', [
        'apprenants' => $pagination['items'],
        'referenciels' => $referenciels,
        'pagination' => $pagination
    ], layout: 'base.layout');
}





function importer_apprenants(): void {
    global $apprenants;

    if (fichier_excel_non_valide()) {
        enregistrer_message_erreur('Impossible d\'importer le fichier.');
        rediriger_vers_liste_apprenants();
        return;
    }

    $cheminFichier = $_FILES['import_excel']['tmp_name'];
    $lignes = charger_lignes_excel($cheminFichier);

    // Pas de validation demandée ici !
    $apprenantsImportes = [];

    foreach (array_slice($lignes, 1) as $ligne) {
        $apprenantsImportes[] = array_merge(extraire_donnees_apprenant($ligne), [
            'id' => time() + rand(1, 999)
        ]);
    }

    if (!empty($apprenantsImportes)) {
        $cheminJson = CheminPage::DATA_JSON->value;
        $apprenants[APPMETHODE::IMPORTER->value]($apprenantsImportes, $cheminJson);
        enregistrer_message_succes('Importation réussie.');
    } else {
        enregistrer_message_erreur('Le fichier est vide ou invalide.');
    }

    rediriger_vers_liste_apprenants();
}





/**
 * Charger les lignes d'un fichier Excel
 */
function charger_lignes_excel(string $cheminFichier): array {
    try {
        $spreadsheet = IOFactory::load($cheminFichier);
        $sheet = $spreadsheet->getActiveSheet();
        return $sheet->toArray();
    } catch (Exception $e) {
        enregistrer_message_erreur('Erreur lors de la lecture du fichier Excel : ' . $e->getMessage());
        rediriger_vers_liste_apprenants();
        exit;
    }
}



/**
 * Vérifie si un fichier Excel est soumis
 */
function fichier_excel_non_valide(): bool {
    return !isset($_FILES['import_excel']) || $_FILES['import_excel']['error'] !== UPLOAD_ERR_OK;
}

/**
 * Extraire les données d'un apprenant depuis une ligne Excel
 */
function extraire_donnees_apprenant(array $ligne): array {
    return [
        'nom_complet' => $ligne[0] ?? '',
        'date_naissance' => $ligne[1] ?? '',
        'lieu_naissance' => $ligne[2] ?? '',
        'adresse' => $ligne[3] ?? '',
        'email' => $ligne[4] ?? '',
        'telephone' => $ligne[5] ?? '',
        'document' => $ligne[6] ?? '',
        'tuteur_nom' => $ligne[7] ?? '',
        'lien_parente' => $ligne[8] ?? '',
        'tuteur_adresse' => $ligne[9] ?? '',
        'tuteur_telephone' => $ligne[10] ?? '',
        'referenciel' => (int)($ligne[11] ?? 0),
        'password' => password_hash($ligne[12] ?? 'password123', PASSWORD_DEFAULT),
        'statut' => 'Retenu',
        'profil' => 'Apprenant'
    ];
}


function enregistrer_message_erreur(string $message): void {
    stocker_session('errors', [$message]);
}


function enregistrer_message_succes(string $message): void {
    stocker_session('success', $message);
}

/**
 * Redirige vers la page liste_apprenant
 */
function rediriger_vers_liste_apprenants(): void {
    redirect_to_route('index.php', ['page' => 'liste_apprenant']);
}



global $apprenants;

/**
 * Afficher la page ajout apprenant
 */
function ajout_apprenant_vue(): void {
    global $model_tab;

    $matricule = generer_matricule();
    $referenciels = charger_referenciels();

    render('apprenant/ajouter_apprenant', [
        'matricule' => $matricule,
        'referenciels' => $referenciels
    ], layout: 'base.layout');
}

/**
 * Traiter ajout apprenant (POST)
 */
// function traiter_ajout_apprenant(): void {
//     global $apprenants, $validator;
//     var_dump('ok');
//     die();
//     try {
//         demarrer_session();

//         // Vérifiez si le formulaire a été soumis
//         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//             enregistrer_message_erreur('Méthode non autorisée.');
//             redirect_to_route('index.php', ['page' => 'ajouter_apprenant']);
//             exit;
//         }

//         // Collectez les données du formulaire
//         $data = [
//             'matricule' => trim($_POST['matricule'] ?? ''),
//             'nom_complet' => trim($_POST['nom_complet'] ?? ''),
//             'date_naissance' => trim($_POST['date_naissance'] ?? ''),
//             'lieu_naissance' => trim($_POST['lieu_naissance'] ?? ''),
//             'adresse' => trim($_POST['adresse'] ?? ''),
//             'email' => trim($_POST['email'] ?? ''), // Utilisation correcte de "email"
//             'telephone' => trim($_POST['telephone'] ?? ''),
//             'referenciel' => (int)($_POST['referenciel'] ?? 0),
//             'tuteur_nom' => trim($_POST['tuteur_nom'] ?? ''),
//             'lien_parente' => trim($_POST['lien_parente'] ?? ''),
//             'tuteur_adresse' => trim($_POST['tuteur_adresse'] ?? ''),
//             'tuteur_telephone' => trim($_POST['tuteur_telephone'] ?? ''),
//             'statut' => 'Retenu',
//             'password' => password_hash('password123', PASSWORD_DEFAULT)
//         ];

//         // Validez les données
//         $errors = $validator[VALIDATORMETHODE::APPRENANT->value]($data); // Utilisation correcte de la constante

//         if (!empty($errors)) {
//             enregistrer_message_erreur('Veuillez corriger les erreurs dans le formulaire.');
//             stocker_session('errors', $errors);
//             stocker_session('old_inputs', $data);
//             redirect_to_route('index.php', ['page' => 'ajouter_apprenant']);
//             exit;
//         }

//         // Ajoutez l'apprenant
//         $cheminJson = CheminPage::DATA_JSON->value;
//         $resultat = $apprenants[APPMETHODE::AJOUTER->value]($data, $cheminJson);

//         if ($resultat) {
//             enregistrer_message_succes('Apprenant ajouté avec succès.');
//         } else {
//             enregistrer_message_erreur('Échec de l\'ajout de l\'apprenant.');
//         }
//     } catch (Exception $e) {
//         error_log('Erreur lors de l\'ajout d\'un apprenant: ' . $e->getMessage());
//         enregistrer_message_erreur('Une erreur est survenue: ' . $e->getMessage());
//     }

//     // Redirection vers la liste des apprenants
//     // redirect_to_route('index.php', ['page' => 'apprenant']);
//     header('Location: index.php?page=apprena');
// }


// function traiter_ajout_apprenant(): void {
//     global $apprenants, $validator;

//     demarrer_session();
    

//     $data = [
//         'matricule' => trim($_POST['matricule'] ?? ''),
//         'nom_complet' => trim($_POST['nom_complet'] ?? ''),
//         'date_naissance' => trim($_POST['date_naissance'] ?? ''),
//         'lieu_naissance' => trim($_POST['lieu_naissance'] ?? ''),
//         'adresse' => trim($_POST['adresse'] ?? ''),
//         'login' => trim($_POST['login'] ?? ''),
//         'telephone' => trim($_POST['telephone'] ?? ''),
//         'referenciel' => $_POST['referenciel'] ?? '',
//         'photo' => $_FILES['document'] ?? null,
//         'tuteur_nom' => trim($_POST['tuteur_nom'] ?? ''),
//         'lien_parente' => trim($_POST['lien_parente'] ?? ''),
//         'tuteur_adresse' => trim($_POST['tuteur_adresse'] ?? ''),
//         'tuteur_telephone' => trim($_POST['tuteur_telephone'] ?? ''),
//         'document' => $_FILES['document'] ?? null
        
//     ];

//     $errors = $validator[VALIDATORMETHODE::APPRENANT->value]($data);

//     if (!empty($errors)) {
//         stocker_session('errors', $errors);
//         stocker_session('old_inputs', $data);
//         redirect_to_route('index.php', ['page' => 'ajouter_apprenant']);
//         exit;
//     }

//     $cheminJson = CheminPage::DATA_JSON->value;
//     $nouvelApprenant = creer_donnees_apprenant($data);

//     $apprenants[APPMETHODE::AJOUTER->value]($nouvelApprenant, $cheminJson);

//     enregistrer_message_succes('Apprenant ajouté avec succès.');
//     redirect_to_route('index.php', ['page' => 'liste_apprenant']);
// }

function traiter_ajout_apprenant(): void {
    global $apprenants, $validator;

    demarrer_session();

    // Générer un mot de passe par défaut
    $default_password = generer_mot_de_passe(10);

    $data = [
        'matricule' => trim($_POST['matricule'] ?? ''),
        'nom_complet' => trim($_POST['nom_complet'] ?? ''),
        'date_naissance' => trim($_POST['date_naissance'] ?? ''),
        'lieu_naissance' => trim($_POST['lieu_naissance'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? ''),
        'login' => trim($_POST['login'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'referenciel' => $_POST['referenciel'] ?? '',
        'photo' => $_FILES['document'] ?? null,
        'tuteur_nom' => trim($_POST['tuteur_nom'] ?? ''),
        'lien_parente' => trim($_POST['lien_parente'] ?? ''),
        'tuteur_adresse' => trim($_POST['tuteur_adresse'] ?? ''),
        'tuteur_telephone' => trim($_POST['tuteur_telephone'] ?? ''),
        'document' => $_FILES['document'] ?? null,
        'password' => password_hash($default_password, PASSWORD_DEFAULT)
    ];

    $errors = $validator[VALIDATORMETHODE::APPRENANT->value]($data);

    if (!empty($errors)) {
        stocker_session('errors', $errors);
        stocker_session('old_inputs', $data);
        redirect_to_route('index.php', ['page' => 'ajouter_apprenant']);
        exit;
    }

    $cheminJson = CheminPage::DATA_JSON->value;
    $nouvelApprenant = creer_donnees_apprenant($data);

    if ($apprenants[APPMETHODE::AJOUTER->value]($nouvelApprenant, $cheminJson)) {
        // Envoi de l'email
        if (envoyer_email_bienvenue($nouvelApprenant, $default_password)) {
            enregistrer_message_succes('Apprenant ajouté avec succès et email envoyé.');
        } else {
            enregistrer_message_succes('Apprenant ajouté avec succès mais l\'email n\'a pas pu être envoyé.');
        }
    } else {
        enregistrer_message_erreur('Échec de l\'ajout de l\'apprenant.');
    }

    redirect_to_route('index.php', ['page' => 'liste_apprenant']);
}


/**
 * Génère un mot de passe aléatoire
 */
function generer_mot_de_passe(int $longueur = 10): string {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $longueurMax = strlen($caracteres);
    $motDePasse = '';
    
    for ($i = 0; $i < $longueur; $i++) {
        $motDePasse .= $caracteres[random_int(0, $longueurMax - 1)];
    }
    
    return $motDePasse;
}

/**
 * Générer un matricule automatique
 */
function generer_matricule(): string {
    return "APP" . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}


function charger_referenciels(): array {
    global $model_tab;

    $chemin = CheminPage::DATA_JSON->value;
    $contenu = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
    return $contenu['referenciel'] ?? [];
}



/**
 * Préparer les données d'un nouvel apprenant
 */
// function creer_donnees_apprenant(array $post): array {
//     return [
//         'matricule' => $post['matricule'],
//         'nom_complet' => $post['nom_complet'],
//         'date_naissance' => $post['date_naissance'],
//         'lieu_naissance' => $post['lieu_naissance'],
//         'adresse' => $post['adresse'],
//         'email' => $post['email'],
//         'telephone' => $post['telephone'],
//         'referenciel' => (int) $post['referenciel'],
//         'photo' => $post['photo']['name'] ?? '', // ou chemin si tu veux upload
//         'statut' => 'Retenu',
//         'profil' => 'Apprenant',
//         'password' => password_hash('password123', PASSWORD_DEFAULT),
//         'id' => time() + rand(1, 999)
//     ];
// }

// function creer_donnees_apprenant(array $post): array {
//     return [
//         'matricule' => $post['matricule'],
//         'nom_complet' => $post['nom_complet'],
//         'date_naissance' => $post['date_naissance'],
//         'lieu_naissance' => $post['lieu_naissance'],
//         'adresse' => $post['adresse'],
//         'login' => $post['login'],
//         'telephone' => $post['telephone'],
//         'referenciel' => (int) $post['referenciel'],
//         'photo' => $post['photo']['name'] ?? '', // ou chemin si tu veux upload
//         'statut' => 'Retenu',
//         'profil' => 'Apprenant',
//         'password' => password_hash('password123', PASSWORD_DEFAULT),
//         'id' => time() + rand(1, 999),
//         'changer'=> 'false',
//         'tuteur_nom' => $post['tuteur_nom'] ?? '',
//         'lien_parente' => $post['lien_parente'] ?? '',
//         'tuteur_adresse' => $post['tuteur_adresse'] ?? '',
//         'tuteur_telephone' => $post['tuteur_telephone'] ?? '',
//         'document' => $post['document']['name'] ?? '', // ou chemin si tu veux upload


                  
          
        
//     ];
//    // envoyerEmailApprenant($post['login'], $post['login'],'password123');


// }

function creer_donnees_apprenant(array $post): array {
    $matricule = generer_matricule();
    
    return [
        'matricule' => $matricule,
        'nom_complet' => $post['nom_complet'],
        'date_naissance' => $post['date_naissance'],
        'lieu_naissance' => $post['lieu_naissance'],
        'adresse' => $post['adresse'],
        'login' => $post['login'],
        'telephone' => $post['telephone'],
        'referenciel' => (int) $post['referenciel'],
        'photo' => $post['photo']['name'] ?? '',
        'statut' => 'Retenu',
        'profil' => 'Apprenant',
        'password' => $post['password'],
        'id' => time() + rand(1, 999),
        'changer'=> 'false',
        'tuteur_nom' => $post['tuteur_nom'],
        'lien_parente' => $post['lien_parente'],
        'tuteur_adresse' => $post['tuteur_adresse'],
        'tuteur_telephone' => $post['tuteur_telephone'],
        'document' => $post['document']['name'] ?? ''
    ];
}




function afficher_detail_apprenant(): void {
    global $apprenants, $model_tab;

    $id = $_GET['id'] ?? null;

    if (!$id) {
        enregistrer_message_erreur('ID apprenant manquant.');
        redirect_to_route('index.php', ['page' => 'liste_apprenant']);
        exit;
    }

    $apprenant = null;
    foreach ($apprenants[APPMETHODE::GET_ALL->value](null, null) as $a) {
        if (($a['id'] ?? '') == $id) {
            $apprenant = $a;
            break;
        }
    }

    if (!$apprenant) {
        enregistrer_message_erreur('Apprenant introuvable.');
        redirect_to_route('index.php', ['page' => 'liste_apprenant']);
        exit;
    }

    $referenciels = charger_referenciels();

    render('apprenant/details', [
        'apprenant' => $apprenant,
        'referenciels' => $referenciels
    ], layout: 'base.layout');
}


function exporter_apprenants(): void {
    global $apprenants;

    $apprenantsData = $apprenants[APPMETHODE::GET_ALL->value](null, null);
    $referenciels = charger_referenciels();

    $format = $_GET['format'] ?? null;

    if ($format === 'csv') {
        exportToCSV($apprenantsData, $referenciels);
    } elseif ($format === 'pdf') {
        exportToPDF($apprenantsData, $referenciels);
    } elseif ($format === 'excel') {
        exportToExcel($apprenantsData, $referenciels);
    } else {
        enregistrer_message_erreur('Format d\'exportation non valide.');
        redirect_to_route('index.php', ['page' => 'liste_apprenant']);
    }
}
