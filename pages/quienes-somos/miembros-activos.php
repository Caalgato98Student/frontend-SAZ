<?php
/**
 * pages/quienes-somos/miembros-activos.php
 * Listado de miembros activos con tarjetas de perfil.
 * Cada tarjeta incluye: foto circular, nombre, especialidad, correo, link a perfil.
 */
$pageTitle       = 'Miembros activos — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Listado de miembros activos de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

// Datos ficticios de 6 miembros
$miembros = [
    [
        'nombre'       => 'Dr. Carlos Mendez Ramirez',
        'especialidad' => 'Astrofisica estelar y espectroscopia',
        'correo'       => 'cmendez@saz.org.mx',
        'slug'         => 'ejemplo-miembro',
    ],
    [
        'nombre'       => 'Mtra. Ana Lucia Fernandez',
        'especialidad' => 'Divulgacion cientifica y educacion',
        'correo'       => 'afernandez@saz.org.mx',
        'slug'         => '',
    ],
    [
        'nombre'       => 'Ing. Roberto Salazar Nunez',
        'especialidad' => 'Instrumentacion optica y telescopios',
        'correo'       => 'rsalazar@saz.org.mx',
        'slug'         => '',
    ],
    [
        'nombre'       => 'Lic. Patricia Herrera Gomez',
        'especialidad' => 'Gestion cultural y vinculacion',
        'correo'       => 'pherrera@saz.org.mx',
        'slug'         => '',
    ],
    [
        'nombre'       => 'Mtro. Fernando Alanis Torres',
        'especialidad' => 'Astronomia observacional y fotometria',
        'correo'       => 'falanis@saz.org.mx',
        'slug'         => '',
    ],
    [
        'nombre'       => 'Ing. Laura Patricia Vega',
        'especialidad' => 'Astrofotografia y procesamiento de imagen',
        'correo'       => 'lvega@saz.org.mx',
        'slug'         => '',
    ],
];

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Miembros activos</h1>
    <p class="mb-4">Conoce a las personas que forman parte de la SAZ y contribuyen a la divulgacion astronomica.</p>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach ($miembros as $m): ?>
        <div class="col">
          <div class="surface-card h-100 text-center">
            <!-- Foto circular placeholder -->
            <div class="member-photo-placeholder mx-auto mb-3">
              <i class="bi bi-person-fill"></i>
            </div>
            <h3 class="h6 mb-1"><?= htmlspecialchars($m['nombre']) ?></h3>
            <p class="text-muted small mb-1"><?= htmlspecialchars($m['especialidad']) ?></p>
            <p class="small mb-2">
              <i class="bi bi-envelope me-1"></i>
              <a href="mailto:<?= htmlspecialchars($m['correo']) ?>" class="link-accent"><?= htmlspecialchars($m['correo']) ?></a>
            </p>
            <?php if (!empty($m['slug'])): ?>
              <a href="<?= $basePath ?>pages/quienes-somos/miembros/<?= htmlspecialchars($m['slug']) ?>.php" class="link-accent small">
                Mas informacion... <i class="bi bi-arrow-right"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
