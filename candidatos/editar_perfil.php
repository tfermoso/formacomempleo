<?php
session_start();

require_once '../includes/header.php';

require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['idcandidato'])) {
    echo "Acceso denegado.";
    exit;
}

$id = $_SESSION['idcandidato'];
$candidato = getCandidatoCompleto($conn, $id);

$mensaje = "";

/* --- PROCESAR FORMULARIO --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Actualizar datos básicos
    $data = [
        'nombre'    => $_POST['nombre'],
        'apellidos' => $_POST['apellidos'],
        'telefono'  => $_POST['telefono'],
        'email'     => $_POST['email'],
        'linkedin'  => $_POST['linkedin'],
        'github'    => $_POST['web'],
        'cp'        => $_POST['cp'],
        'ciudad'    => $_POST['ciudad'],
        'provincia' => $_POST['provincia']
    ];

    actualizarCandidato($conn, $id, $data);

    // 2. Subir foto
    if (!empty($_FILES['foto']['name'])) {
        $foto = subirArchivo($_FILES['foto'], "../uploads/fotos/", ['jpg','jpeg','png']);
        if ($foto) {
            $conn->query("UPDATE candidatos SET foto = '$foto' WHERE id = $id");
        }
    }

    // 3. Subir CV
    if (!empty($_FILES['cv']['name'])) {
        $cv = subirArchivo($_FILES['cv'], "../uploads/cv/", ['pdf','txt']);
        if ($cv) {
            $conn->query("UPDATE candidatos SET cv = '$cv' WHERE id = $id");
        }
    }

    $mensaje = "Perfil actualizado correctamente.";
    $candidato = getCandidatoCompleto($conn, $id); // refrescar datos
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar perfil</title>
</head>
<body>

<h1>Editar perfil</h1>

<?php if ($mensaje): ?>
    <p style="color:green;"><?php echo $mensaje; ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?php echo $candidato['nombre']; ?>"><br><br>

    <label>Apellidos:</label><br>
    <input type="text" name="apellidos" value="<?php echo $candidato['apellidos']; ?>"><br><br>

    <label>Teléfono:</label><br>
    <input type="text" name="telefono" value="<?php echo $candidato['telefono']; ?>"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $candidato['email']; ?>"><br><br>

    <label>LinkedIn:</label><br>
    <input type="text" name="linkedin" value="<?php echo $candidato['linkedin']; ?>"><br><br>

    <label>GitHub:</label><br>
    <input type="text" name="web" value="<?php echo $candidato['web']; ?>"><br><br>

    <label>Código postal:</label><br>
    <input type="text" name="cp" value="<?php echo $candidato['cp']; ?>"><br><br>

    <label>Ciudad:</label><br>
    <input type="text" name="ciudad" value="<?php echo $candidato['ciudad']; ?>"><br><br>

    <label>Provincia:</label><br>
    <input type="text" name="provincia" value="<?php echo $candidato['provincia']; ?>"><br><br>

    <label>Foto (jpg/png):</label><br>
    <input type="file" name="foto"><br><br>

    <label>CV (pdf/txt):</label><br>
    <input type="file" name="cv"><br><br>

    <button type="submit">Guardar cambios</button>
</form>

<br>
<a href="dashboard.php">Volver al dashboard</a>

</body>
</html>
<?php include '../includes/footer.php'; ?>
