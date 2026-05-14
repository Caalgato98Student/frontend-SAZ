<?php
/**
 * pages/quienes-somos/mesa-directiva.php
 * Mesa directiva actual de la SAZ.
 */
$pageTitle       = 'Mesa directiva — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conoce la mesa directiva actual de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

$fotosMesa = [
  'presidencia' => 'ivan-santamaria.png',
  'secretaria' => 'ciro-robles.png',
  'tesoreria' => 'armando-garcia.png'
];

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Mesa directiva</h1>
    <p class="mb-4">Periodo 2024–2026</p>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">

      <!-- Presidencia -->
      <div class="col">
        <div class="surface-card h-100 text-center p-4 shadow-sm">
          <div class="mx-auto mb-3">
            <?php 
            $fotoPath = 'assets/img/miembros/' . $fotosMesa['presidencia'];
            if (file_exists($basePath . $fotoPath)): ?>
              <img src="<?= $basePath . $fotoPath ?>" 
                   alt="Iván Santamaría Najar" 
                   class="member-photo-img-sm shadow-sm"
                   style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
              <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
            <?php endif; ?>
          </div>
          <h2 class="h6 mb-1">Presidencia</h2>
          <p class="mb-1 fw-semibold">Iván Santamaría Najar</p>
          <p class="text-muted small mb-0">c. a Dr. en Ciencias</p>
        </div>
      </div>

      <!-- Secretaría -->
      <div class="col">
        <div class="surface-card h-100 text-center p-4 shadow-sm">
          <div class="mx-auto mb-3">
            <?php 
            $fotoPath = 'assets/img/miembros/' . $fotosMesa['secretaria'];
            if (file_exists($basePath . $fotoPath)): ?>
              <img src="<?= $basePath . $fotoPath ?>" 
                   alt="M.C. Ciro Robles Berumen" 
                   class="member-photo-img-sm shadow-sm"
                   style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
              <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
            <?php endif; ?>
          </div>
          <h2 class="h6 mb-1">Secretaría</h2>
          <p class="mb-1 fw-semibold">Ciro Robles Berumen</p>
          <p class="text-muted small mb-0">Maestro en Ciencias</p>
        </div>
      </div>

      <!-- Tesorería -->
      <div class="col">
        <div class="surface-card h-100 text-center p-4 shadow-sm">
          <div class="mx-auto mb-3">
            <?php 
            $fotoPath = 'assets/img/miembros/' . $fotosMesa['tesoreria'];
            if (file_exists($basePath . $fotoPath)): ?>
              <img src="<?= $basePath . $fotoPath ?>" 
                   alt="L.E. Armando García Castillo" 
                   class="member-photo-img-sm shadow-sm"
                   style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
              <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
            <?php endif; ?>
          </div>
          <h2 class="h6 mb-1">Tesorería</h2>
          <p class="mb-1 fw-semibold">Armando García Castillo</p>
          <p class="text-muted small mb-0">Licenciado en Economía</p>
        </div>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';