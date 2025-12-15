<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
include __DIR__ . '/../includes/header.php';

$empresa_id = $_SESSION['usuario']['empresa_id'];
require_once __DIR__ . '/../includes/conexion.php';

$stmt = $pdo->prepare("SELECT * FROM ofertas WHERE idempresa = ? AND deleted_at IS NULL ORDER BY fecha_publicacion DESC");
$stmt->execute([$empresa_id]);
$ofertas = $stmt->fetchAll();

echo "<h2>Mis ofertas</h2>";
foreach ($ofertas as $o) {
    echo "<div class='card'><h3>" . htmlspecialchars($o['titulo']) . "</h3>";
    echo "<p>" . nl2br(htmlspecialchars(substr($o['descripcion'],0,250))) . "</p>";
    echo "<p><a href='/empresa/editar_oferta.php?id=" . $o['id'] . "'>Editar</a> | <a href='/php/eliminar_oferta.php?id=" . $o['id'] . "'>Eliminar</a></p></div>";
}

include __DIR__ . '/../includes/footer.php';
