<?php
require_once "../includes/conexion.php";

$token = $_GET["token"] ?? "";

$sql = $mysqli->prepare("SELECT id FROM usuarios WHERE token_recuperacion=? AND token_expira > NOW()");
$sql->bind_param("s", $token);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 0) {
    echo "Token inválido o expirado.";
    exit;
}
?>

<form action="restablecer_guardar.php" method="POST">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
    <label>Nueva contraseña:</label>
    <input type="password" name="password" required>
    <button type="submit">Guardar</button>
</form>