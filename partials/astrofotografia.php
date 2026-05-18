<?php
/**
 * partials/astrofotografia.php
 * Muestra las 3 astrofotografías más recientes en la página de inicio.
 * Incluye fecha, descripción y crédito/fuente.
 * Click → lightbox (modal Bootstrap).
 * Lee contenido desde la base de datos vía repositorio.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/repositories/astrofotografia.php';
$astroTres = get_astrofotos_home(3);

if (!isset($basePath)) {
    $basePath = '../'; // Fallback por si se accede al partial directamente
}
?>

<section id="astrofotografia" class="py-5 section-alt">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <h2 class="section-title mb-0">Astrofotografia</h2>
      <a href="<?= $basePath ?>pages/astrofotografia/index.php" class="link-accent">Ver galeria completa <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php if (!empty($astroTres)): ?>
        <?php foreach ($astroTres as $idx => $foto): ?>
          <div class="col">
            <div class="surface-card h-100 card-hover p-0 overflow-hidden" role="button" data-bs-toggle="modal" data-bs-target="#lightbox-<?= $idx ?>">
              <?php if (!empty($foto['imagen'])): ?>
                <img src="<?= $basePath ?>assets/img/astrofotografia/<?= htmlspecialchars($foto['imagen']) ?>"
                     alt="<?= htmlspecialchars($foto['descripcion']) ?>"
                     class="astro-gallery-img">
              <?php else: ?>
                <div class="placeholder-image placeholder-card" role="img" aria-label="Astrofotografia pendiente">
                  <i class="bi bi-stars" style="font-size: 2rem;"></i>
                </div>
              <?php endif; ?>
              <div class="p-3">
                <p class="news-date mb-1">
                  <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($foto['fecha']) ?>
                </p>
                <p class="mb-1"><?= htmlspecialchars($foto['descripcion']) ?></p>
                <p class="text-muted small mb-0">
                  <i class="bi bi-camera me-1"></i><?= htmlspecialchars($foto['fotografo'] ?? $foto['colaborador'] ?? 'Miembro SAZ') ?>
                  <?php if (!empty($foto['fuente'])): ?>
                    · <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($foto['fuente']) ?>
                  <?php endif; ?>
                </p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for ($i = 0; $i < 3; $i++): ?>
          <div class="col">
            <div class="surface-card h-100">
              <div class="placeholder-image placeholder-card mb-3" role="img" aria-label="Astrofotografia pendiente">
                <i class="bi bi-stars" style="font-size: 2rem;"></i>
              </div>
              <p class="news-date mb-1">—</p>
              <p class="mb-0">Imagen pendiente de publicacion</p>
            </div>
          </div>
        <?php endfor; ?>
      <?php endif; ?>
    </div>

    <?php /* ── Lightbox modals (fuera del row para no romper el grid) ── */ ?>
    <?php if (!empty($astroTres)): ?>
      <?php foreach ($astroTres as $idx => $foto): ?>
        <div class="modal fade" id="lightbox-<?= $idx ?>" tabindex="-1" aria-label="<?= htmlspecialchars($foto['descripcion']) ?>" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= htmlspecialchars($foto['descripcion']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>

              <div class="modal-body">
                <?php if (!empty($foto['imagen'])): ?>
                    <img src="<?= $basePath ?>assets/img/astrofotografia/<?= htmlspecialchars($foto['imagen']) ?>"
                        alt="<?= htmlspecialchars($foto['titulo'] ?? 'Astrofotografía') ?>"
                        class="img-fluid rounded mb-3">
                <?php endif; ?>

                <?php if (!empty($foto['descripcion'])): ?>
                    <p class="text-start"><?= htmlspecialchars($foto['descripcion']) ?></p>
                <?php endif; ?>

                <?php if (!empty($foto['hardware']) && is_array($foto['hardware'])): ?>
                    <div class="text-start border-top pt-2 mt-2">
                        <small class="fw-bold d-block mb-1">Equipo utilizado:</small>
                        <ul class="list-inline small text-muted">
                            <?php foreach ($foto['hardware'] as $tipo => $equipo): ?>
                                <?php if (!empty($equipo)): ?>
                                    <li class="list-inline-item">
                                        <strong><?= ucfirst($tipo) ?>:</strong> <?= htmlspecialchars($equipo) ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($foto['parametros']) && is_array($foto['parametros'])): ?>
                    <div class="text-start mt-2">
                        <small class="fw-bold d-block mb-1">Detalles técnicos:</small>
                        <ul class="list-inline small text-muted">
                            <?php foreach ($foto['parametros'] as $param => $val): ?>
                                <?php if (!empty($val)): ?>
                                    <li class="list-inline-item">
                                        <strong><?= ucfirst($param) ?>:</strong> <?= htmlspecialchars($val) ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
              </div>

              <div class="modal-footer justify-content-between">
                  <div>
                      <small class="text-muted">
                          <i class="bi bi-camera me-1"></i>
                          <?= htmlspecialchars($foto['fotografo'] ?? $foto['colaborador'] ?? 'Miembro SAZ') ?>
                          <?php if (!empty($foto['fecha'])): ?>
                              · <?= htmlspecialchars($foto['fecha']) ?>
                          <?php endif; ?>
                      </small>
                  </div>
                  <?php if (!empty($foto['post_procesamiento'])): ?>
                      <div class="w-100 text-start mt-1">
                          <small class="text-muted italic">
                              <strong>Procesado:</strong> <?= htmlspecialchars($foto['post_procesamiento']) ?>
                          </small>
                      </div>
                  <?php endif; ?>
              </div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
