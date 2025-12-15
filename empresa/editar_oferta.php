<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
if ($_SESSION['usuario']['rol'] !== 'empresa') die("Acceso denegado");

include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$empresa_id = $_SESSION['usuario']['empresa_id'];

$stmt = $pdo->prepare("SELECT * FROM ofertas WHERE id=? AND idempresa=?");
$stmt->execute([$id, $empresa_id]);
$oferta = $stmt->fetch();
if (!$oferta) die("Oferta no encontrada");

$token = csrf_token();
?>
<div class="card">
<h2>Editar Oferta</h2>
<form action="/php/crear_oferta.php?id=<?= $id ?>" method="post">
<input type="hidden" name="_csrf" value="<?= $token ?>">
<div class="form-row"><label>Título</label><input type="text" name="titulo" value="<?= htmlspecialchars($oferta['titulo']) ?>" required></div>
<div class="form-row"><label>Descripción</label><textarea name="descripcion" rows="6"><?= htmlspecialchars($oferta['descripcion']) ?></textarea></div>
<button class="btn">Actualizar</button>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
