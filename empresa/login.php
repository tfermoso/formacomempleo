<?php
require_once __DIR__ . "/../includes/funciones.php";

$conn = conectarBD();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $stmt = $conn->prepare("SELECT u.id, u.idempresa, u.password_hash, u.nombre, u.apellidos, e.nombre AS empresa_nombre
                            FROM usuarios u
                            INNER JOIN empresas e ON u.idempresa = e.id
                            WHERE u.email = ? AND u.deleted_at IS NULL");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    if ($usuario && password_verify($password, $usuario["password_hash"])) {
        $_SESSION["idusuario"] = $usuario["id"];
        $_SESSION["idempresa"] = $usuario["idempresa"];
        $_SESSION["usuario_nombre"] = $usuario["nombre"] . " " . $usuario["apellidos"];
        $_SESSION["empresa_nombre"] = $usuario["empresa_nombre"];

        header("Location: dashboard.php");
        exit;
    } else {
        $mensaje = "Credenciales incorrectas.";
    }
}

include './includes/header.php';
?>

<h1>Login Empresa</h1>

<?php if ($mensaje): ?>
    <div class="mensaje-error"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<?php if (isset($_GET["msg"])): ?>
    <div class="mensaje-success">
        <?php echo htmlspecialchars($_GET["msg"]); ?>
    </div>
<?php endif; ?>

<div class="form-container login-form">
    <form method="post">
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña *</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">Entrar</button>

        <div class="login-links">
            <a href="./registro.php"><strong>¿No tienes cuenta?</strong> Regístrate</a>
            <a href="./recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>
    </form>
</div>

<?php include './includes/footer.php'; ?>