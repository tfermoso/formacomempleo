<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $linkedin = trim($_POST['linkedin']);
    $web = trim($_POST['web']);
    $direccion = trim($_POST['direccion']);
    $cp = trim($_POST['cp']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    if (empty($nombre) || empty($apellidos) || empty($email) || empty($password)) {
        $error = "Nombre, apellidos, email y contraseña son obligatorios.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO candidatos (dni, nombre, apellidos, telefono, email, password_hash, linkedin, web, direccion, cp, ciudad, provincia, fecha_nacimiento) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$dni, $nombre, $apellidos, $telefono, $email, $password_hash, $linkedin, $web, $direccion, $cp, $ciudad, $provincia, $fecha_nacimiento]);
        header("Location: ../../dashboard.php?tab=candidatos");
        exit;
    }
}
?>
<link rel="stylesheet" href="../styles.css">
<form method="POST">
    <h2>Crear Candidato</h2>
    <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
    Nombre*: <input type="text" name="nombre"><br>
    Apellidos*: <input type="text" name="apellidos"><br>
    DNI: <input type="text" name="dni"><br>
    Teléfono: <input type="text" name="telefono"><br>
    Email*: <input type="email" name="email"><br>
    Contraseña*: <input type="password" name="password"><br>
    LinkedIn: <input type="url" name="linkedin"><br>
    Web: <input type="url" name="web"><br>
    Dirección: <input type="text" name="direccion"><br>
    CP: <input type="text" name="cp"><br>
    Ciudad: <input type="text" name="ciudad"><br>
    Provincia: <input type="text" name="provincia"><br>
    Fecha de nacimiento: <input type="date" name="fecha_nacimiento"><br>
    <button type="submit">Crear</button>
    <a href="../../dashboard.php?tab=candidatos">Cancelar</a>
</form>
