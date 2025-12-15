<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if (!isAdmin()) die("Acceso denegado");

include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexion.php';

$candidatos = $pdo->query("SELECT * FROM candidatos WHERE deleted_at IS NULL ORDER BY created_at DESC")->fetchAll();

echo "<h2>Candidatos</h2>";
foreach ($candidatos as $c) {
    echo "<div class='card'>";
    echo "<h3>" . htmlspecialchars($c['nombre']) . " " . htmlspecialchars($c['apellidos']) . "</h3>";
    echo "<p>Email: " . htmlspecialchars($c['email']) . "</p>";
    echo "<p>Ciudad: " . htmlspecialchars($c['ciudad']) . "</p>";
    echo "</div>";
}

include __DIR__ . '/../includes/footer.php';
