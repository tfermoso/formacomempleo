<?php
// php/procesar_registro_candidato.php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redir('/candidato/registro.php');
}

// CSRF (opcional, si pones token en formulario)
$token = $_POST['_csrf'] ?? '';
if (!csrf_check($token)) {
    die("Token CSRF inválido.");
}

$nombre = limpiar($_POST['nombre'] ?? '');
$apellidos = limpiar($_POST['apellidos'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!$nombre || !$apellidos || !$email || !$password) {
    die("Faltan datos obligatorios.");
}

if (!validar_email($email)) {
    die("Email no válido.");
}

if (!validar_password($password)) {
    die("La contraseña debe tener al menos 6 caracteres.");
}

// comprobar si email existe en candidatos
$stmt = $pdo->prepare("SELECT id FROM candidatos WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("El email ya está registrado. <a href='/candidato/login.php'>Inicia sesión</a>");
}

// crear candidato
$pw_hash = password_hash($password, PASSWORD_DEFAULT);
$ins = $pdo->prepare("INSERT INTO candidatos (dni, nombre, apellidos, telefono, email, password_hash, direccion, cp, ciudad, provincia, created_at) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$ins->execute([
    $nombre, $apellidos, $_POST['telefono'] ?? null, $email, $pw_hash,
    $_POST['direccion'] ?? null, $_POST['cp'] ?? null, $_POST['ciudad'] ?? null, $_POST['provincia'] ?? null
]);

// iniciar sesión automático
$id = $pdo->lastInsertId();
session_start();
$_SESSION['usuario'] = [
    'id' => $id,
    'nombre' => $nombre,
    'apellidos' => $apellidos,
    'email' => $email,
    'rol' => 'candidato'
];

redir('/candidato/dashboard.php');
