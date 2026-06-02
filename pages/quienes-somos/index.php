<?php
/**
 * pages/quienes-somos/index.php
 * Página "Quiénes somos" — fusiona Presentación + Misión, Visión y Objetivos.
 * Accesible desde el topbar. No aparece en el dropdown "Conócenos" del navbar.
 */
$pageTitle       = 'Quienes somos — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conoce la historia, mision, vision y objetivos de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/configuracion.php';

$historia             = get_config('quienes_somos_historia')              ?? '';
$mision               = get_config('quienes_somos_mision')                ?? '';
$vision               = get_config('quienes_somos_vision')                ?? '';
$objetivoGeneral      = get_config('quienes_somos_objetivo_general')      ?? '';
$objetivosParticulares = get_config('quienes_somos_objetivos_particulares') ?? '';
$heroImagen           = get_config('hero_imagen')     ?? 'assets/img/aniversarioXV.png';
$heroImagenAlt        = get_config('hero_imagen_alt') ?? 'XV Aniversario de la Sociedad Astronómica de Zacatecas';

ob_start();
?>

<!-- ── Presentación ── -->
<section class="py-5 section-alt" aria-labelledby="quienes-somos-title">
  <div class="container">
    <h1 id="quienes-somos-title" class="section-title mb-4">Quienes somos</h1>
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="surface-card h-100">
          <div class="lead mb-3"><?= $historia ?></div>
        </div>
      </div>
      <div class="col-lg-4">
        <img src="<?= $basePath ?><?= htmlspecialchars($heroImagen) ?>"
             alt="<?= htmlspecialchars($heroImagenAlt) ?>"
             class="img-fluid rounded-3">
      </div>
    </div>
  </div>
</section>

<!-- ── Misión, Visión y Objetivos ── -->
<section class="py-5">
  <div class="container">
    <h2 id="mvv-title" class="section-title mb-4">Mision, Vision y Objetivos</h2>
    <div class="row g-4">
      <article class="col-md-4">
        <div class="surface-card h-100">
          <div class="text-center mb-3">
            <i class="bi bi-bullseye text-primary" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="h5 text-center">Misión</h3>
          <div class="mb-0"><?= $mision ?></div>
        </div>
      </article>

      <article class="col-md-4">
        <div class="surface-card h-100">
          <div class="text-center mb-3">
            <i class="bi bi-eye text-primary" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="h5 text-center">Visión</h3>
          <div class="mb-0"><?= $vision ?></div>
        </div>
      </article>

      <article class="col-md-4">
        <div class="surface-card h-100 d-flex flex-column">
          <div class="text-center mb-3">
            <i class="bi bi-list-check text-primary" style="font-size: 2.5rem;" aria-hidden="true"></i>
          </div>
          <h3 class="h5 text-center mb-3">Objetivos</h3>

          <div class="mb-3">
            <strong class="text-primary d-block mb-1"><i class="bi bi-star-fill me-1"></i> Objetivo General:</strong>
            <div class="small mb-0 text-justify"><?= $objetivoGeneral ?></div>
          </div>

          <hr class="my-2 opacity-25">

          <div class="mt-auto">
            <button class="btn btn-outline-primary btn-sm w-100 mb-2" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseObjetivos"
                    aria-expanded="false" aria-controls="collapseObjetivos">
              Objetivos Particulares
            </button>
            <div class="collapse" id="collapseObjetivos">
              <div class="small mb-0 ps-1 mt-2"><?= $objetivosParticulares ?></div>
            </div>
          </div>
        </div>
      </article>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
