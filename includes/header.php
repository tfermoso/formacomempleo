<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['idcandidato'])) {
    echo "Acceso denegado.";
    exit;
}

$id = (int)$_SESSION['idcandidato'];
$candidato = getCandidatoCompleto($conn, $id);

// Foto del candidato
$foto = (!empty($candidato['foto']))
    ? "../uploads/fotos/" . $candidato['foto']
    : "../uploads/fotos/default.png";

// Logo de la empresa
$logo = "../media/img/logo.png";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área del candidato</title>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("dropdown-menu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        document.addEventListener("click", function(e) {
            const menu = document.getElementById("dropdown-menu");
            const trigger = document.getElementById("nombre-trigger");

            if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = "none";
            }
        });
    </script>
</head>

<body>

<div class="header">
    <img src="<?php echo $logo; ?>" class="logo" alt="Logo empresa">

    <div class="perfil-header">
        <img src="<?php echo $foto; ?>" class="foto" alt="Foto perfil">

        <span id="nombre-trigger" class="nombre" onclick="toggleMenu()">
            <?php echo $candidato['nombre']; ?>
        </span>

        <div id="dropdown-menu" class="dropdown">
            <a href="editar_perfil.php">Editar perfil</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>
</div>
