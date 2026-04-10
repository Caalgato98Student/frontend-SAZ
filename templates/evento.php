<?php
/**
 * templates/evento.php
 * Template reutilizable para páginas de eventos.
 * Lee datos desde un archivo JSON en content/eventos/.
 *
 * Variables requeridas antes de incluir:
 *   $eventoSlug — nombre del archivo JSON (sin extensión)
 *   $basePath   — ruta relativa a la raíz del proyecto
 */

$eventoFile = __DIR__ . '/../content/eventos/' . $eventoSlug . '.json';
$evento = file_exists($eventoFile)
    ? json_decode(file_get_contents($eventoFile), true)
    : null;

if (!$evento) {
    echo '<section class="py-5"><div class="container"><div class="alert alert-warning">No se encontro informacion del evento.</div></div></section>';
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
    <p class="lead mb-4"><?= htmlspecialchars($evento['descripcion']) ?></p>

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

    <!-- Ediciones anuales -->
    <?php if (!empty($evento['ediciones'])): ?>
      <h2 class="h4 mb-4">Información</h2>
      <div class="row g-4">
        <?php foreach ($evento['ediciones'] as $ed): ?>
          <div class="col-md-6">
            <div class="surface-card h-100">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-primary"><?= htmlspecialchars($ed['anio']) ?></span>
                <span class="news-date mb-0">
                  <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($ed['fecha']) ?>
                </span>
              </div>
              <?php if (!empty($ed['lugar'])): ?>
                <p class="small text-muted mb-2">
                  <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($ed['lugar']) ?>
                </p>
              <?php endif; ?>
              <p class="mb-0"><?= htmlspecialchars($ed['resumen']) ?></p>

              <?php if (!empty($ed['pdf'])): ?>
                <div class="mt-4">
                  <a href="<?= $basePath ?>assets/pdf/eventos/<?= htmlspecialchars($ed['pdf']) ?>" 
                    target="_blank" 
                    class="btn btn-danger d-flex align-items-center justify-content-center gap-2 mb-3">
                    <i class="bi bi-file-earmark-pdf-fill fs-4"></i>
                    <span>Descargar Programa Completo (PDF)</span>
                  </a>

                  <div class="d-none d-md-block">
                    <div class="ratio ratio-16x9 border rounded shadow-sm">
                      <embed src="<?= $basePath ?>assets/pdf/eventos/<?= htmlspecialchars($ed['pdf']) ?>" type="application/pdf" />
                    </div>
                    <p class="small text-muted text-center mt-2">¿No puedes ver el archivo? Usa el botón de descarga.</p>
                  </div>
                </div>
              <?php endif; ?>

              <?php if (!empty($ed['imagenes'])): ?>
                <div class="row g-2 mt-3">
                  <?php foreach ($ed['imagenes'] as $img): ?>
                    <div class="col-sm-6">
                      <figure class="mb-0">
                        <img src="<?= $basePath ?>assets/img/eventos/<?= htmlspecialchars($img['archivo']) ?>"
                            alt="<?= htmlspecialchars($img['label']) ?>"
                            class="img-fluid rounded border shadow-sm">
                        <figcaption class="small text-center mt-1 text-muted">
                          <?= htmlspecialchars($img['label']) ?>
                        </figcaption>
                      </figure>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php elseif (!empty($ed['imagen'])): ?>
                <img src="<?= $basePath ?>assets/img/eventos/<?= htmlspecialchars($ed['imagen']) ?>"
                    class="img-fluid rounded mt-3">
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
