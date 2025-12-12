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

// Redirige al login si no está logueado
function redirectIfNotLoggedIn()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
?>