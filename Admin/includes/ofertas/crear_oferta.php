<?php
session_start();
require_once "../../../conexion.php";

if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../index.php');
    exit;
}

// Obtener listas para select
$empresas = $pdo->query("SELECT id, nombre FROM empresas")->fetchAll(PDO::FETCH_ASSOC);
$sectores = $pdo->query("SELECT id, nombre FROM sectores")->fetchAll(PDO::FETCH_ASSOC);
$modalidades = $pdo->query("SELECT id, nombre FROM modalidad")->fetchAll(PDO::FETCH_ASSOC);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idempresa = $_POST['idempresa'];
    $idsector = $_POST['idsector'];
    $idmodalidad = $_POST['idmodalidad'];
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $salario_min = $_POST['salario_min'] ?: null;
    $salario_max = $_POST['salario_max'] ?: null;
    $tipo_contrato = trim($_POST['tipo_contrato']);
    $jornada = trim($_POST['jornada']);
    $ubicacion = trim($_POST['ubicacion']);

    if (empty($idempresa) || empty($idsector) || empty($idmodalidad) || empty($titulo) || empty($descripcion)) {
        $error = "Empresa, sector, modalidad, título y descripción son obligatorios.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO ofertas (idempresa, idsector, idmodalidad, titulo, descripcion, salario_min, salario_max, tipo_contrato, jornada, ubicacion) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$idempresa,$idsector,$idmodalidad,$titulo,$descripcion,$salario_min,$salario_max,$tipo_contrato,$jornada,$ubicacion]);
        header("Location: ../../dashboard.php?tab=ofertas");
        exit;
    }
}
?>
<link rel="stylesheet" href="../styles.css">
<form method="POST">
    <h2>Crear Oferta</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    Empresa*: 
    <select name="idempresa">
        <?php foreach($empresas as $e): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    Sector*: 
    <select name="idsector">
        <?php foreach($sectores as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    Modalidad*: 
    <select name="idmodalidad">
        <?php foreach($modalidades as $m): ?>
            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
        <?php endforeach; ?>
    </select><br>

    Título*: <input type="text" name="titulo"><br>
    Descripción*: <textarea name="descripcion"></textarea><br>
    Salario mínimo: <input type="number" step="0.01" name="salario_min"><br>
    Salario máximo: <input type="number" step="0.01" name="salario_max"><br>
    Tipo de contrato: <input type="text" name="tipo_contrato"><br>
    Jornada: <input type="text" name="jornada"><br>
    Ubicación: <input type="text" name="ubicacion"><br>

    <button type="submit">Crear</button>
    <a href="../../dashboard.php?tab=ofertas">Cancelar</a>
</form>
