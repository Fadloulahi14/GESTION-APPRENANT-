<?php
global $model_tab;
require_once __DIR__ . '/../enums/model.enum.php';
require_once __DIR__ . '/../enums/chemin_page.php';

use App\Enums\CheminPage;
use App\Models\JSONMETHODE;
use App\Models\APPMETHODE;

$json = CheminPage::DATA_JSON->value;
$jsontoarray = $model_tab[JSONMETHODE::JSONTOARRAY->value];

global $apprenants;

$apprenants = [
    // Liste tous les apprenants (utilisateurs avec le profil "Apprenant")
    APPMETHODE::GET_ALL->value => function (?string $nomRecherche = null, ?int $referencielId = null) use ($jsontoarray, $json) {
        $utilisateurs = $jsontoarray($json, "utilisateurs");

        return array_filter($utilisateurs, function ($utilisateur) use ($nomRecherche, $referencielId) {
            $isApprenant =( $utilisateur['profil'] === 'Apprenant'&& ($utilisateur['statut'] === 'Retenu' || $utilisateur['statut'] === 'Remplacer'));
            $matchNom = !$nomRecherche || str_contains(strtolower($utilisateur['nom_complet']), strtolower($nomRecherche));
            $matchReferenciel = !$referencielId || (isset($utilisateur['referenciel']) && $utilisateur['referenciel'] === $referencielId);
            return $isApprenant && $matchNom && $matchReferenciel;
        });
    },

    // Ajoute un apprenant (utilisateur avec le profil "Apprenant")
    APPMETHODE::AJOUTER->value => function (array $nouvelApprenant, string $chemin): bool {
        try {
            global $model_tab;

            // Vérifiez si le fichier JSON existe
            if (!file_exists($chemin)) {
                // Créez une structure de base si le fichier n'existe pas
                $data = [
                    'utilisateurs' => [],
                    'referenciel' => [],
                    'promotions' => [],
                    'apprenants' => []
                ];
            } else {
                // Chargez les données existantes
                $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);

                if (!isset($data['utilisateurs'])) {
                    $data['utilisateurs'] = [];
                }
            }

            // Ajoutez le profil "Apprenant" au nouvel utilisateur
            $nouvelApprenant['profil'] = 'Apprenant';
            $nouvelApprenant['id'] = time() + rand(1, 999); // Génération d'un ID unique
            $data['utilisateurs'][] = $nouvelApprenant;

            // Sauvegardez les modifications dans le fichier JSON
            return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
        } catch (Exception $e) {
            error_log('Erreur lors de l\'ajout d\'un apprenant: ' . $e->getMessage());
            return false;
        }
    },

    // Importe plusieurs apprenants à partir d'un tableau
    APPMETHODE::IMPORTER->value => function (array $nouveauxApprenants, string $chemin): bool {
        global $model_tab;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);

        if (!isset($data['utilisateurs'])) {
            $data['utilisateurs'] = [];
        }

        // Ajouter le profil "Apprenant" à chaque nouvel utilisateur
        foreach ($nouveauxApprenants as &$apprenant) {
            $apprenant['profil'] = 'Apprenant';
        }

        $data['utilisateurs'] = array_merge($data['utilisateurs'], $nouveauxApprenants);

        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    }
];