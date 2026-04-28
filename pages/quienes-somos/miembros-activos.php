<?php
/**
 * pages/quienes-somos/miembros-activos.php
 * Listado de miembros activos con tarjetas de perfil.
 * Cada tarjeta incluye: foto circular, nombre, especialidad, correo, link a perfil.
 */
$pageTitle       = 'Miembros activos — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conoce a los científicos, divulgadores y entusiastas que forman parte de la SAZ.';
$basePath        = '../../';

// Leer todos los miembros
$miembros = [];
$dirMiembros = __DIR__ . '/../../content/miembros/';

if (is_dir($dirMiembros)) {
    foreach (glob($dirMiembros . '*.json') as $archivo) {
        if (basename($archivo) === 'ejemplo.json') continue;

        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            if (empty($datos['id'])) {
                $datos['id'] = basename($archivo, '.json');
            }
            $miembros[] = $datos;
        }
    }
    usort($miembros, fn($a, $b) => strcmp($a['nombre'], $b['nombre']));
}

ob_start();
?>

<section class="py-5" aria-labelledby="members-title">
  <div class="container">
    <h1 id="members-title" class="section-title mb-4">Miembros activos</h1>
    <p class="mb-5">Conoce a las personas que forman parte de la SAZ y contribuyen a la divulgación astronómica en nuestro estado.</p>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php foreach ($miembros as $m): ?>
        <article class="col">
          <div class="surface-card h-100 text-center shadow-sm p-4">

            <div class="mx-auto mb-3">
              <?php 
              $fotoPath = 'assets/img/miembros/' . ($m['imagen'] ?? '');
              if (!empty($m['imagen']) && file_exists($basePath . $fotoPath)): ?>
                <img src="<?= $basePath . $fotoPath ?>" 
                     alt="Foto de <?= htmlspecialchars($m['nombre']) ?>" 
                     class="member-photo-img-sm shadow-sm"
                     style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
                     <?php else: ?>

                <div class="member-photo-placeholder mx-auto mb-3" aria-hidden="true">
                  <i class="bi bi-person-fill" style="font-size: 3rem;"></i>
                </div>
              <?php endif; ?>
            </div>  
            
            <h2 class="h6 mb-1"><?= htmlspecialchars($m['nombre']) ?></h2>
            <p class="text-muted small mb-3"><?= htmlspecialchars($m['especialidad']) ?></p>
            
            <div class="mb-4">
              <span class="visually-hidden">Correo electrónico: </span>
              <a href="mailto:<?= htmlspecialchars($m['correo']) ?>" class="link-accent small text-decoration-none">
                <i class="bi bi-envelope me-1" aria-hidden="true"></i>
                <?= !empty($m['correo']) ? htmlspecialchars($m['correo']) : 'contacto@sazac.com.mx' ?>
              </a>
            </div>

            <div class="d-grid gap-2">
              <?php if (!empty($m['id'])): ?>
                <a href="perfil-miembro.php?id=<?= htmlspecialchars($m['id']) ?>"
                   class="btn btn-sm btn-outline-primary"
                   aria-label="Ver perfil completo de <?= htmlspecialchars($m['nombre']) ?>">
                  Más información <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
              <?php endif; ?>

              

          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
