<?php

require_once __DIR__ . "/../includes/funciones.php";

redirectIfNotLoggedIn();
$conn = conectarBD();

$mensaje = "";
$tipoMensaje = "";

// Validar ID de oferta
if (!isset($_GET["id"])) {
    die("ID de oferta no especificado.");
}
$idoferta = intval($_GET["id"]);

// Cargar oferta
$stmt = $conn->prepare("SELECT id, titulo, descripcion, idsector, idmodalidad, estado, publicar_hasta
                        FROM ofertas
                        WHERE id = ? AND idempresa = ? AND deleted_at IS NULL");
$stmt->bind_param("ii", $idoferta, $_SESSION["idempresa"]);
$stmt->execute();
$resultado = $stmt->get_result();
$oferta = $resultado->fetch_assoc();
$stmt->close();

if (!$oferta) {
    die("Oferta no encontrada o no pertenece a tu empresa.");
}

// Procesar actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $idsector = intval($_POST["idsector"] ?? 0);
    $idmodalidad = intval($_POST["idmodalidad"] ?? 0);
    $estado = $_POST["estado"] ?? "borrador";
    $publicar_hasta = $_POST["publicar_hasta"] ?? null;

    // Validaciones
    if ($titulo === "" || $descripcion === "" || $idsector === 0 || $idmodalidad === 0 || !$publicar_hasta) {
        $mensaje = "Todos los campos obligatorios deben completarse.";
        $tipoMensaje = "error";
    } elseif ($publicar_hasta < date('Y-m-d')) {
        $mensaje = "La fecha 'publicar hasta' no puede ser anterior a hoy.";
        $tipoMensaje = "error";
    } else {
        $stmt = $conn->prepare("UPDATE ofertas
                                SET titulo = ?, descripcion = ?, idsector = ?, idmodalidad = ?, estado = ?, publicar_hasta = ?
                                WHERE id = ? AND idempresa = ?");
        $stmt->bind_param("ssiissii", $titulo, $descripcion, $idsector, $idmodalidad, $estado, $publicar_hasta, $idoferta, $_SESSION["idempresa"]);

        if ($stmt->execute()) {
            $mensaje = "Oferta actualizada correctamente.";
            $tipoMensaje = "success";
            // Refrescar datos en memoria
            $oferta["titulo"] = $titulo;
            $oferta["descripcion"] = $descripcion;
            $oferta["idsector"] = $idsector;
            $oferta["idmodalidad"] = $idmodalidad;
            $oferta["estado"] = $estado;
            $oferta["publicar_hasta"] = $publicar_hasta;
        } else {
            $mensaje = "Error al actualizar la oferta: " . $conn->error;
            $tipoMensaje = "error";
        }
        $stmt->close();
    }
}

// Cargar sectores y modalidades
$sectores = obtenerSectores($conn);
$modalidades = obtenerModalidades($conn);

$conn->close();

// Estados disponibles
$estados = ["borrador", "publicada", "pausada", "cerrada", "vencida"];

// Preparar valor de fecha para el input date (YYYY-MM-DD)
$valorFecha = isset($oferta["publicar_hasta"]) && $oferta["publicar_hasta"] !== ""
    ? htmlspecialchars($oferta["publicar_hasta"])
    : "";
$minFecha = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Oferta</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h1>Editar oferta</h1>

    <?php mostrarMensaje($mensaje, $tipoMensaje); ?>

    <form method="post">
        <label for="titulo">Título*</label>
        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($oferta["titulo"]); ?>"
            required>

        <label for="descripcion">Descripción*</label>
        <textarea id="descripcion" name="descripcion"
            required><?php echo htmlspecialchars($oferta["descripcion"]); ?></textarea>

        <label for="idsector">Sector*</label>
        <select id="idsector" name="idsector" required>
            <?php while ($s = $sectores->fetch_assoc()): ?>
                <option value="<?php echo $s["id"]; ?>" <?php echo ($s["id"] == $oferta["idsector"]) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($s["nombre"]); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="idmodalidad">Modalidad*</label>
        <select id="idmodalidad" name="idmodalidad" required>
            <?php while ($m = $modalidades->fetch_assoc()): ?>
                <option value="<?php echo $m["id"]; ?>" <?php echo ($m["id"] == $oferta["idmodalidad"]) ? "selected" : ""; ?>>
                    <?php echo htmlspecialchars($m["nombre"]); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="estado">Estado*</label>
        <select id="estado" name="estado" required>
            <?php foreach ($estados as $e): ?>
                <option value="<?php echo $e; ?>" <?php echo ($e == $oferta["estado"]) ? "selected" : ""; ?>>
                    <?php echo ucfirst($e); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="publicar_hasta">Publicar hasta*</label>
        <input type="date" id="publicar_hasta" name="publicar_hasta" value="<?php echo $valorFecha; ?>"
            min="<?php echo $minFecha; ?>" required>

        <button type="submit" class="boton editar">Guardar cambios</button>
        <a href="dashboard.php" class="boton volver">Volver</a>
    </form>
</body>

</html>