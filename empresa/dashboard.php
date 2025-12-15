<?php
require_once __DIR__ . "/../includes/funciones.php";

redirectIfNotLoggedIn();
$conn = conectarBD();

// Obtener usuario logueado
$usuario = obtenerUsuarioLogueado($conn, $_SESSION["idusuario"]);
$idempresa = intval($_SESSION["idempresa"]);

// Cargar ofertas de la empresa
$stmt = $conn->prepare("SELECT id, titulo, estado, fecha_publicacion, publicar_hasta 
                        FROM ofertas 
                        WHERE idempresa = ? AND deleted_at IS NULL
                        ORDER BY fecha_publicacion DESC");
$stmt->bind_param("i", $idempresa);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();
$conn->close();

include './includes/header.php';
?>

<div class="dashboard-wrapper">

    <?php
    $mensajes = getFlash();
    if (!empty($mensajes)) {
        foreach ($mensajes as $tipo => $lista) {
            foreach ($lista as $msg) {
                echo "<div class='alert alert-$tipo'>$msg</div>";
            }
        }
    }
    ?>

    <h1 class="titulo-dashboard">Mis Ofertas</h1>

    <p class="bienvenida">
        Bienvenido, <strong><?php echo htmlspecialchars($usuario["nombre"] . " " . $usuario["apellidos"]); ?></strong>
    </p>

    <div class="acciones-centradas">
        <a href="nueva_oferta.php" class="boton nuevo">+ Crear nueva oferta</a>
    </div>

    <div class="card">
        <table class="tabla-ofertas">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Publicada</th>
                    <th>Visible hasta</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["titulo"]); ?></td>

                            <td>
                                <span class="estado estado-<?php echo $row["estado"]; ?>">
                                    <?php echo ucfirst($row["estado"]); ?>
                                </span>
                            </td>

                            <td><?php echo date("d/m/Y", strtotime($row["fecha_publicacion"])); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($row["publicar_hasta"])); ?></td>

                            <td class="acciones">
                                <a href="editar_oferta.php?id=<?php echo $row["id"]; ?>" class="boton editar">Editar</a>
                                <a href="eliminar_oferta.php?id=<?php echo $row["id"]; ?>" class="boton eliminar">Eliminar</a>
                                <a href="candidatos_oferta.php?id=<?php echo $row["id"]; ?>" class="boton ver">Candidatos</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="sin-ofertas">No hay ofertas publicadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>