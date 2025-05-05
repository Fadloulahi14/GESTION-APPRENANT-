<?php
require_once __DIR__ . '/../enums/chemin_page.php';
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../enums/model.enum.php';

use App\Enums\CheminPage;
use App\Models\JSONMETHODE;

/**
 * Affiche la page de connexion pour les apprenants
 */
function voir_page_login_apprenant(): void {
    demarrer_session();
    render('login/login_apprenant', [], layout: null);
}

/**
 * Traite la tentative de connexion d'un apprenant
 */
function authentifier_apprenant(): void {
    global $model_tab;
    demarrer_session();
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect_to_route('index.php', ['page' => 'login_apprenant']);
        exit;
    }
    
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validation basique
    if (empty($login) || empty($password)) {
        stocker_session('error', 'Veuillez remplir tous les champs');
        redirect_to_route('index.php', ['page' => 'login_apprenant']);
        exit;
    }
    
    // Charger les données des utilisateurs
    $chemin = CheminPage::DATA_JSON->value;
    $contenu = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
    $utilisateurs = $contenu['utilisateurs'] ?? [];
    
    // Rechercher l'apprenant par login
    $apprenant = null;
    foreach ($utilisateurs as $utilisateur) {
        if (($utilisateur['login'] ?? '') === $login && ($utilisateur['profil'] ?? '') === 'Apprenant') {
            $apprenant = $utilisateur;
            break;
        }
    }
    
    // Vérifier si l'apprenant existe
    if (!$apprenant) {
        stocker_session('error', 'Identifiants incorrects');
        redirect_to_route('index.php', ['page' => 'login_apprenant']);
        exit;
    }
    
    // Vérifier le mot de passe
    if (!password_verify($password, $apprenant['password'])) {
        stocker_session('error', 'Identifiants incorrects');
        redirect_to_route('index.php', ['page' => 'login_apprenant']);
        exit;
    }
    
    // Vérifier si c'est la première connexion (changer = false ou non défini)
    $premiereConnexion = !isset($apprenant['changer']) || $apprenant['changer'] === 'false';
    
    if ($premiereConnexion) {
        // Rediriger vers le formulaire de changement de mot de passe
        redirect_to_route('index.php', [
            'page' => 'login_apprenant',
            'first_login' => 'true',
            'login' => $login
        ]);
        exit;
    }
    
    // Connexion réussie, stocker les informations de l'apprenant en session
    stocker_session('apprenant_connecte', true);
    stocker_session('apprenant_id', $apprenant['id']);
    stocker_session('apprenant_nom', $apprenant['nom_complet']);
    
    // Rediriger vers la page de détails de l'apprenant
    redirect_to_route('index.php', ['page' => 'apprenant_dashboard']);
}

/**
 * Traite le changement de mot de passe lors de la première connexion
 */
function changer_mot_de_passe_apprenant(): void {
    global $model_tab;
    demarrer_session();
    
    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect_to_route('index.php', ['page' => 'login_apprenant']);
        exit;
    }
    
    $login = $_POST['login'] ?? '';
    $nouveauMotDePasse = $_POST['new_password'] ?? '';
    $confirmMotDePasse = $_POST['confirm_password'] ?? '';
    
    // Validation basique
    if (empty($login) || empty($nouveauMotDePasse) || empty($confirmMotDePasse)) {
        stocker_session('error', 'Veuillez remplir tous les champs');
        redirect_to_route('index.php', [
            'page' => 'login_apprenant',
            'first_login' => 'true',
            'login' => $login
        ]);
        exit;
    }
    
    // Vérifier que les mots de passe correspondent
    if ($nouveauMotDePasse !== $confirmMotDePasse) {
        stocker_session('error', 'Les mots de passe ne correspondent pas');
        redirect_to_route('index.php', [
            'page' => 'login_apprenant',
            'first_login' => 'true',
            'login' => $login
        ]);
        exit;
    }
    
    // Vérifier la complexité du mot de passe
    if (!valider_complexite_mot_de_passe($nouveauMotDePasse)) {
        stocker_session('error', 'Le mot de passe ne respecte pas les critères de sécurité');
        redirect_to_route('index.php', [
            'page' => 'login_apprenant',
            'first_login' => 'true',
            'login' => $login
        ]);
        exit;
    }
    
    // Charger les données des utilisateurs
    $chemin = CheminPage::DATA_JSON->value;
    $contenu = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
    $utilisateurs = $contenu['utilisateurs'] ?? [];
    
    // Rechercher l'apprenant et mettre à jour son mot de passe
    $apprenantTrouve = false;
    foreach ($utilisateurs as $index => $utilisateur) {
        if (($utilisateur['login'] ?? '') === $login && ($utilisateur['profil'] ?? '') === 'Apprenant') {
            // Mettre à jour le mot de passe et marquer comme changé
            $utilisateurs[$index]['password'] = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
            $utilisateurs[$index]['changer'] = 'true';
            $apprenantTrouve = true;
            $apprenant = $utilisateurs[$index];
            break;
        }
    }
    
    // Vérifier si l'apprenant a été trouvé
    if (!$apprenantTrouve) {
        stocker_session('error', 'Apprenant introuvable');
        redirect_to_route('index.php', ['page' => 'login_apprenant']);
        exit;
    }
    
    // Enregistrer les modifications
    $contenu['utilisateurs'] = $utilisateurs;
    $model_tab[JSONMETHODE::ARRAYTOJSON->value]($contenu, $chemin);
    
    // Connexion réussie, stocker les informations de l'apprenant en session
    stocker_session('apprenant_connecte', true);
    stocker_session('apprenant_id', $apprenant['id']);
    stocker_session('apprenant_nom', $apprenant['nom_complet']);
    
    // Message de succès et redirection
    stocker_session('success', 'Mot de passe changé avec succès');
    redirect_to_route('index.php', ['page' => 'apprenant_dashboard']);
}

/**
 * Déconnecte l'apprenant
 */
function deconnecter_apprenant(): void {
    demarrer_session();
    
    // Supprimer les variables de session liées à l'apprenant
    supprimer_session('apprenant_connecte');
    supprimer_session('apprenant_id');
    supprimer_session('apprenant_nom');
    
    // Rediriger vers la page de connexion
    redirect_to_route('index.php', ['page' => 'login_apprenant']);
}

/**
 * Valide la complexité du mot de passe
 */
function valider_complexite_mot_de_passe(string $motDePasse): bool {
    // Au moins 8 caractères
    if (strlen($motDePasse) < 8) {
        return false;
    }
    
    // Au moins une lettre majuscule
    if (!preg_match('/[A-Z]/', $motDePasse)) {
        return false;
    }
    
    // Au moins un chiffre
    if (!preg_match('/[0-9]/', $motDePasse)) {
        return false;
    }
    
    // Au moins un caractère spécial
    if (!preg_match('/[^A-Za-z0-9]/', $motDePasse)) {
        return false;
    }
    
    return true;
}