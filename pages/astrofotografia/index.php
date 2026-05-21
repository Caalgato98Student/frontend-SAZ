<?php
/**
 * pages/astrofotografia/index.php
 * Portal de entrada — Selección de categorías de astrofotografía.
 * Muestra lista vertical: Icono ● Título ● Flecha de acceso.
 * Cada fila enlaza a pages/astrofotografia/galeria/index.php?categoria=XXX
 */
$pageTitle       = 'Astrofotografía — Sociedad Astronómica de Zacatecas';
$pageDescription = 'Explora la galería de astrofotografía de la SAZ organizada por categoría: Sol, Luna y Espacio Profundo.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/astrofotografia.php';

$cats = get_astrofoto_categorias();
$categorias = array_map(fn($c) => [
    'slug'      => $c['slug'],
    'titulo'    => $c['nombre'],
    'subtitulo' => $c['descripcion'] ?? '',
    'icon_bi'   => $c['icono'],
    'color'     => $c['color'],
    'bg'        => $c['color'] . '1f',
    'count'     => count(get_astrofotos_por_categoria($c['slug'])),
], $cats);

ob_start();
?>

<section class="py-5">
  <div class="container" style="max-width: 720px;">

    <!-- Encabezado -->
    <div class="mb-5">
      <span class="text-accent text-uppercase small fw-bold d-block mb-2">
        <i class="bi bi-camera me-1"></i>Galería Fotográfica
      </span>
      <h1 class="section-title display-6 mb-2">Astrofotografía SAZ</h1>
      <p class="lead text-muted mb-0">
        Selecciona una categoría para explorar las imágenes capturadas por los miembros de la Sociedad Astronómica de Zacatecas.
      </p>
    </div>

    <!-- Lista de categorías -->
    <div class="astro-menu-list surface-card p-0 overflow-hidden">
      <?php foreach ($categorias as $i => $cat): ?>
        <a href="<?= $basePath ?>pages/astrofotografia/galeria/index.php?categoria=<?= $cat['slug'] ?>"
           class="astro-menu-row d-flex align-items-center gap-3 text-decoration-none px-4 py-3
                  <?= $i < count($categorias) - 1 ? 'border-bottom' : '' ?>"
           id="cat-<?= $cat['slug'] ?>">

          <!-- Icono circular -->
          <div class="astro-menu-icon flex-shrink-0"
               style="background:<?= $cat['bg'] ?>; color:<?= $cat['color'] ?>;">
            <i class="bi <?= $cat['icon_bi'] ?>"></i>
          </div>

          <!-- Texto -->
          <div class="flex-grow-1">
            <span class="fw-bold d-block" style="color: var(--text-main);">
              <?= htmlspecialchars($cat['titulo']) ?>
            </span>
            <span class="small text-muted"><?= htmlspecialchars($cat['subtitulo']) ?></span>
          </div>

          <!-- Badge contador -->
          <?php if ($cat['count'] > 0): ?>
            <span class="badge astro-menu-badge"
                  style="background:<?= $cat['bg'] ?>; color:<?= $cat['color'] ?>; border: 1px solid <?= $cat['color'] ?>30;">
              <?= $cat['count'] ?> foto<?= $cat['count'] !== 1 ? 's' : '' ?>
            </span>
          <?php endif; ?>

          <!-- Flecha -->
          <i class="bi bi-arrow-right-circle astro-menu-arrow flex-shrink-0"
             style="color:<?= $cat['color'] ?>; font-size: 1.4rem;"></i>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Nota informativa -->
    <div class="notice-box rounded-3 px-4 py-3 mt-4 small">
      <i class="bi bi-info-circle me-2"></i>
      Las imágenes son propiedad de sus respectivos autores. Para su reproducción, contactar a la SAZ.
    </div>

  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
