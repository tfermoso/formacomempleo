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

<?php

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

    <?php include '../includes/header.php'; ?>

    <main class="container">
        <h2 class="hero-title">Iniciar sesión</h2>

        <?php if ($mensaje): ?>
            <div class="alert"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST" class="form-card">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required class="form-input">

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required class="form-input">

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </main>

    <?php include '../includes/footer.php'; ?>

</body>

</html>