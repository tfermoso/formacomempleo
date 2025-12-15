<?php include './includes/header.php'; ?>

<h1>Recuperar contraseña</h1>

<form action="recuperar_procesar.php" method="POST" style="max-width:400px;">
    <label>Email asociado a la cuenta:</label>
    <input type="email" name="email" required>

    <button type="submit">Enviar enlace de recuperación</button>
</form>

<?php include './includes/footer.php'; ?>