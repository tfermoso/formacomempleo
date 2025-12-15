<?php

require_once __DIR__ . "/../includes/funciones.php";

redirectIfNotLoggedIn();
$conn = conectarBD();

$mensaje = "";
$tipoMensaje = "";

// Procesar creación
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $idsector = intval($_POST["idsector"] ?? 0);
    $idmodalidad = intval($_POST["idmodalidad"] ?? 0);
    $estado = $_POST["estado"] ?? "borrador";
    $publicar_hasta = $_POST["publicar_hasta"] ?? null;

    if ($publicar_hasta && $publicar_hasta < date('Y-m-d')) {
        $mensaje = "La fecha 'publicar hasta' no puede ser anterior a hoy.";
        $tipoMensaje = "error";
    } elseif ($titulo === "" || $descripcion === "" || $idsector === 0 || $idmodalidad === 0) {
        $mensaje = "Todos los campos obligatorios deben completarse.";
        $tipoMensaje = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO ofertas 
            (idempresa, idsector, idmodalidad, titulo, descripcion, requisitos, funciones, salario_min, salario_max, tipo_contrato, jornada, ubicacion, fecha_publicacion, publicar_hasta, estado) 
            VALUES (?, ?, ?, ?, ?, '', '', 0, 0, '', '', '', CURDATE(), ?, ?)");
        $stmt->bind_param("iiissss", $_SESSION["idempresa"], $idsector, $idmodalidad, $titulo, $descripcion, $publicar_hasta, $estado);

        if ($stmt->execute()) {
            setFlash("success", "Oferta creada correctamente.");
            header("Location: dashboard.php");
            exit;
        } else {
            $mensaje = "Error al crear la oferta: " . $conn->error;
            $tipoMensaje = "error";
        }

        $stmt->close();
    }
}

// Cargar sectores y modalidades con funciones
$sectores = obtenerSectores($conn);
$modalidades = obtenerModalidades($conn);

$conn->close();


include './includes/header.php';
?>


<h1>Crear nueva oferta</h1>

<?php mostrarMensaje($mensaje, $tipoMensaje); ?>

<form method="post">
    <label for="titulo">Título*</label>
    <input type="text" id="titulo" name="titulo" required>

    <label for="descripcion">Descripción*</label>
    <textarea id="descripcion" name="descripcion" required></textarea>

    <label for="idsector">Sector*</label>
    <select id="idsector" name="idsector" required>
        <?php while ($s = $sectores->fetch_assoc()): ?>
            <option value="<?php echo $s["id"]; ?>"><?php echo htmlspecialchars($s["nombre"]); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="idmodalidad">Modalidad*</label>
    <select id="idmodalidad" name="idmodalidad" required>
        <?php while ($m = $modalidades->fetch_assoc()): ?>
            <option value="<?php echo $m["id"]; ?>"><?php echo htmlspecialchars($m["nombre"]); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="estado">Estado*</label>
    <select id="estado" name="estado" required>
        <?php
        $estados = ["borrador", "publicada", "pausada", "cerrada", "vencida"];
        foreach ($estados as $e): ?>
            <option value="<?php echo $e; ?>"><?php echo ucfirst($e); ?></option>
        <?php endforeach; ?>
    </select>

    <label for="publicar_hasta">Publicar hasta*</label>
    <input type="date" id="publicar_hasta" name="publicar_hasta" min="<?php echo date('Y-m-d'); ?>" required>

    <button type="submit" class="boton nuevo">Crear oferta</button>
    <a href="dashboard.php" class="boton volver">Volver</a>
</form>
</body>

</html>