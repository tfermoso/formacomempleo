<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ../../dashboard.php?tab=usuarios');
    exit;
}

$id = (int)$_GET['id'];

// Comprobar si es admin
$stmt = $db->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

if ($is_admin) {
    echo "<script>alert('No se puede eliminar un administrador.'); window.location.href='../../dashboard.php?tab=usuarios';</script>";
    exit;
}

// Borrado lógico
$stmt = $db->prepare("UPDATE usuarios SET deleted_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: ../../dashboard.php?tab=usuarios");
exit;
