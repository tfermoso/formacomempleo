<?php
session_start();
require_once "../../../conexion.php"; // Conexión PDO

// Verificar si el admin está logueado
if (!isset($_SESSION['admin_login'])) {
    header('Location: ../../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    // Validaciones
    if (empty($nombre) || empty($apellidos) || empty($email) || empty($password) || empty($password2)) {
        $error = "Todos los campos obligatorios deben completarse";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de email inválido";
    } elseif ($password !== $password2) {
        $error = "Las contraseñas no coinciden";
    } else {
        // Verificar que el email no esté registrado
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "El email ya está registrado";
        } else {
            // Insertar usuario, siempre is_admin = 0
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre, apellidos, email, telefono, password_hash, is_admin)
                VALUES (?, ?, ?, ?, ?, 0)
            ");
            $stmt->execute([$nombre, $apellidos, $email, $telefono, $password_hash]);

            // Redirigir al dashboard tras crear usuario
            header('Location: ../../dashboard.php?tab=usuarios');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
  

    <main class="container">
        <div class="form-container create-form">
            <div class="form-header">
                <h1 class="form-title">Crear Usuario</h1>
                
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                    <label>Nombre*</label>
                    <input type="text" name="nombre" required><br>
                    <label>Apellidos*</label>
                    <input type="text" name="apellidos" required><br>
                    <label>Email*</label>
                    <input type="email" name="email" required><br>
                    <label>Teléfono</label>
                    <input type="text" name="telefono"><br>
                    <label>Contraseña*</label>
                    <input type="password" name="password" required><br>
                    <label>Repetir Contraseña*</label>
                    <input type="password" name="password2" required><br>
             
                <button type="submit" >Crear Usuario</button>
                    <a href="../../dashboard.php?tab=usuarios">Cancelar</a>
                
            </form>
        </div>
    </main>

    
</body>
</html>
