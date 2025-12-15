<?php
session_start();
require_once '../includes/config.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password_hash FROM candidatos WHERE email = ? AND deleted_at IS NULL");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $candidato = $resultado->fetch_assoc();

        if (password_verify($password, $candidato['password_hash'])) {
            $_SESSION['idcandidato'] = $candidato['id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "No existe una cuenta con ese email.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión | Formacom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    <?php include '../candidatos/includes/header.php'; ?>

    <main class="contenido-empresa login-form">
        <h1>Iniciar sesión</h1>

        <?php if ($mensaje): ?>
            <div class="mensaje-error"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <div class="login-links">
            <a href="registro.php"><strong>¿No tienes cuenta?</strong> Regístrate</a><br>
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>
    </main>

    <footer class="footer-empresa">
        &copy; <?php echo date('Y'); ?> Formacom Empleo. Todos los derechos reservados.
    </footer>
</body>

</html>