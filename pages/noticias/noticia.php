<?php
/**
 * pages/noticias/noticia.php
 * Vista individual de una noticia.
 * Acceso: noticia.php?id=noticia-001
 * Lee contenido desde content/noticias/[id].json
 */
$basePath = '../../';
$id = $_GET['id'] ?? '';

// Buscar el archivo JSON
$noticia = null;
if ($id) {
    // Sanitizar: solo permitir alfanuméricos, guiones y guiones bajos
    $idLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
    $archivoJson = __DIR__ . '/../../content/noticias/' . $idLimpio . '.json';
    if (file_exists($archivoJson)) {
        $noticia = json_decode(file_get_contents($archivoJson), true);
    }
}

$pageTitle = $noticia
    ? htmlspecialchars($noticia['titulo']) . ' — SAZ'
    : 'Noticia no encontrada — SAZ';
$pageDescription = $noticia
    ? htmlspecialchars($noticia['resumen'] ?? '')
    : 'La noticia solicitada no fue encontrada.';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php" class="link-accent">Inicio</a></li>
        <li class="breadcrumb-item"><a href="index.php" class="link-accent">Noticias</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $noticia ? htmlspecialchars($noticia['titulo']) : 'No encontrada' ?></li>
      </ol>
    </nav>

    <?php if ($noticia): ?>
      <article class="col-lg-8 mx-auto">
        <?php if (!empty($noticia['imagen'])): ?>
          <img src="<?= $basePath ?>assets/img/noticias/<?= htmlspecialchars($noticia['imagen']) ?>"
               alt="<?= htmlspecialchars($noticia['titulo']) ?>"
               class="img-fluid rounded-3 mb-4 w-100">
        <?php else: ?>
          <div class="placeholder-image placeholder-hero mb-4">
            <i class="bi bi-newspaper" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">Imagen pendiente</p>
          </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
          <span class="news-date">
            <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($noticia['fecha']) ?>
          </span>
          <?php if (!empty($noticia['autor'])): ?>
            <span class="text-muted small">
              <i class="bi bi-person me-1"></i><?= htmlspecialchars($noticia['autor']) ?>
            </span>
          <?php endif; ?>
          <?php if (!empty($noticia['categoria'])): ?>
            <span class="badge bg-secondary"><?= htmlspecialchars($noticia['categoria']) ?></span>
          <?php endif; ?>
        </div>

        <h1 class="section-title mb-4"><?= htmlspecialchars($noticia['titulo']) ?></h1>

        <?php if (!empty($noticia['resumen'])): ?>
          <p class="lead mb-4"><?= htmlspecialchars($noticia['resumen']) ?></p>
        <?php endif; ?>

        <div class="surface-card">  
          <p class="mb-0">  <?= $noticia['contenido'] ?> </p>
        </div>

        <div class="mt-4">
          <a href="index.php" class="link-accent"><i class="bi bi-arrow-left me-1"></i>Volver al archivo de noticias</a>
        </div>
      </article>
    <?php else: ?>
      <div class="col-lg-8 mx-auto text-center">
        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
        <h1 class="section-title mt-3">Noticia no encontrada</h1>
        <p class="text-muted">La noticia solicitada no existe o fue removida.</p>
        <a href="index.php" class="btn btn-primary mt-3"><i class="bi bi-arrow-left me-1"></i>Ir al archivo de noticias</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
