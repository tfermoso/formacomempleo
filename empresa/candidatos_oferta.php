<?php
require_once __DIR__ . "/../includes/funciones.php";

redirectIfNotLoggedIn();
$conn = conectarBD();

$idoferta = intval($_GET["id"] ?? 0);
if ($idoferta === 0) {
    die("ID de oferta no especificado.");
}

$stmt = $conn->prepare("SELECT c.id, c.nombre, c.apellidos, c.email, c.telefono, c.cv, oc.estado, oc.fecha_inscripcion
                        FROM candidatos c
                        INNER JOIN ofertas_candidatos oc ON c.id = oc.idcandidato
                        INNER JOIN ofertas o ON oc.idoferta = o.id
                        WHERE o.id = ? AND o.idempresa = ?");
$stmt->bind_param("ii", $idoferta, $_SESSION["idempresa"]);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Candidatos interesados</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h1>Candidatos interesados en la oferta #<?php echo $idoferta; ?></h1>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Fecha inscripción</th>
                <th>Perfil</th>
                <th>CV</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0): ?>
                <?php while ($c = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c["nombre"] . " " . $c["apellidos"]); ?></td>
                        <td><?php echo htmlspecialchars($c["email"]); ?></td>
                        <td><?php echo htmlspecialchars($c["telefono"]); ?></td>
                        <td><?php echo htmlspecialchars($c["estado"]); ?></td>
                        <td><?php echo htmlspecialchars($c["fecha_inscripcion"]); ?></td>
                        <td><a href="perfil_candidato.php?id=<?php echo $c["id"]; ?>" class="boton">Ver perfil</a></td>
                        <td>
                            <?php if ($c["cv"]): ?>
                                <a href="../<?php echo htmlspecialchars($c["cv"]); ?>" class="boton" download>Descargar CV</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay candidatos interesados en esta oferta.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="boton volver">Volver</a>
</body>

</html>