<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ../../dashboard.php?tab=ofertas');
    exit;
}

$id = (int)$_GET['id'];

// Borrado lógico: actualizar deleted_at
$stmt = $db->prepare("UPDATE ofertas SET deleted_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: ../../dashboard.php?tab=ofertas");
exit;
