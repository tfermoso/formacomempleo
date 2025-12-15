<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ../../dashboard.php?tab=candidatos');
    exit;
}

$id = (int) $_GET['id'];

/* ================= OBTENER CANDIDATO ================= */
$stmt = $db->prepare("SELECT * FROM candidatos WHERE id = ? AND deleted_at IS NULL");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$candidato = $result->fetch_assoc();

if (!$candidato) {
    header('Location: ../../dashboard.php?tab=candidatos');
    exit;
}

$error = '';

/* ================= ACTUALIZAR ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $linkedin = trim($_POST['linkedin']);
    $web = trim($_POST['web']);
    $direccion = trim($_POST['direccion']);
    $cp = trim($_POST['cp']);
    $ciudad = trim($_POST['ciudad']);
    $provincia = trim($_POST['provincia']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    if (empty($nombre) || empty($apellidos) || empty($email)) {
        $error = "Nombre, apellidos y email son obligatorios.";
    } else {

        if (!empty($password)) {
            // Actualizar CON contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("
                UPDATE candidatos 
                SET dni=?, nombre=?, apellidos=?, telefono=?, email=?, password_hash=?,
                    linkedin=?, web=?, direccion=?, cp=?, ciudad=?, provincia=?, fecha_nacimiento=?
                WHERE id=?
            ");

            $stmt->bind_param(
                "sssssssssssssi",
                $dni, $nombre, $apellidos, $telefono, $email, $password_hash,
                $linkedin, $web, $direccion, $cp, $ciudad, $provincia,
                $fecha_nacimiento, $id
            );

        } else {
            // Actualizar SIN contraseña
            $stmt = $db->prepare("
                UPDATE candidatos 
                SET dni=?, nombre=?, apellidos=?, telefono=?, email=?,
                    linkedin=?, web=?, direccion=?, cp=?, ciudad=?, provincia=?, fecha_nacimiento=?
                WHERE id=?
            ");

            $stmt->bind_param(
                "ssssssssssssi",
                $dni, $nombre, $apellidos, $telefono, $email,
                $linkedin, $web, $direccion, $cp, $ciudad,
                $provincia, $fecha_nacimiento, $id
            );
        }

        $stmt->execute();
        header("Location: ../../dashboard.php?tab=candidatos");
        exit;
    }
}
?>

<link rel="stylesheet" href="../styles.css">

<form method="POST">
    <h2>Editar Candidato</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    Nombre*: <input type="text" name="nombre" value="<?= htmlspecialchars($candidato['nombre']) ?>"><br>
    Apellidos*: <input type="text" name="apellidos" value="<?= htmlspecialchars($candidato['apellidos']) ?>"><br>
    DNI: <input type="text" name="dni" value="<?= htmlspecialchars($candidato['dni']) ?>"><br>
    Teléfono: <input type="text" name="telefono" value="<?= htmlspecialchars($candidato['telefono']) ?>"><br>
    Email*: <input type="email" name="email" value="<?= htmlspecialchars($candidato['email']) ?>"><br>
    Contraseña: <input type="password" name="password" placeholder="Dejar vacío si no cambia"><br>
    LinkedIn: <input type="url" name="linkedin" value="<?= htmlspecialchars($candidato['linkedin']) ?>"><br>
    Web: <input type="url" name="web" value="<?= htmlspecialchars($candidato['web']) ?>"><br>
    Dirección: <input type="text" name="direccion" value="<?= htmlspecialchars($candidato['direccion']) ?>"><br>
    CP: <input type="text" name="cp" value="<?= htmlspecialchars($candidato['cp']) ?>"><br>
    Ciudad: <input type="text" name="ciudad" value="<?= htmlspecialchars($candidato['ciudad']) ?>"><br>
    Provincia: <input type="text" name="provincia" value="<?= htmlspecialchars($candidato['provincia']) ?>"><br>
    Fecha de nacimiento: <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($candidato['fecha_nacimiento']) ?>"><br>

    <button type="submit">Actualizar</button>
    <a href="../../dashboard.php?tab=candidatos">Cancelar</a>
</form>

