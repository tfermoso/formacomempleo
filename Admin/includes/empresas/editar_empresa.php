<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) { header('Location: ../../index.php'); exit; }

if (!isset($_GET['id'])) { header('Location: ../../dashboard.php?tab=empresas'); exit; }
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
$stmt->execute([$id]);
$empresa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empresa) { header('Location: ../../dashboard.php?tab=empresas'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cif = trim($_POST['cif']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email_contacto']);
    $direccion = trim($_POST['direccion']);
    $cp = trim($_POST['cp']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);

    if (empty($cif) || empty($nombre) || empty($email)) {
        $error = "CIF, nombre y email son obligatorios";
    } else {
        $stmt = $pdo->prepare("UPDATE empresas SET cif=?, nombre=?, telefono=?, email_contacto=?, direccion=?, cp=?, ciudad=?, provincia=? WHERE id=?");
        $stmt->execute([$cif,$nombre,$telefono,$email,$direccion,$cp,$ciudad,$provincia,$id]);
        header("Location: ../../dashboard.php?tab=empresas");
        exit;
    }
}
?>
<link rel="stylesheet" href="../styles.css">
<form method="POST">
    <h2>Editar Empresa</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    CIF*: <input type="text" name="cif" value="<?= htmlspecialchars($empresa['cif']) ?>"><br>
    Nombre*: <input type="text" name="nombre" value="<?= htmlspecialchars($empresa['nombre']) ?>"><br>
    Teléfono: <input type="text" name="telefono" value="<?= htmlspecialchars($empresa['telefono']) ?>"><br>
    Email*: <input type="email" name="email_contacto" value="<?= htmlspecialchars($empresa['email_contacto']) ?>"><br>
    Dirección: <input type="text" name="direccion" value="<?= htmlspecialchars($empresa['direccion']) ?>"><br>
    CP: <input type="text" name="cp" value="<?= htmlspecialchars($empresa['cp']) ?>"><br>
    Ciudad: <input type="text" name="ciudad" value="<?= htmlspecialchars($empresa['ciudad']) ?>"><br>
    Provincia: <input type="text" name="provincia" value="<?= htmlspecialchars($empresa['provincia']) ?>"><br>
    <button type="submit">Actualizar</button>
    <a href="../../dashboard.php?tab=empresas">Cancelar</a>
</form>
