<?php include 'includes/header.php'; ?>

<!-- Hero principal -->
<section class="hero">
  <div class="hero-overlay">
    <h1 class="hero-title">Agencia de colocación nº 1200000119</h1>
    <p class="hero-subtitle">Inscríbete en nuestra Agencia de Colocación</p>
    <p class="hero-text">Te ayudaremos a encontrar ofertas acordes a tu perfil</p>
  </div>
</section>

<!-- Opciones principales -->
<section class="options">
  <div class="option-block empresas">
    <img src="assets/img/cropped-empleado-1.png" alt="Empresas">
    <h2>Busco personal</h2>
    <p>¿Necesitas ampliar la plantilla de tu empresa?</p>
    <a href="empresa/" class="btn btn-light">ENVÍANOS TU OFERTA</a>
  </div>
  <div class="option-block candidatos">
    <img src="assets/img/cropped-candidato-1.png" alt="Candidatos">
    <h2>Busco empleo</h2>
    <p>Inscríbete para enviarnos tu curriculum</p>
    <a href="candidato/" class="btn btn-light">INSCRÍBETE AHORA</a>
  </div>
</section>

<!-- Cursos -->
<section class="courses">
  <h2>¿Quieres mejorar tu carrera profesional?</h2>
  <p>Realiza uno de nuestros cursos de formación para completar tu CV</p>
  <a href="https://www.formacom.es/formacion/" class="btn btn-secondary">NUESTROS CURSOS</a>
</section>

<!-- Últimas ofertas -->
<?php
require_once 'includes/funciones.php';

$conexion = conectarBD();

$sql = "
    SELECT o.id, o.titulo, e.nombre AS empresa, s.nombre AS sector
    FROM ofertas o
    JOIN empresas e ON o.idempresa = e.id
    JOIN sectores s ON o.idsector = s.id
    WHERE o.estado = 'publicada'
    ORDER BY o.fecha_publicacion DESC
    LIMIT 6
";

$result = $conexion->query($sql);
?>
<section class="latest-offers" id="ultimas-ofertas">
  <h2>Últimas ofertas publicadas</h2>
  <div class="offers-grid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <article class="offer-card">
          <h3><?= htmlspecialchars($row['titulo']) ?></h3>
          <p class="company"><?= htmlspecialchars($row['empresa']) ?></p>
          <a href="#por-definir" class="btn btn-primary">Ver oferta</a>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No hay ofertas publicadas en este momento.</p>
    <?php endif; ?>
  </div>
  <a href="#por-definir" class="btn btn-secondary-inverted">Ver todas las ofertas</a>
</section>

<?php include 'includes/footer.php'; ?>
