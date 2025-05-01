<?php
namespace App\Models;

enum JSONMETHODE: string
{
    case ARRAYTOJSON = 'array_to_json';
    case JSONTOARRAY = 'json_to_array';
}

enum AUTHMETHODE: string
{
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case REGISTER = 'register';
    case FORGOT_PASSWORD = 'forgot_password';
    case RESET_PASSWORD = "reset_password";
}

enum PROMOMETHODE: string {
    case GET_ALL = 'get_all';
    case ACTIVER_PROMO = 'activer_promo';
    case AJOUTER_PROMO = 'ajouter_promo'; // Nouvelle constante ajoutée
}

enum REFMETHODE: string {
    case GET_ALL = 'get_all';
    case GET_ACTIVE = 'get_active';
    case AJOUTER = 'ajouter';
    case AFFECTER = 'affecter';
    case AFFECTER_MULTIPLE = 'affecter_multiple';
}

enum APPMETHODE: string {
    case GET_ALL = 'get_all';
    case GET_ACTIVE = 'get_active';
    case AJOUTER = 'ajouter';
    case IMPORTER = 'importer';

    
}