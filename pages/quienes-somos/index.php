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
<section class="py-5 section-alt" aria-labelledby="quienes-somos-title">
  <div class="container">
    <h1 id="quienes-somos-title" class="section-title mb-4">Quienes somos</h1>
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="surface-card h-100">
          <p class="lead mb-3">El 23 de junio de 2010, se fundó la Sociedad Astronómica de Zacatecas A.C., con el objetivo de reunir entusiastas, estudiantes y profesionales
            interesados en la astronomía, para promover el conocimiento del cielo y las ciencias dl espacio en el estado. Surgió como una iniciativa ciudadana impulsada
            por un pequeño grupo de aficionados, divulgadores y científicos que buscaban dar un carácter formal y organizado a las actividades astronómicas que ya se realizaban
            en el estado, creando así un espacio permanente para la formación, la observación y la divulgación científica.
          </p>
          
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
    <h2 id="mvv-title" class="section-title mb-4">Mision, Vision y Objetivos</h2>
    <div class="row g-4">
      <article class="col-md-4">
        <div class="surface-card h-100">
          <div class="text-center mb-3">
            <i class="bi bi-bullseye text-primary" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="h5 text-center">Misión</h3>
          <p class="mb-0">"Promover la investigación especializada así como la afición por la astronomía para elevar el conocimiento del Universo y el interés por su estudio y comprensión."</p>
          <p class="mb-0">Lema: "Compartiendo miradas al Universo profundo"</p>
        </div>
      </article>

      <article class="col-md-4">
        <div class="surface-card h-100">
          <div class="text-center mb-3">
            <i class="bi bi-eye text-primary" style="font-size: 2.5rem;"></i>
          </div>
          <h3 class="h5 text-center">Visión</h3>
          <p class="mb-0">"Ser en 2030 la sociedad astronómica líder en el centro-norte de México, reconocida por su excelencia en la promoción de la cultura científica, la formación de aficionados y profesionales en astronomía, y por consolidar a Zacatecas como un referente nacional en la divulgación, observación e investigación del Universo, actuando siempre con un espíritu de fraternidad, humanismo y compromiso social."</p>
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
            <p class="small mb-0 text-justify">
              "El fomento y la promoción de una mayor cultura científica, principalmente astronómica y ciencias y ramas afines tales como astrobiología, arqueoastronomía, ciencias planetarias, matemáticas, física, geofísica, biología, instrumentación, éstos últimos principalmente en sus aspectos relativos a la astronomía, entre otras; pero principalmente fomentar y promover en todos sus aspectos la astronomía, como especialidad científica o de afición."
            </p>
          </div>

          <hr class="my-2 opacity-25">

          <div class="mt-auto">
            <button class="btn btn-outline-primary btn-sm w-100 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObjetivos" aria-expanded="false" aria-controls="collapseObjetivos">
              Ver Objetivos Particulares
            </button>
            
            <div class="collapse" id="collapseObjetivos">
              <ul class="small mb-0 ps-3 mt-2">
                <li>Enseñanza, difusión, promoción y divulgación de la astronomía en el Estado de Zacatecas mediante cursos, talleres, conferencias, clases, videos, observaciones astronómicas y otras actividades afines.</li>
                <li>Investigación y estudio de la astronomía y ciencias afines.</li>
                <li>Observación astronómica y realización de trabajos de campo y de investigación.</li>
                <li>Popularización de una cultura de observación cotidiana del cielo (fomento a la astronomía de afición).</li>
                <li>Difusión a través de todos los medios de comunicación masiva de sus objetivos, actividades y eventos astronómicos.</li>
                <li>Fomentar el interés y gusto por la astronomía en la población del Estado de Zacatecas, de todas las edades.</li>
                <li>Llevar a cabo observaciones astronómicas periódicas en todo el estado de Zacatecas, principalmente en lugares propicios.</li>
                <li>Utilizar la capacidad profesional de los integrantes para desarrollar y explotar medios de difusión, publicando obras encaminadas a la educación y el entretenimiento astronómico.</li>
                <li>Prestar servicios de asesoría en diferentes áreas relacionadas con su objeto de estudio.</li>
                <li>Fomentar y establecer relaciones y convenios de colaboración con sociedades afines e instituciones de educación e investigación científica, nacionales o internacionales.</li>
                <li>Obtener y otorgar derechos de autor, concesiones y derechos reales y personales para todo tipo de actividades.</li>
                <li>Organizar continuamente eventos de observación astronómica, pequeños y/o masivos, en colaboración con otros organismos.</li>
                <li>Ofrecer programas educativos de formación en astronomía (afición o disciplina científica) desde plataformas de educación informal, no formal o formal.</li>
                </ul>
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
