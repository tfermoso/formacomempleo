<?php
session_start();
require_once "../conexion.php"; // archivo con $pdo

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        // Busca el usuario admin
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND is_admin = 1 LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Convertimos la contraseña introducida a SHA-256
            $password_sha256 = hash('sha256', $password);

            if ($password_sha256 === $usuario['password_hash']) {
                // Credenciales correctas
                $_SESSION['admin_login'] = $usuario['id'];
                $_SESSION['admin_nombre'] = $usuario['nombre'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Credenciales incorrectas o no eres administrador";
            }
        } else {
            $error = "Credenciales incorrectas o no eres administrador";
        }
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
