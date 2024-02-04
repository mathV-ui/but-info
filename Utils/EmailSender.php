
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    
    public static function sendVerificationEmail($email, $subject, $message) {
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';
        require 'PHPMailer/src/Exception.php';

        $mail = new PHPMailer(true);

        try {
            // Paramètres du serveur SMTP
            require '../../credentials.php';
            $mail->isSMTP();
            $mail->Host = $hostMail;
            $mail->SMTPAuth = true;
            $mail->Username = $nomMail; 
            $mail->Password = $mdpMail;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Utiliser ENCRYPTION_SMTPS pour SSL/TLS
            $mail->Port = 465; // Port 465 pour SSL/TLS

            // Expéditeur et destinataire
            $mail->setFrom('noreply@but-info.xyz', 'but-info');
            $mail->addAddress($email);

            // Contenu de l'e-mail
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            // Envoyer l'e-mail
            $mail->send();
        } catch (Exception $e) {
            // Gérer les erreurs d'envoi d'e-mail
            echo '\n EmailSender: Erreur lors de l\'envoi de l\'e-mail : ', $mail->ErrorInfo ,'\n';
        }
    }
}
