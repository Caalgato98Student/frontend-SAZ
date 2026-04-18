<?php
/**
 * pages/colaboradores/index.php
 * Tarjetas de colaboradores con nombre, profesión y red social.
 */
$pageTitle       = 'Colaboradores — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Colaboradores de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

$colaboradores = [
    ['nombre' => 'Dr. Carlos Mendez Ramirez',    'profesion' => 'Astronomo',                  'red' => '#', 'red_nombre' => 'ResearchGate'],
    ['nombre' => 'Mtra. Ana Lucia Fernandez',     'profesion' => 'Divulgadora cientifica',     'red' => '#', 'red_nombre' => 'Twitter'],
    ['nombre' => 'Ing. Roberto Salazar Nunez',    'profesion' => 'Ingeniero optico',           'red' => '#', 'red_nombre' => 'LinkedIn'],
    ['nombre' => 'Lic. Patricia Herrera Gomez',   'profesion' => 'Gestora cultural',           'red' => '#', 'red_nombre' => 'Instagram'],
    ['nombre' => 'Mtro. Fernando Alanis Torres',  'profesion' => 'Astronomo observacional',    'red' => '#', 'red_nombre' => 'Facebook'],
    ['nombre' => 'Ing. Laura Patricia Vega',      'profesion' => 'Astrofotografa',             'red' => '#', 'red_nombre' => 'Instagram'],
];

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Colaboradores</h1>
    <p class="mb-4">Personas e instituciones que contribuyen al desarrollo de las actividades de la SAZ.</p>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach ($colaboradores as $col): ?>
        <div class="col">
          <div class="surface-card h-100">
            <div class="d-flex align-items-center gap-3">
              <div class="member-photo-placeholder-sm">
                <i class="bi bi-person-fill"></i>
              </div>
              <div>
                <h3 class="h6 mb-1"><?= htmlspecialchars($col['nombre']) ?></h3>
                <p class="text-muted small mb-1"><?= htmlspecialchars($col['profesion']) ?></p>
                <a href="<?= htmlspecialchars($col['red']) ?>" class="link-accent small" target="_blank" rel="noopener noreferrer">
                  <i class="bi bi-link-45deg me-1"></i><?= htmlspecialchars($col['red_nombre']) ?>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
