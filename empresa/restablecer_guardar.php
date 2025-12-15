<?php
require_once "../includes/conexion.php";

$token = $_POST["token"] ?? "";
$password = $_POST["password"] ?? "";

if (strlen($password) < 8) {
    echo "La contraseña debe tener al menos 8 caracteres.";
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$sql = $mysqli->prepare("UPDATE usuarios SET password_hash=?, token_recuperacion=NULL, token_expira=NULL WHERE token_recuperacion=?");
$sql->bind_param("ss", $passwordHash, $token);
$sql->execute();

echo "Contraseña actualizada correctamente.";
