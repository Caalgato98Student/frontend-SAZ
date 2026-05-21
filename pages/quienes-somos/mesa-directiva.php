<?php
/**
 * pages/quienes-somos/mesa-directiva.php
 * Mesa directiva actual de la SAZ.
 */
$pageTitle       = 'Mesa directiva — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conoce la mesa directiva actual de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/configuracion.php';
require_once __DIR__ . '/../../includes/repositories/miembros.php';

$mesaDirectivaPeriodo = get_config('mesa_directiva_periodo') ?? '2024–2026';
$miembrosMesa         = get_mesa_directiva();

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Mesa directiva</h1>
    <p class="mb-4">Periodo <?= htmlspecialchars($mesaDirectivaPeriodo) ?></p>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
      <?php foreach ($miembrosMesa as $m): ?>
      <div class="col">
        <div class="surface-card h-100 text-center p-4 shadow-sm">
          <div class="mx-auto mb-3">
            <?php if ($m['imagen'] && file_exists($basePath . 'assets/img/miembros/' . $m['imagen'])): ?>
              <img src="<?= $basePath ?>assets/img/miembros/<?= htmlspecialchars($m['imagen']) ?>"
                   alt="<?= htmlspecialchars($m['nombre']) ?>"
                   class="member-photo-img-sm shadow-sm"
                   style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
              <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
            <?php endif; ?>
          </div>
          <h2 class="h6 mb-1"><?= htmlspecialchars($m['cargo'] ?? '') ?></h2>
          <p class="mb-1 fw-semibold"><?= htmlspecialchars($m['nombre']) ?></p>
          <p class="text-muted small mb-0"><?= htmlspecialchars($m['especialidad'] ?? '') ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';