<?php
// php/procesar_login_candidato.php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redir('/candidato/login.php');
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    die("Faltan datos.");
}

$stmt = $pdo->prepare("SELECT * FROM candidatos WHERE email = ? AND deleted_at IS NULL");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    die("Credenciales incorrectas. <a href='/candidato/login.php'>Volver</a>");
}

// ok
session_start();
$_SESSION['usuario'] = [
    'id' => $user['id'],
    'nombre' => $user['nombre'],
    'apellidos' => $user['apellidos'],
    'email' => $user['email'],
    'rol' => 'candidato'
];

redir('/candidato/dashboard.php');
