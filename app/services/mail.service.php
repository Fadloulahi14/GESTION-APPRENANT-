<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Envoie un email de bienvenue à un nouvel apprenant
 * 
 * @param array $apprenant Les données de l'apprenant
 * @param string $password Le mot de passe par défaut
 * @return bool Succès ou échec de l'envoi
 */
function envoyer_email_bienvenue(array $apprenant, string $password): bool {
    try {
        $mail = new PHPMailer(true);
        


        // $login->isSMTP();
        // $login->Host       = 'smtp.gmail.com'; // Remplacez par votre serveur SMTP
        // $login->SMTPAuth   = true;
        // $login->Username   = 'fallou.ndiaye22@isep-thies.edu.sn'; // Remplacez par votre email
        // $login->Password   = 'agrr effl ylta zgxm'; // Remplacez par votre mot de passe ou clé d'application
        // $login->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $login->Port       = 587;
        // $login->CharSet    = 'UTF-8';
        
        // // Destinataires
        // $login->setFrom('fallou.ndiaye22@isep-thies.edu.sn', 'Sonatel Academy');
        // $login->addAddress($apprenant['login'], $apprenant['nom_complet']);
        
        // Configuration du serveur
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fallou.ndiaye22@isep-thies.edu.sn'; // Remplacez par votre email
        $mail->Password = 'agrr effl ylta zgxm'; // Remplacez par votre mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Destinataires
        $mail->setFrom('fallou.ndiaye22@isep-thies.edu.sn', 'Sonatel Academy');
        $mail->addAddress($apprenant['login'], $apprenant['nom_complet']);
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Bienvenue à Sonatel Academy';
        $mail->Body = generer_template_email_bienvenue($apprenant, $password);
        $mail->AltBody = "Bienvenue à Sonatel Academy!\n\n" .
                        "Votre compte a été créé avec succès.\n" .
                        "Matricule: {$apprenant['matricule']}\n" .
                        "Login: {$apprenant['login']}\n" .
                        "Mot de passe: $password\n\n" .
                        "Veuillez vous connecter pour accéder à votre espace personnel.";
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Erreur d'envoi d'email: " . $e->getMessage());
        return false;
    }
}

/**
 * Génère le template HTML pour l'email de bienvenue
 */
function generer_template_email_bienvenue(array $apprenant, string $password): string {
    // Logo Sonatel Academy en base64 (à remplacer par votre logo réel)
    $logo = 'https://sonatelacademy.sn/wp-content/uploads/2022/09/logo-sonatel-academy.png';
    
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bienvenue à Sonatel Academy</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                line-height: 1.6;
                color: #333;
                margin: 0;
                padding: 0;
                background-color: #f5f5f5;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                background-color: #ff7900;
                padding: 20px;
                text-align: center;
            }
            .header img {
                max-width: 200px;
                height: auto;
            }
            .content {
                padding: 30px;
            }
            .welcome {
                font-size: 22px;
                font-weight: bold;
                color: #ff7900;
                margin-bottom: 20px;
            }
            .message {
                margin-bottom: 25px;
                color: #555;
            }
            .credentials {
                background-color: #f9f9f9;
                padding: 20px;
                border-radius: 6px;
                margin-bottom: 25px;
                border-left: 4px solid #ff7900;
            }
            .credentials p {
                margin: 8px 0;
            }
            .credentials strong {
                color: #333;
            }
            .button {
                display: inline-block;
                background-color: #ff7900;
                color: white;
                text-decoration: none;
                padding: 12px 25px;
                border-radius: 4px;
                font-weight: bold;
                margin: 15px 0;
            }
            .footer {
                background-color: #333;
                color: white;
                text-align: center;
                padding: 15px;
                font-size: 12px;
            }
            .social {
                margin-top: 15px;
            }
            .social a {
                display: inline-block;
                margin: 0 10px;
                color: white;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="{$logo}" alt="Sonatel Academy Logo">
            </div>
            <div class="content">
                <div class="welcome">Bienvenue à Sonatel Academy, {$apprenant['nom_complet']}!</div>
                
                <div class="message">
                    <p>Félicitations! Votre compte a été créé avec succès. Vous faites maintenant partie de la communauté Sonatel Academy, le centre de formation de référence en technologies numériques au Sénégal.</p>
                    <p>Vous pouvez dès à présent vous connecter à votre espace personnel pour accéder à vos cours, ressources pédagogiques et suivre votre progression.</p>
                </div>
                
                <div class="credentials">
                    <p><strong>Matricule:</strong> {$apprenant['matricule']}</p>
                    <p><strong>Email:</strong> {$apprenant['login']}</p>
                    <p><strong>Mot de passe:</strong> {$password}</p>
                </div>
                
                <p>Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe dès votre première connexion.</p>
                
                <center><a href="https://sonatelacademy.sn/login" class="button">Se connecter</a></center>
                
                <p>Si vous avez des questions, n'hésitez pas à contacter notre équipe pédagogique.</p>
                
                <p>Cordialement,<br>
                L'équipe Sonatel Academy</p>
            </div>
            
            <div class="footer">
                <p>Sonatel Academy - Centre de formation aux métiers du numérique</p>
                <p>Adresse: Technopole, Dakar, Sénégal | Téléphone: +221 33 839 13 13</p>
                <div class="social">
                    <a href="https://www.facebook.com/SonatelAcademy/">Facebook</a> |
                    <a href="https://twitter.com/sonatelacademy">Twitter</a> |
                    <a href="https://www.linkedin.com/company/sonatel-academy/">LinkedIn</a>
                </div>
                <p>© 2025 Falilloulahi NDIAYE.</p>
            </div>
        </div>
    </body>
    </html>
    HTML;
}