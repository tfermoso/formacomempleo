<?php
session_start();
require_once 'includes/config.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $linkedin = trim($_POST['linkedin']);
    $web = trim($_POST['web']);
    $direccion = trim($_POST['direccion']);
    $cp = trim($_POST['cp']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);

    // Comprobar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM candidatos WHERE email = ? AND deleted_at IS NULL");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $mensaje = "Ya existe una cuenta con ese email.";
    } else {

        // Hashear contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo candidato
        $stmt = $conn->prepare("
            INSERT INTO candidatos 
            (dni, nombre, apellidos, telefono, email, password_hash, linkedin, web, direccion, cp, ciudad, provincia, fecha_nacimiento, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->bind_param(
            "sssssssssssss",
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
            $fecha_nacimiento
        );

        if ($stmt->execute()) {
            $mensaje = "Registro completado. Ya puedes iniciar sesión.";
        } else {
            $mensaje = "Error al registrar usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro | Formacom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/registro.css">
</head>

<body>

    <?php include '../candidatos/includes/header.php'; ?>

    <main class="contenido-empresa">
        <h1>Registro</h1>

        <?php if ($mensaje): ?>
            <div class="alert"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" name="dni" id="dni">
            </div>

            <div class="form-group">
                <label for="nombre">Nombre*</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos*</label>
                <input type="text" name="apellidos" id="apellidos" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono">
            </div>

            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña*</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label for="linkedin">LinkedIn</label>
                <input type="text" name="linkedin" id="linkedin">
            </div>

            <div class="form-group">
                <label for="web">Web personal</label>
                <input type="text" name="web" id="web">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion">
            </div>

            <div class="form-group">
                <label for="cp">Código postal</label>
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
                <label for="fecha_nacimiento">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">
            </div>

            <button type="submit" class="btn btn-primary">Registrarse</button>
        </form>

        <p style="text-align:center; margin-top:1rem;">
            <a href="login.php" class="btn">Volver al login</a>
        </p>
    </main>

    <?php include '../candidatos/includes/footer.php'; ?>

</body>

</html>