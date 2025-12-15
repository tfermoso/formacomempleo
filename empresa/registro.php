<?php
$titulo = "Registro Empresa";
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/funciones.php';
$token = csrf_token();
?>
<div class="card">
  <h2>Registro Empresa</h2>
  <form action="/php/procesar_registro.php" method="post">
    <input type="hidden" name="_csrf" value="<?= $token ?>">
    <div class="form-row">
      <label>CIF</label>
      <input type="text" name="cif" required>
    </div>
    <div class="form-row">
      <label>Nombre empresa</label>
      <input type="text" name="nombre" required>
    </div>
    <div class="form-row">
      <label>Email contacto</label>
      <input type="email" name="email_contacto" required>
    </div>
    <div class="form-row">
      <label>Teléfono</label>
      <input type="text" name="telefono">
    </div>
    <div class="form-row">
      <label>Persona de contacto</label>
      <input type="text" name="persona_contacto">
    </div>
    <div class="form-row">
      <label>Contraseña (para panel empresa)</label>
      <input type="password" name="password" required>
    </div>
    <button class="btn" type="submit">Crear cuenta empresa</button>
  </form>
  <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
