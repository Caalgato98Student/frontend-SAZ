<?php
/**
 * pages/convocatorias/index.php
 * Todas las convocatorias: activas primero con estilo prominente,
 * inactivas después con texto tenue.
 * Lee contenido desde la base de datos mediante repositorio.
 */
$pageTitle       = 'Convocatorias — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Convocatorias abiertas y cerradas de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/convocatorias.php';
$convocatorias = get_convocatorias_publicas();

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Convocatorias</h1>

    <?php if (!empty($convocatorias)): ?>
      <div class="row g-4">
        <?php foreach ($convocatorias as $conv): ?>
          <?php $esActiva = !empty($conv['activa']); ?>
          <div class="col-md-6">
            <div class="surface-card h-100 <?= $esActiva ? 'convocatoria-activa' : 'convocatoria-inactiva' ?> d-flex flex-column justify-content-between">
              
              <div>
                <?php if (!empty($conv['imagen'])): ?>
                  <div class="mb-3 overflow-hidden rounded" style="max-height: 220px;">
                    <img src="<?= $basePath ?>assets/img/convocatorias/<?= htmlspecialchars($conv['imagen']) ?>" 
                         class="img-fluid w-100" 
                         alt="Portada - <?= htmlspecialchars($conv['titulo']) ?>"
                         style="object-fit: cover; object-position: center;">
                  </div>
                <?php endif; ?>

                <div class="d-flex align-items-center gap-2 mb-3">
                  <?php if ($esActiva): ?>
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activa</span>
                  <?php else: ?>
                    <span class="badge bg-secondary"><i class="bi bi-clock-history me-1"></i>Cerrada</span>
                  <?php endif; ?>
                  <span class="news-date mb-0">
                    <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($conv['fecha']) ?>
                  </span>
                </div>

                <h3 class="h5 <?= $esActiva ? '' : 'text-muted' ?> fw-bold mb-3"><?= htmlspecialchars($conv['titulo']) ?></h3>
                
                <?php if (!empty($conv['resumen'])): ?>
                  <p class="<?= $esActiva ? '' : 'text-muted' ?> mb-3 text-secondary" style="font-size: 0.95rem; line-height: 1.5;"><?= htmlspecialchars($conv['resumen']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($conv['contenido'])): ?>
                  <div class="convocatoria-body small <?= $esActiva ? '' : 'text-muted' ?> mb-3">
                    <?= $conv['contenido'] ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mt-3 pt-3 border-top">
                <?php if (!empty($conv['cierre'])): ?>
                  <p class="small text-danger fw-semibold mb-2">
                    <i class="bi bi-calendar-x me-1"></i>Fecha de cierre: <?= htmlspecialchars($conv['cierre']) ?>
                  </p>
                <?php endif; ?>
                
                <?php if (!empty($conv['pdf'])): ?>
                  <div class="mt-2">
                    <a href="<?= $basePath . 'assets/pdf/' . htmlspecialchars($conv['pdf']) ?>" 
                       class="btn btn-primary btn-sm" 
                       target="_blank"
                       aria-label="Descargar Convocatoria en formato PDF para <?= htmlspecialchars($conv['titulo']) ?>">
                      <i class="bi bi-file-earmark-pdf me-1"></i> Descargar Convocatoria (PDF)
                    </a>
                  </div>
                <?php endif; ?>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="surface-card">
        <p class="mb-0 text-muted">No hay convocatorias publicadas aun.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
