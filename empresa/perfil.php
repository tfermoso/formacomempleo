<?php
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/conexion.php';

redirectIfNotLoggedIn();

$conn = conectarBD();

// ==========================
// Cargar datos de empresa
// ==========================
$stmt = $conn->prepare("SELECT nombre, cif, telefono, web, persona_contacto, email_contacto, direccion, cp, ciudad, provincia 
                        FROM empresas 
                        WHERE id = ?");
$stmt->bind_param("i", $_SESSION["idempresa"]);
$stmt->execute();
$empresa = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ==========================
// Cargar datos del usuario
// ==========================
$stmt = $conn->prepare("SELECT nombre, apellidos, telefono, email 
                        FROM usuarios 
                        WHERE id = ?");
$stmt->bind_param("i", $_SESSION["idusuario"]);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();

$mensaje = "";
$tipoMensaje = "";

// ==========================
// Procesar actualización
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Empresa
    $nombreEmpresa   = limpiarTexto($_POST["nombre_empresa"]);
    $telefonoEmpresa = limpiarTexto($_POST["telefono_empresa"]);
    $webEmpresa      = limpiarTexto($_POST["web_empresa"]);
    $personaContacto = limpiarTexto($_POST["persona_contacto"]);
    $emailEmpresa    = limpiarTexto($_POST["email_empresa"]);
    $direccion       = limpiarTexto($_POST["direccion"]);
    $cp              = limpiarTexto($_POST["cp"]);
    $ciudad          = limpiarTexto($_POST["ciudad"]);
    $provincia       = limpiarTexto($_POST["provincia"]);

    // Usuario
    $nombreUsuario   = limpiarTexto($_POST["nombre_usuario"]);
    $apellidosUsuario = limpiarTexto($_POST["apellidos_usuario"]);
    $telefonoUsuario = limpiarTexto($_POST["telefono_usuario"]);
    $emailUsuario    = limpiarTexto($_POST["email_usuario"]);

    // Validaciones básicas
    if ($nombreEmpresa === "" || $emailEmpresa === "" || $nombreUsuario === "" || $emailUsuario === "") {
        setFlash("error", "Los campos obligatorios no pueden estar vacíos.");
        header("Location: perfil.php");
        exit;
    }

    // ==========================
    // Actualizar empresa
    // ==========================
    $stmt = $conn->prepare("UPDATE empresas 
                            SET nombre = ?, telefono = ?, web = ?, persona_contacto = ?, email_contacto = ?, direccion = ?, cp = ?, ciudad = ?, provincia = ?
                            WHERE id = ?");
    $stmt->bind_param(
        "sssssssssi",
        $nombreEmpresa,
        $telefonoEmpresa,
        $webEmpresa,
        $personaContacto,
        $emailEmpresa,
        $direccion,
        $cp,
        $ciudad,
        $provincia,
        $_SESSION["idempresa"]
    );
    $stmt->execute();
    $stmt->close();

    // ==========================
    // Actualizar usuario
    // ==========================
    $stmt = $conn->prepare("UPDATE usuarios 
                            SET nombre = ?, apellidos = ?, telefono = ?, email = ?
                            WHERE id = ?");
    $stmt->bind_param(
        "ssssi",
        $nombreUsuario,
        $apellidosUsuario,
        $telefonoUsuario,
        $emailUsuario,
        $_SESSION["idusuario"]
    );
    $stmt->execute();
    $stmt->close();

    setFlash("success", "Perfil actualizado correctamente.");
    header("Location: perfil.php");
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<h1>Perfil de Empresa y Usuario</h1>

<?php
$mensajes = getFlash();
if (!empty($mensajes)) {
    foreach ($mensajes as $tipo => $lista) {
        foreach ($lista as $msg) {
            echo "<p class='mensaje-$tipo'>$msg</p>";
        }
    }
}
?>

<form method="post" class="form-perfil">

    <h2>Datos de la Empresa</h2>

    <label>Nombre empresa *</label>
    <input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($empresa["nombre"]); ?>" required>

    <label>Email empresa *</label>
    <input type="email" name="email_empresa" value="<?php echo htmlspecialchars($empresa["email_contacto"]); ?>" required>

    <label>Teléfono</label>
    <input type="text" name="telefono_empresa" value="<?php echo htmlspecialchars($empresa["telefono"]); ?>">

    <label>Web</label>
    <input type="text" name="web_empresa" value="<?php echo htmlspecialchars($empresa["web"]); ?>">

    <label>Persona de contacto</label>
    <input type="text" name="persona_contacto" value="<?php echo htmlspecialchars($empresa["persona_contacto"]); ?>">

    <label>Dirección</label>
    <input type="text" name="direccion" value="<?php echo htmlspecialchars($empresa["direccion"]); ?>">

    <label>CP</label>
    <input type="text" name="cp" value="<?php echo htmlspecialchars($empresa["cp"]); ?>">

    <label>Ciudad</label>
    <input type="text" name="ciudad" value="<?php echo htmlspecialchars($empresa["ciudad"]); ?>">

    <label>Provincia</label>
    <input type="text" name="provincia" value="<?php echo htmlspecialchars($empresa["provincia"]); ?>">

    <h2>Datos del Usuario Responsable</h2>

    <label>Nombre *</label>
    <input type="text" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario["nombre"]); ?>" required>

    <label>Apellidos *</label>
    <input type="text" name="apellidos_usuario" value="<?php echo htmlspecialchars($usuario["apellidos"]); ?>" required>

    <label>Email *</label>
    <input type="email" name="email_usuario" value="<?php echo htmlspecialchars($usuario["email"]); ?>" required>

    <label>Teléfono *</label>
    <input type="text" name="telefono_usuario" value="<?php echo htmlspecialchars($usuario["telefono"]); ?>" required>

    <button type="submit" class="boton guardar">Guardar cambios</button>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>