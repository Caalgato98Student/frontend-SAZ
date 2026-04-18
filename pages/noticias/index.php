<?php
/**
 * pages/noticias/index.php
 * Archivo de noticias con grid 3×3 y paginación (9 por página).
 * Lee contenido desde content/noticias/*.json
 */
$pageTitle       = 'Archivo de noticias — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Todas las noticias publicadas por la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

// Leer todas las noticias
$noticias = [];
$dirNoticias = __DIR__ . '/../../content/noticias/';
if (is_dir($dirNoticias)) {
    foreach (glob($dirNoticias . '*.json') as $archivo) {
        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            if (empty($datos['id'])) {
                $datos['id'] = basename($archivo, '.json');
            }
            $noticias[] = $datos;
        }
    }
    usort($noticias, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));
}

// Paginación
$porPagina  = 9;
$totalItems = count($noticias);
$totalPags  = max(1, ceil($totalItems / $porPagina));
$pagActual  = max(1, min($totalPags, intval($_GET['pag'] ?? 1)));
$offset     = ($pagActual - 1) * $porPagina;
$noticiasPag = array_slice($noticias, $offset, $porPagina);

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Archivo de noticias</h1>

    <?php if (!empty($noticiasPag)): ?>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
        <?php foreach ($noticiasPag as $noticia): ?>
          <article class="col">
            <a href="noticia.php?id=<?= urlencode($noticia['id']) ?>" class="text-decoration-none text-reset">
              <div class="surface-card h-100 card-hover">
                <?php if (!empty($noticia['imagen'])): ?>
                  <img src="<?= $basePath ?>assets/img/noticias/<?= htmlspecialchars($noticia['imagen']) ?>"
                       alt="<?= htmlspecialchars($noticia['titulo']) ?>"
                       class="img-fluid rounded mb-3">
                <?php else: ?>
                  <div class="placeholder-image placeholder-card mb-3" role="img" aria-label="Imagen pendiente">
                    <i class="bi bi-newspaper" style="font-size: 2rem;"></i>
                  </div>
                <?php endif; ?>
                <p class="news-date mb-2">
                  <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($noticia['fecha']) ?>
                  <?php if (!empty($noticia['categoria'])): ?>
                    · <span class="badge bg-secondary"><?= htmlspecialchars($noticia['categoria']) ?></span>
                  <?php endif; ?>
                </p>
                <h3 class="h5"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                <?php if (!empty($noticia['resumen'])): ?>
                  <p class="text-muted small mb-0"><?= htmlspecialchars($noticia['resumen']) ?></p>
                <?php endif; ?>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- Paginación -->
      <?php if ($totalPags > 1): ?>
        <nav aria-label="Paginacion de noticias">
          <ul class="pagination justify-content-center">
            <li class="page-item <?= $pagActual <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?pag=<?= $pagActual - 1 ?>" aria-label="Anterior">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <?php for ($i = 1; $i <= $totalPags; $i++): ?>
              <li class="page-item <?= $i === $pagActual ? 'active' : '' ?>">
                <a class="page-link" href="?pag=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $pagActual >= $totalPags ? 'disabled' : '' ?>">
              <a class="page-link" href="?pag=<?= $pagActual + 1 ?>" aria-label="Siguiente">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>

    <?php else: ?>
      <div class="surface-card">
        <p class="mb-0 text-muted">No hay noticias publicadas aun.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
