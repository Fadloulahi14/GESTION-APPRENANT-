<!DOCTYPE html>
<html lang="fr">
<head>

<?php
    require_once __DIR__ . '/../../enums/chemin_page.php';
    use App\Enums\CheminPage;
    $url = "http://" . $_SERVER["HTTP_HOST"];
    demarrer_session();
    
    // Récupération des erreurs et anciennes valeurs
    $erreurs = recuperer_session('errors', []);
    $old = recuperer_session('old_inputs', []);
    require_once CheminPage::ERROR_FR->value;
    ?>
    

    <meta charset="UTF-8">
    <title>Créer une promotion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f2f2f2;
        }

        .container {
            margin-top: 5%;
            width: 90%;

           
            background-color: white;
            
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        form {
            width: 100%;
        }

        label {
            display: block;
            margin-bottom: 15px;
        }

        input[type="text"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        .date-group {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .date-group select {
            flex: 1;
        }

        .drop-zone {
            border: 2px dashed #aaa;
            padding: 20px;
            text-align: center;
            margin-top: 5px;
            border-radius: 6px;
            background-color: #f9f9f9;
            position: relative;
        }

        .drop-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .drop-text {
            color: #ff7900;
            font-weight: bold;
        }

        .ref-list {
            display: flex;
            justify-content: center;
            align-items: center;
            gap : 40px;
            margin-top: 10px;
            padding: 10px;
            background: #f6f6f6;
            border-radius: 6px;
            border: 1px solid #ddd;
        overflow: auto;
            max-height: 200px;
        }

        .ref-item {
            margin-bottom: 5px;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
            gap: 10px;
        }

        .buttons button,
        .buttons a {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1rem;
            cursor: pointer;
        }

        .cancel-btn {
            background-color: #ccc;
            color: #333;
        }

        .submit-btn {
            background-color: #ff7900;
            color: white;
            font-weight: bold;
        }

        @media screen and (max-width: 600px) {
            .date-group {
                flex-direction: column;
            }
        }
        .error-message {
    color: red;
    font-size: 0.9rem;
    margin-top: 5px;
    margin-bottom: 10px;
}
.date-fields {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.date-fields label {
    flex: 1;
}

input[type="date"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
}

@media screen and (max-width: 600px) {
    .date-fields {
        flex-direction: column;
        gap: 15px;
    }
}
    </style>
</head>
<body>
<div class="container">
    <h2>Créer une nouvelle promotion</h2>

    <form action="index.php?page=add_promo" method="POST" enctype="multipart/form-data">
        <!-- Nom promotion -->
        <label>
            Nom de la promotion
            <input 
                type="text" 
                name="nom_promo" 
                value="<?= htmlspecialchars($old['nom_promo'] ?? '') ?>"
                class="<?= isset($erreurs['nom_promo']) ? 'error' : '' ?>">
            <?php if (isset($erreurs['nom_promo'])): ?>
                <span class="error-message"><?= $error[$erreurs['nom_promo']] ?></span>
            <?php endif; ?>
        </label>

        <!-- Dates -->
        <div class="date-fields">
            <label>
                Date de début
                <input 
                    type="text"
                    name="date_debut" 
                    value="<?= htmlspecialchars($old['date_debut'] ?? '') ?>"
                    class="<?= isset($erreurs['date_debut']) ? 'error' : '' ?>">
                <?php if (isset($erreurs['date_debut'])): ?>
                    <span class="error-message"><?= $error[$erreurs['date_debut']] ?></span>
                <?php endif; ?>
            </label>

            <label>
                Date de fin
                <input 
                    type="text"
                    name="date_fin" 
                    value="<?= htmlspecialchars($old['date_fin'] ?? '') ?>"
                    class="<?= isset($erreurs['date_fin']) ? 'error' : '' ?>">
                <?php if (isset($erreurs['date_fin'])): ?>
                    <span class="error-message"><?= $error[$erreurs['date_fin']] ?></span>
                <?php endif; ?>
            </label>
        </div>

        <!-- Photo -->
        <label>
            Photo de la promotion
            <div class="drop-zone <?= isset($erreurs['photo']) ? 'error' : '' ?>">
                <span class="drop-text">Cliquez ou glissez une image ici</span>
                <input type="file" name="photo" accept="image/png, image/jpeg">
            </div>
            <?php if (isset($erreurs['photo'])): ?>
                <span class="error-message"><?= $error[$erreurs['photo']] ?></span>
            <?php endif; ?>
        </label>

        <!-- Référentiels -->
        <label>
            Référentiels associés
            <div class="ref-list <?= isset($erreurs['referenciels']) ? 'error' : '' ?>">
                <?php foreach ($referentiels as $ref): ?>
                    <div class="ref-item">
                        <input 
                            type="checkbox" 
                            id="ref-<?= $ref['id'] ?>" 
                            name="referenciels[]" 
                            value="<?= $ref['id'] ?>"
                            <?= (isset($old['referenciels']) && in_array($ref['id'], $old['referenciels'])) ? 'checked' : '' ?>>
                        <label for="ref-<?= $ref['id'] ?>"><?= htmlspecialchars($ref['nom']) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (isset($erreurs['referenciels'])): ?>
                <span class="error-message"><?= $error[$erreurs['referenciels']] ?></span>
            <?php endif; ?>
        </label>

        <!-- Actions -->
        <div class="buttons">
            <a href="index.php?page=liste_promo" class="cancel-btn">Annuler</a>
            <button type="submit" class="submit-btn">Créer</button>
        </div>
    </form>

    <?php 
    supprimer_session('errors');
    supprimer_session('old_inputs');
    ?>
</div>

<style>
.error {
    border-color: #ff0000 !important;
}

.error-message {
    color: #ff0000;
    font-size: 0.8rem;
    margin-top: 5px;
    display: block;
}

.drop-zone.error {
    border-color: #ff0000 !important;
}

.ref-list.error {
    border-color: #ff0000 !important;
}
</style>

</body>
</html>
