<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$titulo = "Panel Empresa";
include __DIR__ . '/../includes/header.php';

$uid = $_SESSION['usuario']['id'] ?? null;
$empresa_id = $_SESSION['usuario']['empresa_id'] ?? null;

echo "<h2>Panel de Empresa</h2>";
echo "<p>Bienvenido, " . htmlspecialchars($_SESSION['usuario']['nombre']) . "</p>";
echo "<ul>
    <li><a href='crear_oferta.php'>Crear oferta</a></li>
    <li><a href='mis_ofertas.php'>Mis ofertas</a></li>
    <li><a href='/candidato/ofertas.php'>Ver ofertas públicas</a></li>
    <li><a href='/candidato/logout.php'>Cerrar sesión</a></li>
</ul>";

include __DIR__ . '/../includes/footer.php';
