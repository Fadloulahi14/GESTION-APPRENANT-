<?php
require_once __DIR__ . '/../../../enums/erreur.enum.php';

use App\ENUM\ERREUR\ErreurEnum;

$error = [
    // Erreurs liées à l'authentification
    // ErreurEnum::LOGIN_REQUIRED->value => "L'email est requis.",
    // ErreurEnum::LOGIN_EMAIL->value => "L'email doit être une adresse email valide.",
    // ErreurEnum::PASSWORD_REQUIRED->value => 'Le mot de passe est requis.',
    // ErreurEnum::PASSWORD_INVALID->value => 'Le mot de passe doit contenir au moins 6 caractères.',
    // ErreurEnum::LOGIN_INCORRECT->value => "L'email ou mot de passe incorrect.",

    // Erreurs liées aux promotions
    "promo.name.required" => "Le nom de la promotion est obligatoire",
    "promo.date.required" => "La date est obligatoire",
    "promo.date.invalid" => "La date de fin doit être postérieure à la date de début",
    "promo.photo.required" => "Une photo est requise",
    "promo.ref.required" => "Sélectionnez au moins un référentiel",
    "promo.name.exists" => "Ce nom de promotion existe déjà",
    "promo.date.format" => "Le format de date est incorrect",
    "promo.date.ordre" => "La date de fin doit être postérieure à la date de début",
    "promo.add.failed" => "Échec de l'ajout de la promotion"
 

    // Erreurs liées aux référentiels
    // ErreurEnum::REF_NOM_REQUIRED->value => "Le nom du référentiel est requis.",
    // ErreurEnum::REF_NOM_EXISTS->value => "Ce nom de référentiel existe déjà.",
    // ErreurEnum::REF_DESCRIPTION_REQUIRED->value => "La description du référentiel est requise.",
    // ErreurEnum::REF_CAPACITE_REQUIRED->value => "La capacité est requise et doit être un nombre valide.",
    // ErreurEnum::REF_SESSIONS_REQUIRED->value => "Le nombre de sessions est requis.",
    // ErreurEnum::REF_PHOTO_REQUIRED->value => "La photo du référentiel est requise.",
    // ErreurEnum::REF_PHOTO_INVALID->value => "La photo doit être au format JPG ou PNG et ne pas dépasser 2MB.",

  
        // ... vos erreurs existantes ...
    // ErreurEnum::PROMO_NAME_REQUIRED->value => 'Le nom de la promotion est obligatoire',
    // ErreurEnum::PROMO_DATE_REQUIRED->value => 'La date est obligatoire',
    // ErreurEnum::REF_PHOTO_REQUIRED->value => 'La photo de la promotion est obligatoire',
    // ErreurEnum::REF_PHOTO_INVALID->value => 'Le format de la photo est invalide',
    // ErreurEnum::REF_SESSIONS_REQUIRED->value => 'Veuillez sélectionner au moins un référentiel',
    // ErreurEnum::PROMO_ADD_FAILED->value => 'Une erreur est survenue lors de la création de la promotion',
];
