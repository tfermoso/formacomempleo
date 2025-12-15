<?php
session_start();
require_once "../../../conexion.php"; // Conexión mysqli

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ../../dashboard.php?tab=usuarios');
    exit;
}
$id = (int)$_GET['id'];

// Obtener usuario
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header('Location: ../../dashboard.php?tab=usuarios');
    exit;
}

// Obtener empresas
$empresas_result = $db->query("SELECT id, nombre FROM empresas");
$empresas = [];
while ($row = $empresas_result->fetch_assoc()) {
    $empresas[] = $row;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $idempresa = $_POST['idempresa'] ?: null;
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($nombre) || empty($apellidos) || empty($email)) {
        $error = "Nombre, apellidos y email son obligatorios.";
    } else {
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE usuarios SET nombre=?, apellidos=?, telefono=?, email=?, password_hash=?, idempresa=?, is_admin=? WHERE id=?");
            $stmt->bind_param("sssssiii", $nombre, $apellidos, $telefono, $email, $password_hash, $idempresa, $is_admin, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $db->prepare("UPDATE usuarios SET nombre=?, apellidos=?, telefono=?, email=?, idempresa=?, is_admin=? WHERE id=?");
            $stmt->bind_param("ssssiii", $nombre, $apellidos, $telefono, $email, $idempresa, $is_admin, $id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: ../../dashboard.php?tab=usuarios");
        exit;
    }
}
?>
<link rel="stylesheet" href="../styles.css">
<form method="POST">
    <h2>Editar Usuario</h2>
    <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
    Nombre*: <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>"><br>
    Apellidos*: <input type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>"><br>
    Teléfono: <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>"><br>
    Email*: <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>"><br>
    Contraseña: <input type="password" name="password" placeholder="Dejar vacío si no cambia"><br>
    Empresa:
    <select name="idempresa">
        <option value="">Ninguna</option>
        <?php foreach($empresas as $e): ?>
            <option value="<?= $e['id'] ?>" <?= $e['id']==$usuario['idempresa']?'selected':'' ?>><?= htmlspecialchars($e['nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>
    
    <button type="submit">Actualizar</button>
    <a href="../../dashboard.php?tab=usuarios">Cancelar</a>
</form>
