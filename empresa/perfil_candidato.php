<?php
require_once __DIR__ . "/../includes/funciones.php";

redirectIfNotLoggedIn();
$conn = conectarBD();

$idcandidato = intval($_GET["id"] ?? 0);
$stmt = $conn->prepare("SELECT nombre, apellidos, email, telefono, cv, linkedin, web, ciudad, provincia 
                        FROM candidatos WHERE id = ?");
$stmt->bind_param("i", $idcandidato);
$stmt->execute();
$resultado = $stmt->get_result();
$candidato = $resultado->fetch_assoc();
$stmt->close();
$conn->close();

if (!$candidato) {
    die("Candidato no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($candidato["nombre"]); ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h1>Perfil de <?php echo htmlspecialchars($candidato["nombre"] . " " . $candidato["apellidos"]); ?></h1>
    <p>Email: <?php echo htmlspecialchars($candidato["email"]); ?></p>
    <p>Teléfono: <?php echo htmlspecialchars($candidato["telefono"]); ?></p>
    <p>Ciudad: <?php echo htmlspecialchars($candidato["ciudad"]); ?>
        (<?php echo htmlspecialchars($candidato["provincia"]); ?>)</p>
    <?php if ($candidato["linkedin"]): ?>
        <p>LinkedIn: <a href="<?php echo htmlspecialchars($candidato["linkedin"]); ?>" target="_blank">Perfil</a></p>
    <?php endif; ?>
    <?php if ($candidato["web"]): ?>
        <p>Web personal: <a href="<?php echo htmlspecialchars($candidato["web"]); ?>"
                target="_blank"><?php echo htmlspecialchars($candidato["web"]); ?></a></p>
    <?php endif; ?>
    <?php if ($candidato["cv"]): ?>
        <p><a href="../<?php echo htmlspecialchars($candidato["cv"]); ?>" download>Descargar CV en PDF</a></p>
    <?php endif; ?>
    <a href="dashboard.php" class="boton volver">Volver</a>
</body>

</html>