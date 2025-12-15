<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if ($_SESSION['usuario']['rol'] !== 'empresa') die("Acceso denegado");

include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexion.php';

$id_oferta = (int)($_GET['id'] ?? 0);
$empresa_id = $_SESSION['usuario']['empresa_id'];

// comprobar que la oferta pertenece a la empresa
$stmt = $pdo->prepare("SELECT * FROM ofertas WHERE id=? AND idempresa=?");
$stmt->execute([$id_oferta, $empresa_id]);
$oferta = $stmt->fetch();
if (!$oferta) die("Oferta no encontrada");

$candidatos = $pdo->prepare("SELECT c.* FROM ofertas_candidatos oc JOIN candidatos c ON c.id=oc.idcandidato WHERE oc.idoferta=?");
$candidatos->execute([$id_oferta]);
$candidatos = $candidatos->fetchAll();

echo "<h2>Candidatos para: " . htmlspecialchars($oferta['titulo']) . "</h2>";
foreach ($candidatos as $c) {
    echo "<div class='card'>";
    echo "<h3>" . htmlspecialchars($c['nombre']) . " " . htmlspecialchars($c['apellidos']) . "</h3>";
    echo "<p>Email: " . htmlspecialchars($c['email']) . "</p>";
    echo "<p>Ciudad: " . htmlspecialchars($c['ciudad']) . "</p>";
    echo "</div>";
}

include __DIR__ . '/../includes/footer.php';
