<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogueado = isset($_SESSION["usuario_nombre"]);
$empresaNombre = $_SESSION["empresa_nombre"] ?? null;
$usuarioNombre = $_SESSION["usuario_nombre"] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Área Empresa - Formacom</title>
    <link rel="stylesheet" href="../assets/css/empresa.css">
</head>

<body>

    <header class="header-empresa">
        <div class="header-left">
            <a href="../empresa/dashboard.php">
                <img src="../assets/img/logo.png" alt="Formacom" class="logo-empresa">
            </a>
        </div>

        <nav class="header-right">
            <ul class="menu-principal">

                <?php if ($usuarioLogueado): ?>

                    <li><a href="../empresa/dashboard.php">Dashboard</a></li>



                    <li class="menu-usuario">
                        <span class="usuario-nombre">
                            <?php echo htmlspecialchars($usuarioNombre); ?> ▼
                        </span>

                        <ul class="submenu">
                            <li><a href="../empresa/perfil.php">Perfil</a></li>
                            <li><a href="../empresa/logout.php">Cerrar sesión</a></li>
                        </ul>
                    </li>

                <?php else: ?>

                    <li><a href="./login.php">Iniciar sesión</a></li>
                    <li><a href="./registro.php">Registrarse</a></li>

                <?php endif; ?>

            </ul>
        </nav>
    </header>

    <main class="contenido-empresa">