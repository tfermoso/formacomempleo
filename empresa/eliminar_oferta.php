<?php
session_start();
require_once __DIR__ . "/../includes/functions.php";


redirectIfNotLoggedIn();

$conn = conectarBD();

// Validar ID de oferta
if (!isset($_GET["id"])) {
    die("ID de oferta no especificado.");
}
$idoferta = intval($_GET["id"]);

// Borrado lógico: marcar deleted_at
$stmt = $conn->prepare("UPDATE ofertas 
                        SET deleted_at = NOW() 
                        WHERE id = ? AND idempresa = ?");
$stmt->bind_param("ii", $idoferta, $_SESSION["idempresa"]);

if ($stmt->execute()) {
    $mensaje = "Oferta eliminada correctamente.";
    $tipoMensaje = "success";
} else {
    $mensaje = "Error al eliminar la oferta: " . $conn->error;
    $tipoMensaje = "error";
}
$stmt->close();
$conn->close();

// Redirigir de vuelta al dashboard con mensaje
header("Location: dashboard.php?msg=" . urlencode($mensaje) . "&type=" . urlencode($tipoMensaje));
exit;
