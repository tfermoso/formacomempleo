<?php
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'empresa') die("Acceso denegado.");
$id = (int)($_GET['id'] ?? 0);
$empresa_id = $_SESSION['usuario']['empresa_id'];

if (!$id) die("ID inválido.");

// asegurar que la oferta pertenece a la empresa
$stmt = $pdo->prepare("SELECT idempresa FROM ofertas WHERE id = ?");
$stmt->execute([$id]);
$r = $stmt->fetch();
if (!$r || $r['idempresa'] != $empresa_id) die("No autorizado.");

$del = $pdo->prepare("UPDATE ofertas SET deleted_at = NOW() WHERE id = ?");
$del->execute([$id]);

redir('/empresa/mis_ofertas.php');
