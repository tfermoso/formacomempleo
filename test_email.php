<?php
require_once "includes/enviar_email.php";

$resultado = enviarEmail(
    "destinatario@ejemplo.com",
    "Prueba desde PHPMailer",
    "<h1>Hola!</h1><p>aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa.</p>"
);

echo "<pre>";
var_dump($resultado);
echo "</pre>";
