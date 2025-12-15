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
    <title>Registro</title>
</head>
<body>

<h2>Registro</h2>

<?php if ($mensaje): ?>
    <p><?php echo $mensaje; ?></p>
<?php endif; ?>

<form method="POST">

    DNI:<br>
    <input type="text" name="dni"><br><br>

    Nombre:*<br>
    <input type="text" name="nombre" required><br><br>

    Apellidos:*<br>
    <input type="text" name="apellidos" required><br><br>

    Teléfono:<br>
    <input type="text" name="telefono"><br><br>

    Email:*<br>
    <input type="email" name="email" required><br><br>

    Contraseña:*<br>
    <input type="password" name="password" required><br><br>

    LinkedIn:<br>
    <input type="text" name="linkedin"><br><br>

    Web personal:<br>
    <input type="text" name="web"><br><br>

    Dirección:<br>
    <input type="text" name="direccion"><br><br>

    Código postal:<br>
    <input type="text" name="cp"><br><br>

    Ciudad:<br>
    <input type="text" name="ciudad"><br><br>

    Provincia:<br>
    <input type="text" name="provincia"><br><br>

    Fecha de nacimiento:<br>
    <input type="date" name="fecha_nacimiento"><br><br>

    <button type="submit">Registrarse</button>
</form>

<br>
<a href="login.php">Volver al login</a>

</body>
</html>
<?php include 'includes/footer.php'; ?>
