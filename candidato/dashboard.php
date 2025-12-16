<?php
session_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/funciones.php'; // 👈 igual que en login.php

// 🔐 Protección de acceso
if (!isset($_SESSION['idcandidato'])) {
    header("Location: login.php");
    exit;
}

$conn = conectarBD();

$idcandidato = (int)$_SESSION['idcandidato'];
$mensaje = "";
$mensajeTipo = "success"; // success | error

// =========================
// ACCIONES POST
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['idoferta'])) {

    $accion = $_POST['accion'];
    $idoferta = (int)$_POST['idoferta'];

    if ($accion === 'eliminar_inscripcion') {
        if (eliminarInscripcion($conn, $idoferta, $idcandidato)) {
            $mensaje = "Inscripción eliminada correctamente.";
            $mensajeTipo = "success";
        } else {
            $mensaje = "Error al eliminar la inscripción.";
            $mensajeTipo = "error";
        }
    }

    if ($accion === 'inscribirse') {
        if (inscribirseEnOferta($conn, $idoferta, $idcandidato)) {
            $mensaje = "Te has inscrito correctamente en la oferta.";
            $mensajeTipo = "success";
        } else {
            $mensaje = "Error al inscribirte.";
            $mensajeTipo = "error";
        }
    }
}

// =========================
// DATOS
// =========================
$candidato = getCandidatoCompleto($conn, $idcandidato);

$ofertasInscritas   = getOfertasInscritas($conn, $idcandidato);
$ofertasNoInscritas = getOfertasNoInscritas($conn, $idcandidato);

include __DIR__ . '/includes/header.php';
?>

<h1>Dashboard candidato</h1>

<?php if ($mensaje): ?>
    <div class="<?= $mensajeTipo === 'success' ? 'mensaje-success' : 'mensaje-error' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<div class="acciones-superiores">
    <button type="button" onclick="mostrarTab('inscritas')">Ofertas inscritas</button>
    <button type="button" onclick="mostrarTab('abiertas')">Ofertas abiertas</button>
</div>

<!-- TAB INSCRITAS -->
<div id="inscritas" class="tab-content active">
    <h2>Ofertas en las que estás inscrito</h2>

    <?php if ($ofertasInscritas->num_rows === 0): ?>
        <p>No estás inscrito en ninguna oferta.</p>
    <?php else: ?>
        <table class="tabla-dashboard">
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>

            <?php while ($row = $ofertasInscritas->fetch_assoc()): ?>
                <?php $id = (int)$row['id']; ?>
                <tr>
                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                    <td><?= htmlspecialchars($row['estado_inscripcion']) ?></td>
                    <td class="td-acciones">
                        <form method="post" class="form-inline"
                              onsubmit="return confirm('¿Seguro que deseas eliminar tu inscripción?')">
                            <input type="hidden" name="accion" value="eliminar_inscripcion">
                            <input type="hidden" name="idoferta" value="<?= $id ?>">
                            <button type="submit">Eliminar</button>
                        </form>

                        <button type="button" class="btn-sec"
                                onclick="toggleDetalles('inscrita-<?= $id ?>')">
                            Mostrar más
                        </button>
                    </td>
                </tr>

                <tr id="inscrita-<?= $id ?>" class="detalles">
                    <td colspan="3">
                        <strong>Empresa:</strong> <?= htmlspecialchars($row['empresa']) ?><br>
                        <strong>Fecha publicación:</strong> <?= htmlspecialchars($row['fecha_publicacion']) ?><br>
                        <strong>Fecha inscripción:</strong> <?= htmlspecialchars($row['fecha_inscripcion']) ?><br>
                        <strong>Descripción:</strong> <?= nl2br(htmlspecialchars($row['descripcion'])) ?><br>
                        <strong>Requisitos:</strong> <?= nl2br(htmlspecialchars($row['requisitos'])) ?><br>
                        <strong>Funciones:</strong> <?= nl2br(htmlspecialchars($row['funciones'])) ?><br>
                        <strong>Salario:</strong> <?= htmlspecialchars($row['salario_min'] . " - " . $row['salario_max']) ?><br>
                        <strong>Contrato:</strong> <?= htmlspecialchars($row['tipo_contrato']) ?><br>
                        <strong>Jornada:</strong> <?= htmlspecialchars($row['jornada']) ?><br>
                        <strong>Ubicación:</strong> <?= htmlspecialchars($row['ubicacion']) ?><br>
                        <strong>Fecha incorporación:</strong> <?= htmlspecialchars($row['fecha_incorporacion']) ?><br>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<!-- TAB ABIERTAS -->
<div id="abiertas" class="tab-content">
    <h2>Ofertas abiertas</h2>

    <?php if ($ofertasNoInscritas->num_rows === 0): ?>
        <p>No hay ofertas abiertas disponibles.</p>
    <?php else: ?>
        <table class="tabla-dashboard">
            <tr>
                <th>Título</th>
                <th>Acciones</th>
            </tr>

            <?php while ($row = $ofertasNoInscritas->fetch_assoc()): ?>
                <?php $id = (int)$row['id']; ?>
                <tr>
                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                    <td class="td-acciones">
                        <form method="post" class="form-inline">
                            <input type="hidden" name="accion" value="inscribirse">
                            <input type="hidden" name="idoferta" value="<?= $id ?>">
                            <button type="submit">Inscribirse</button>
                        </form>

                        <button type="button" class="btn-sec"
                                onclick="toggleDetalles('abierta-<?= $id ?>')">
                            Mostrar más
                        </button>
                    </td>
                </tr>

                <tr id="abierta-<?= $id ?>" class="detalles">
                    <td colspan="2">
                        <strong>Empresa:</strong> <?= htmlspecialchars($row['empresa']) ?><br>
                        <strong>Fecha publicación:</strong> <?= htmlspecialchars($row['fecha_publicacion']) ?><br>
                        <strong>Descripción:</strong> <?= nl2br(htmlspecialchars($row['descripcion'])) ?><br>
                        <strong>Requisitos:</strong> <?= nl2br(htmlspecialchars($row['requisitos'])) ?><br>
                        <strong>Funciones:</strong> <?= nl2br(htmlspecialchars($row['funciones'])) ?><br>
                        <strong>Salario:</strong> <?= htmlspecialchars($row['salario_min'] . " - " . $row['salario_max']) ?><br>
                        <strong>Contrato:</strong> <?= htmlspecialchars($row['tipo_contrato']) ?><br>
                        <strong>Jornada:</strong> <?= htmlspecialchars($row['jornada']) ?><br>
                        <strong>Ubicación:</strong> <?= htmlspecialchars($row['ubicacion']) ?><br>
                        <strong>Fecha incorporación:</strong> <?= htmlspecialchars($row['fecha_incorporacion']) ?><br>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<script>
function mostrarTab(id) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.getElementById(id).classList.add('active');
}
function toggleDetalles(id) {
    const fila = document.getElementById(id);
    if (!fila) return;
    fila.style.display = (fila.style.display === 'table-row') ? 'none' : 'table-row';
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
