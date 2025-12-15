<?php
// Generar y comprobar token CSRF
session_start();

require_once __DIR__ . "/conexion.php";

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");


function generarTokenCSRF(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function comprobarTokenCSRF(string $tokenFormulario): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $tokenFormulario);
}



//Sanitización básica

function limpiarTexto(string $valor): string
{
    $valor = trim($valor);
    $valor = stripslashes($valor);
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}



//Validación completa de CIF (lado servidor)

function validarCIF(string $cif): bool
{
    $cif = strtoupper(trim($cif));

    // Patrón básico CIF/NIF empresa
    if (!preg_match('/^[ABCDEFGHJNPQRSUVW]\d{7}[0-9A-J]$/', $cif)) {
        return false;
    }

    $letraInicial = $cif[0];
    $numeros = substr($cif, 1, 7);
    $control = $cif[8];

    $sumaPares = 0;
    $sumaImpares = 0;

    for ($i = 0; $i < 7; $i++) {
        $digito = (int)$numeros[$i];
        if (($i + 1) % 2 === 0) {
            // posición par (2,4,6)
            $sumaPares += $digito;
        } else {
            // posición impar (1,3,5,7)
            $doble = $digito * 2;
            $sumaImpares += (int)floor($doble / 10) + ($doble % 10);
        }
    }

    $sumaTotal = $sumaPares + $sumaImpares;
    $unidad = $sumaTotal % 10;
    $digitoControl = ($unidad === 0) ? 0 : 10 - $unidad;

    $controlNumerico = (string)$digitoControl;
    $controlLetra = 'JABCDEFGHI'[$digitoControl];

    // Tipos de entidades según letra inicial
    if (in_array($letraInicial, ['A', 'B', 'E', 'H'])) {
        // Debe ser numérico
        return $control === $controlNumerico;
    } elseif (in_array($letraInicial, ['K', 'P', 'Q', 'S', 'N', 'W', 'R'])) {
        // Debe ser letra
        return $control === $controlLetra;
    } else {
        // Puede ser o número o letra
        return $control === $controlNumerico || $control === $controlLetra;
    }
}

//Mensaje de error
function setFlash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'][$tipo][] = $mensaje;
}

function getFlash(): array
{
    if (!isset($_SESSION['flash'])) {
        return [];
    }
    $mensajes = $_SESSION['flash'];
    unset($_SESSION['flash']); // Se borran después de mostrarlos
    return $mensajes;
}



//FUNCIONES MARCOS


require_once __DIR__ . "/conexion.php";

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
