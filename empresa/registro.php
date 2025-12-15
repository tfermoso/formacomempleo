<?php
require_once '../includes/funciones.php';
require_once '../includes/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tokenCSRF = generarTokenCSRF();


// =========================
// PROCESAMIENTO DEL FORMULARIO
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errores = [];

    // 1. CSRF
    if (!isset($_POST['csrf_token']) || !comprobarTokenCSRF($_POST['csrf_token'])) {
        $errores[] = "Petición no válida (CSRF).";
    }

    // 2. Recoger y limpiar datos
    $cif             = limpiarTexto($_POST['cif'] ?? '');
    $nombreEmpresa   = limpiarTexto($_POST['nombre_empresa'] ?? '');
    $emailEmpresa    = limpiarTexto($_POST['email_empresa'] ?? '');
    $telefonoEmpresa = limpiarTexto($_POST['telefono_empresa'] ?? '');
    $webEmpresa      = limpiarTexto($_POST['web_empresa'] ?? '');
    $personaContacto = limpiarTexto($_POST['persona_contacto'] ?? '');
    $direccion       = limpiarTexto($_POST['direccion_empresa'] ?? '');
    $cp              = limpiarTexto($_POST['cp_empresa'] ?? '');
    $ciudad          = limpiarTexto($_POST['ciudad_empresa'] ?? '');
    $provincia       = limpiarTexto($_POST['provincia_empresa'] ?? '');

    $nombreUsuario   = limpiarTexto($_POST['nombre_usuario'] ?? '');
    $apellidosUsuario = limpiarTexto($_POST['apellidos_usuario'] ?? '');
    $emailUsuario    = limpiarTexto($_POST['email_usuario'] ?? '');
    $telefonoUsuario = limpiarTexto($_POST['telefono_usuario'] ?? '');
    $password        = $_POST['password'] ?? '';
    $password2       = $_POST['password2'] ?? '';

    // 3. Validaciones backend
    if (empty($cif) || !validarCIF($cif)) {
        $errores[] = "El CIF no es válido.";
    }

    if (empty($nombreEmpresa)) {
        $errores[] = "El nombre de la empresa es obligatorio.";
    }

    if (empty($emailEmpresa) || !filter_var($emailEmpresa, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email de la empresa no es válido.";
    }

    if (empty($nombreUsuario)) {
        $errores[] = "El nombre del usuario es obligatorio.";
    }

    if (empty($apellidosUsuario)) {
        $errores[] = "Los apellidos del usuario son obligatorios.";
    }

    if (empty($emailUsuario) || !filter_var($emailUsuario, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email del usuario no es válido.";
    }

    if (empty($telefonoUsuario)) {
        $errores[] = "El teléfono del usuario es obligatorio.";
    }

    if (empty($password) || strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    if ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    // 4. Comprobar empresa duplicada
    if (empty($errores)) {
        $stmt = $mysqli->prepare("SELECT id FROM empresas WHERE cif = ?");
        $stmt->bind_param("s", $cif);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errores[] = "Ya existe una empresa registrada con ese CIF.";
        }
    }

    // 5. Comprobar usuario duplicado
    if (empty($errores)) {
        $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $emailUsuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errores[] = "Ya existe un usuario registrado con ese email.";
        }
    }

    // Si hay errores → volver al formulario
    if (!empty($errores)) {
        setFlash('error', $errores[0]);
        header("Location: registro.php");
        exit;
    }

    // =========================
    // 6. TRANSACCIÓN MYSQLI
    // =========================
    $mysqli->begin_transaction();

    try {

        // Insert empresa
        $sqlEmpresa = "INSERT INTO empresas 
            (cif, nombre, telefono, web, persona_contacto, email_contacto, direccion, cp, ciudad, provincia, logo, verificada)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, 0)";

        $stmtEmpresa = $mysqli->prepare($sqlEmpresa);
        $stmtEmpresa->bind_param(
            "ssssssssss",
            $cif,
            $nombreEmpresa,
            $telefonoEmpresa,
            $webEmpresa,
            $personaContacto,
            $emailEmpresa,
            $direccion,
            $cp,
            $ciudad,
            $provincia
        );
        $stmtEmpresa->execute();

        $idEmpresa = $mysqli->insert_id;

        // Insert usuario
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sqlUsuario = "INSERT INTO usuarios 
            (nombre, apellidos, telefono, email, password_hash, idempresa, is_admin)
            VALUES (?, ?, ?, ?, ?, ?, 0)";

        $stmtUsuario = $mysqli->prepare($sqlUsuario);
        $stmtUsuario->bind_param(
            "sssssi",
            $nombreUsuario,
            $apellidosUsuario,
            $telefonoUsuario,
            $emailUsuario,
            $passwordHash,
            $idEmpresa
        );
        $stmtUsuario->execute();

        // Confirmar transacción
        $mysqli->commit();

        setFlash('success', 'Empresa registrada correctamente.');
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $mysqli->rollback();
        setFlash('error', 'Error al registrar la empresa. Inténtalo de nuevo.');
        header("Location: registro.php");
        exit;
    }
}


// =========================
// FORMULARIO
// =========================

include './includes/header.php';
?>

<h1>Registro de empresa</h1>

<?php
$mensajes = getFlash();
if (!empty($mensajes)) {
    foreach ($mensajes as $tipo => $lista) {
        foreach ($lista as $msg) {
            echo "<p class='mensaje-$tipo'>$msg</p>";
        }
    }
}
?>

<div class="form-container">
<form action="registro.php" method="post" id="formRegistroEmpresa" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo $tokenCSRF; ?>">

    <h2>Datos de la empresa</h2>

    <div class="form-group">
        <label for="cif">CIF *</label>
        <input type="text" name="cif" id="cif" required>
    </div>

    <div class="form-group">
        <label for="nombre_empresa">Nombre empresa *</label>
        <input type="text" name="nombre_empresa" id="nombre_empresa" required>
    </div>

    <div class="form-group">
        <label for="email_empresa">Email empresa *</label>
        <input type="email" name="email_empresa" id="email_empresa" required>
    </div>

    <div class="form-group">
        <label for="telefono_empresa">Teléfono empresa</label>
        <input type="text" name="telefono_empresa" id="telefono_empresa">
    </div>

    <div class="form-group">
        <label for="web_empresa">Web</label>
        <input type="url" name="web_empresa" id="web_empresa">
    </div>

    <div class="form-group">
        <label for="persona_contacto">Persona de contacto</label>
        <input type="text" name="persona_contacto" id="persona_contacto">
    </div>

    <div class="form-group">
        <label for="direccion_empresa">Dirección</label>
        <input type="text" name="direccion_empresa" id="direccion_empresa">
    </div>

    <div class="form-group">
        <label for="cp_empresa">CP</label>
        <input type="text" name="cp_empresa" id="cp_empresa">
    </div>

    <div class="form-group">
        <label for="ciudad_empresa">Ciudad</label>
        <input type="text" name="ciudad_empresa" id="ciudad_empresa">
    </div>

    <div class="form-group">
        <label for="provincia_empresa">Provincia</label>
        <input type="text" name="provincia_empresa" id="provincia_empresa">
    </div>

    <h2>Datos del usuario responsable</h2>

    <div class="form-group">
        <label for="nombre_usuario">Nombre *</label>
        <input type="text" name="nombre_usuario" id="nombre_usuario" required>
    </div>

    <div class="form-group">
        <label for="apellidos_usuario">Apellidos *</label>
        <input type="text" name="apellidos_usuario" id="apellidos_usuario" required>
    </div>

    <div class="form-group">
        <label for="email_usuario">Email usuario *</label>
        <input type="email" name="email_usuario" id="email_usuario" required>
    </div>

    <div class="form-group">
        <label for="telefono_usuario">Teléfono usuario *</label>
        <input type="text" name="telefono_usuario" id="telefono_usuario" required>
    </div>

    <div class="form-group">
        <label for="password">Contraseña *</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div class="form-group">
        <label for="password2">Repite la contraseña *</label>
        <input type="password" name="password2" id="password2" required>
    </div>

    <button type="submit" name="registrar">Registrar empresa</button>
</form>
</div>

<?php include './includes/footer.php'; ?>