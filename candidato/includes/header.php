<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogueado = isset($_SESSION["idcandidato"]);
$usuarioNombre = $_SESSION["candidato_nombre"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Área Candidato - Formacom</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/candidato.css">
    <script src="../assets/js/empresa.js" defer></script>
</head>

<body>

    <header class="header-empresa">
        <div class="header-left">
            <a href="../">
                <img src="../assets/img/logo-horizontal-1.png" alt="Formacom" class="logo-empresa">
            </a>
        </div>

        <button class="nav-toggle-empresa">☰</button>

        <nav class="header-right">
            <ul class="menu-principal">

                <?php if ($usuarioLogueado): ?>

                    <li><a href="../candidato/dashboard.php">Dashboard</a></li>

                    <li class="menu-usuario">
                        <span class="usuario-nombre">
                            <?php echo htmlspecialchars($usuarioNombre); ?> ▼
                        </span>

                        <ul class="submenu">
                            <li><a href="../candidato/perfil.php">Perfil</a></li>
                            <li><a href="../candidato/logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>

                <?php else: ?>

                    <li><a href="./login.php">Iniciar sesión</a></li>
                    <li><a href="./registro.php">Registrarse</a></li>

                <?php endif; ?>

            </ul>
        </nav>
    </header>

    <div class="nav-overlay-empresa"></div>

    <main class="contenido-candidato">