<?php
/**
 * pages/quienes-somos/mesa-directiva.php
 * Mesa directiva actual de la SAZ.
 */
$pageTitle       = 'Mesa directiva — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conoce la mesa directiva actual de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Mesa directiva</h1>
    <p class="mb-4">Periodo 2024–2026</p>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
      <div class="col">
        <div class="surface-card h-100 text-center">
          <div class="mb-3"><i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i></div>
          <h3 class="h6">Presidencia</h3>
          <p class="mb-1 fw-semibold">M.C. Iván Santamaría Najar</p>
          <p class="text-muted small mb-0">Maestria en Ciencias</p>
        </div>
      </div>
      <div class="col">
        <div class="surface-card h-100 text-center">
          <div class="mb-3"><i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i></div>
          <h3 class="h6">Secretaría</h3>
          <p class="mb-1 fw-semibold">M.C. Ciro Robles Berumen</p>
          <p class="text-muted small mb-0">Maestro en Ciencias</p>
        </div>
      </div>
      <div class="col">
        <div class="surface-card h-100 text-center">
          <div class="mb-3"><i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i></div>
          <h3 class="h6">Tesorería</h3>
          <p class="mb-1 fw-semibold">L.E. Armando García Castillo</p>
          <p class="text-muted small mb-0">Licenciado en Economía</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
