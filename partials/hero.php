<?php
if (!function_exists('get_config')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/repositories/configuracion.php';
}
$_heroTitulo    = get_config('hero_titulo')    ?? 'Sociedad Astronómica de Zacatecas';
$_heroSubtitulo = get_config('hero_subtitulo') ?? 'Comunidad dedicada a la divulgación científica, observación astronómica e impulso de proyectos académicos en Zacatecas.';
$_heroImagen    = get_config('hero_imagen')    ?? 'assets/img/aniversarioXV.png';
$_heroImagenAlt = get_config('hero_imagen_alt') ?? 'XV Aniversario de la Sociedad Astronómica de Zacatecas';
?>
<section id="inicio" class="hero-section py-5">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($_heroTitulo) ?></h1>
        <p class="lead mb-4"><?= htmlspecialchars($_heroSubtitulo) ?></p>
      </div>
      <div class="col-lg-6 text-end">
        <?php if (!empty($_heroImagen)): ?>
        <img src="<?= $basePath ?><?= htmlspecialchars($_heroImagen) ?>"
             alt="<?= htmlspecialchars($_heroImagenAlt) ?>"
             class="img-fluid rounded-3">
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
