<?php
$pageTitle       = 'Observacion Diurna — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Sesiones educativas de observacion diurna para publico general.';
$basePath        = '../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Observacion Diurna</li>
      </ol>
    </nav>

    <div class="text-center mb-5">
      <i class="bi bi-brightness-high text-info" style="font-size: 3rem;"></i>
      <h1 class="section-title mt-3">Observacion Diurna</h1>
      <p class="lead col-lg-8 mx-auto">Sesiones educativas para publico general donde se exploran fenomenos astronomicos visibles durante el dia.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-moon-stars me-2"></i>La Luna de dia</h3>
          <p class="mb-0">Observacion de la Luna cuando es visible durante el dia. Se explican las fases lunares y la mecanica orbital.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-globe me-2"></i>Planetas visibles</h3>
          <p class="mb-0">En condiciones favorables, Venus puede observarse a plena luz del dia. Se explica por que y como localizarlo.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-compass me-2"></i>Gnomonica</h3>
          <p class="mb-0">Uso de relojes de sol y proyeccion de sombras para comprender el movimiento aparente del Sol y las estaciones del ano.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-cloud-sun me-2"></i>Meteorologia basica</h3>
          <p class="mb-0">Introduccion a la atmosfera terrestre: como afecta la observacion astronomica y como interpretar condiciones del cielo.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
