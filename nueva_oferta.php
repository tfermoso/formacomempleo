<?php
session_start();
require_once __DIR__ . "/includes/functions.php";

redirectIfNotLoggedIn();

$conn = conectarBD();
$mensaje = "";
$tipoMensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idempresa = $_SESSION["idempresa"];
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $idsector = intval($_POST["idsector"] ?? 0);
    $idmodalidad = intval($_POST["idmodalidad"] ?? 0);
    $estado = "publicada"; // por defecto

    if ($titulo === "" || $descripcion === "" || $idsector === 0 || $idmodalidad === 0) {
        $mensaje = "Todos los campos obligatorios deben completarse.";
        $tipoMensaje = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO ofertas 
            (idempresa, idsector, idmodalidad, titulo, descripcion, estado, fecha_publicacion) 
            VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
        $stmt->bind_param("iiisss", $idempresa, $idsector, $idmodalidad, $titulo, $descripcion, $estado);

        if ($stmt->execute()) {
            $mensaje = "Oferta creada correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al crear la oferta: " . $conn->error;
            $tipoMensaje = "error";
        }
        $stmt->close();
    }
}

// Cargar sectores y modalidades para los select
$sectores = $conn->query("SELECT id, nombre FROM sectores ORDER BY nombre ASC");
$modalidades = $conn->query("SELECT id, nombre FROM modalidad ORDER BY nombre ASC");

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Oferta</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <h1>Crear nueva oferta</h1>

    <?php if ($mensaje): ?>
        <div class="alert <?php echo $tipoMensaje === 'success' ? 'alert-success' : 'alert-error'; ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label for="titulo">Título*</label>
        <input type="text" id="titulo" name="titulo" required>

        <label for="descripcion">Descripción*</label>
        <textarea id="descripcion" name="descripcion" required></textarea>

        <label for="idsector">Sector*</label>
        <select id="idsector" name="idsector" required>
            <option value="">-- Selecciona sector --</option>
            <?php while ($s = $sectores->fetch_assoc()): ?>
                <option value="<?php echo $s["id"]; ?>"><?php echo htmlspecialchars($s["nombre"]); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="idmodalidad">Modalidad*</label>
        <select id="idmodalidad" name="idmodalidad" required>
            <option value="">-- Selecciona modalidad --</option>
            <?php while ($m = $modalidades->fetch_assoc()): ?>
                <option value="<?php echo $m["id"]; ?>"><?php echo htmlspecialchars($m["nombre"]); ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit" class="boton nuevo">Guardar oferta</button>
        <a href="dashboard.php" class="boton volver">Volver</a>
    </form>
</body>

</html>