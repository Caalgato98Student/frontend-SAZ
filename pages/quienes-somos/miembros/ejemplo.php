<?php
/**
 * pages/quienes-somos/miembros/ejemplo-miembro.php
 * Página individual de ejemplo de un miembro.
 * Demuestra el formato para futuras páginas de perfil.
 */
$pageTitle       = 'Dr. Carlos Mendez Ramirez — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Perfil del Dr. Carlos Mendez Ramirez, miembro activo de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= $basePath ?>pages/quienes-somos/miembros-activos.php" class="link-accent">Miembros activos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dr. Carlos Mendez Ramirez</li>
      </ol>
    </nav>

    <div class="row g-4">
      <!-- Columna de perfil -->
      <div class="col-lg-4">
        <div class="surface-card text-center">
          <div class="member-photo-placeholder-lg mx-auto mb-3">
            <i class="bi bi-person-fill"></i>
          </div>
          <h1 class="h4 mb-1">Dr. Carlos Mendez Ramirez</h1>
          <p class="text-muted mb-3">Astrofisica estelar y espectroscopia</p>
          <hr>
          <p class="small mb-1"><i class="bi bi-envelope me-2"></i>cmendez@saz.org.mx</p>
          <p class="small mb-1"><i class="bi bi-telephone me-2"></i>+52 492 000 0001</p>
          <p class="small mb-0"><i class="bi bi-geo-alt me-2"></i>Zacatecas, Mexico</p>
        </div>
      </div>

      <!-- Columna de información extendida -->
      <div class="col-lg-8">
        <div class="surface-card mb-4">
          <h2 class="h5 mb-3"><i class="bi bi-mortarboard me-2"></i>Formacion academica</h2>
          <ul>
            <li>Doctorado en Astrofisica — Universidad Nacional Autonoma de Mexico (2018)</li>
            <li>Maestria en Ciencias Fisicas — Universidad Autonoma de Zacatecas (2014)</li>
            <li>Licenciatura en Fisica — Universidad Autonoma de Zacatecas (2012)</li>
          </ul>
        </div>

        <div class="surface-card mb-4">
          <h2 class="h5 mb-3"><i class="bi bi-search me-2"></i>Lineas de investigacion</h2>
          <ul>
            <li>Clasificacion espectral de estrellas variables</li>
            <li>Abundancias quimicas en estrellas de la Via Lactea</li>
            <li>Instrumentacion para espectroscopia de baja resolucion</li>
          </ul>
        </div>

        <div class="surface-card">
          <h2 class="h5 mb-3"><i class="bi bi-journal-text me-2"></i>Publicaciones destacadas</h2>
          <p class="text-muted mb-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
          <p class="text-muted mb-2">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
          <p class="text-muted mb-0">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../base.php';
