<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/funciones.php";

if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
if(isset($_SESSION["idcandidato"])&& $_SESSION["idcandidato"]!="" && isset($_SESSION["candidato_nombre"])&& $_SESSION["candidato_nombre"]!=""){
    header("Location: dashboard.php");
    exit;
}
$conn = conectarBD();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // 🔹 CONSULTA A LA TABLA CANDIDATOS
    $stmt = $conn->prepare("
        SELECT id, password_hash, nombre, apellidos
        FROM candidatos
        WHERE email = ? AND deleted_at IS NULL
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $candidato = $resultado->fetch_assoc();
    $stmt->close();

    if ($candidato && password_verify($password, $candidato["password_hash"])) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION["idcandidato"] = $candidato["id"];
        $_SESSION["candidato_nombre"] = $candidato["nombre"] . " " . $candidato["apellidos"];

        header("Location: dashboard.php");
        exit;
    } else {
        $mensaje = "Credenciales incorrectas.";
    }
}

include './includes/header.php';
?>

<h1>Login Candidato</h1>

<?php if ($mensaje): ?>
    <div class="mensaje-error"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION["msg"])): ?>
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