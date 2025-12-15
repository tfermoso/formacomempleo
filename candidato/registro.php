<?php
$titulo = "Registro de Candidato";
include "../includes/header.php";
?>

<div class="card">
<h2>Registro de Candidato</h2>

<form action="../php/procesar_registro.php" method="post">

    <div class="form-row">
        <label>Nombre</label>
        <input type="text" name="nombre" required>
    </div>

    <div class="form-row">
        <label>Apellidos</label>
        <input type="text" name="apellidos" required>
    </div>

    <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-row">
        <label>Contraseña</label>
        <input type="password" name="password" required>
    </div>

    <button class="btn" type="submit">Crear cuenta</button>
</form>

<p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</div>

<?php include "../includes/footer.php"; ?>
