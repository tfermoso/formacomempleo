<?php
session_start();
require_once 'includes/config.php';

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
    <title>Iniciar sesión</title>
</head>
<body>

<h2>Iniciar sesión</h2>

<?php if ($mensaje): ?>
    <p><?php echo $mensaje; ?></p>
<?php endif; ?>

<form method="POST">
    Email:<br>
    <input type="email" name="email" required><br><br>

    Contraseña:<br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Entrar</button>
</form>

</body>
</html>
<?php include 'includes/footer.php'; ?>
