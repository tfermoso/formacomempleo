<?php
session_start();
require_once __DIR__ . "/../includes/functions.php";

redirectIfNotLoggedIn();
$conn = conectarBD();

// Obtener usuario logueado
$usuario = obtenerUsuarioLogueado($conn, $_SESSION["idusuario"]);
$idempresa = intval($_SESSION["idempresa"]);

// Cargar ofertas de la empresa
$stmt = $conn->prepare("SELECT id, titulo, estado, fecha_publicacion, publicar_hasta 
                        FROM ofertas 
                        WHERE idempresa = ? AND deleted_at IS NULL");
$stmt->bind_param("i", $idempresa);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Empresa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php if (isset($_GET["msg"])): ?>
        <div class="alert <?php echo $_GET["type"] === 'success' ? 'alert-success' : 'alert-error'; ?>">
            <?php echo htmlspecialchars($_GET["msg"]); ?>
        </div>
    <?php endif; ?>

    <h1>Mis Ofertas</h1>
    <p class="bienvenida">
        Bienvenido, <?php echo htmlspecialchars($usuario["nombre"] . " " . $usuario["apellidos"]); ?>
    </p>

    <p class="acciones-centradas">
        <a href="nueva_oferta.php" class="boton nuevo">Crear nueva oferta</a>
        <a href="logout.php" class="boton volver">Cerrar sesión</a>
    </p>

    <div class="table-container">
        <table>
            <thead>
                <tr>

                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha publicación</th>
                    <th>Publicar hasta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>

                            <td><?php echo htmlspecialchars($row["titulo"]); ?></td>
                            <td><?php echo htmlspecialchars($row["estado"]); ?></td>
                            <td><?php echo htmlspecialchars($row["fecha_publicacion"]); ?></td>
                            <td><?php echo htmlspecialchars($row["publicar_hasta"]); ?></td>
                            <td>
                                <a href="editar_oferta.php?id=<?php echo $row["id"]; ?>" class="boton editar">Editar</a>
                                <a href="eliminar_oferta.php?id=<?php echo $row["id"]; ?>" class="boton eliminar">Eliminar</a>
                                <a href="candidatos_oferta.php?id=<?php echo $row["id"]; ?>" class="boton">Ver candidatos</a>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay ofertas publicadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>