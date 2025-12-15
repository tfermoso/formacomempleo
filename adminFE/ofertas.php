<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if (!isAdmin()) die("Acceso denegado");

include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexion.php';

$ofertas = $pdo->query("SELECT o.*, e.nombre as empresa FROM ofertas o JOIN empresas e ON e.id=o.idempresa WHERE o.deleted_at IS NULL ORDER BY fecha_publicacion DESC")->fetchAll();

echo "<h2>Ofertas</h2>";
foreach ($ofertas as $o) {
    echo "<div class='card'>";
    echo "<h3>" . htmlspecialchars($o['titulo']) . "</h3>";
    echo "<p>Empresa: " . htmlspecialchars($o['empresa']) . "</p>";
    echo "<p>Estado: " . htmlspecialchars($o['estado']) . "</p>";
    echo "</div>";
}

include __DIR__ . '/../includes/footer.php';
