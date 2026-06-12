<?php
/**
 * templates/evento.php
 * Template reutilizable para páginas de eventos.
 * Lee datos desde la base de datos mediante el repositorio de eventos.
 *
 * Variables requeridas antes de incluir:
 *   $eventoSlug — slug del evento (columna `slug` en la tabla `eventos`)
 *   $basePath   — ruta relativa a la raíz del proyecto
 */

if (!isset($evento)) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/repositories/eventos.php';
    $evento = get_evento_completo($eventoSlug);
}

if (!$evento) {
    echo '<section class="py-5"><div class="container"><div class="alert alert-warning">No se encontró información del evento.</div></div></section>';
    return;
}
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($evento['titulo']) ?></li>
      </ol>
    </nav>

    <h1 class="section-title mb-3"><?= htmlspecialchars($evento['titulo']) ?></h1>
    <?php if (!empty($evento['descripcion'])): ?>
    <div class="lead mb-4"><?= $evento['descripcion'] ?></div>
    <?php endif; ?>

    <?php if (!empty($evento['imagen_principal'])): ?>
      <img src="<?= $basePath ?>assets/img/eventos/<?= htmlspecialchars($evento['imagen_principal']) ?>"
           alt="<?= htmlspecialchars($evento['titulo']) ?>"
           class="img-fluid rounded-3 mb-5">
    <?php else: ?>
      <div class="placeholder-image placeholder-hero mb-5">
        <i class="bi bi-calendar-event" style="font-size: 3rem;"></i>
        <p class="mt-2 mb-0">Imagen del evento pendiente</p>
      </div>
    <?php endif; ?>

    <?php if (!empty($evento['ediciones'])): ?>
      <h2 class="h4 mb-4">Información</h2>
      <div class="row g-4">
        <?php foreach ($evento['ediciones'] as $ed): ?>
          <div class="col-md-6">
            <div class="surface-card h-100 d-flex flex-column justify-content-between">
              
              <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge bg-primary"><?= htmlspecialchars($ed['anio']) ?></span>
                  <span class="news-date mb-0">
                    <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($ed['fecha']) ?>
                  </span>
                </div>
                
                <?php if (!empty($ed['lugar'])): ?>
                  <p class="small text-muted mb-2">
                    <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars(html_entity_decode($ed['lugar'], ENT_QUOTES, 'UTF-8')) ?>
                  </p>
                <?php endif; ?>

                <?php if (!empty($ed['resumen'])): ?>
                  <div class="mt-2 evento-resumen small text-secondary"><?= $ed['resumen'] ?></div>
                <?php endif; ?>
              </div>

              <div>
                <?php if (!empty($ed['pdf'])): ?>
                  <div class="mt-4">
                    <a href="<?= $basePath ?>assets/pdf/eventos/<?= htmlspecialchars($ed['pdf']) ?>" 
                      target="_blank" 
                      class="btn btn-danger btn-sm d-flex align-items-center justify-content-center gap-2 mb-3"
                      aria-label="Descargar Programa Completo en PDF para la edición <?= htmlspecialchars($ed['anio']) ?>">
                      <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                      <span>Descargar Programa Completo (PDF)</span>
                    </a>

                    <div class="d-none d-md-block">
                      <div class="ratio ratio-16x9 border rounded shadow-sm">
                        <embed src="<?= $basePath ?>assets/pdf/eventos/<?= htmlspecialchars($ed['pdf']) ?>" type="application/pdf" />
                      </div>
                      <p class="small text-muted text-center mt-2" style="font-size: 0.75rem;">¿No puedes ver el archivo? Usa el botón de descarga.</p>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if (!empty($ed['imagenes']) && count($ed['imagenes']) > 1): ?>
                  <?php 
                    $carouselId = 'carousel-edicion-' . $ed['id']; 
                    $totalImg = count($ed['imagenes']);
                    $eventoAltBase = htmlspecialchars($evento['titulo']) . ' — Edición ' . htmlspecialchars($ed['anio']);
                  ?>
                  <div id="<?= $carouselId ?>" class="carousel slide rounded-3 shadow-sm overflow-hidden mt-4" data-bs-ride="carousel" data-bs-interval="4000">
                    
                    <div class="carousel-indicators">
                      <?php for ($i = 0; $i < $totalImg; $i++): ?>
                        <button type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Ver imagen <?= $i + 1 ?> de la edición <?= htmlspecialchars($ed['anio']) ?>"></button>
                      <?php endfor; ?>
                    </div>

                    <div class="carousel-inner">
                      <?php foreach ($ed['imagenes'] as $idx => $img): ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                          <img src="<?= $basePath ?>assets/img/eventos/<?= htmlspecialchars($img['archivo']) ?>" 
                               alt="<?= !empty($img['label']) ? htmlspecialchars($img['label']) : $eventoAltBase . ' - Galería ' . ($idx + 1) ?>" 
                               class="d-block w-100" 
                               style="max-height: 300px; object-fit: cover;"
                               loading="lazy">
                          <?php if (!empty($img['label'])): ?>
                            <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.65); border-radius: 4px; padding: 4px 8px; bottom: 10px;">
                              <p class="mb-0 small text-white"><?= htmlspecialchars($img['label']) ?></p>
                            </div>
                          <?php endif; ?>
                        </div>
                      <?php endforeach; ?>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Anterior imagen</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Siguiente imagen</span>
                    </button>
                  </div>

                <?php elseif (!empty($ed['imagenes']) && count($ed['imagenes']) === 1): ?>
                  <div class="mt-4 overflow-hidden rounded-3 shadow-sm">
                    <img src="<?= $basePath ?>assets/img/eventos/<?= htmlspecialchars($ed['imagenes'][0]['archivo']) ?>" 
                         alt="<?= !empty($ed['imagenes'][0]['label']) ? htmlspecialchars($ed['imagenes'][0]['label']) : htmlspecialchars($evento['titulo']) ?>" 
                         class="img-fluid w-100"
                         style="max-height: 300px; object-fit: cover;"
                         loading="lazy">
                  </div>

                <?php elseif (!empty($ed['imagen'])): ?>
                  <div class="mt-4 overflow-hidden rounded-3 shadow-sm">
                    <img src="<?= $basePath ?>assets/img/eventos/<?= htmlspecialchars($ed['imagen']) ?>" 
                         alt="Edición <?= htmlspecialchars($ed['anio']) ?> del evento <?= htmlspecialchars($evento['titulo']) ?>" 
                         class="img-fluid w-100"
                         style="max-height: 300px; object-fit: cover;"
                         loading="lazy">
                  </div>
                <?php endif; ?>
              </div>

            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>
