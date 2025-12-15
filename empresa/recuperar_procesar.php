<?php
require_once "../includes/conexion.php";
require_once "../includes/emails.php";

$email = $_POST["email"] ?? "";

// Buscar empresa/usuario por email
$sql = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
$sql->bind_param("s", $email);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 0) {
    echo "No existe ninguna cuenta con ese email.";
    exit;
}

// Crear token
$token = bin2hex(random_bytes(32));
$expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Guardar token en BD
$update = $mysqli->prepare("UPDATE usuarios SET token_recuperacion=?, token_expira=? WHERE email=?");
$update->bind_param("sss", $token, $expira, $email);
$update->execute();

// Enviar email
enviarEmailRecuperarPassword($email, $token);

echo "Se ha enviado un enlace de recuperación a tu correo.";
