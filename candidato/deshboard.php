<?php
require "../includes/auth.php";
requireLogin();

$titulo = "Panel del Candidato";
include "../includes/header.php";
?>

<h2>Bienvenido, <?= $_SESSION['usuario']['nombre'] ?></h2>

<ul>
    <li><a href="perfil.php">Mi Perfil</a></li>
    <li><a href="../empresa/ofertas.php">Ver Ofertas</a></li>
    <li><a href="logout.php">Cerrar sesión</a></li>
</ul>

<?php include "../includes/footer.php"; ?>
