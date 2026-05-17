<?php
/**
 * templates/observacion.php
 * Template reutilizable para páginas de observación (diurna, nocturna, solar).
 * Requiere: $observacion (array de get_observacion_por_slug()), $basePath
 */

// Si no hay datos, mostrar mensaje informativo y retornar
if (!$observacion) {
    echo '<section class="py-5"><div class="container">';
    echo '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>';
    echo 'Esta sección de observación aún no tiene contenido configurado en la base de datos.</div>';
    echo '</div></section>';
    return;
}
?>
<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          <?= htmlspecialchars($observacion['titulo']) ?>
        </li>
      </ol>
    </nav>

    <div class="text-center mb-5">
      <i class="<?= htmlspecialchars($observacion['icono'] ?? 'bi bi-eye') ?>" style="font-size: 3rem;"></i>
      <h1 class="section-title mt-3"><?= htmlspecialchars($observacion['titulo']) ?></h1>
      <?php if (!empty($observacion['descripcion_intro'])): ?>
      <p class="lead col-lg-8 mx-auto"><?= htmlspecialchars($observacion['descripcion_intro']) ?></p>
      <?php endif; ?>
    </div>

    <?php if (!empty($observacion['items'])): ?>
    <div class="row g-4">
      <?php foreach ($observacion['items'] as $item): ?>
      <div class="col-md-<?= count($observacion['items']) <= 3 ? '4' : '6' ?>">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3">
            <?php if (!empty($item['icono'])): ?>
              <i class="<?= htmlspecialchars($item['icono']) ?>"></i>
            <?php endif; ?>
            <?= htmlspecialchars($item['titulo']) ?>
          </h3>
          <p class="mb-0"><?= htmlspecialchars($item['descripcion'] ?? '') ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($observacion['recomendaciones'])): ?>
    <div class="surface-card mt-5">
      <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2"></i>Recomendaciones</h2>
      <?= $observacion['recomendaciones'] /* HTML de TinyMCE */ ?>
    </div>
    <?php endif; ?>
  </div>
</section>
