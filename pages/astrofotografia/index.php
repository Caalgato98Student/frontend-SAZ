<?php
/**
 * pages/astrofotografia/index.php
 * Galería de astrofotografía organizada por colaborador.
 * Click en imagen → lightbox (modal Bootstrap).
 * Lee contenido desde content/astrofotografia/*.json
 */
$pageTitle       = 'Astrofotografia — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Galeria de astrofotografia de los miembros y colaboradores de la SAZ.';
$basePath        = '../../';

// Leer todas las fotos
$fotos = [];
$dirAstro = __DIR__ . '/../../content/astrofotografia/';
if (is_dir($dirAstro)) {
    foreach (glob($dirAstro . '*.json') as $archivo) {
        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            if (empty($datos['id'])) {
                $datos['id'] = basename($archivo, '.json');
            }
            $fotos[] = $datos;
        }
    }
    usort($fotos, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));
}

// Agrupar por colaborador
$porColaborador = [];
foreach ($fotos as $foto) {
    $nombre = $foto['colaborador'] ?? 'Sin acreditar';
    $porColaborador[$nombre][] = $foto;
}

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-2">Galeria de Astrofotografia</h1>
    <p class="lead mb-5">Imagenes del cosmos capturadas por los miembros y colaboradores de la SAZ. Haz clic en cualquier imagen para ampliarla.</p>

    <?php if (!empty($porColaborador)): ?>
      <?php foreach ($porColaborador as $colaborador => $imagenes): ?>
        <div class="mb-5">
          <h2 class="h4 mb-3">
            <i class="bi bi-camera me-2"></i><?= htmlspecialchars($colaborador) ?>
          </h2>
          <div class="gallery-grid">
            <?php foreach ($imagenes as $idx => $foto): ?>
              <?php $modalId = 'galeria-' . preg_replace('/[^a-z0-9]/', '', strtolower($foto['id'])); ?>
              <div class="gallery-grid-item">
                <div class="surface-card card-hover" role="button" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                  <?php if (!empty($foto['imagen'])): ?>
                    <img src="<?= $basePath ?>assets/img/astrofotografia/<?= htmlspecialchars($foto['imagen']) ?>"
                         alt="<?= htmlspecialchars($foto['descripcion']) ?>"
                         class="img-fluid rounded mb-3">
                  <?php else: ?>
                    <div class="placeholder-image placeholder-card mb-3" role="img" aria-label="Imagen pendiente">
                      <i class="bi bi-stars" style="font-size: 2rem;"></i>
                    </div>
                  <?php endif; ?>
                  <p class="news-date mb-1"><i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($foto['fecha']) ?></p>
                  <p class="small mb-0"><?= htmlspecialchars($foto['descripcion']) ?></p>
                  <?php if (!empty($foto['fuente'])): ?>
                    <p class="text-muted small mb-0 mt-1"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($foto['fuente']) ?></p>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Lightbox modal -->
              <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
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
                          <p class="mt-2">Imagen pendiente de publicacion</p>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="modal-footer justify-content-between">
                      <small class="text-muted">
                        <i class="bi bi-camera me-1"></i><?= htmlspecialchars($colaborador) ?>
                        · <?= htmlspecialchars($foto['fecha']) ?>
                      </small>
                      <?php if (!empty($foto['fuente'])): ?>
                        <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($foto['fuente']) ?></small>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="surface-card">
        <p class="mb-0 text-muted">La galeria sera publicada proximamente.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
