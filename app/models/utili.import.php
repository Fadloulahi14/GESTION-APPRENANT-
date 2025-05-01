<?php
/**
 * Obtenir les ID des référentiels actifs
 */

 
function obtenir_referentiels_actifs(array $promotions): array {
    $today = date('Y-m-d');
    $currentYear = date('Y');
    $referentiels = [];

    foreach ($promotions as $promo) {
        if (
            isset($promo['statut'], $promo['dateDebut'], $promo['dateFin']) &&
            strtolower($promo['statut']) === 'active' &&
            date('Y', strtotime($promo['dateDebut'])) === $currentYear &&
            strtotime($promo['dateFin']) > strtotime($promo['dateDebut'])
        ) {
            if (isset($promo['referenciels']) && is_array($promo['referenciels'])) {
                foreach ($promo['referenciels'] as $refId) {
                    if (!in_array($refId, $referentiels)) {
                        $referentiels[] = $refId;
                    }
                }
            }
        }
    }

    return $referentiels;
}

/**
 * Filtrer uniquement les apprenants valides
 */
function filtrer_apprenants_valides(array $nouveauxApprenants, array $referentielsActifs): array {
    $apprenants = [];

    foreach ($nouveauxApprenants as $apprenant) {
        if (isset($apprenant['referenciel']) && in_array($apprenant['referenciel'], $referentielsActifs)) {
            $apprenant['profil'] = 'Apprenant';
            $apprenants[] = $apprenant;
        }
    }

    return $apprenants;
}

/**
 * Ajouter les apprenants valides dans les utilisateurs existants
 */
function ajouter_apprenants_au_data(array &$utilisateurs, array $apprenantsValides): void {
    foreach ($apprenantsValides as $app) {
        $utilisateurs[] = $app;
    }
}
?>