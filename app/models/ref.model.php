<?php
require_once __DIR__ . '/../enums/model.enum.php';
require_once __DIR__ . '/../enums/chemin_page.php';

use App\Models\REFMETHODE;
use App\Models\JSONMETHODE;
use App\Enums\CheminPage;

global $ref_model;



/**
 * Tableau de modèles de référentiels avec différentes méthodes de manipulation
 * 
 * @var array $ref_model Contient des fonctions pour gérer les référentiels
 */
$ref_model = [
    REFMETHODE::GET_ALL->value => function(): array {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        return $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin)['referenciel'] ?? [];
    },
    
    REFMETHODE::AJOUTER->value => function(array $referenciel): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
        
        if (!isset($data['referenciel'])) {
            $data['referenciel'] = [];
        }
        
        $data['referenciel'][] = $referenciel;
        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    },
    
    REFMETHODE::AFFECTER_MULTIPLE->value => function(array $ref_ids, int $promo_id): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
        
        if (!isset($data['promotions'])) {
            return false;
        }
    
        $updated = false;
        foreach ($data['promotions'] as &$promo) {
            if ($promo['id'] === $promo_id) {
                // Assurer que tous les IDs sont des entiers et uniques
                $ref_ids = array_values(array_unique(array_map('intval', $ref_ids)));
                
                // Mettre à jour ou vider la liste des référentiels
                $promo['referenciels'] = $ref_ids;
                
                $updated = true;
                break;
            }
        }
        
        if (!$updated) {
            return false;
        }
        
        // Sauvegarder les modifications
        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    }

];

