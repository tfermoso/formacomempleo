<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../index.php');
    exit;
}

if (!isset($_GET['id'])) { header('Location: ../../dashboard.php?tab=ofertas'); exit; }
$id = (int)$_GET['id'];

// Obtener la oferta
$stmt = $db->prepare("SELECT * FROM ofertas WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$oferta = $result->fetch_assoc();
$stmt->close();

if (!$oferta) { header('Location: ../../dashboard.php?tab=ofertas'); exit; }

// Listas
$empresas = $db->query("SELECT id, nombre FROM empresas")->fetch_all(MYSQLI_ASSOC);
$sectores = $db->query("SELECT id, nombre FROM sectores")->fetch_all(MYSQLI_ASSOC);
$modalidades = $db->query("SELECT id, nombre FROM modalidad")->fetch_all(MYSQLI_ASSOC);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idempresa = $_POST['idempresa'];
    $idsector = $_POST['idsector'];
    $idmodalidad = $_POST['idmodalidad'];
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $salario_min = $_POST['salario_min'] !== '' ? $_POST['salario_min'] : null;
    $salario_max = $_POST['salario_max'] !== '' ? $_POST['salario_max'] : null;
    $tipo_contrato = trim($_POST['tipo_contrato']);
    $jornada = trim($_POST['jornada']);
    $ubicacion = trim($_POST['ubicacion']);

    if (empty($idempresa) || empty($idsector) || empty($idmodalidad) || empty($titulo) || empty($descripcion)) {
        $error = "Empresa, sector, modalidad, título y descripción son obligatorios.";
    } else {
        $stmt = $db->prepare("UPDATE ofertas SET idempresa=?, idsector=?, idmodalidad=?, titulo=?, descripcion=?, salario_min=?, salario_max=?, tipo_contrato=?, jornada=?, ubicacion=? WHERE id=?");
        $stmt->bind_param(
            "iiissdssssi",
            $idempresa,
            $idsector,
            $idmodalidad,
            $titulo,
            $descripcion,
            $salario_min,
            $salario_max,
            $tipo_contrato,
            $jornada,
            $ubicacion,
            $id
        );

        if ($stmt->execute()) {
            header("Location: ../../dashboard.php?tab=ofertas");
            exit;
        } else {
            $error = "Error al actualizar la oferta: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<link rel="stylesheet" href="../styles.css">
<form method="POST">
    <h2>Editar Oferta</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    Empresa*: 
    <select name="idempresa">
        <?php foreach($empresas as $e): ?>
            <option value="<?= $e['id'] ?>" <?= $e['id']==$oferta['idempresa']?'selected':'' ?>><?= htmlspecialchars($e['nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    Sector*: 
    <select name="idsector">
        <?php foreach($sectores as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $s['id']==$oferta['idsector']?'selected':'' ?>><?= htmlspecialchars($s['nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    Modalidad*: 
    <select name="idmodalidad">
        <?php foreach($modalidades as $m): ?>
            <option value="<?= $m['id'] ?>" <?= $m['id']==$oferta['idmodalidad']?'selected':'' ?>><?= htmlspecialchars($m['nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    Título*: <input type="text" name="titulo" value="<?= htmlspecialchars($oferta['titulo']) ?>"><br>
    Descripción*: <textarea name="descripcion"><?= htmlspecialchars($oferta['descripcion']) ?></textarea><br>
    Salario mínimo: <input type="number" step="0.01" name="salario_min" value="<?= htmlspecialchars($oferta['salario_min']) ?>"><br>
    Salario máximo: <input type="number" step="0.01" name="salario_max" value="<?= htmlspecialchars($oferta['salario_max']) ?>"><br>
    Tipo de contrato: <input type="text" name="tipo_contrato" value="<?= htmlspecialchars($oferta['tipo_contrato']) ?>"><br>
    Jornada: <input type="text" name="jornada" value="<?= htmlspecialchars($oferta['jornada']) ?>"><br>
    Ubicación: <input type="text" name="ubicacion" value="<?= htmlspecialchars($oferta['ubicacion']) ?>"><br>

    <button type="submit">Actualizar</button>
    <a href="../../dashboard.php?tab=ofertas">Cancelar</a>
</form>
