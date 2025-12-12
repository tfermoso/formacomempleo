<?php
require_once __DIR__ . "/includes/functions.php";

$conn = conectarBD();


$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $stmt = $conn->prepare("SELECT id, idempresa, password_hash FROM usuarios WHERE email = ? AND deleted_at IS NULL");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    if ($usuario && password_verify($password, $usuario["password_hash"])) {
        $_SESSION["idusuario"] = $usuario["id"];
        $_SESSION["idempresa"] = $usuario["idempresa"];
        header("Location: dashboard.php");
        exit;
    } else {
        $mensaje = "Credenciales incorrectas.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Empresa</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>Login Empresa</h1>
    <?php if ($mensaje): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Email: <input type="email" name="email" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br><br>
        <button type="submit" class="boton nuevo">Entrar</button>
    </form>
</body>

</html>