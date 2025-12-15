<?php
// php/crear_oferta.php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redir('/empresa/crear_oferta.php');
if (!csrf_check($_POST['_csrf'] ?? '')) die("CSRF inválido.");

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'empresa') {
    die("Acceso denegado.");
}

$empresa_id = $_SESSION['usuario']['empresa_id'];
$titulo = limpiar($_POST['titulo'] ?? '');
$descripcion = limpiar($_POST['descripcion'] ?? '');
$idmodalidad = (int)($_POST['idmodalidad'] ?? 0);
$idsector = (int)($_POST['idsector'] ?? 0);

if (!$titulo || !$descripcion || !$idmodalidad || !$idsector) {
    die("Faltan campos obligatorios.");
}

$salario_min = $_POST['salario_min'] ?: null;
$salario_max = $_POST['salario_max'] ?: null;
$tipo_contrato = limpiar($_POST['tipo_contrato'] ?? null);
$jornada = limpiar($_POST['jornada'] ?? null);
$ubicacion = limpiar($_POST['ubicacion'] ?? null);
$fecha_publicacion = date('Y-m-d');

$stmt = $pdo->prepare("INSERT INTO ofertas (idempresa, idsector, idmodalidad, titulo, descripcion, requisitos, funciones, salario_min, salario_max, tipo_contrato, jornada, ubicacion, fecha_publicacion, estado, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'publicada', NOW())");
$stmt->execute([$empresa_id, $idsector, $idmodalidad, $titulo, $descripcion, $_POST['requisitos'] ?? null, null, $salario_min, $salario_max, $tipo_contrato, $jornada, $ubicacion, $fecha_publicacion]);

redir('/empresa/mis_ofertas.php');
