<?php
/**
 * partials/noticias-portada.php
 * Muestra las 5 noticias más recientes en la página de inicio.
 * Cada tarjeta es clickeable y enlaza a la noticia completa.
 * Lee contenido desde content/noticias/*.json
 */

$noticias = [];
$dirNoticias = __DIR__ . '/../content/noticias/';
if (is_dir($dirNoticias)) {
    foreach (glob($dirNoticias . '*.json') as $archivo) {
        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            // Usar el nombre del archivo como ID si no viene en el JSON
            if (empty($datos['id'])) {
                $datos['id'] = basename($archivo, '.json');
            }
            $noticias[] = $datos;
        }
    }
    usort($noticias, fn($a, $b) => strtotime($b['fecha']) - strtotime($a['fecha']));
}

// 5 más recientes para la portada
$noticiasCinco = array_slice($noticias, 0, 5);
?>

<section class="py-5" id="noticias-recientes">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <h2 class="section-title mb-0">Noticias recientes</h2>
      <a href="<?= $basePath ?>pages/noticias/index.php" class="link-accent">Ver todas las noticias <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php if (!empty($noticiasCinco)): ?>
        <?php foreach ($noticiasCinco as $noticia): ?>
          <article class="col">
            <a href="<?= $basePath ?>pages/noticias/noticia.php?id=<?= urlencode($noticia['id']) ?>" class="text-decoration-none text-reset">
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
                </p>
                <h3 class="h5"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                <?php if (!empty($noticia['resumen'])): ?>
                  <p class="text-muted small mb-0"><?= htmlspecialchars($noticia['resumen']) ?></p>
                <?php endif; ?>
              </div>
            </a>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for ($i = 0; $i < 5; $i++): ?>
          <article class="col">
            <div class="surface-card h-100">
              <div class="placeholder-image placeholder-card mb-3" role="img" aria-label="Imagen nota principal pendiente">
                <i class="bi bi-newspaper" style="font-size: 2rem;"></i>
              </div>
              <p class="news-date mb-2">—</p>
              <h3 class="h5">Noticia proximamente</h3>
            </div>
          </article>
        <?php endfor; ?>
      <?php endif; ?>
    </div>
  </div>
</section>
