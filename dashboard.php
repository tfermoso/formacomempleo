<?php
session_start();

require_once __DIR__ . '/utility/config.php';
require_once __DIR__ . '/utility/functions.php';

if (!isset($_SESSION['idcandidato'])) {
    echo "Acceso denegado. Inicia sesión.";
    exit;
}

$idcandidato = (int)$_SESSION['idcandidato'];
$mensaje = "";

/* --- ACCIONES POST --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Eliminar inscripción
    if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar_inscripcion') {
        $idoferta = (int)$_POST['idoferta'];
        if (eliminarInscripcion($conn, $idoferta, $idcandidato)) {
            $mensaje = "Inscripción eliminada correctamente.";
        } else {
            $mensaje = "Error al eliminar inscripción.";
        }
    }

    // Inscribirse
    if (isset($_POST['accion']) && $_POST['accion'] === 'inscribirse') {
        $idoferta = (int)$_POST['idoferta'];
        if (inscribirseEnOferta($conn, $idoferta, $idcandidato)) {
            $mensaje = "Te has inscrito correctamente en la oferta.";
        } else {
            $mensaje = "Error al inscribirte.";
        }
    }
}

/* --- DATOS DEL CANDIDATO --- */
$candidato = getCandidatoCompleto($conn, $idcandidato);

/* Foto del candidato */
$foto = (!empty($candidato['foto']))
    ? "uploads/fotos/" . $candidato['foto']
    : "uploads/fotos/default.png"; // crea una imagen default si quieres

/* --- OFERTAS --- */
$ofertasInscritas   = getOfertasInscritas($conn, $idcandidato);
$ofertasNoInscritas = getOfertasNoInscritas($conn, $idcandidato);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard candidato</title>
    <style>
        body { font-family: Arial; }
        .perfil {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .perfil img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        .detalles { display: none; }
        .enlace-mostrar-mas { color: blue; cursor: pointer; }
        .mensaje { color: darkred; margin: 10px 0; }
        .acciones-superiores {
            margin-top: 10px;
        }
        .acciones-superiores button {
            padding: 8px 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<!-- ✅ FOTO + NOMBRE + BOTÓN EDITAR PERFIL -->
<div class="perfil">
    <img src="<?php echo $foto; ?>" alt="Foto de perfil">
    <div>
        <h1><?php echo $candidato['nombre'] . " " . $candidato['apellidos']; ?></h1>
        <a href="editar_perfil.php"><button>Editar perfil</button></a>
    </div>
</div>

<?php if ($mensaje): ?>
    <p class="mensaje"><?php echo $mensaje; ?></p>
<?php endif; ?>

<!-- ✅ BOTONES DE PESTAÑAS -->
<div class="acciones-superiores">
    <button onclick="mostrarTab('inscritas')">Ofertas inscritas</button>
    <button onclick="mostrarTab('abiertas')">Ofertas abiertas</button>
</div>

<!-- ✅ TAB INSCRITAS -->
<div id="inscritas" class="tab-content active">
    <h2>Ofertas en las que estás inscrito</h2>

    <?php if ($ofertasInscritas->num_rows === 0): ?>
        <p>No estás inscrito en ninguna oferta.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Título</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>

            <?php while ($row = $ofertasInscritas->fetch_assoc()): ?>
                <?php $id = $row['id']; ?>
                <tr>
                    <td><?php echo $row['titulo']; ?></td>
                    <td><?php echo $row['estado_inscripcion']; ?></td>
                    <td>
                        <form method="post" style="display:inline"
                              onsubmit="return confirm('¿Seguro que deseas eliminar tu inscripción?')">
                            <input type="hidden" name="accion" value="eliminar_inscripcion">
                            <input type="hidden" name="idoferta" value="<?php echo $id; ?>">
                            <button type="submit">Eliminar</button>
                        </form>

                        <span class="enlace-mostrar-mas" onclick="toggleDetalles('inscrita-<?php echo $id; ?>')">
                            Mostrar más
                        </span>
                    </td>
                </tr>

                <tr id="inscrita-<?php echo $id; ?>" class="detalles">
                    <td colspan="3">
                        <strong>Empresa:</strong> <?php echo $row['empresa']; ?><br>
                        <strong>Fecha publicación:</strong> <?php echo $row['fecha_publicacion']; ?><br>
                        <strong>Fecha inscripción:</strong> <?php echo $row['fecha_inscripcion']; ?><br>
                        <strong>Descripción:</strong> <?php echo nl2br($row['descripcion']); ?><br>
                        <strong>Requisitos:</strong> <?php echo nl2br($row['requisitos']); ?><br>
                        <strong>Funciones:</strong> <?php echo nl2br($row['funciones']); ?><br>
                        <strong>Salario:</strong> <?php echo $row['salario_min'] . " - " . $row['salario_max']; ?><br>
                        <strong>Contrato:</strong> <?php echo $row['tipo_contrato']; ?><br>
                        <strong>Jornada:</strong> <?php echo $row['jornada']; ?><br>
                        <strong>Ubicación:</strong> <?php echo $row['ubicacion']; ?><br>
                        <strong>Fecha incorporación:</strong> <?php echo $row['fecha_incorporacion']; ?><br>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<!-- ✅ TAB ABIERTAS -->
<div id="abiertas" class="tab-content">
    <h2>Ofertas abiertas</h2>

    <?php if ($ofertasNoInscritas->num_rows === 0): ?>
        <p>No hay ofertas abiertas disponibles.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Título</th>
                <th>Acciones</th>
            </tr>

            <?php while ($row = $ofertasNoInscritas->fetch_assoc()): ?>
                <?php $id = $row['id']; ?>
                <tr>
                    <td><?php echo $row['titulo']; ?></td>
                    <td>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="accion" value="inscribirse">
                            <input type="hidden" name="idoferta" value="<?php echo $id; ?>">
                            <button type="submit">Inscribirse</button>
                        </form>

                        <span class="enlace-mostrar-mas" onclick="toggleDetalles('abierta-<?php echo $id; ?>')">
                            Mostrar más
                        </span>
                    </td>
                </tr>

                <tr id="abierta-<?php echo $id; ?>" class="detalles">
                    <td colspan="2">
                        <strong>Empresa:</strong> <?php echo $row['empresa']; ?><br>
                        <strong>Fecha publicación:</strong> <?php echo $row['fecha_publicacion']; ?><br>
                        <strong>Descripción:</strong> <?php echo nl2br($row['descripcion']); ?><br>
                        <strong>Requisitos:</strong> <?php echo nl2br($row['requisitos']); ?><br>
                        <strong>Funciones:</strong> <?php echo nl2br($row['funciones']); ?><br>
                        <strong>Salario:</strong> <?php echo $row['salario_min'] . " - " . $row['salario_max']; ?><br>
                        <strong>Contrato:</strong> <?php echo $row['tipo_contrato']; ?><br>
                        <strong>Jornada:</strong> <?php echo $row['jornada']; ?><br>
                        <strong>Ubicación:</strong> <?php echo $row['ubicacion']; ?><br>
                        <strong>Fecha incorporación:</strong> <?php echo $row['fecha_incorporacion']; ?><br>
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
    fila.style.display = fila.style.display === 'table-row' ? 'none' : 'table-row';
}
</script>

</body>
</html>
