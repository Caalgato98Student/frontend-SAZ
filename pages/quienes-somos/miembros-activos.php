<?php
/**
 * pages/quienes-somos/miembros-activos.php
 * Listado de miembros activos con tarjetas de perfil.
 * Cada tarjeta incluye: foto circular, nombre, especialidad, correo, link a perfil.
 */
$pageTitle       = 'Miembros activos — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conoce a los científicos, divulgadores y entusiastas que forman parte de la SAZ.';
$basePath        = '../../';

// Datos ficticios de 6 miembros
$miembros = [
    [
        'nombre'       => 'Dr. Alejandro González Sánchez',
        'especialidad' => 'Dinámica Galáctica, Astronomía Extragaláctica y Cosmología',
        'correo'       => 'alejandro.gonzalez@uaz.edu.mx',
        'slug'         => 'alejandro-gonzalez-sanchez',
    ],
    [
        'nombre'       => 'Pendiente',
        'especialidad' => '',
        'correo'       => '',
        'slug'         => 'ejemplo',
    ],
];

ob_start();
?>

<section class="py-5" aria-labelledby="members-title">
  <div class="container">
    <h1 id="members-title" class="section-title mb-4">Miembros activos</h1>
    <p class="mb-5">Conoce a las personas que forman parte de la SAZ y contribuyen a la divulgación astronómica en nuestro estado.</p>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach ($miembros as $m): ?>
        <article class="col">
          <div class="surface-card h-100 text-center">
            <div class="member-photo-placeholder mx-auto mb-3" aria-hidden="true">
              <i class="bi bi-person-fill"></i>
            </div>
            
            <h2 class="h6 mb-1"><?= htmlspecialchars($m['nombre']) ?></h2>
            <p class="text-muted small mb-3"><?= htmlspecialchars($m['especialidad']) ?></p>
            
            <div class="mb-3">
              <span class="visually-hidden">Correo electrónico: </span>
              <a href="mailto:<?= htmlspecialchars($m['correo']) ?>" class="link-accent small">
                <i class="bi bi-envelope me-1" aria-hidden="true"></i>
                <?= htmlspecialchars($m['correo']) ?>
              </a>
            </div>

            <?php if (!empty($m['slug'])): ?>
              <a href="<?= $basePath ?>pages/quienes-somos/miembros/<?= htmlspecialchars($m['slug']) ?>.php" 
                 class="btn btn-sm btn-outline-primary"
                 aria-label="Ver perfil completo de <?= htmlspecialchars($m['nombre']) ?>">
                Más información <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
              </a>
            <?php else: ?>
              <span class="text-muted small italic">Perfil en actualización</span>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
