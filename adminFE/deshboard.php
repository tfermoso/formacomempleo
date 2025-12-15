<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if (!isAdmin()) { die("Acceso denegado."); }
$titulo = "Admin Dashboard";
include __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/conexion.php';

// stats
$c = $pdo->query("SELECT COUNT(*) FROM candidatos")->fetchColumn();
$e = $pdo->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
$o = $pdo->query("SELECT COUNT(*) FROM ofertas")->fetchColumn();

echo "<h2>Panel de Administración</h2>";
echo "<div class='card'><p>Candidatos: $c</p><p>Empresas: $e</p><p>Ofertas: $o</p></div>";
echo "<ul>
    <li><a href='empresas.php'>Gestionar empresas</a></li>
    <li><a href='ofertas.php'>Gestionar ofertas</a></li>
    <li><a href='candidatos.php'>Gestionar candidatos</a></li>
    <li><a href='/candidato/logout.php'>Cerrar sesión</a></li>
</ul>";

include __DIR__ . '/../includes/footer.php';
