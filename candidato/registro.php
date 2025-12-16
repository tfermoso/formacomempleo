<?php
session_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';

$conn = conectarBD();
$errores = [];

// CSRF
$tokenCSRF = generarTokenCSRF();

/* =========================
   PROCESAR FORMULARIO
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. CSRF
    if (!isset($_POST['csrf_token']) || !comprobarTokenCSRF($_POST['csrf_token'])) {
        $errores[] = "Petición no válida.";
    }

    // 2. Recoger datos
    $dni              = limpiarTexto($_POST['dni'] ?? '');
    $nombre           = limpiarTexto($_POST['nombre'] ?? '');
    $apellidos        = limpiarTexto($_POST['apellidos'] ?? '');
    $telefono         = limpiarTexto($_POST['telefono'] ?? '');
    $email            = limpiarTexto($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password2        = $_POST['password2'] ?? '';
    $linkedin         = limpiarTexto($_POST['linkedin'] ?? '');
    $web              = limpiarTexto($_POST['web'] ?? '');
    $direccion        = limpiarTexto($_POST['direccion'] ?? '');
    $cp               = limpiarTexto($_POST['cp'] ?? '');
    $ciudad           = limpiarTexto($_POST['ciudad'] ?? '');
    $provincia        = limpiarTexto($_POST['provincia'] ?? '');
    $fechaNacimiento  = $_POST['fecha_nacimiento'] ?? '';

    // 3. Validaciones
    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($apellidos)) $errores[] = "Los apellidos son obligatorios.";
    if (empty($fechaNacimiento)) $errores[] = "La fecha de nacimiento es obligatoria.";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido.";
    }

    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    if ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (!empty($dni) && !preg_match("/^[0-9A-Z]{7,10}$/i", $dni)) {
        $errores[] = "El DNI no es válido.";
    }

    if (!empty($telefono) && !preg_match("/^[0-9]{9}$/", $telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }

    // Mayor de edad
    if (!empty($fechaNacimiento)) {
        $fn = new DateTime($fechaNacimiento);
        $hoy = new DateTime();
        if ($hoy->diff($fn)->y < 18) {
            $errores[] = "Debes ser mayor de 18 años.";
        }
    }

    // 4. Duplicados
    if (empty($errores)) {
        $stmt = $conn->prepare("SELECT id FROM candidatos WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errores[] = "El email ya está registrado.";
        }
        $stmt->close();
    }

    if (empty($errores) && $dni) {
        $stmt = $conn->prepare("SELECT id FROM candidatos WHERE dni = ?");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errores[] = "El DNI ya está registrado.";
        }
        $stmt->close();
    }

    // 5. Subida de archivos
    $cvRuta = null;
    $fotoRuta = null;

    if (empty($errores) && !empty($_FILES['cv']['name'])) {
        $cvRuta = subirArchivo($_FILES['cv'], 'uploads/cv', ['pdf','txt']);
        if (!$cvRuta) {
            $errores[] = "Error al subir el CV.";
        }
    }

    if (empty($errores) && !empty($_FILES['foto']['name'])) {
        $fotoRuta = subirArchivo($_FILES['foto'], 'uploads/fotos', ['jpg','jpeg','png']);
        if (!$fotoRuta) {
            $errores[] = "Error al subir la foto.";
        }
    }

    // 6. Insertar candidato
    if (empty($errores)) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO candidatos
            (dni, nombre, apellidos, telefono, email, password_hash, linkedin, web, direccion, cp, ciudad, provincia, fecha_nacimiento, cv, foto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssssssssss",
            $dni,
            $nombre,
            $apellidos,
            $telefono,
            $email,
            $passwordHash,
            $linkedin,
            $web,
            $direccion,
            $cp,
            $ciudad,
            $provincia,
            $fechaNacimiento,
            $cvRuta,
            $fotoRuta
        );

        if ($stmt->execute()) {
            header("Location: login.php?msg=Registro completado correctamente");
            exit;
        } else {
            $errores[] = "Error al registrar el candidato.";
        }

        $stmt->close();
    }
}

include __DIR__ . '/includes/header.php';
?>

<h1>Registro de candidato</h1>

<?php if (!empty($errores)): ?>
    <div class="mensaje-error">
        <?php foreach ($errores as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="form-container">
<form method="POST" enctype="multipart/form-data">

    <input type="hidden" name="csrf_token" value="<?= $tokenCSRF ?>">

    <h2>Datos personales</h2>

    <div class="form-group">
        <label>DNI *</label>
        <input type="text" name="dni" required>
    </div>

    <div class="form-group">
        <label>Nombre *</label>
        <input type="text" name="nombre" required>
    </div>

    <div class="form-group">
        <label>Apellidos *</label>
        <input type="text" name="apellidos" required>
    </div>

    <div class="form-group">
        <label>Fecha de nacimiento *</label>
        <input type="date" name="fecha_nacimiento" required>
    </div>

    <div class="form-group">
        <label>Teléfono *</label>
        <input type="text" name="telefono" required>
    </div>

    <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Contraseña *</label>
        <input type="password" name="password" required>
    </div>

    <div class="form-group">
        <label>Repetir contraseña *</label>
        <input type="password" name="password2" required>
    </div>

    <div class="form-group">
        <label>CV (PDF / TXT) *</label>
        <input type="file" name="cv" required>
    </div>

    <h2>Información adicional</h2>

    <div class="form-group">
        <label>LinkedIn</label>
        <input type="url" name="linkedin">
    </div>

    <div class="form-group">
        <label>Web / GitHub</label>
        <input type="url" name="web">
    </div>

    <div class="form-group">
        <label>Dirección</label>
        <input type="text" name="direccion">
    </div>

    <div class="form-group">
        <label>Código Postal</label>
        <input type="text" name="cp">
    </div>

    <div class="form-group">
        <label>Ciudad</label>
        <input type="text" name="ciudad">
    </div>

    <div class="form-group">
        <label>Provincia</label>
        <input type="text" name="provincia">
    </div>

    <div class="form-group">
        <label>Foto (JPG / PNG)</label>
        <input type="file" name="foto">
    </div>

    <button type="submit">Registrarse</button>

    <div class="login-links">
        <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
    </div>

</form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
