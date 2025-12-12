<?php
require_once __DIR__ . "/db.php";

function conectarBD()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}

// Comprueba si el usuario está logueado
function isLoggedIn()
{
    return isset($_SESSION["idusuario"]) && isset($_SESSION["idempresa"]);
}

// Redirigir si no hay sesión iniciada
function redirectIfNotLoggedIn()
{
    if (!isset($_SESSION["idusuario"]) || !isset($_SESSION["idempresa"])) {
        $_SESSION["flash_msg"] = "Debes iniciar sesión.";
        $_SESSION["flash_type"] = "error";
        header("Location: login.php");
        exit;
    }
}


// Obtener datos del usuario logueado
function obtenerUsuarioLogueado($conn, $idusuario)
{
    $stmt = $conn->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $idusuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();
    return $usuario;
}

// Obtener lista de sectores
function obtenerSectores($conn)
{
    return $conn->query("SELECT id, nombre FROM sectores ORDER BY nombre ASC");
}

// Obtener lista de modalidades
function obtenerModalidades($conn)
{
    return $conn->query("SELECT id, nombre FROM modalidad ORDER BY nombre ASC");
}

// Mostrar mensajes de alerta
function mostrarMensaje($mensaje, $tipo)
{
    if ($mensaje) {
        $clase = $tipo === "success" ? "alert-success" : "alert-error";
        echo '<div class="alert ' . $clase . '">' . htmlspecialchars($mensaje) . '</div>';
    }
}