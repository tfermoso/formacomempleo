<?php

require_once __DIR__ . "/../includes/funciones.php";

$conn = conectarBD();
$mensaje = "";



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // Traemos también nombre/apellidos del usuario y nombre de la empresa
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
        // Guardamos en sesión todo lo necesario
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
    <div class="alert alert-error"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<?php if (isset($_GET["msg"])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET["msg"]); ?>
    </div>
<?php endif; ?>

<form method="post">
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Password: <input type="password" name="password" required></label><br><br>
    <button type="submit" class="boton nuevo">Entrar</button>
</form>

<p style="margin-top:10px;">
    <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
</p>

<?php include __DIR__ . '/includes/footer.php'; ?>