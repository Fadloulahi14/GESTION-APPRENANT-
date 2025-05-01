<?php
declare(strict_types=1);
require_once __DIR__ . '/../enums/validator.enum.php';
require_once __DIR__ . '/../enums/erreur.enum.php';
require_once __DIR__ . '/../enums/model.enum.php';


use App\ENUM\VALIDATOR\VALIDATORMETHODE;
use App\Models\PROMOMETHODE;

use App\ENUM\ERREUR\ErreurEnum;
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
$validator[VALIDATORMETHODE::APPRENANT->value] = function (array $data): array {
    $errors = [];

    // Nom complet obligatoire
    if (empty(trim($data['nom_complet'] ?? ''))) {
        $errors['nom_complet'] = ErreurEnum::APPRENANT_NOM_REQUIRED->value;
    }

    // Email obligatoire et valide
    if (empty(trim($data['email'] ?? ''))) {
        $errors['email'] = ErreurEnum::APPRENANT_EMAIL_REQUIRED->value;
    } else {
        $email = trim($data['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ErreurEnum::APPRENANT_EMAIL_INVALID->value;
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
    if (empty(trim($data['matricule'] ?? ''))) {
        $errors['matricule'] = ErreurEnum::APPRENANT_MATRICULE_REQUIRED->value;
    } else {
        $chemin = \App\Enums\CheminPage::DATA_JSON->value;
        if (file_exists($chemin)) {
            $contenu = json_decode(file_get_contents($chemin), true);
            $utilisateurs = $contenu['utilisateurs'] ?? [];

            $matriculeSaisi = strtolower(trim($data['matricule']));
            $doublon = array_filter($utilisateurs, fn($u) => strtolower($u['matricule'] ?? '') === $matriculeSaisi);

            if (!empty($doublon)) {
                $errors['matricule'] = ErreurEnum::APPRENANT_MATRICULE_EXISTS->value;
            }
        }
    }

    // Ajoutez d'autres validations si nécessaire...

    return $errors;
};