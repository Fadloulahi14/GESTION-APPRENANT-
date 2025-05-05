<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';

use App\Enums\CheminPage;

$url = "http://" . $_SERVER["HTTP_HOST"];
$css_login = CheminPage::CSS_LOGIN->value;
$logo_image = CheminPage::IMG_LOGO_LOGIN->value;
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $url . $css_login ?>">
    <title>Connexion Apprenant</title>
    <style>
        :root {
            --orange: #ff7a00;
            --orange-light: #ff9f00;
            --teal: #009999;
            --text-dark: #000;
            --text-gray: #333;
            --background: #f5f5f5;
            --input-border: #ccc;
            --white: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: var(--background);
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .form-container {
            position: relative;
            width: 100%;
            height: auto;
            max-width: 30%;
            background-color: var(--white);
            border-radius: 20px;
            padding: 30px 40px;
            z-index: 1;
        }

        .alert-succes {
            padding: 10px;
            color: var(--teal);
            text-align: center;
            width: 100%;
            background-color: rgba(123, 255, 123, 0.2);
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .error-message {
            padding: 10px;
            color: #dc3545;
            text-align: center;
            width: 100%;
            background-color: rgba(255, 123, 123, 0.2);
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .with-shadow::before, .with-shadow::after {
            content: "";
            position: absolute;
            border-radius: 20px;
            z-index: -1;
        }

        .with-shadow::before {
            top: 8px;
            right: -8px;
            bottom: 0;
            width: 100%;
            border-right: 8px solid var(--orange);
        }

        .with-shadow::after {
            bottom: -8px;
            left: 0;
            height: 100%;
            width: 100%;
            border-bottom: 8px solid var(--teal);
        }

        .logo {
            height: 40px;
            width: 150px;
            margin: 0 auto;
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .welcome {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: var(--text-dark);
        }

        .academy {
            color: var(--orange);
            font-weight: bold;
        }

        .main-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 14px;
            color: var(--text-gray);
            margin-top: 5px;
        }

        .input {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid var(--input-border);
            font-size: 14px;
        }

        .input.alert {
            border: 1px solid #dc3545;
        }

        .page-link {
            text-align: right;
            font-size: 12px;
            text-decoration: none;
        }

        .page-link-label {
            color: var(--orange);
            cursor: pointer;
        }

        .page-link-label:hover {
            text-decoration: underline;
        }

        .form-btn {
            margin-top: 20px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(to right, var(--orange), var(--orange-light));
            color: var(--white);
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: 0.3s;
        }

        .form-btn:hover {
            opacity: 0.9;
        }

        .first-time-login {
            display: none;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 10px;
        }

        .password-requirements ul {
            padding-left: 20px;
            margin-top: 5px;
        }

        /* 📱 Responsive */
        @media (max-width: 768px) {
            .form-container {
                max-width: 90%;
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }

            .main-title {
                font-size: 20px;
            }

            .form-btn {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="form-container with-shadow">
        <div class="logo">
            <img class="logo-img" src="<?= $url . $logo_image ?>" alt="logo Sonatel">
        </div>
        <p class="welcome">Bienvenue sur<br><span class="academy">Ecole du code Sonatel Academy</span></p>
        
        <!-- Formulaire de connexion standard -->
        <div id="loginForm">
            <p class="main-title">Se connecter</p>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?= $_SESSION['error'] ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-succes">
                    <?= $_SESSION['success'] ?>
                </div>
            <?php endif; ?>
            
            <form class="form" method="POST" action="index.php?page=auth_apprenant">
                <label for="login">Email</label>
                <input 
                    type="text" 
                    id="login" 
                    name="login" 
                    class="input <?= !empty($_SESSION['error']) ? 'alert' : '' ?>" 
                    placeholder="Matricule ou email" 
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
                
                <label for="password">Mot de passe</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="input <?= !empty($_SESSION['error']) ? 'alert' : '' ?>" 
                    placeholder="Mot de passe">
                
                <a href="?page=resetPassword" class="page-link">
                    <span class="page-link-label">Mot de passe oublié ?</span>
                </a>
                
                <button class="form-btn">Se connecter</button>
            </form>
        </div>
        
        <!-- Formulaire pour le premier changement de mot de passe -->
        <div id="firstTimeLogin" class="first-time-login">
            <p class="main-title">Première connexion</p>
            <p style="text-align: center; margin-bottom: 20px;">Pour des raisons de sécurité, veuillez changer votre mot de passe.</p>
            
            <form class="form" action="index.php?page=change_password_apprenant" method="POST">
                <input type="hidden" name="login" id="hiddenLogin">
                
                <label for="new_password">Nouveau mot de passe</label>
                <input
                    type="password"
                    name="new_password"
                    id="new_password"
                    class="input"
                    placeholder="Entrez votre nouveau mot de passe">
                    
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input
                    type="password"
                    name="confirm_password"
                    id="confirm_password"
                    class="input"
                    placeholder="Confirmez votre nouveau mot de passe">
                
                <div class="password-requirements">
                    <p>Le mot de passe doit contenir :</p>
                    <ul>
                        <li>Au moins 8 caractères</li>
                        <li>Au moins une lettre majuscule</li>
                        <li>Au moins un chiffre</li>
                        <li>Au moins un caractère spécial</li>
                    </ul>
                </div>
                
                <button type="submit" class="form-btn">Changer le mot de passe</button>
            </form>
        </div>
    </div>
    
    <script>
        // Si l'URL contient le paramètre first_login=true, afficher le formulaire de changement de mot de passe
        if (window.location.search.includes('first_login=true')) {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('firstTimeLogin').style.display = 'block';
            
            // Récupérer le login depuis l'URL et le mettre dans le champ caché
            const urlParams = new URLSearchParams(window.location.search);
            const login = urlParams.get('login');
            if (login) {
                document.getElementById('hiddenLogin').value = login;
            }
        }
    </script>
    
    <?php unset($_SESSION['success']); ?>
    <?php unset($_SESSION['errors']); ?>
</body>
</html>