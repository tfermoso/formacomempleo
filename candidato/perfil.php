<?php
require "../includes/auth.php";
requireLogin();
$titulo = "Mi Perfil";
include "../includes/header.php";
?>

<h2>Mi Perfil</h2>

<p><strong>Nombre:</strong> <?= $_SESSION['usuario']['nombre'] ?></p>
<p><strong>Apellidos:</strong> <?= $_SESSION['usuario']['apellidos'] ?></p>
<p><strong>Email:</strong> <?= $_SESSION['usuario']['email'] ?></p>

<a class="btn" href="dashboard.php">Volver al panel</a>

<?php include "../includes/footer.php"; ?>
