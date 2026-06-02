<?php
/**
 * templates/actividad.php
 * Template reutilizable para páginas de actividades.
 *
 * Variables requeridas:
 *   $actividadTitulo  — nombre de la actividad
 *   $actividadIcono   — clase de ícono Bootstrap Icons
 *   $actividadDesc    — descripción principal
 *   $actividadItems   — array de elementos descriptivos
 *   $basePath         — ruta relativa a la raíz
 *
 * Variables opcionales:
 *   $actividadImagenesDir — carpeta relativa a la raíz que contiene las imágenes
 *                           (ej. 'assets/img/actividades/charlas/'). Se escanea
 *                           automáticamente: basta con agregar imágenes a la carpeta.
 */

$actividadImagenesDir = $actividadImagenesDir ?? null;

if (!function_exists('get_config')) {
    require_once __DIR__ . '/../includes/repositories/configuracion.php';
}
$_actividadInfoNota = get_config('actividades_info_nota') ?? 'Esta sección será actualizada conforme se programen nuevas sesiones. Para más detalles, contacta a la SAZ a través de la página de contacto.';

if (!isset($imagenes) || empty($imagenes)) {
    $imagenes = [];

    if ($actividadImagenesDir !== null) {
        $rootPath = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR;
        $scanPath = $rootPath . str_replace('/', DIRECTORY_SEPARATOR, rtrim($actividadImagenesDir, '/'));

        if (is_dir($scanPath)) {
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            foreach (scandir($scanPath) as $file) {
                if ($file === '.' || $file === '..') continue;
                if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedExt)) {
                    $imagenes[] = rtrim($actividadImagenesDir, '/') . '/' . $file;
                }
            }
            sort($imagenes);
        }
    }
}

$actividadImagenAlt = htmlspecialchars($actividadTitulo) . ' — Sociedad Astronómica de Zacatecas';
$carouselId         = 'carousel-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($actividadTitulo));
$totalImagenes      = count($imagenes);
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($actividadTitulo) ?></li>
      </ol>
    </nav>

    <!-- Hero: texto + imagen en layout split -->
    <div class="row align-items-center g-4 g-lg-5 mb-5">

      <!-- Columna de texto -->
      <div class="col-lg-5">
        <span class="text-accent text-uppercase small fw-semibold letter-spacing-1 d-block mb-2">
          <i class="<?= $actividadIcono ?> me-1"></i>Actividades SAZ
        </span>
        <h1 class="section-title mb-3"><?= htmlspecialchars($actividadTitulo) ?></h1>
        <p class="lead text-muted mb-0"><?= htmlspecialchars($actividadDesc) ?></p>
      </div>

      <!-- Columna de imagen / carousel -->
      <div class="col-lg-7">

        <?php if ($totalImagenes === 0): ?>
          <!-- Sin imágenes: placeholder -->
          <div class="placeholder-image placeholder-hero d-flex flex-column align-items-center justify-content-center gap-2"
               role="img"
               aria-label="Imagen de <?= htmlspecialchars($actividadTitulo) ?> — próximamente">
            <i class="<?= $actividadIcono ?>" style="font-size: 2.5rem; opacity: 0.5;"></i>
            <span class="small fw-semibold">Imagen próximamente</span>
          </div>

        <?php elseif ($totalImagenes === 1): ?>
          <!-- Una sola imagen: sin chrome de carousel -->
          <img
            src="<?= $basePath . htmlspecialchars($imagenes[0]) ?>"
            alt="<?= $actividadImagenAlt ?>"
            class="img-fluid rounded-3 shadow-sm w-100 actividad-carousel-img"
            loading="lazy"
          >

        <?php else: ?>
          <!-- Múltiples imágenes: Bootstrap Carousel con auto-play -->
          <div id="<?= $carouselId ?>"
               class="carousel slide rounded-3 shadow-sm overflow-hidden"
               data-bs-ride="carousel"
               data-bs-interval="4000">

            <!-- Indicadores -->
            <div class="carousel-indicators">
              <?php foreach ($imagenes as $idx => $src): ?>
                <button type="button"
                        data-bs-target="#<?= $carouselId ?>"
                        data-bs-slide-to="<?= $idx ?>"
                        <?= $idx === 0 ? 'class="active" aria-current="true"' : '' ?>
                        aria-label="Imagen <?= $idx + 1 ?>">
                </button>
              <?php endforeach; ?>
            </div>

            <!-- Slides -->
            <div class="carousel-inner">
              <?php foreach ($imagenes as $idx => $src): ?>
                <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                  <img
                    src="<?= $basePath . htmlspecialchars($src) ?>"
                    alt="<?= $actividadImagenAlt ?>"
                    class="d-block w-100 actividad-carousel-img"
                    loading="lazy"
                  >
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Controles -->
            <button class="carousel-control-prev" type="button"
                    data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button"
                    data-bs-target="#<?= $carouselId ?>" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Siguiente</span>
            </button>

          </div><!-- /carousel -->
        <?php endif; ?>

      </div>
    </div><!-- /row hero -->

    <?php if (!empty($actividadItems)): ?>
      <div class="row g-4">
        <?php foreach ($actividadItems as $item): ?>
          <div class="col-md-6">
            <div class="surface-card h-100">
              <h3 class="h6 mb-2"><?= htmlspecialchars($item['titulo']) ?></h3>
              <p class="text-muted small mb-0"><?= htmlspecialchars($item['descripcion']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="surface-card mt-5">
      <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2"></i>Información</h2>
      <p class="mb-0 text-muted"><?= htmlspecialchars($_actividadInfoNota) ?></p>
    </div>
  </div>
</section>
