<?php
require_once ("includes/config.php");
$errores = [];
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitizar entradas
    $dni = trim($_POST["dni"]);
    $nombre = trim($_POST["nombre"]);
    $apellidos = trim($_POST["apellidos"]);
    $telefono = trim($_POST["telefono"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password2 = trim($_POST["password2"]);
    $linkedin = trim($_POST["linkedin"]);
    $web = trim($_POST["web"]);
    $direccion = trim($_POST["direccion"]);
    $cp = trim($_POST["cp"]);
    $ciudad = trim($_POST["ciudad"]);
    $provincia = trim($_POST["provincia"]);
    $fecha_nacimiento = trim($_POST["fecha_nacimiento"]);

    // VALIDACIONES -----------------------------

    if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
    if (empty($apellidos)) $errores[] = "Los apellidos son obligatorios.";
    if(empty($fecha_nacimiento)) $errores[]= "La fecha de nacimiento es obligatoria.";

    if (empty($email)) {
        $errores[] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no tiene un formato válido.";
    }

    if (empty($password) || empty($password2)) {
        $errores[] = "Debes escribir la contraseña dos veces.";
    } elseif ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (!empty($dni) && !preg_match("/^[0-9A-Z]{7,10}$/i", $dni)) {
        $errores[] = "El DNI no es válido.";
    }

    if (!empty($telefono) && !preg_match("/^[0-9]{9}$/", $telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }

     if (!empty($fecha_nacimiento)) {
        $fecha_nac = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nac)->y;

        if ($edad < 18) {
            $errores[] = "Debes ser mayor de 18 años para registrarte.";
        }
    }
    // VALIDAR ARCHIVOS -----------------------------

 // ✔ Validar PDF del CV
    $cv_ruta_final = null;
    if (!empty($_FILES["cv"]["name"])) {
        $cv = $_FILES["cv"];
        $extension_cv = strtolower(pathinfo($cv["name"], PATHINFO_EXTENSION));

        if ($extension_cv !== "pdf") {
            $errores[] = "El CV debe ser un archivo PDF.";
        } elseif ($cv["size"] > 5 * 1024 * 1024) { // 5 MB
            $errores[] = "El CV no puede superar los 5 MB.";
        }
    }

    // ✔ Validar FOTO
    $foto_ruta_final = null;
    if (!empty($_FILES["foto"]["name"])) {
        $foto = $_FILES["foto"];
        $extension_foto = strtolower(pathinfo($foto["name"], PATHINFO_EXTENSION));

        if (!in_array($extension_foto, ["jpg", "jpeg", "png"])) {
            $errores[] = "La foto debe ser JPG o PNG.";
        } elseif ($foto["size"] > 3 * 1024 * 1024) { // 3 MB
            $errores[] = "La foto no puede superar los 3 MB.";
        }
    }

    // SI NO HAY ERRORES → INSERTAR
    if (empty($errores)) {

       

        // Verificar duplicados email
        $sql = "SELECT id FROM candidatos WHERE email = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errores[] = "El email ya está registrado.";
        }

        // Verificar duplicados DNI
        if (!empty($dni)) {
            $sql = "SELECT id FROM candidatos WHERE dni = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("s", $dni);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errores[] = "El DNI ya está registrado.";
            }
        }

        // Sube CV si es válido
        if (empty($errores) && !empty($_FILES["cv"]["name"])) {
            $cv_ruta_final = "uploads/cv/" . uniqid("CV_") . ".pdf";
            move_uploaded_file($_FILES["cv"]["tmp_name"], $cv_ruta_final);
        }

        // Sube FOTO si es válida
        if (empty($errores) && !empty($_FILES["foto"]["name"])) {
            $foto_ruta_final = "uploads/fotos/" . uniqid("FOTO_") . "." . $extension_foto;
            move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_ruta_final);
        }

        // Insertar si sigue sin errores
        if (empty($errores)) {

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO candidatos 
                (dni, nombre, apellidos, telefono, email, password_hash, linkedin, web, direccion, cp, ciudad, provincia, fecha_nacimiento, cv, foto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
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
                $cv_ruta_final,
                $foto_ruta_final
            );

            if ($stmt->execute()) {
                $exito = "Registro completado con éxito.";
            } else {
                $errores[] = "Error al guardar: " . $db->error;
            }
        }

        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Candidato</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="favicon/favicon.ico">

</head>
<body>

<h2>Registro de Candidato</h2>

<?php if (!empty($errores)): ?>
    <div  class="error-mensaje">
       
            <?php foreach ($errores as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        
    </div>
<?php endif; ?>

<?php if ($exito): ?>
    <div class="exito-mensaje"><?= $exito ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <p>Los campos con <span class="obligatorio">*</span> son obligatorios.</p>

   <label>DNI<span class="obligatorio"><span class="obligatorio">*</span></span>: </label> <input type="text" name="dni" required><br><br>

    <label>Nombre<span class="obligatorio">*</span>: </label><input type="text" name="nombre" required><br><br>

    <label>Apellidos<span class="obligatorio">*</span>: </label><input type="text" name="apellidos" required><br><br>

    <label>Teléfono<span class="obligatorio">*</span>: </label><input type="text" name="telefono" required><br><br>

    <label>Email<span class="obligatorio">*</span>: </label><input type="email" name="email" required><br><br>

    <label>Contraseña<span class="obligatorio">*</span>: </label><input type="password" name="password" required><br><br>

    <label>Repetir contraseña<span class="obligatorio">*</span>: </label><input type="password" name="password2" required><br><br>

    <label>LinkedIn: </label><input type="url" name="linkedin"><br><br>

    <label>Web personal: </label><input type="url" name="web"><br><br>

    <label>Dirección: </label><input type="text" name="direccion"><br><br>

    <label>Código Postal: </label><input type="text" name="cp"><br><br>

    <label>Ciudad: </label><input type="text" name="ciudad"><br><br>

    <label>Provincia: </label><input type="text" name="provincia"><br><br>

    <label>Fecha de nacimiento<span class="obligatorio">*</span>: </label><input type="date" name="fecha_nacimiento"><br><br>

   
</label>
   <div id="preview-container">
    <img id="preview" src="#" alt="Previsualización" style="display:none;">
</div>

<label>Foto (JPG/PNG máx 3MB): </label>
<input type="file" name="foto" accept="image/jpeg, image/png" id="foto-input">
<label>Curriculum (PDF máx 5MB)<span class="obligatorio">*</span>: </label>
    <input type="file" name="cv" accept="application/pdf" required><br><br>

    <button type="submit">Registrarse</button>
  
</form>

<p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</div>

<script>
const inputFoto = document.getElementById('foto-input');
const previewImg = document.getElementById('preview');

inputFoto.addEventListener('change', function(){
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            previewImg.setAttribute('src', e.target.result);
            previewImg.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewImg.style.display = 'none';
    }
});
</script>

</body>
</html>
<?php include 'includes/footer.php'; ?>
