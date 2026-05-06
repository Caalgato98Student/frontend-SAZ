<?php
/**
 * templates/actividad.php
 * Template reutilizable para páginas de actividades.
 * Páginas estáticas informativas.
 *
 * Variables requeridas:
 *   $actividadTitulo  — nombre de la actividad
 *   $actividadIcono   — clase de ícono Bootstrap Icons
 *   $actividadDesc    — descripción principal
 *   $actividadItems   — array de elementos descriptivos
 *   $basePath         — ruta relativa a la raíz
 *
 * Variables opcionales:
 *   $actividadImagen  — ruta relativa a la imagen (desde $basePath).
 *                       Si está vacía o no se define, se muestra un placeholder.
 *   $actividadImagenAlt — texto alternativo para la imagen (accesibilidad).
 */
$actividadImagen    = $actividadImagen    ?? null;
$actividadImagenAlt = $actividadImagenAlt ?? htmlspecialchars($actividadTitulo) . ' — Sociedad Astronómica de Zacatecas';
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

      <!-- Columna de imagen -->
      <div class="col-lg-7">
        <?php if (!empty($actividadImagen)): ?>
          <img
            src="<?= $basePath . htmlspecialchars($actividadImagen) ?>"
            alt="<?= $actividadImagenAlt ?>"
            class="img-fluid rounded-3 shadow-sm w-100"
            style="object-fit: cover; max-height: 360px;"
            loading="lazy"
          >
        <?php else: ?>
          <!-- Placeholder: reemplazar src con la imagen real cuando esté disponible -->
          <div class="placeholder-image placeholder-hero d-flex flex-column align-items-center justify-content-center gap-2" role="img" aria-label="Imagen de <?= htmlspecialchars($actividadTitulo) ?> — próximamente">
            <i class="<?= $actividadIcono ?>" style="font-size: 2.5rem; opacity: 0.5;"></i>
            <span class="small fw-semibold">Imagen próximamente</span>
            <code class="small opacity-50">assets/img/actividades/<?= strtolower(htmlspecialchars($actividadTitulo)) ?>.png</code>
          </div>
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
      <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2"></i>Informacion</h2>
      <p class="mb-0 text-muted">Esta seccion sera actualizada conforme se programen nuevas sesiones. Para mas detalles, contacta a la SAZ a traves de la pagina de <a href="<?= $basePath ?>pages/contacto/index.php" class="link-accent">contacto</a>.</p>
    </div>
  </div>
</section>
