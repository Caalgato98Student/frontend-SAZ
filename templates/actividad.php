<?php
/**
 * templates/actividad.php
 * Template reutilizable para páginas de actividades.
 * Páginas estáticas informativas.
 *
 * Variables requeridas:
 *   $actividadTitulo — nombre de la actividad
 *   $actividadIcono  — clase de ícono Bootstrap Icons
 *   $actividadDesc   — descripción principal
 *   $actividadItems  — array de elementos descriptivos
 *   $basePath        — ruta relativa a la raíz
 */
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($actividadTitulo) ?></li>
      </ol>
    </nav>

    <div class="text-center mb-5">
      <i class="<?= $actividadIcono ?> text-primary" style="font-size: 3rem;"></i>
      <h1 class="section-title mt-3"><?= htmlspecialchars($actividadTitulo) ?></h1>
      <p class="lead col-lg-8 mx-auto"><?= htmlspecialchars($actividadDesc) ?></p>
    </div>

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
