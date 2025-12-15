<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexion.php';

$ofertas = $pdo->query("SELECT o.*, e.nombre as empresa FROM ofertas o JOIN empresas e ON e.id=o.idempresa WHERE o.deleted_at IS NULL AND estado='publicada' ORDER BY fecha_publicacion DESC")->fetchAll();

echo "<h2>Ofertas disponibles</h2>";
foreach ($ofertas as $o) {
    echo "<div class='card'>";
    echo "<h3>" . htmlspecialchars($o['titulo']) . "</h3>";
    echo "<p>Empresa: " . htmlspecialchars($o['empresa']) . "</p>";
    echo "<p>" . nl2br(htmlspecialchars(substr($o['descripcion'],0,200))) . "...</p>";
    echo "</div>";
}

include __DIR__ . '/../includes/footer.php';
