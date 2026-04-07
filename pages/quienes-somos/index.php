<?php
/**
 * pages/quienes-somos/index.php
 * Página "Quiénes somos" — fusiona Presentación + Misión, Visión y Objetivos.
 * Accesible desde el topbar. No aparece en el dropdown "Conócenos" del navbar.
 */
$pageTitle       = 'Quienes somos — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conoce la historia, mision, vision y objetivos de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

ob_start();
?>

<!-- ── Presentación ── -->
<section class="py-5 section-alt">
  <div class="container">
    <h1 class="section-title mb-4">Quienes somos</h1>
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="surface-card h-100">
          <p class="lead mb-3">Somos una sociedad civil que promueve el estudio y la divulgacion de la astronomia mediante actividades publicas, academicas y de vinculacion con instituciones educativas y cientificas.</p>
          <p class="mb-3">Fundada en 2011, la Sociedad Astronomica de Zacatecas (SAZ) reune a profesionales, estudiantes y entusiastas de la astronomia con el objetivo comun de acercar el conocimiento del universo a la comunidad zacatecana.</p>
          <p class="mb-0">A lo largo de mas de una decada, hemos organizado eventos de observacion publica, conferencias, talleres y proyectos de colaboracion con universidades y centros de investigacion a nivel regional, nacional e internacional.</p>
        </div>
      </div>
      <div class="col-lg-4">
        <img src="<?= $basePath ?>assets/img/aniversarioXV.png" alt="XV Aniversario de la Sociedad Astronomica de Zacatecas" class="img-fluid rounded-3">
      </div>
    </div>
  </div>
</section>

<!-- ── Misión, Visión y Objetivos ── -->
<section class="py-5">
  <div class="container">
    <h2 class="section-title mb-4">Mision, Vision y Objetivos</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="surface-card h-100">
          <div class="text-center mb-3">
            <i class="bi bi-bullseye text-primary" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="h5 text-center">Mision</h3>
          <p class="mb-0">Promover el conocimiento astronomico mediante divulgacion, investigacion y actividades comunitarias que acerquen la ciencia a todos los sectores de la poblacion.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="surface-card h-100">
          <div class="text-center mb-3">
            <i class="bi bi-eye text-primary" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="h5 text-center">Vision</h3>
          <p class="mb-0">Ser referente regional en educacion y participacion cientifica en astronomia, reconocidos por nuestra capacidad de articular esfuerzos entre la academia, el gobierno y la sociedad civil.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="surface-card h-100">
          <div class="text-center mb-3">
            <i class="bi bi-list-check text-primary" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="h5 text-center">Objetivos</h3>
          <ul class="mb-0">
            <li>Formacion continua de miembros y publico.</li>
            <li>Vinculacion institucional con universidades y centros de investigacion.</li>
            <li>Organizacion de eventos publicos periodicos.</li>
            <li>Impulso a proyectos de investigacion y astrofotografia.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
