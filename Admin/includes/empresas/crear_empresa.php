<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cif = trim($_POST['cif']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email_contacto']);
    $direccion = trim($_POST['direccion']);
    $cp = trim($_POST['cp']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);

    if (empty($cif) || empty($nombre) || empty($email)) {
        $error = "CIF, nombre y email son obligatorios";
    } else {
        // Preparar statement mysqli
        $stmt = $db->prepare("INSERT INTO empresas (cif, nombre, telefono, email_contacto, direccion, cp, ciudad, provincia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $cif, $nombre, $telefono, $email, $direccion, $cp, $ciudad, $provincia);

        if ($stmt->execute()) {
            header("Location: ../../dashboard.php?tab=empresas");
            exit;
        } else {
            $error = "Error al crear la empresa: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<link rel="stylesheet" href="../styles.css">
<form method="POST">
    <h2>Crear Empresa</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    CIF*: <input type="text" name="cif"><br>
    Nombre*: <input type="text" name="nombre"><br>
    Teléfono: <input type="text" name="telefono"><br>
    Email*: <input type="email" name="email_contacto"><br>
    Dirección: <input type="text" name="direccion"><br>
    CP: <input type="text" name="cp"><br>
    Ciudad: <input type="text" name="ciudad"><br>
    Provincia: <input type="text" name="provincia"><br>
    <button type="submit">Crear</button>
    <a href="../../dashboard.php?tab=empresas">Cancelar</a>
</form>
