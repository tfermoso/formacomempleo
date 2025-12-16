<?php
session_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php';

// 🔐 Protección de acceso
if (!isset($_SESSION['idcandidato'])) {
    header("Location: login.php");
    exit;
}

$conn = conectarBD();
$id = (int)$_SESSION['idcandidato'];

$candidato = getCandidatoCompleto($conn, $id);
$mensaje = "";
$tipoMensaje = "success";

/* =========================
   PROCESAR FORMULARIO
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = [
        'nombre'    => limpiarTexto($_POST['nombre'] ?? ''),
        'apellidos' => limpiarTexto($_POST['apellidos'] ?? ''),
        'telefono'  => limpiarTexto($_POST['telefono'] ?? ''),
        'email'     => limpiarTexto($_POST['email'] ?? ''),
        'linkedin'  => limpiarTexto($_POST['linkedin'] ?? ''),
        'web'       => limpiarTexto($_POST['web'] ?? ''),
        'cp'        => limpiarTexto($_POST['cp'] ?? ''),
        'ciudad'    => limpiarTexto($_POST['ciudad'] ?? ''),
        'provincia' => limpiarTexto($_POST['provincia'] ?? '')
    ];

    if (actualizarCandidato($conn, $id, $data)) {

        // 📸 FOTO
        if (!empty($_FILES['foto']['name'])) {
            $foto = subirArchivo($_FILES['foto'], 'uploads/fotos', ['jpg','jpeg','png']);
            if ($foto) {
                actualizarCampoCandidato($conn, $id, 'foto', $foto);
            }
        }

        // 📄 CV
        if (!empty($_FILES['cv']['name'])) {
            $cv = subirArchivo($_FILES['cv'], 'uploads/cv', ['pdf','txt']);
            if ($cv) {
                actualizarCampoCandidato($conn, $id, 'cv', $cv);
            }
        }

        $mensaje = "Perfil actualizado correctamente.";
        $tipoMensaje = "success";
        $candidato = getCandidatoCompleto($conn, $id);

    } else {
        $mensaje = "Error al actualizar el perfil.";
        $tipoMensaje = "error";
    }
}

include __DIR__ . '/includes/header.php';
?>

<h1>Editar perfil</h1>

<?php if ($mensaje): ?>
    <div class="<?= $tipoMensaje === 'success' ? 'mensaje-success' : 'mensaje-error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<div class="form-container">
<form method="POST" enctype="multipart/form-data">

    <h2>Datos personales</h2>

    <div class="form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($candidato['nombre']) ?>">
    </div>

    <div class="form-group">
        <label>Apellidos</label>
        <input type="text" name="apellidos" value="<?= htmlspecialchars($candidato['apellidos']) ?>">
    </div>

    <div class="form-group">
        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($candidato['telefono']) ?>">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($candidato['email']) ?>">
    </div>

    <h2>Perfil profesional</h2>

    <div class="form-group">
        <label>LinkedIn</label>
        <input type="url" name="linkedin" value="<?= htmlspecialchars($candidato['linkedin']) ?>">
    </div>

    <div class="form-group">
        <label>Web / GitHub</label>
        <input type="url" name="web" value="<?= htmlspecialchars($candidato['web']) ?>">
    </div>

    <h2>Ubicación</h2>

    <div class="form-group">
        <label>Código Postal</label>
        <input type="text" name="cp" value="<?= htmlspecialchars($candidato['cp']) ?>">
    </div>

    <div class="form-group">
        <label>Ciudad</label>
        <input type="text" name="ciudad" value="<?= htmlspecialchars($candidato['ciudad']) ?>">
    </div>

    <div class="form-group">
        <label>Provincia</label>
        <input type="text" name="provincia" value="<?= htmlspecialchars($candidato['provincia']) ?>">
    </div>

    <h2>Archivos</h2>

    <div class="form-group">
        <label>Foto (JPG / PNG)</label>
        <input type="file" name="foto">
    </div>

    <div class="form-group">
        <label>Currículum (PDF / TXT)</label>
        <input type="file" name="cv">
    </div>

    <button type="submit">Guardar cambios</button>

    <div class="login-links">
        <a href="dashboard.php">Volver al dashboard</a>
    </div>

</form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
