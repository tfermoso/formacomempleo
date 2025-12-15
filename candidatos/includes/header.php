<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$logged = false;
$candidato = null;

if (isset($_SESSION['idcandidato'])) {
    $id = (int) $_SESSION['idcandidato'];
    $candidato = getCandidatoCompleto($conn, $id);

    // Foto del candidato
    $foto = (!empty($candidato['foto']))
        ? "uploads/fotos/" . $candidato['foto']
        : "uploads/fotos/default.png";

    $logged = true;

    // Redirigir al dashboard si hay sesión y no estamos ya en dashboard.php
    $currentScript = basename($_SERVER['PHP_SELF']);
    if ($currentScript !== 'dashboard.php') {
        header('Location: dashboard.php');
        exit;
    }
}

// Logo de la empresa
$logo = "../assets/img/logo-horizontal-1.png";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORMACOM Agencia de colocación 1200000129</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/landing.css">
    <script src="../assets/js/menu.js" defer></script>
    <meta name="description" content="Agencia de colocación nº 1200000119. Encuentra empleo o personal con Formacom.">
    <script>
        function toggleMenu() {
            const menu = document.getElementById("dropdown-menu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        document.addEventListener("click", function (e) {
            const menu = document.getElementById("dropdown-menu");
            const trigger = document.getElementById("nombre-trigger");

            if (trigger && menu && !trigger.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = "none";
            }
        });
    </script>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <!-- Logo a la izquierda -->
            <a href="index.php" class="logo">
                <img src="assets/img/logo-vertical-1.png" alt="Logo Formacom" class="logo-img">
            </a>

            <!-- Navegación (desktop) -->
            <nav class="main-nav">
                <ul>
                    <?php if ($logged): ?>
                        <li><a href="dashboard.php">Inicio</a></li>
                        <li>
                            <span id="nombre-trigger" class="nombre" onclick="toggleMenu()">
                                <?php echo htmlspecialchars($candidato['nombre'] ?? 'Usuario'); ?>
                            </span>

                            <div id="dropdown-menu" class="dropdown">
                                <a href="editar_perfil.php">Editar perfil</a>
                                <a href="logout.php">Cerrar sesión</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="../index.php">Inicio</a></li>
                        <li><a href="login.php">Iniciar sesión</a></li>
                        <li><a href="registro.php">Registro</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Botón hamburguesa (mobile, alineado a la derecha) -->
            <button class="nav-toggle" aria-label="Abrir menú">&#9776;</button>

            <!-- Overlay para cerrar menú al hacer clic fuera -->
            <div class="nav-overlay"></div>
        </div>
    </header>
    <main class="site-content">