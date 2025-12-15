<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) { header('Location: ../l../ogin.php'); exit; }

if (!isset($_GET['id'])) { header('Location: ../../dashboard.php?tab=candidatos'); exit; }
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM candidatos WHERE id=?");
$stmt->execute([$id]);

header("Location: ../../dashboard.php?tab=candidatos");
exit;
