<?php

session_start();
require_once "../conexion.php"; // usa $db (mysqli)

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {

        // Buscar usuario administrador
        $sql = "SELECT id, nombre, password_hash 
                FROM usuarios 
                WHERE email = ? AND is_admin = 1 
                LIMIT 1";

        $stmt = $db->prepare($sql);

        if (!$stmt) {
            die("Error en prepare(): " . $db->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        if ($usuario) {

            // Convertir la contraseña introducida a SHA-256
            $password_sha256 = hash('sha256', $password);

            if ($password_sha256 === $usuario['password_hash']) {

                // Login correcto
                $_SESSION['admin_login'] = $usuario['id'];
                $_SESSION['admin_nombre'] = $usuario['nombre'];

                header("Location: dashboard.php");
                exit;

            } else {
                $error = "Credenciales incorrectas o no eres administrador";
            }

        } else {
            $error = "Credenciales incorrectas o no eres administrador";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador</title>
     <link rel="stylesheet" href="includes/styles.css">
</head>
<body>
<h2>Login Administrador</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Iniciar Sesión</button>
</form>
</body>
</html>
