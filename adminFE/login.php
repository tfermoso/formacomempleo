<?php
$titulo = "Admin Login";
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/funciones.php';
$token = csrf_token();
?>
<div class="card">
  <h2>Admin - Inicio sesión</h2>
  <form action="/php/procesar_login_admin.php" method="post">
    <input type="hidden" name="_csrf" value="<?= $token ?>">
    <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
    <div class="form-row"><label>Contraseña</label><input type="password" name="password" required></div>
    <button class="btn">Entrar</button>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
