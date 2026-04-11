<?php
/**
 * Perfil del Dr. Alejandro González Sánchez
 */
$pageTitle       = 'Dr. Alejandro González Sánchez — Sociedad Astronómica de Zacatecas';
$pageDescription = 'Perfil del Dr. Alejandro González Sánchez, docente-investigador y cofundador de la SAZ.';
$basePath        = '../../../';

ob_start();
?>

<section class="py-5" aria-labelledby="perfil-nombre">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= $basePath ?>pages/quienes-somos/miembros-activos.php" class="link-accent">Miembros activos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dr. Alejandro González Sánchez</li>
      </ol>
    </nav>

    <div class="row g-4">
      <article class="col-lg-4">
        <div class="surface-card text-center">
            <div class="mx-auto mb-3">
            <?php 
            $fotoPath = 'assets/img/miembros/agonzalez.png';
            
            if (file_exists($basePath . $fotoPath)): ?>
                <img src="<?= $basePath . $fotoPath ?>" 
                    alt="Fotografía del <?= htmlspecialchars($m['nombre'] ?? 'Dr. Alejandro González Sánchez') ?>" 
                    class="member-photo-img-lg">
            <?php else: ?>
                <div class="member-photo-placeholder-lg mx-auto" aria-hidden="true">
                <i class="bi bi-person-fill"></i>
                </div>
            <?php endif; ?>
            </div>

            <h1 id="perfil-nombre" class="h4 mb-1">Dr. Alejandro González Sánchez</h1>
            <p class="text-muted mb-3">Docente-investigador Titular C (LUMAT-UAZ)</p>
            
            <hr aria-hidden="true">
            
            <div class="text-start small">
                <p class="mb-1">
                    <i class="bi bi-envelope me-2" aria-hidden="true"></i>
                    <strong>Correo:</strong> alejandro.gonzalez@uaz.edu.mx
                </p>
                <p class="mb-1">
                    <i class="bi bi-geo-alt me-2" aria-hidden="true"></i>
                    <strong>Ubicación:</strong> Zacatecas, México
                </p>
                <p class="mb-0">
                    <i class="bi bi-award me-2" aria-hidden="true"></i>
                    <strong>Distinción:</strong> SNI Nivel I
                </p>
            </div>
        </div>
      </article>

      <div class="col-lg-8">
        <section class="surface-card mb-4" aria-labelledby="header-formacion">
          <h2 id="header-formacion" class="h5 mb-3"><i class="bi bi-mortarboard me-2" aria-hidden="true"></i>Formación académica</h2>
          <ul class="mb-0">
            <li>Doctor en Astrofísica — División de Física y Astronomía de la Universidad de Sussex, Inglaterra.</li>
            <li>Maestría en Física — Facultad de Ciencias, UNAM</li>
            <li>Licenciatura en Física — Facultad de Ciencias, UNAM</li>
          </ul>
        </section>

        <section class="surface-card mb-4" aria-labelledby="header-investigacion">
          <h2 id="header-investigacion" class="h5 mb-3"><i class="bi bi-search me-2" aria-hidden="true"></i>Líneas de investigación</h2>         
          <p class="small mb-3">Especialista en Dinámica Galáctica, Astronomía Extragaláctica y Cosmología. Sus intereses incluyen:</p>
          <ul class="mb-0">
            <li>Estructura filamentaria del Universo</li>
            <li>Alineamiento de estructuras en cúmulos y supercúmulos</li>
            <li>Física de partículas elementales para el estudio de supernovas y materia obscura</li>
          </ul>
        </section>

        <section class="surface-card mb-4" aria-labelledby="header-investigacion">
          <h2 id="header-investigacion" class="h5 mb-3"><i class="bi bi-search me-2" aria-hidden="true"></i>Proyectos de Investigación recietes:</h2>         
        <p>Con financiamiento externo
          <ul class="mb-0">
            <li>“Compartiendo la Astronomía en Zacatecas, entre campos, minas y estrellas”. SECIHTI extinto CONACYT. Proyecto: ACADEMIAS-2025-A-54. Responsable.</li>
            <li>“Alineamiento de Galaxias en estructuras Filamentarias”. CONACYT, 2015. Responsable.</li>
            <li>“Torcas gravitacionales y por fricción dinámica sobre galaxias en cúmulos”. PROMEP, 2009-2013. Responsable.</li>
          </ul></p>
        <p>Sin financiamiento
          <ul class="mb-0">
            <li>“El espectro de masas de agujeros negros”. Ciencia y Tecnología de la Luz y la Materia, UAZ. 2025-2027. Responsable.</li>
            <li>“Reconstruyendo el Universo Local”. Ciencia y Tecnología de la Luz y la Materia, UAZ. 2020-2024. Responsable.</li>
            <li>“Alineamiento de galaxias en cúmulos con perfil de densidad core vs cuspy”. UAZ, Unidad Académica de Física-Unidad Académica de Ciencia y Tecnología de la Luz y la Materia. 2016-2019. Responsable.</li>
          </ul></p>
        </section>

        <section class="surface-card mb-4" aria-labelledby="header-investigacion">
          <h2 id="header-investigacion" class="h5 mb-3"><i class="bi bi-search me-2" aria-hidden="true"></i>Tesis Dirigidas</h2>         
          <ul class="mb-0">
            <li>1 Tesis de Doctorado“Materia obscura bipartita”. Mississippi Valenzuela Durán. Posgrado en Ciencia y Tecnología de la Luz y la Materia. UAZ. En progreso. 2020-2026</li>
            <li>4 tesis a nivel maestría, una de ellas “Detección de Hipertrofia Ventricular Izquierda usando wavelets” de Manuel Sandoval Martínez del Posgrado en Matemáticas Aplicadas. UJAT (2006) fue premiada como mejo tesis en el área de ciencias de la Universidad Juárez Autónoma de Tabasco.</li>
            <li>12 tesis a nivel licenciatura, de las cuales una “Propiedades físicas de regiones de formación estelar” de Genaro Suárez Castro, fue premiada en 2013 fue como mejor tesis de Astronomía a nivel Licenciatura 2013 Instituto de Astronomía, UNAM.</li>
          </ul>
        </section>

        <section class="surface-card mb-4" aria-labelledby="header-investigacion">
          <h2 id="header-investigacion" class="h5 mb-3"><i class="bi bi-search me-2" aria-hidden="true"></i>Estancias Académicas</h2>         
          <ul class="mb-0">
            <li>División de Física y Astronomía de la Universidad de Sussex, Inglaterra</li>
            <li>Departamento de Astronomía y Astrofísica de la Universidad de Glasgow, Escocia</li>
            <li>Departamento de Astronomía de la Universidad de Edimburgo, Escocia</li>
          </ul>
        </section>

        <section class="surface-card mb-4" aria-labelledby="header-investigacion">
          <h2 id="header-investigacion" class="h5 mb-3"><i class="bi bi-search me-2" aria-hidden="true"></i>Premios y Distinciones</h2>         
          <ul class="mb-0">
            <li>Miembro del SNI, nivel I.</li>
            <li>Perfil PRODEP desde 2004 a la fecha</li>
            <li>Miembro de la Unión Astronómica Internacional-División de Astronomía Extragaláctica y Cosmología.</li>
          </ul>
        </section>

        <section class="surface-card mb-4" aria-labelledby="header-divulgacion">
          <h2 id="header-divulgacion" class="h5 mb-3"><i class="bi bi-megaphone me-2" aria-hidden="true"></i>Divulgación y Liderazgo</h2>
          <ul class="mb-0">
            <li>Cofundador de la Sociedad Astronómica de Zacatecas (2010) </li>
            <li>Líder del equipo mexicano en la Olimpiada Internacional de Astronomía (Sochi, Rusia) vía electrónica, obteniendo la mención diamante.</li>
            <li>Autor de más de 150 artículos de divulgación científica en diversos medios</li>
          </ul>
        </section>

        <section class="surface-card" aria-labelledby="header-publicaciones">
          <h2 id="header-publicaciones" class="h5 mb-3"><i class="bi bi-journal-text me-2" aria-hidden="true"></i>Publicaciones Recientes</h2>
          <p class="small text-muted mb-3">Colaborador en revistas internacionales de alto impacto como <em>Monthly Notices of the Royal Astronomical Society</em> y <em>The European Physical Journal</em>.</p>
          <ul class="small mb-0">
            <li>“Primordial tidal shear for current alignments of cosmic structures”. A.González-Sánchez, P.R. Rivera-Ortiz y L.F.A. Teodoro. Revista Mexicana de Astronomía y Astrofísica. En progreso.</li>
            <li>“Small-scale alignment effects of galaxies in clusters: the full story”. A.González-Sánchez y L.F.A. Teodoro. Monthly Notices of the Royal Astronomical Society. En progreso</li>
            <li>“El origen e impulso de la Cosmología en México”. A. González-Sánchez y A.Flores-Márquez. Digesto/Saberes. Aceptado.</li>
          </ul>
        </section>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../../base.php';