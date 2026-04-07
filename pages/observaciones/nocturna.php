<?php
$pageTitle       = 'Observacion Nocturna — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Sesiones de observacion nocturna de planetas, nebulosas y galaxias.';
$basePath        = '../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Observacion Nocturna</li>
      </ol>
    </nav>

    <div class="text-center mb-5">
      <i class="bi bi-moon-stars-fill text-primary" style="font-size: 3rem;"></i>
      <h1 class="section-title mt-3">Observacion Nocturna</h1>
      <p class="lead col-lg-8 mx-auto">Sesiones de observacion de planetas, nebulosas, cumulos estelares y galaxias con telescopios de diferentes tipos y aperturas.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-globe2 me-2 text-warning"></i>Planetas</h3>
          <p class="mb-0">Observacion de los planetas visibles: Jupiter con sus lunas galileanas, Saturno y sus anillos, Marte y Venus.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-cloud-haze2 me-2 text-info"></i>Cielo profundo</h3>
          <p class="mb-0">Nebulosas de emision y reflexion, cumulos abiertos y globulares, galaxias cercanas como Andromeda y el Triangulo.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-stars me-2 text-warning"></i>Estrellas dobles</h3>
          <p class="mb-0">Sistemas estelares multiples con contrastes de color y brillo. Ideales para telescopios de cualquier apertura.</p>
        </div>
      </div>
    </div>

    <div class="surface-card mt-5">
      <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2"></i>Recomendaciones</h2>
      <ul class="mb-0">
        <li>Llegar 15 minutos antes para adaptacion visual a la oscuridad.</li>
        <li>Llevar ropa abrigadora; las noches zacatecanas pueden ser frias.</li>
        <li>Evitar el uso de luces blancas; se recomienda linterna roja.</li>
        <li>No se requiere telescopio propio; la SAZ proporciona equipo.</li>
      </ul>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
