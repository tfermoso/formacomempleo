<?php
session_start();
require_once "../../../conexion.php"; // usa $db (mysqli)

// Verificar sesión admin
if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../login.php');
    exit;
}

// Verificar ID
if (!isset($_GET['id'])) {
    header('Location: ../../dashboard.php?tab=candidatos');
    exit;
}

$id = (int)$_GET['id'];

// Borrado lógico (NO se elimina el registro)
$stmt = $db->prepare("UPDATE candidatos SET deleted_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt->close();

// Volver al dashboard
header("Location: ../../dashboard.php?tab=candidatos");
exit;
