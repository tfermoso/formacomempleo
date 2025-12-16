<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/funciones.php";

// 🔐 Control de acceso (empresa)
redirectIfNotLoggedIn();

$conn = conectarBD();

$mensaje = "";
$tipoMensaje = "";

/* =========================
   PROCESAR FORMULARIO
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $idsector = (int)($_POST["idsector"] ?? 0);
    $idmodalidad = (int)($_POST["idmodalidad"] ?? 0);
    $estado = $_POST["estado"] ?? "borrador";
    $publicar_hasta = $_POST["publicar_hasta"] ?? null;

    if ($publicar_hasta && $publicar_hasta < date("Y-m-d")) {
        $mensaje = "La fecha 'publicar hasta' no puede ser anterior a hoy.";
        $tipoMensaje = "error";

    } elseif ($titulo === "" || $descripcion === "" || $idsector === 0 || $idmodalidad === 0) {
        $mensaje = "Todos los campos obligatorios deben completarse.";
        $tipoMensaje = "error";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO ofertas
            (idempresa, idsector, idmodalidad, titulo, descripcion,
             requisitos, funciones, salario_min, salario_max,
             tipo_contrato, jornada, ubicacion,
             fecha_publicacion, publicar_hasta, estado)
            VALUES (?, ?, ?, ?, ?, '', '', 0, 0, '', '', '',
                    CURDATE(), ?, ?)
        ");

        $stmt->bind_param(
            "iiissss",
            $_SESSION["idempresa"],
            $idsector,
            $idmodalidad,
            $titulo,
            $descripcion,
            $publicar_hasta,
            $estado
        );

        if ($stmt->execute()) {
            $mensaje = "Oferta creada correctamente.";
            $tipoMensaje = "success";
        } else {
            $mensaje = "Error al crear la oferta.";
            $tipoMensaje = "error";
        }

        $stmt->close();
    }
}

/* =========================
   DATOS PARA SELECTS
========================= */
$sectores = obtenerSectores($conn);
$modalidades = obtenerModalidades($conn);

$conn->close();

include __DIR__ . "/includes/header.php";
?>

<h1>Crear nueva oferta</h1>

<?php if ($mensaje): ?>
    <div class="<?= $tipoMensaje === 'success' ? 'mensaje-success' : 'mensaje-error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<div class="form-container">
<form method="post">

    <h2>Datos de la oferta</h2>

    <div class="oferta-grid">

        <!-- =========================
             COLUMNA IZQUIERDA
        ========================= -->
        <div class="oferta-col-izq">

            <div class="form-group">
                <label for="titulo">Título *</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>

            <div class="form-group">
                <label for="idsector">Sector *</label>
                <select id="idsector" name="idsector" required>
                    <option value="">-- Selecciona un sector --</option>
                    <?php while ($s = $sectores->fetch_assoc()): ?>
                        <option value="<?= $s["id"]; ?>">
                            <?= htmlspecialchars($s["nombre"]); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="idmodalidad">Modalidad *</label>
                <select id="idmodalidad" name="idmodalidad" required>
                    <option value="">-- Selecciona modalidad --</option>
                    <?php while ($m = $modalidades->fetch_assoc()): ?>
                        <option value="<?= $m["id"]; ?>">
                            <?= htmlspecialchars($m["nombre"]); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado *</label>
                <select id="estado" name="estado" required>
                    <?php foreach (["borrador","publicada","pausada","cerrada","vencida"] as $e): ?>
                        <option value="<?= $e ?>"><?= ucfirst($e) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="publicar_hasta">Publicar hasta *</label>
                <input type="date"
                       id="publicar_hasta"
                       name="publicar_hasta"
                       min="<?= date("Y-m-d"); ?>"
                       required>
            </div>

        </div>

        <!-- =========================
             COLUMNA DERECHA
        ========================= -->
        <div class="oferta-col-der">

            <div class="form-group">
                <label for="descripcion">Descripción *</label>
                <textarea id="descripcion"
                          name="descripcion"
                          rows="14"
                          required></textarea>
            </div>

        </div>

    </div>

    <button type="submit">Crear oferta</button>

    <div class="login-links">
        <a href="dashboard.php">Volver al dashboard</a>
    </div>

</form>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
