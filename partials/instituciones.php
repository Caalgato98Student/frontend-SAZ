<?php
if (!function_exists('get_config')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/repositories/configuracion.php';
}
if (!function_exists('get_instituciones_activas')) {
    require_once __DIR__ . '/../includes/repositories/instituciones.php';
}
$_instTitulo      = get_config('instituciones_titulo')      ?? 'Instituciones con las que colaboramos';
$_instDescripcion = get_config('instituciones_descripcion') ?? 'Colaboramos con universidades, centros de investigación y organizaciones de divulgación científica a nivel regional, nacional e internacional.';
$_instituciones   = get_instituciones_activas();
?>
<!-- ============================================================
     partials/instituciones.php
     Carrusel de instituciones colaboradoras.
     Cambio automático cada 4 segundos, con flechas y puntos.
     ============================================================ -->
<section id="instituciones-colaboradoras" class="py-5 section-alt">
  <div class="container">
    <h2 class="section-title mb-3"><?= htmlspecialchars($_instTitulo) ?></h2>
    <p><?= htmlspecialchars($_instDescripcion) ?></p>

    <?php if (!empty($_instituciones)): ?>
    <div id="institucionesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
      <!-- Indicadores (dots) -->
      <div class="carousel-indicators">
        <?php foreach ($_instituciones as $i => $_inst): ?>
        <button type="button" data-bs-target="#institucionesCarousel" data-bs-slide-to="<?= $i ?>"
          <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?>
          aria-label="Slide <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
      </div>

      <div class="carousel-inner">
        <?php foreach ($_instituciones as $i => $_inst): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
          <?php if (!empty($_inst['imagen'])): ?>
            <?php if (!empty($_inst['url'])): ?>
            <a href="<?= htmlspecialchars($_inst['url']) ?>" target="_blank" rel="noopener noreferrer">
              <img src="<?= $basePath ?>assets/img/instituciones/<?= htmlspecialchars($_inst['imagen']) ?>"
                   alt="<?= htmlspecialchars($_inst['nombre']) ?>"
                   class="img-fluid mx-auto d-block" style="max-height: 120px;">
            </a>
            <?php else: ?>
            <img src="<?= $basePath ?>assets/img/instituciones/<?= htmlspecialchars($_inst['imagen']) ?>"
                 alt="<?= htmlspecialchars($_inst['nombre']) ?>"
                 class="img-fluid mx-auto d-block" style="max-height: 120px;">
            <?php endif; ?>
          <?php else: ?>
          <div class="placeholder-image placeholder-hero">
            <i class="bi bi-building" style="font-size: 2.5rem;"></i>
            <p class="mt-2"><?= htmlspecialchars($_inst['nombre']) ?></p>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Controles prev/next -->
      <button class="carousel-control-prev" type="button" data-bs-target="#institucionesCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#institucionesCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
      </button>
    </div>
    <?php endif; ?>
  </div>
</section>
