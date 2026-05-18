<?php
/**
 * partials/convocatorias.php
 * Muestra las 3 convocatorias más recientes en la página de inicio.
 * Activas con estilo prominente, inactivas con texto tenue.
 * Lee contenido desde la base de datos mediante el repositorio de convocatorias.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/repositories/convocatorias.php';
$convocatoriasTres = get_convocatorias_home(3);
?>

<section class="py-5" id="convocatorias">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <h2 class="section-title mb-0">Convocatorias</h2>
      <a href="<?= $basePath ?>pages/convocatorias/index.php" class="link-accent">Ver todas <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4">
      <?php if (!empty($convocatoriasTres)): ?>
        <?php foreach ($convocatoriasTres as $conv): ?>
          <?php $esActiva = !empty($conv['activa']); ?>
          <div class="col-md-4">
            <div class="surface-card h-100 <?= $esActiva ? 'convocatoria-activa' : 'convocatoria-inactiva' ?>">
              <div class="d-flex align-items-center gap-2 mb-2">
                <?php if ($esActiva): ?>
                  <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activa</span>
                <?php else: ?>
                  <span class="badge bg-secondary"><i class="bi bi-clock-history me-1"></i>Cerrada</span>
                <?php endif; ?>
              </div>
              <h3 class="h6 <?= $esActiva ? '' : 'text-muted' ?>"><?= htmlspecialchars($conv['titulo']) ?></h3>
              <?php if (!empty($conv['resumen'])): ?>
                <p class="small <?= $esActiva ? '' : 'text-muted' ?> mb-2"><?= htmlspecialchars($conv['resumen']) ?></p>
              <?php endif; ?>
              <p class="news-date mb-0">
                <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($conv['fecha']) ?>
                <?php if (!empty($conv['cierre'])): ?>
                  · Cierre: <?= htmlspecialchars($conv['cierre']) ?>
                <?php endif; ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="surface-card">
            <p class="mb-0 text-muted">No hay convocatorias publicadas aun.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
