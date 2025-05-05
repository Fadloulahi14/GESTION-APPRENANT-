<?php
// declare(strict_types=1);
// require_once __DIR__ . '/../enums/chemin_page.php';
// require_once __DIR__ . '/../enums/validator.enum.php';
// require_once __DIR__ . '/../enums/erreur.enum.php';
// require_once __DIR__ . '/../enums/model.enum.php';
// require_once __DIR__ . '/../models/model.php';

// use App\ENUM\VALIDATOR\VALIDATORMETHODE;
// use App\Models\PROMOMETHODE;
// use App\ENUM\CheminPage;
// use App\Models\JSONMETHODE;

// use App\ENUM\ERREUR\ErreurEnum;
// global $validator;



declare(strict_types=1);
require_once __DIR__ . '/../enums/validator.enum.php';
require_once __DIR__ . '/../enums/erreur.enum.php';
require_once __DIR__ . '/../enums/model.enum.php';
require_once __DIR__ . '/../enums/chemin_page.php';
require_once __DIR__ . '/../models/model.php';

use App\ENUM\VALIDATOR\VALIDATORMETHODE;
use App\ENUM\ERREUR\ErreurEnum;
use App\Enums\CheminPage;
use App\Models\JSONMETHODE;
use App\Models\APPMETHODE;
use App\Models\AUTHMETHODE;
use App\Models\VALIDATORMETHODE as VALIDATORMETHODE_MODEL;
global $validator;

$validator[VALIDATORMETHODE::VALID_GENERAL->value] = function(array $data) use (&$validator): array {
    global $promos; 
    $errors = [];

    if (empty(trim($data['nom_promo'] ?? ''))) {
        $errors['nom_promo'] = ErreurEnum::PROMO_NAME_REQUIRED->value;
    }

    if (empty(trim($data['date_debut'] ?? ''))) {
        $errors['date_debut'] = ErreurEnum::PROMO_DATE_REQUIRED->value;
    }

    if (empty(trim($data['date_fin'] ?? ''))) {
        $errors['date_fin'] = ErreurEnum::PROMO_DATE_REQUIRED->value;
    }

    if (empty($data['referenciels'])) {
        $errors['referenciels'] = ErreurEnum::PROMO_REF_REQUIRED->value;
    }

    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $errors['photo'] = ErreurEnum::PROMO_PHOTO_REQUIRED->value;
    }

    // Validation du nom unique
    if (empty($errors['nom_promo'])) {
        $liste_promos = $promos[PROMOMETHODE::GET_ALL->value]();
        if ($errorUnique = $validator[VALIDATORMETHODE::PROMO_NOM_UNIQUE->value]($data['nom_promo'], $liste_promos)) {
            $errors['nom_promo'] = $errorUnique;
        }
    }

    return $errors;



    
};


// Validation des apprenants
// $validator[VALIDATORMETHODE::APPRENANT->value] = function (array $data): array {
//     $errors = [];

//     // Nom complet obligatoire
//     if (empty(trim($data['nom_complet'] ?? ''))) {
//         $errors['nom_complet'] = ErreurEnum::APPRENANT_NOM_REQUIRED->value;
//     }

//     // Email obligatoire et valide
//     if (empty(trim($data['email'] ?? ''))) {
//         $errors['email'] = ErreurEnum::APPRENANT_EMAIL_REQUIRED->value;
//     } else {
//         $email = trim($data['email']);
//         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//             $errors['email'] = ErreurEnum::APPRENANT_EMAIL_INVALID->value;
//         }
//     }

//     // Téléphone obligatoire, valide et unique
//     if (empty(trim($data['telephone'] ?? ''))) {
//         $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_REQUIRED->value;
//     } elseif (!preg_match('/^\d{9}$/', $data['telephone'])) {
//         $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_INVALID->value;
//     } else {
//         $chemin = \App\Enums\CheminPage::DATA_JSON->value;
//         if (file_exists($chemin)) {
//             $contenu = json_decode(file_get_contents($chemin), true);
//             $utilisateurs = $contenu['utilisateurs'] ?? [];

//             $telephoneSaisi = trim($data['telephone']);
//             $doublon = array_filter($utilisateurs, fn($u) => ($u['telephone'] ?? '') === $telephoneSaisi);

//             if (!empty($doublon)) {
//                 $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_EXISTS->value;
//             }
//         }
//     }

//     // Matricule obligatoire et unique
//     if (empty(trim($data['matricule'] ?? ''))) {
//         $errors['matricule'] = ErreurEnum::APPRENANT_MATRICULE_REQUIRED->value;
//     } else {
//         $chemin = \App\Enums\CheminPage::DATA_JSON->value;
//         if (file_exists($chemin)) {
//             $contenu = json_decode(file_get_contents($chemin), true);
//             $utilisateurs = $contenu['utilisateurs'] ?? [];

//             $matriculeSaisi = strtolower(trim($data['matricule']));
//             $doublon = array_filter($utilisateurs, fn($u) => strtolower($u['matricule'] ?? '') === $matriculeSaisi);

//             if (!empty($doublon)) {
//                 $errors['matricule'] = ErreurEnum::APPRENANT_MATRICULE_EXISTS->value;
//             }
//         }
//     }

//     // Ajoutez d'autres validations si nécessaire...

//     return $errors;
// };


$validator[VALIDATORMETHODE::APPRENANT->value] =function (array $data): array {
    $errors = [];

    // Nom complet obligatoire
    if (empty(trim($data['nom_complet'] ?? ''))) {
        $errors['nom_complet'] = ErreurEnum::APPRENANT_NOM_REQUIRED->value;
    }

    // // Login obligatoire et valide
    // if (empty(trim($data['login'] ?? ''))) {
    //     $errors['login'] = ErreurEnum::APPRENANT_EMAIL_REQUIRED->value;
    // } else {
    //     $login = trim($data['login']);
    //     if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
    //         $errors['login'] = ErreurEnum::APPRENANT_EMAIL_INVALID->value;
    //     }
    // }




    $login = trim($data['login'] ?? '');

    if ($login === '') {
        $errors['login'] = ErreurEnum::APPRENANT_EMAIL_REQUIRED->value;
    } elseif (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $errors['login'] = ErreurEnum::APPRENANT_EMAIL_INVALID->value;
    } else {
        // Vérification de l'unicité
        $chemin = CheminPage::DATA_JSON->value;
        global $model_tab;
        $utilisateurs = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin, 'utilisateurs');
    
        $loginExiste = array_filter($utilisateurs, fn($u) => isset($u['login']) && strtolower($u['login']) === strtolower($login));
    
        if (!empty($loginExiste)) {
            $errors['login'] = ErreurEnum::APPRENANT_EMAIL_UNIQUE->value;
        }
    }
    







    // Téléphone obligatoire, valide et unique
    if (empty(trim($data['telephone'] ?? ''))) {
        $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_REQUIRED->value;
    } elseif (!preg_match('/^\d{9}$/', $data['telephone'])) {
        $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_INVALID->value;
    } else {
        $chemin = \App\Enums\CheminPage::DATA_JSON->value;
        if (file_exists($chemin)) {
            $contenu = json_decode(file_get_contents($chemin), true);
            $utilisateurs = $contenu['utilisateurs'] ?? [];

            $telephoneSaisi = trim($data['telephone']);
            $doublon = array_filter($utilisateurs, fn($u) => ($u['telephone'] ?? '') === $telephoneSaisi);

            if (!empty($doublon)) {
                $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_EXISTS->value;
            }
        }
    }

    // Matricule obligatoire et unique
    // if (empty(trim($data['matricule'] ?? ''))) {
    //     $errors['matricule'] = ErreurEnum::APPRENANT_MATRICULE_REQUIRED->value;
    // } else {
    //     $chemin = \App\Enums\CheminPage::DATA_JSON->value;
    //     if (file_exists($chemin)) {
    //         $contenu = json_decode(file_get_contents($chemin), true);
    //         $utilisateurs = $contenu['utilisateurs'] ?? [];

    //         $matriculeSaisi = strtolower(trim($data['matricule']));
    //         $doublon = array_filter($utilisateurs, fn($u) => strtolower($u['matricule'] ?? '') === $matriculeSaisi);

    //         if (!empty($doublon)) {
    //             $errors['matricule'] = ErreurEnum::APPRENANT_MATRICULE_EXISTS->value;
    //         }
    //     }
    // }


    
    // Date de naissance obligatoire et format YYYY-MM-DD
    if (empty(trim($data['date_naissance'] ?? ''))) {
        $errors['date_naissance'] = ErreurEnum::APPRENANT_DATE_NAISSANCE_REQUIRED->value;
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_naissance'])) {
        $errors['date_naissance'] = ErreurEnum::APPRENANT_DATE_NAISSANCE_INVALID->value;
    }

    // Lieu de naissance obligatoire
    if (empty(trim($data['lieu_naissance'] ?? ''))) {
        $errors['lieu_naissance'] = ErreurEnum::APPRENANT_LIEU_NAISSANCE_REQUIRED->value;
    }

    // Adresse obligatoire
    if (empty(trim($data['adresse'] ?? ''))) {
        $errors['adresse'] = ErreurEnum::APPRENANT_ADRESSE_REQUIRED->value;
    }

    // Référentiel obligatoire
    if (empty($data['referenciel'])) {
        $errors['referenciel'] = ErreurEnum::APPRENANT_REFERENTIEL_REQUIRED->value;
    }

    // Tuteur - Nom obligatoire
    if (empty(trim($data['tuteur_nom'] ?? ''))) {
        $errors['tuteur_nom'] = ErreurEnum::APPRENANT_TUTEUR_NOM_REQUIRED->value;
    }

    // Tuteur - Lien de parenté obligatoire
    if (empty(trim($data['lien_parente'] ?? ''))) {
        $errors['lien_parente'] = ErreurEnum::APPRENANT_LIEN_PARENT_REQUIRED->value;
    }

    // Tuteur - Adresse obligatoire
    if (empty(trim($data['tuteur_adresse'] ?? ''))) {
        $errors['tuteur_adresse'] = ErreurEnum::APPRENANT_TUTEUR_ADRESSE_REQUIRED->value;
    }

    // Tuteur - Téléphone obligatoire et format 9 chiffres
    if (empty(trim($data['tuteur_telephone'] ?? ''))) {
        $errors['tuteur_telephone'] = ErreurEnum::APPRENANT_TUTEUR_TELEPHONE_REQUIRED->value;
    } elseif (!preg_match('/^\d{9}$/', $data['tuteur_telephone'])) {
        $errors['tuteur_telephone'] = ErreurEnum::APPRENANT_TUTEUR_TELEPHONE_INVALID->value;
    }

    return $errors;
};
