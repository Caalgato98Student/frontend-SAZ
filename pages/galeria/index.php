<?php
/**
 * pages/galeria/index.php
 * Galería dinámica de astrofotografía filtrada por categoría.
 *
 * URL de acceso: pages/galeria/index.php?categoria=sol|luna|profundo
 *
 * LÓGICA DE FILTRADO:
 *   El sistema detecta la categoría leyendo el PREFIJO del nombre del archivo JSON.
 *   Convención de nombres:
 *     sol-XXXXXXX.json      → Categoría "Sol"
 *     luna-XXXXXXX.json     → Categoría "Luna"
 *     profundo-XXXXXXX.json → Categoría "Espacio Profundo"
 */

// ── 1. Parámetros de entrada ─────────────────────────────────
$categoriaSlug = $_GET['categoria'] ?? 'sol';
$categoriaSlug = preg_replace('/[^a-z\-]/', '', strtolower($categoriaSlug));

$categoriasValidas = ['sol', 'luna', 'profundo'];
if (!in_array($categoriaSlug, $categoriasValidas, true)) {
    $categoriaSlug = 'sol';
}

$FOTOS_POR_PAGINA = 12;
$paginaActual = max(1, (int)($_GET['page'] ?? 1));

// ── 2. Configuración visual por categoría ─────────────────────
$config = [
    'sol' => [
        'titulo'    => 'Sol',
        'icon_bi'   => 'bi-sun-fill',
        'color'     => '#f59e0b',
        'descripcion' => 'Fotografía solar, manchas, fáculas y prominencias',
    ],
    'luna' => [
        'titulo'    => 'Luna',
        'icon_bi'   => 'bi-moon-stars-fill',
        'color'     => '#7dd3fc',
        'descripcion' => 'Fases lunares, cráteres, mares y formaciones',
    ],
    'profundo' => [
        'titulo'    => 'Espacio Profundo',
        'icon_bi'   => 'bi-stars',
        'color'     => '#a78bfa',
        'descripcion' => 'Nebulosas, galaxias, cúmulos estelares y objetos del catálogo Messier',
    ],
];
$catConfig = $config[$categoriaSlug];

$pageTitle       = $catConfig['titulo'] . ' — Astrofotografía SAZ';
$pageDescription = $catConfig['descripcion'] . '. Galería de la Sociedad Astronómica de Zacatecas.';
$basePath        = '../../';

// ── 3. Leer y filtrar fotos por prefijo de nombre de archivo ──
$fotos = [];
$dirAstro = __DIR__ . '/../../content/astrofotografia/';
if (is_dir($dirAstro)) {
    foreach (glob($dirAstro . $categoriaSlug . '-*.json') as $archivo) {
        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            if (empty($datos['id'])) {
                $datos['id'] = basename($archivo, '.json');
            }
            $fotos[] = $datos;
        }
    }
    // Ordenar por fecha descendente
    usort($fotos, function ($a, $b) {
        return strtotime($b['fecha'] ?? '2000-01-01') - strtotime($a['fecha'] ?? '2000-01-01');
    });
}

// ── 4. Paginación ─────────────────────────────────────────────
$totalFotos  = count($fotos);
$totalPaginas = max(1, (int)ceil($totalFotos / $FOTOS_POR_PAGINA));
$paginaActual = min($paginaActual, $totalPaginas);
$offset       = ($paginaActual - 1) * $FOTOS_POR_PAGINA;
$fotosPagina  = array_slice($fotos, $offset, $FOTOS_POR_PAGINA);

// Helper para URL de página
function urlPagina(string $slug, int $page): string {
    return '?categoria=' . urlencode($slug) . '&page=' . $page;
}

ob_start();
?>

<section class="py-5">
  <div class="container">

    <!-- ── Migas de pan ── -->
    <nav aria-label="Ruta de navegación" class="mb-4">
      <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item">
          <a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a>
        </li>
        <li class="breadcrumb-item">
          <a href="<?= $basePath ?>pages/astrofotografia/index.php" class="link-accent">Astrofotografía</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          <?= htmlspecialchars($catConfig['titulo']) ?>
        </li>
      </ol>
    </nav>

    <!-- ── Encabezado de categoría ── -->
    <div class="d-flex align-items-center gap-3 mb-2">
      <div class="astro-cat-badge" style="background: <?= $catConfig['color'] ?>22; color: <?= $catConfig['color'] ?>;">
        <i class="bi <?= $catConfig['icon_bi'] ?>"></i>
      </div>
      <div>
        <h1 class="section-title h2 mb-0"><?= htmlspecialchars($catConfig['titulo']) ?></h1>
        <p class="text-muted small mb-0"><?= htmlspecialchars($catConfig['descripcion']) ?></p>
      </div>
    </div>

    <?php if ($totalFotos > 0): ?>
      <p class="text-muted small mb-4">
        Mostrando <?= $offset + 1 ?>–<?= min($offset + $FOTOS_POR_PAGINA, $totalFotos) ?>
        de <?= $totalFotos ?> fotografía<?= $totalFotos !== 1 ? 's' : '' ?> &nbsp;·&nbsp; Página <?= $paginaActual ?> de <?= $totalPaginas ?>
      </p>
    <?php endif; ?>

    <!-- ── Cuadrícula de fotos ── -->
    <?php if (!empty($fotosPagina)): ?>
      <div class="gallery-grid mb-5">
        <?php foreach ($fotosPagina as $foto):
            $modalId = 'modal-' . preg_replace('/[^a-z0-9]/', '', strtolower($foto['id']));
            $tieneImg = !empty($foto['imagen']);
        ?>

          <!-- Tarjeta -->
          <div class="gallery-grid-item">
            <div class="surface-card card-hover p-0 overflow-hidden"
                 role="button"
                 tabindex="0"
                 data-bs-toggle="modal"
                 data-bs-target="#<?= $modalId ?>"
                 aria-label="Ver ficha técnica de <?= htmlspecialchars($foto['titulo'] ?? $foto['id']) ?>">

              <!-- Imagen o placeholder -->
              <?php if ($tieneImg): ?>
                <img src="<?= $basePath ?>assets/img/astrofotografia/<?= htmlspecialchars($foto['imagen']) ?>"
                     alt="<?= htmlspecialchars($foto['titulo'] ?? '') ?>"
                     class="w-100 astro-gallery-img">
              <?php else: ?>
                <div class="astro-placeholder-img d-flex flex-column align-items-center justify-content-center"
                     style="border-bottom: 3px solid <?= $catConfig['color'] ?>44;">
                  <i class="bi <?= $catConfig['icon_bi'] ?>" style="color: <?= $catConfig['color'] ?>; font-size: 2.5rem;"></i>
                  <span class="small text-muted mt-2">Imagen pendiente</span>
                </div>
              <?php endif; ?>

              <!-- Info rápida -->
              <div class="p-3">
                <p class="fw-bold mb-1 lh-sm small">
                  <?= htmlspecialchars($foto['titulo'] ?? $foto['id']) ?>
                </p>
                <p class="news-date mb-0">
                  <i class="bi bi-person me-1"></i><?= htmlspecialchars($foto['fotografo'] ?? 'SAZ') ?>
                </p>
                <p class="news-date mb-0">
                  <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($foto['fecha'] ?? '') ?>
                </p>
              </div>
            </div>
          </div>

          <!-- ── Modal: Ficha Técnica ── -->
          <div class="modal fade" id="<?= $modalId ?>" tabindex="-1"
               aria-labelledby="<?= $modalId ?>-label" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
              <div class="modal-content astro-modal">

                <!-- Header del modal -->
                <div class="modal-header astro-modal-header" style="border-bottom: 2px solid <?= $catConfig['color'] ?>44;">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi <?= $catConfig['icon_bi'] ?>" style="color:<?= $catConfig['color'] ?>; font-size: 1.3rem;"></i>
                    <h5 class="modal-title fw-bold mb-0" id="<?= $modalId ?>-label">
                      <?= htmlspecialchars($foto['titulo'] ?? $foto['id']) ?>
                    </h5>
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Body del modal -->
                <div class="modal-body p-0 d-flex flex-column">

                  <!-- Imagen grande -->
                  <div class="w-100 bg-dark text-center">
                    <?php if ($tieneImg): ?>
                      <img src="<?= $basePath ?>assets/img/astrofotografia/<?= htmlspecialchars($foto['imagen']) ?>"
                           alt="<?= htmlspecialchars($foto['titulo'] ?? '') ?>"
                           class="img-fluid" style="max-height: 70vh; object-fit: contain;">
                    <?php else: ?>
                      <div class="astro-modal-placeholder d-flex flex-column align-items-center justify-content-center" style="min-height: 400px;">
                        <i class="bi <?= $catConfig['icon_bi'] ?>" style="color:<?= $catConfig['color'] ?>; font-size: 4rem; opacity:.5;"></i>
                        <p class="text-muted mt-3">Imagen pendiente de publicación</p>
                      </div>
                    <?php endif; ?>
                  </div>

                  <!-- Ficha técnica al pie -->
                  <div class="p-4" style="background: var(--surface-alt);">
                    <?php 
                      $hw = $foto['hardware'] ?? [];
                      $par = $foto['parametros'] ?? [];
                      
                      $equipo = array_filter([
                          !empty($hw['telescopio']) ? 'Telescopio ' . $hw['telescopio'] : null,
                          !empty($hw['montura']) ? 'Montura ' . $hw['montura'] : null,
                          !empty($hw['camara']) ? 'Cámara ' . $hw['camara'] : null
                      ]);
                      
                      $datos = array_filter([
                          $par['integracion'] ?? null,
                          $par['iso_gain'] ?? null,
                          !empty($par['filtros']) ? 'Filtro ' . $par['filtros'] : null
                      ]);
                    ?>
                    <div style="color: var(--text-main); font-size: 0.95rem; line-height: 1.8;">
                      <?php if (!empty($foto['titulo'])): ?>
                        <div><strong style="font-weight: 600;">Título:</strong> <?= htmlspecialchars($foto['titulo']) ?></div>
                      <?php endif; ?>
                      
                      <?php if (!empty($foto['fotografo'])): ?>
                        <div><strong style="font-weight: 600;">Fotógrafo:</strong> <?= htmlspecialchars($foto['fotografo']) ?></div>
                      <?php endif; ?>
                      
                      <?php if (!empty($foto['lugar']) || !empty($foto['fecha'])): ?>
                        <div><strong style="font-weight: 600;">Lugar/Fecha:</strong> <?= htmlspecialchars(implode(' - ', array_filter([$foto['lugar'] ?? null, $foto['fecha'] ?? null]))) ?></div>
                      <?php endif; ?>
                      
                      <?php if (!empty($equipo)): ?>
                        <div><strong style="font-weight: 600;">Equipo:</strong> <?= htmlspecialchars(implode(', ', $equipo)) ?>.</div>
                      <?php endif; ?>
                      
                      <?php if (!empty($datos)): ?>
                        <div><strong style="font-weight: 600;">Datos:</strong> <?= htmlspecialchars(implode(', ', $datos)) ?>.</div>
                      <?php endif; ?>
                      
                      <?php if (!empty($foto['post_procesamiento'])): ?>
                        <div><strong style="font-weight: 600;">Procesamiento:</strong> <?= htmlspecialchars($foto['post_procesamiento']) ?>.</div>
                      <?php endif; ?>
                      
                      <?php if (!empty($foto['descripcion'])): ?>
                        <div class="mt-3 text-muted fst-italic">
                          <i class="bi bi-chat-quote me-1"></i><?= htmlspecialchars($foto['descripcion']) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                </div><!-- /modal-body -->

              </div><!-- /modal-content -->
            </div>
          </div><!-- /modal -->

        <?php endforeach; ?>
      </div><!-- /gallery-grid -->

      <!-- ── Paginación ── -->
      <?php if ($totalPaginas > 1): ?>
        <nav aria-label="Paginación de galería" class="d-flex justify-content-center">
          <ul class="pagination astro-pagination">

            <!-- Anterior -->
            <li class="page-item <?= $paginaActual <= 1 ? 'disabled' : '' ?>">
              <a class="page-link"
                 href="<?= urlPagina($categoriaSlug, $paginaActual - 1) ?>"
                 aria-label="Página anterior">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>

            <!-- Números de página -->
            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
              <li class="page-item <?= $p === $paginaActual ? 'active' : '' ?>">
                <a class="page-link" href="<?= urlPagina($categoriaSlug, $p) ?>"
                   <?= $p === $paginaActual ? 'aria-current="page"' : '' ?>>
                  <?= $p ?>
                </a>
              </li>
            <?php endfor; ?>

            <!-- Siguiente -->
            <li class="page-item <?= $paginaActual >= $totalPaginas ? 'disabled' : '' ?>">
              <a class="page-link"
                 href="<?= urlPagina($categoriaSlug, $paginaActual + 1) ?>"
                 aria-label="Página siguiente">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>

          </ul>
        </nav>
      <?php endif; ?>

    <?php else: ?>
      <!-- Sin fotos en esta categoría -->
      <div class="surface-card text-center py-5">
        <i class="bi <?= $catConfig['icon_bi'] ?>" style="font-size: 3rem; color: <?= $catConfig['color'] ?>; opacity:.4;"></i>
        <h3 class="h5 mt-3">Sin fotografías aún</h3>
        <p class="text-muted mb-4">
          Aún no hay imágenes publicadas en la categoría <strong><?= htmlspecialchars($catConfig['titulo']) ?></strong>.
        </p>
        <a href="<?= $basePath ?>pages/astrofotografia/index.php" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>Volver al índice
        </a>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
