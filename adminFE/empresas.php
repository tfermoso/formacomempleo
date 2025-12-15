<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if (!isAdmin()) die("Acceso denegado.");
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexion.php';

$rows = $pdo->query("SELECT * FROM empresas ORDER BY created_at DESC")->fetchAll();

echo "<h2>Empresas</h2>";
foreach ($rows as $r) {
    echo "<div class='card'><h3>" . htmlspecialchars($r['nombre']) . "</h3>";
    echo "<p>CIF: " . htmlspecialchars($r['cif']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($r['email_contacto']) . "</p></div>";
}

include __DIR__ . '/../includes/footer.php';
