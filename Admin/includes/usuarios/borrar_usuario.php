<?php
session_start();
require_once "../../../conexion.php";

// Verificar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../login.php');
    exit;
}

// Verificar si se ha pasado el ID del usuario a eliminar
if (!isset($_GET['id'])) {
    header('Location: ../../dashboard.php?tab=usuarios');
    exit;
}

$id = (int)$_GET['id'];

// Obtener el ID del administrador actual (el que está logueado)
$admin_id = $_SESSION['admin_login'];

// Verificar si el usuario a eliminar es el mismo que el administrador
if ($id == $admin_id) {
    // Mostrar un mensaje de error si se intenta eliminar al administrador
    echo '<p style="color: red;">No puedes eliminar tu propia cuenta de administrador.</p>';
    echo '<a href="../../dashboard.php?tab=usuarios">Volver al Dashboard</a>';
    exit;
}

// Si el usuario no es el administrador, proceder con la eliminación
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=?");
$stmt->execute([$id]);

header("Location: ../../dashboard.php?tab=usuarios");
exit;
?>
