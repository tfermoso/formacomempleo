<?php
//Funciones Candidatos
function getCandidato(mysqli $conn, int $id) {
    $stmt = $conn->prepare("
        SELECT nombre, apellidos 
        FROM candidatos 
        WHERE id = ? AND deleted_at IS NULL
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function conectarBD() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    $conexion->set_charset("utf8mb4");
    return $conexion;
}

function getOfertasInscritas(mysqli $conn, int $idcandidato) {
    $sql = "
        SELECT 
            o.id,
            o.titulo,
            oc.estado AS estado_inscripcion,
            oc.fecha_inscripcion,
            o.descripcion,
            o.requisitos,
            o.funciones,
            o.salario_min,
            o.salario_max,
            o.tipo_contrato,
            o.jornada,
            o.ubicacion,
            o.fecha_incorporacion,
            o.fecha_publicacion,
            e.nombre AS empresa
        FROM ofertas o
        INNER JOIN ofertas_candidatos oc ON o.id = oc.idoferta
        INNER JOIN empresas e ON o.idempresa = e.id
        WHERE oc.idcandidato = ?
          AND o.deleted_at IS NULL
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idcandidato);
    $stmt->execute();
    return $stmt->get_result();
}

function getOfertasNoInscritas(mysqli $conn, int $idcandidato) {
    $sql = "
        SELECT 
            o.id,
            o.titulo,
            o.descripcion,
            o.requisitos,
            o.funciones,
            o.salario_min,
            o.salario_max,
            o.tipo_contrato,
            o.jornada,
            o.ubicacion,
            o.fecha_incorporacion,
            o.fecha_publicacion,
            e.nombre AS empresa
        FROM ofertas o
        INNER JOIN empresas e ON o.idempresa = e.id
        WHERE o.deleted_at IS NULL
          AND o.estado = 'abierta'
          AND o.id NOT IN (
                SELECT idoferta 
                FROM ofertas_candidatos 
                WHERE idcandidato = ?
          )
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idcandidato);
    $stmt->execute();
    return $stmt->get_result();
}

function eliminarInscripcion(mysqli $conn, int $idoferta, int $idcandidato): bool {
    $stmt = $conn->prepare("
        DELETE FROM ofertas_candidatos 
        WHERE idoferta = ? AND idcandidato = ?
    ");
    $stmt->bind_param("ii", $idoferta, $idcandidato);
    return $stmt->execute();
}

function inscribirseEnOferta(mysqli $conn, int $idoferta, int $idcandidato): bool {
    $stmt = $conn->prepare("
        INSERT INTO ofertas_candidatos (idoferta, idcandidato, fecha_inscripcion, estado)
        VALUES (?, ?, NOW(), 'pendiente')
    ");
    $stmt->bind_param("ii", $idoferta, $idcandidato);
    return $stmt->execute();
}

?>
<?php

function getCandidatoCompleto($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM candidatos WHERE id = ? AND deleted_at IS NULL");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function actualizarCandidato(mysqli $conn, int $id, array $data) {
    $sql = "
        UPDATE candidatos SET
            nombre = ?,
            apellidos = ?,
            telefono = ?,
            email = ?,
            linkedin = ?,
            web = ?,
            cp = ?,
            ciudad = ?,
            provincia = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssissi",
        $data['nombre'],
        $data['apellidos'],
        $data['telefono'],
        $data['email'],
        $data['linkedin'],
        $data['web'],
        $data['cp'],
        $data['ciudad'],
        $data['provincia'],
        $id
    );

    return $stmt->execute();
}

function subirArchivo(array $archivo, string $carpetaRelativa, array $extensionesPermitidas)
{
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $extensionesPermitidas)) {
        return false;
    }

    // 📍 Ruta ABSOLUTA en el servidor
    $rutaBase = realpath(__DIR__ . '/../'); // formacomempleo/
    $rutaDestino = $rutaBase . '/' . trim($carpetaRelativa, '/');

    // 📂 Crear carpeta si no existe
    if (!is_dir($rutaDestino)) {
        mkdir($rutaDestino, 0755, true);
    }

    $nombreArchivo = uniqid() . '.' . $extension;
    $rutaFinal = $rutaDestino . '/' . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
        return $nombreArchivo; // 👈 SOLO el nombre se guarda en BBDD
    }

    return false;
}
/**
 * Actualiza un único campo de un candidato
 */
function actualizarCampoCandidato(mysqli $conn, int $idCandidato, string $campo, string $valor): bool
{
    // Lista blanca de campos permitidos (MUY IMPORTANTE)
    $camposPermitidos = ['foto', 'cv'];

    if (!in_array($campo, $camposPermitidos)) {
        return false;
    }

    $sql = "UPDATE candidatos SET $campo = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $valor, $idCandidato);

    return $stmt->execute();
}

//Funciones empresas
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
