<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) { header('Location: login.php'); exit; }

if (!isset($_GET['id'])) { header('Location: ../../dashboard.php?tab=empresas'); exit; }
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM empresas WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../../dashboard.php?tab=empresas");
exit;
