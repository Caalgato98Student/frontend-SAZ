<?php
/**
 * partials/astrofotografia.php
 * Muestra las 3 astrofotografías más recientes en la página de inicio.
 * Incluye fecha, descripción y crédito/fuente.
 * Click → lightbox (modal Bootstrap).
 * Lee contenido desde content/astrofotografia/*.json
 */

$astrofotos = [];
$dirAstro = __DIR__ . '/../content/astrofotografia/';
if (is_dir($dirAstro)) {
    foreach (glob($dirAstro . '*.json') as $archivo) {
        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            if (empty($datos['id'])) {
                $datos['id'] = basename($archivo, '.json');
            }
            $astrofotos[] = $datos;
        }
    }
    usort($astrofotos, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));
}

$astroTres = array_slice($astrofotos, 0, 3);
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
            <div class="surface-card h-100 card-hover" role="button" data-bs-toggle="modal" data-bs-target="#lightbox-<?= $idx ?>">
              <?php if (!empty($foto['imagen'])): ?>
                <img src="<?= $basePath ?>assets/img/astrofotografia/<?= htmlspecialchars($foto['imagen']) ?>"
                     alt="<?= htmlspecialchars($foto['descripcion']) ?>"
                     class="img-fluid rounded mb-3">
              <?php else: ?>
                <div class="placeholder-image placeholder-card mb-3" role="img" aria-label="Astrofotografia pendiente">
                  <i class="bi bi-stars" style="font-size: 2rem;"></i>
                </div>
              <?php endif; ?>
              <p class="news-date mb-1">
                <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($foto['fecha']) ?>
              </p>
              <p class="mb-1"><?= htmlspecialchars($foto['descripcion']) ?></p>
              <p class="text-muted small mb-0">
                <i class="bi bi-camera me-1"></i><?= htmlspecialchars($foto['colaborador']) ?>
                <?php if (!empty($foto['fuente'])): ?>
                  · <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($foto['fuente']) ?>
                <?php endif; ?>
              </p>
            </div>
          </div>

          <!-- Lightbox modal -->
          <div class="modal fade" id="lightbox-<?= $idx ?>" tabindex="-1" aria-label="<?= htmlspecialchars($foto['descripcion']) ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><?= htmlspecialchars($foto['descripcion']) ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                  <?php if (!empty($foto['imagen'])): ?>
                    <img src="<?= $basePath ?>assets/img/astrofotografia/<?= htmlspecialchars($foto['imagen']) ?>"
                         alt="<?= htmlspecialchars($foto['descripcion']) ?>"
                         class="img-fluid rounded">
                  <?php else: ?>
                    <div class="placeholder-image placeholder-hero">
                      <i class="bi bi-stars" style="font-size: 3rem;"></i>
                      <p>Imagen pendiente de publicacion</p>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="modal-footer justify-content-between">
                  <div>
                    <small class="text-muted">
                      <i class="bi bi-camera me-1"></i><?= htmlspecialchars($foto['colaborador']) ?>
                      · <?= htmlspecialchars($foto['fecha']) ?>
                    </small>
                  </div>
                  <div>
                    <?php if (!empty($foto['fuente'])): ?>
                      <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($foto['fuente']) ?></small>
                    <?php endif; ?>
                  </div>
                </div>
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
  </div>
</section>
