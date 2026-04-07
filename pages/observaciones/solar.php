<?php
$pageTitle       = 'Observacion Solar — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Sesiones de observacion segura del Sol organizadas por la SAZ.';
$basePath        = '../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Observacion Solar</li>
      </ol>
    </nav>

    <div class="text-center mb-5">
      <i class="bi bi-sun text-warning" style="font-size: 3rem;"></i>
      <h1 class="section-title mt-3">Observacion Solar</h1>
      <p class="lead col-lg-8 mx-auto">Jornadas de observacion segura del Sol con filtros solares certificados y telescopios especializados.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-shield-check me-2 text-success"></i>Seguridad</h3>
          <p class="mb-0">Todas las observaciones solares se realizan con filtros certificados ISO 12312-2. Nunca se debe observar el Sol directamente sin proteccion adecuada.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-telescope me-2 text-primary"></i>Equipo</h3>
          <p class="mb-0">Utilizamos telescopios solares con filtro H-alpha que permiten observar protuberancias, manchas solares y filamentos en la cromosfera.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-calendar-event me-2 text-info"></i>Programacion</h3>
          <p class="mb-0">Las sesiones se realizan generalmente los sabados por la manana en plazas publicas de Zacatecas. Consulta el calendario de eventos para fechas especificas.</p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="surface-card h-100">
          <h3 class="h5 mb-3"><i class="bi bi-people me-2 text-primary"></i>Publico</h3>
          <p class="mb-0">Actividad abierta a todas las edades. No se requiere experiencia previa ni inscripcion. Solo acude al punto de observacion en la fecha indicada.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
