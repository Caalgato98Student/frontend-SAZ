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
        Sociedad Astronomica de Zacatecas
      </a>
      <div class="d-flex flex-wrap gap-3">
        <a href="<?= $basePath ?>pages/quienes-somos/index.php" class="topbar-link">Quienes somos</a>
        <a href="<?= $basePath ?>pages/suscribirse/index.php" class="topbar-link">Suscribirse</a>
        <a href="<?= $basePath ?>pages/contacto/index.php" class="topbar-link">Contacto</a>
        <a href="#" class="topbar-link" target="_blank" rel="noopener noreferrer">LavNet-Zac-Mx</a>
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
              aria-expanded="false">Conocenos</a>
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
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/eventos/noche-estrellas.php">Noche de las Estrellas</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/eventos/semana-espacio.php">Semana Mundial del Espacio</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/eventos/maraton-messier.php">Maraton Messier</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/eventos/equinoccio.php">Equinoccio de Primavera</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/eventos/olimpiadas.php">Olimpiadas de Astronomía</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/eventos/semanadelaastronomia.php">Semana de la Astronomía</a></li>
            </ul>
          </li>

          <!-- Actividades ▼ -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">Actividades</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/actividades/conferencias.php">Conferencias</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/actividades/charlas.php">Charlas</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/actividades/talleres.php">Talleres</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/actividades/cursos.php">Cursos</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/actividades/diplomados.php">Diplomados</a></li>
            </ul>
          </li>

          <!-- Observaciones ▼ -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">Observaciones</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/observaciones/solar.php">Solar</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/observaciones/diurna.php">Diurna</a></li>
              <li><a class="dropdown-item" href="<?= $basePath ?>pages/observaciones/nocturna.php">Nocturna</a></li>
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
            <a class="nav-link" href="<?= $basePath ?>pages/astrofotografia/index.php">Astrofotografia</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $basePath ?>pages/colaboradores/index.php">Colaboradores</a>
          </li>
          

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