<?php
if (!function_exists('get_actividades_activas')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/repositories/actividades.php';
}
if (!function_exists('get_observaciones_activas')) {
    require_once __DIR__ . '/../includes/repositories/observaciones.php';
}
if (!function_exists('get_eventos_activos')) {
    require_once __DIR__ . '/../includes/repositories/eventos.php';
}
if (!function_exists('get_config')) {
    require_once __DIR__ . '/../includes/repositories/configuracion.php';
}
$_navActividades   = get_actividades_activas();
$_navObservaciones = get_observaciones_activas();
$_navEventos       = get_eventos_activos();
$_sitioNombre      = get_config('sitio_nombre')    ?? 'Sociedad Astronómica de Zacatecas';
$_lavnetUrl        = get_config('footer_lavnet_url')    ?? 'http://gipimo.ddns.net:8000/lavnet-zac/';
$_lavnetNombre     = get_config('footer_lavnet_nombre') ?? 'LavNet-Zac-Mx';
?>
<!-- ============================================================
     partials/header.php
     Topbar con accesos rápidos + Navbar principal con dropdowns.
     Incluye toggle de modo claro/oscuro.
     ============================================================ -->
<header class="sticky-top surface-nav border-bottom">

  <!-- ── Topbar ── -->
  <div class="topbar py-2">
    <div class="container d-flex flex-wrap align-items-center justify-content-between">
      <a class="navbar-brand fw-bold d-inline-flex align-items-center gap-2 mb-0" href="<?= $basePath ?>index.php">
        <img src="<?= $basePath ?>assets/img/logo-SAZ.png" alt="Logo SAZ" height="40">
        <?= htmlspecialchars($_sitioNombre) ?>
      </a>
      <div class="d-flex flex-wrap gap-3">
        <a href="<?= $basePath ?>pages/quienes-somos/index.php" class="topbar-link">Quienes somos</a>
        <a href="<?= $basePath ?>pages/suscribirse/index.php" class="topbar-link">Suscribirse</a>
        <a href="<?= $basePath ?>pages/contacto/index.php" class="topbar-link">Contacto</a>
        <a href="<?= htmlspecialchars($_lavnetUrl) ?>" class="topbar-link" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($_lavnetNombre) ?></a>
      </div>
    </div>
  </div>

  <!-- ── Navbar principal ── -->
  <nav class="navbar navbar-expand-lg py-0 border-top" aria-label="Menu principal">
    <div class="container">
      <button class="navbar-toggler mx-auto my-1" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarPrincipal" aria-controls="navbarPrincipal" aria-expanded="false"
        aria-label="Abrir menu de navegacion">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarPrincipal">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">

          <!-- Inicio -->
          <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>index.php">Inicio</a>
          </li>

          <!-- Conócenos ▼ -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">Conócenos</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/quienes-somos/directorio.php">Directorio</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/quienes-somos/mesa-directiva.php">Mesa directiva</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/quienes-somos/miembros-activos.php">Miembros activos</a></li>
            </ul>
          </li>

          <!-- Eventos ▼ -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">Eventos</a>
            <ul class="dropdown-menu">
              <?php foreach ($_navEventos as $_ev): ?>
              <li>
                <a class="dropdown-item"
                   href="<?= $basePath ?>pages/eventos/ver.php?slug=<?= urlencode($_ev['slug']) ?>">
                  <?= htmlspecialchars($_ev['titulo']) ?>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- Actividades ▼ -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">Actividades</a>
            <ul class="dropdown-menu">
              <?php foreach ($_navActividades as $_act): ?>
              <li>
                <a class="dropdown-item"
                   href="<?= $basePath ?>pages/actividades/ver.php?slug=<?= urlencode($_act['slug']) ?>">
                  <?= htmlspecialchars($_act['titulo']) ?>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- Observaciones ▼ -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">Observaciones</a>
            <ul class="dropdown-menu">
              <?php foreach ($_navObservaciones as $_obs): ?>
              <li>
                <a class="dropdown-item"
                   href="<?= $basePath ?>pages/observaciones/ver.php?slug=<?= urlencode($_obs['slug']) ?>">
                  <?= htmlspecialchars($_obs['titulo']) ?>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- Links directos -->
          <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>pages/convocatorias/index.php">Convocatorias</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>pages/noticias/index.php">Archivo de noticias</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>pages/astrofotografia/index.php">Astrofotografía</a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>pages/colaboradores/index.php">Colaboradores</a>
          </li> -->
          

          <!-- Toggle modo claro/oscuro -->
          <li class="nav-item d-flex align-items-center ms-lg-3">
            <button type="button" id="themeToggle" class="btn btn-sm btn-outline-secondary border-0"
              aria-label="Cambiar tema claro/oscuro" title="Cambiar tema">
              <i class="bi bi-sun-fill" id="themeIcon"></i>
            </button>
          </li>

        </ul>
      </div>
    </div>
  </nav>

</header>