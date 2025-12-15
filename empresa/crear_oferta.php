<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$titulo = "Crear Oferta";
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/funciones.php';
$token = csrf_token();
?>
<div class="card">
  <h2>Crear oferta</h2>
  <form action="/php/crear_oferta.php" method="post">
    <input type="hidden" name="_csrf" value="<?= $token ?>">
    <div class="form-row"><label>Título</label><input type="text" name="titulo" required></div>
    <div class="form-row"><label>Descripción</label><textarea name="descripcion" rows="6" required></textarea></div>
    <div class="form-row"><label>Requisitos</label><textarea name="requisitos"></textarea></div>
    <div class="form-row"><label>Salario mínimo</label><input type="text" name="salario_min"></div>
    <div class="form-row"><label>Salario máximo</label><input type="text" name="salario_max"></div>
    <div class="form-row"><label>Ubicación</label><input type="text" name="ubicacion"></div>
    <div class="form-row"><label>Tipo contrato</label><input type="text" name="tipo_contrato"></div>
    <div class="form-row"><label>Jornada</label><input type="text" name="jornada"></div>
    <div class="form-row"><label>Modalidad (ID)</label><input type="number" name="idmodalidad" required></div>
    <div class="form-row"><label>Sector (ID)</label><input type="number" name="idsector" required></div>
    <button class="btn">Publicar oferta</button>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
