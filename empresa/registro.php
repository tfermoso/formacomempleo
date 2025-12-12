<?php

require_once '../includes/funciones.php';
require_once '../includes/conexion.php';

$tokenCSRF = generarTokenCSRF();


//Procesamiento del formulario en el mismo
require_once '../includes/funciones.php';
require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];

    // 1. Comprobar CSRF
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

    // Empresa
    if (empty($cif) || !validarCIF($cif)) {
        $errores[] = "El CIF no es válido.";
    }

    if (empty($nombreEmpresa)) {
        $errores[] = "El nombre de la empresa es obligatorio.";
    }

    if (empty($emailEmpresa) || !filter_var($emailEmpresa, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email de la empresa no es válido.";
    }

    // Usuario
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

    // 4. Comprobar que no exista empresa con ese CIF
    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT id FROM empresas WHERE cif = ?");
        $stmt->execute([$cif]);
        if ($stmt->fetch()) {
            $errores[] = "Ya existe una empresa registrada con ese CIF.";
        }
    }

    // 5. Comprobar que no exista usuario con ese email
    if (empty($errores)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$emailUsuario]);
        if ($stmt->fetch()) {
            $errores[] = "Ya existe un usuario registrado con ese email.";
        }
    }

    if (!empty($errores)) {
        // De momento, redirigimos con el primer error
        $error = urlencode($errores[0]);
        header("Location: registro.php?error=$error");
        exit;
    }

    // 6. Insertar empresa + usuario en una transacción
    try {
        $pdo->beginTransaction();

        $sqlEmpresa = "INSERT INTO empresas 
            (cif, nombre, telefono, web, persona_contacto, email_contacto, direccion, cp, ciudad, provincia, logo, verificada)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, 0)";
        $stmtEmpresa = $pdo->prepare($sqlEmpresa);
        $stmtEmpresa->execute([
            $cif,
            $nombreEmpresa,
            $telefonoEmpresa ?: null,
            $webEmpresa ?: null,
            $personaContacto ?: null,
            $emailEmpresa,
            $direccion ?: null,
            $cp ?: null,
            $ciudad ?: null,
            $provincia ?: null
        ]);

        $idEmpresa = $pdo->lastInsertId();

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sqlUsuario = "INSERT INTO usuarios 
            (nombre, apellidos, telefono, email, password_hash, idempresa, is_admin)
            VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmtUsuario = $pdo->prepare($sqlUsuario);
        $stmtUsuario->execute([
            $nombreUsuario,
            $apellidosUsuario,
            $telefonoUsuario,
            $emailUsuario,
            $passwordHash,
            $idEmpresa
        ]);

        $pdo->commit();

        // Aquí podrías enviar email de confirmación con PHPMailer

        header("Location: ../index.php?registro=ok");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        // Para depuración puedes hacer log del error
        $error = urlencode("Error al registrar la empresa. Inténtalo de nuevo.");
        header("Location: registro.php?error=$error");
        exit;
    }
}

$tokenCSRF = generarTokenCSRF();
?>



//Formulario de registro empresa
// Aquí podrías gestionar mensajes de error/éxito
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de empresa</title>
    <script src="../assets/js/registro_empresa.js" defer></script>
</head>

<body>
    <h1>Registro de empresa</h1>

    <?php
    // Mostrar errores si los hay (se puede mejorar más adelante)
    if (!empty($_GET['error'])) {
        echo '<p style="color:red;">' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>

    <form action="registro.php" method="post" id="formRegistroEmpresa" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo $tokenCSRF; ?>">

        <h2>Datos de la empresa</h2>

        <label for="cif">CIF *</label>
        <input type="text" name="cif" id="cif" required>

        <label for="nombre_empresa">Nombre empresa *</label>
        <input type="text" name="nombre_empresa" id="nombre_empresa" required>

        <label for="email_empresa">Email empresa *</label>
        <input type="email" name="email_empresa" id="email_empresa" required>

        <label for="telefono_empresa">Teléfono empresa</label>
        <input type="text" name="telefono_empresa" id="telefono_empresa">

        <label for="web_empresa">Web</label>
        <input type="url" name="web_empresa" id="web_empresa">

        <label for="persona_contacto">Persona de contacto</label>
        <input type="text" name="persona_contacto" id="persona_contacto">

        <label for="direccion_empresa">Dirección</label>
        <input type="text" name="direccion_empresa" id="direccion_empresa">

        <label for="cp_empresa">CP</label>
        <input type="text" name="cp_empresa" id="cp_empresa">

        <label for="ciudad_empresa">Ciudad</label>
        <input type="text" name="ciudad_empresa" id="ciudad_empresa">

        <label for="provincia_empresa">Provincia</label>
        <input type="text" name="provincia_empresa" id="provincia_empresa">

        <h2>Datos del usuario responsable</h2>

        <label for="nombre_usuario">Nombre *</label>
        <input type="text" name="nombre_usuario" id="nombre_usuario" required>

        <label for="apellidos_usuario">Apellidos *</label>
        <input type="text" name="apellidos_usuario" id="apellidos_usuario" required>

        <label for="email_usuario">Email usuario *</label>
        <input type="email" name="email_usuario" id="email_usuario" required>

        <label for="telefono_usuario">Teléfono usuario *</label>
        <input type="text" name="telefono_usuario" id="telefono_usuario" required>

        <label for="password">Contraseña *</label>
        <input type="password" name="password" id="password" required>

        <label for="password2">Repite la contraseña *</label>
        <input type="password" name="password2" id="password2" required>

        <button type="submit" name="registrar">Registrar empresa</button>
    </form>
</body>

</html>