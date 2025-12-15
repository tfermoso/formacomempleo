<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/config_email.php';

function enviarEmail($para, $asunto, $mensajeHTML)
{
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->Port       = SMTP_PORT;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Remitente
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);

        // Destinatario
        $mail->addAddress($para);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHTML;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Error al enviar: {$mail->ErrorInfo}";
    }
}


$datos = enviarEmail("tomas.fermoso@gmail.com", "Prueba de email", "<h1>Hola</h1><p>Este es un email de prueba.</p>");
echo $datos;
