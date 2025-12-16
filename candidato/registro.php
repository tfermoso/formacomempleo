<?php
require_once __DIR__ . "/../includes/config.php";

require_once __DIR__ . "/../includes/funciones.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = conectarBD();
$errores = [];
$exito = "";

// CSRF
$tokenCSRF = generarTokenCSRF();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =========================
    // 1. CSRF
    // =========================
    if (!isset($_POST["csrf_token"]) || !comprobarTokenCSRF($_POST["csrf_token"])) {
        $errores[] = "Petición no válida.";
    }

    // =========================
    // 2. Datos
    // =========================
    $dni = limpiarTexto($_POST["dni"] ?? "");
    $nombre = limpiarTexto($_POST["nombre"] ?? "");
    $apellidos = limpiarTexto($_POST["apellidos"] ?? "");
    $telefono = limpiarTexto($_POST["telefono"] ?? "");
    $email = limpiarTexto($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $password2 = $_POST["password2"] ?? "";
    $linkedin = limpiarTexto($_POST["linkedin"] ?? "");
    $web = limpiarTexto($_POST["web"] ?? "");
    $direccion = limpiarTexto($_POST["direccion"] ?? "");
    $cp = limpiarTexto($_POST["cp"] ?? "");
    $ciudad = limpiarTexto($_POST["ciudad"] ?? "");
    $provincia = limpiarTexto($_POST["provincia"] ?? "");
    $fecha_nacimiento = $_POST["fecha_nacimiento"] ?? "";

    // =========================
    // 3. Validaciones
    // =========================
    if (empty($nombre))
        $errores[] = "El nombre es obligatorio.";
    if (empty($apellidos))
        $errores[] = "Los apellidos son obligatorios.";
    if (empty($fecha_nacimiento))
        $errores[] = "La fecha de nacimiento es obligatoria.";

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
    if (!empty($fecha_nacimiento)) {
        $fn = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        if ($hoy->diff($fn)->y < 18) {
            $errores[] = "Debes ser mayor de 18 años para registrarte.";
        }
    }

    // =========================
    // 4. Duplicados
    // =========================
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

    // =========================
    // 5. Subida de archivos
    // =========================
    $cvRuta = null;
    $fotoRuta = null;

    if (empty($errores) && !empty($_FILES["cv"]["name"])) {
        $cvRuta = "../uploads/cv/" . uniqid("CV_") . ".pdf";
        move_uploaded_file($_FILES["cv"]["tmp_name"], $cvRuta);
    }

    if (empty($errores) && !empty($_FILES["foto"]["name"])) {
        $ext = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
        $fotoRuta = "../uploads/fotos/" . uniqid("FOTO_") . "." . $ext;
        move_uploaded_file($_FILES["foto"]["tmp_name"], $fotoRuta);
    }

    // =========================
    // 6. Insert
    // =========================
    if (empty($errores)) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

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
            $password_hash,
            $linkedin,
            $web,
            $direccion,
            $cp,
            $ciudad,
            $provincia,
            $fecha_nacimiento,
            $cvRuta,
            $fotoRuta
        );

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Registro completado correctamente";
            header("Location: login.php");
            exit;
           
        } else {
            $errores[] = "Error al registrar el candidato.";
        }

        $stmt->close();
    }
}
// =========================
// FORMULARIO
// =========================

include './includes/header.php';
?>

<h1>Registro de Candidato</h1>

<?php if (!empty($errores)): ?>
    <div class="error-mensaje">
        <?php foreach ($errores as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($exito): ?>
    <div class="exito-mensaje"><?= $exito ?></div>
<?php endif; ?>
<div class="form-container">
    <form method="POST" enctype="multipart/form-data" class="registro-form">

        <input type="hidden" name="csrf_token" value="<?= $tokenCSRF ?>">

        <h2>Datos personales</h2>

        <div class="form-group">
            <label for="dni">DNI *</label>
            <input type="text" name="dni" id="dni" required>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre *</label>
            <input type="text" name="nombre" id="nombre" required>
        </div>

        <div class="form-group">
            <label for="apellidos">Apellidos *</label>
            <input type="text" name="apellidos" id="apellidos" required>
        </div>
        <div class="form-group">
            <label for="fecha_nacimiento">Fecha de nacimiento *</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono *</label>
            <input type="text" name="telefono" id="telefono" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña *</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div class="form-group">
            <label for="password2">Repite la contraseña *</label>
            <input type="password" name="password2" id="password2" required>
        </div>
        <div class="form-group">
            <label for="cv">Currículum (PDF máx 5MB) *</label>
            <input type="file" name="cv" accept="application/pdf" required>
        </div>

        <h2>Información adicional</h2>

        <div class="form-group">
            <label for="linkedin">LinkedIn</label>
            <input type="url" name="linkedin" id="linkedin">
        </div>

        <div class="form-group">
            <label for="web">Web personal</label>
            <input type="url" name="web" id="web">
        </div>

        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion">
        </div>

        <div class="form-group">
            <label for="cp">Código Postal</label>
            <input type="text" name="cp" id="cp">
        </div>

        <div class="form-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" name="ciudad" id="ciudad">
        </div>

        <div class="form-group">
            <label for="provincia">Provincia</label>
            <input type="text" name="provincia" id="provincia">
        </div>



        <div class="form-group">
            <label for="foto">Foto (JPG/PNG máx 3MB)</label>
            <input type="file" name="foto" id="foto-input" accept="image/jpeg, image/png">
        </div>

        <div id="preview-container" class="form-group">
            <img id="preview" src="#" alt="Previsualización" style="display:none;">
        </div>



        <button type="submit" class="btn-primary">Registrarse</button>

        <div class="login-links">
            <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
        </div>

    </form>
</div>


<script>
    const inputFoto = document.getElementById('foto-input');
    const previewImg = document.getElementById('preview');

    inputFoto.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.style.display = 'none';
        }
    });
</script>


<?php include './includes/footer.php'; ?>