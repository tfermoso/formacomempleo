<?php
$titulo = "Login Candidato";
include "../includes/header.php";
?>


<div class="card">
<h2>Iniciar Sesión</h2>

<form action="../php/procesar_login_candidato.php" method="post">
    <div class="form-row">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-row">
        <label>Contraseña</label>
        <input type="password" name="password" required>
    </div>

    <button class="btn">Entrar</button>
</form>

<p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>

</div>

<?php include "../includes/footer.php"; ?>
