<?php
namespace App\ENUM\ERREUR;

enum ErreurEnum: string
{
    // Authentification
    case LOGIN_REQUIRED = 'login_required';
    case LOGIN_EMAIL = 'login_email';
    case PASSWORD_REQUIRED = 'password_required';
    case PASSWORD_INVALID = 'password_invalid';
    case LOGIN_INCORRECT = 'login_incorrect';

    // Promotions
   
   
    
  
        case PROMO_NAME_REQUIRED = "promo.name.required";
        case PROMO_DATE_REQUIRED = "promo.date.required";
        case PROMO_DATE_INVALID = "promo.date.invalid";
        case PROMO_PHOTO_REQUIRED = "promo.photo.required";
        case PROMO_REF_REQUIRED = "promo.ref.required";
        case PROMO_NOM_EXISTS = "promo.name.exists";
        case PROMO_DATE_FORMAT = "promo.date.format";
        case PROMO_DATE_INFERIEUR = "promo.date.ordre";
        case PROMO_ADD_FAILED = "promo.add.failed";
    
    // Référentiels
    case REF_NOM_REQUIRED = 'ref_nom_required';
    case REF_NOM_EXISTS = 'ref_nom_exists';
    case REF_DESCRIPTION_REQUIRED = 'ref_description_required';
    case REF_CAPACITE_REQUIRED = 'ref_capacite_required';
    case REF_SESSIONS_REQUIRED = 'ref_sessions_required';
    case REF_PHOTO_REQUIRED = 'ref_photo_required';
    case REF_PHOTO_INVALID = 'ref_photo_invalid';

    case EXCEL_FORMAT_INVALID = 'Le format du fichier Excel ne correspond pas au modèle';
case EXCEL_LIGNE_ERREUR = 'Erreur à la ligne';
case EXCEL_IMPORT_SUCCES = 'Importation réussie';
case EXCEL_IMPORT_PARTIEL = 'Importation partiellement réussie';
case EXCEL_IMPORT_ECHEC = 'Échec de l\'importation';




// Apprenants



    

// case APPRENANT_NOM_REQUIRED = 'nom_apprenant_obligatoire';
// case APPRENANT_EMAIL_REQUIRED = 'email_apprenant_obligatoire';
// case APPRENANT_EMAIL_INVALID = 'email_apprenant_invalide';
// case APPRENANT_TELEPHONE_REQUIRED = 'telephone_apprenant_obligatoire';
// case APPRENANT_TELEPHONE_INVALID = 'telephone_apprenant_invalide';
// case APPRENANT_MATRICULE_REQUIRED = 'matricule_apprenant_obligatoire';
// case APPRENANT_MATRICULE_EXISTS = 'matricule_apprenant_existe_deja';
// case APPRENANT_REFERENTIEL_REQUIRED = 'referentiel_apprenant_obligatoire';
// case APPRENANT_PHOTO_REQUIRED = 'photo_apprenant_obligatoire';
// case APPRENANT_PHOTO_INVALID = 'photo_apprenant_invalide';
// case APPRENANT_DATE_NAISSANCE_REQUIRED = 'date_naissance_apprenant_obligatoire';
// case APPRENANT_DATE_NAISSANCE_INVALID = 'date_naissance_apprenant_invalide';
// case APPRENANT_LIEU_NAISSANCE_REQUIRED = 'lieu_naissance_apprenant_obligatoire';
// case APPRENANT_ADRESSE_REQUIRED = 'adresse_apprenant_obligatoire';
// case APPRENANT_ADD_FAILED = 'ajout_apprenant_echoue';
// case APPRENANT_UPDATE_FAILED = 'modification_apprenant_echoue';
// case APPRENANT_DELETE_FAILED = 'suppression_apprenant_echoue';

// case APPRENANT_TUTEUR_NOM_REQUIRED = 'Le nom du tuteur est obligatoire.';
// case APPRENANT_LIEN_PARENT_REQUIRED = 'Le lien de parenté est obligatoire.';
// case APPRENANT_TUTEUR_ADRESSE_REQUIRED = 'L\'adresse du tuteur est obligatoire.';
// case APPRENANT_TUTEUR_TELEPHONE_REQUIRED = 'Le téléphone du tuteur est obligatoire.';
// case APPRENANT_TUTEUR_TELEPHONE_INVALID = 'Le téléphone du tuteur doit être un numéro valide de 9 chiffres.';
// case APPRENANT_TELEPHONE_EXISTS = 'Le téléphone existe dejas.';



// Importation d'apprenants
case EXCEL_EMAIL_DUPLIQUE = 'email_unique';
case EXCEL_MATRICULE_DUPLIQUE = 'matricule_unique';
case EXCEL_COLONNE_VIDE = 'colonne_non_vide';
case FICHIER_NON_EXCEL = 'fichier_valid';


case APPRENANT_NOM_REQUIRED = 'nom_apprenant_obligatoire';
    case APPRENANT_EMAIL_REQUIRED = 'email_apprenant_obligatoire';
    case APPRENANT_EMAIL_INVALID = 'email_apprenant_invalide';
    case APPRENANT_TELEPHONE_REQUIRED = 'telephone_apprenant_obligatoire';
    case APPRENANT_TELEPHONE_INVALID = 'telephone_apprenant_invalide';
    case APPRENANT_MATRICULE_REQUIRED = 'matricule_apprenant_obligatoire';
    case APPRENANT_MATRICULE_EXISTS = 'matricule_apprenant_existe_deja';
    case APPRENANT_REFERENTIEL_REQUIRED = 'referentiel_apprenant_obligatoire';
    case APPRENANT_PHOTO_REQUIRED = 'photo_apprenant_obligatoire';
    case APPRENANT_PHOTO_INVALID = 'photo_apprenant_invalide';
    case APPRENANT_DATE_NAISSANCE_REQUIRED = 'date_naissance_apprenant_obligatoire';
    case APPRENANT_DATE_NAISSANCE_INVALID = 'date_naissance_apprenant_invalide';
    case APPRENANT_LIEU_NAISSANCE_REQUIRED = 'lieu_naissance_apprenant_obligatoire';
    case APPRENANT_ADRESSE_REQUIRED = 'adresse_apprenant_obligatoire';
    case APPRENANT_ADD_FAILED = 'ajout_apprenant_echoue';
    case APPRENANT_UPDATE_FAILED = 'modification_apprenant_echoue';
    case APPRENANT_DELETE_FAILED = 'suppression_apprenant_echoue';

    case APPRENANT_TUTEUR_NOM_REQUIRED = 'Le nom du tuteur est obligatoire.';
case APPRENANT_LIEN_PARENT_REQUIRED = 'Le lien de parenté est obligatoire.';
case APPRENANT_TUTEUR_ADRESSE_REQUIRED = 'L\'adresse du tuteur est obligatoire.';
case APPRENANT_TUTEUR_TELEPHONE_REQUIRED = 'Le téléphone du tuteur est obligatoire.';
case APPRENANT_TUTEUR_TELEPHONE_INVALID = 'Le téléphone du tuteur doit être un numéro valide de 9 chiffres.';
case APPRENANT_TELEPHONE_EXISTS = 'Le téléphone existe dejas.';


case APPRENANT_EMAIL_UNIQUE = 'Cette adresse email est déjà utilisée.';



    
}
