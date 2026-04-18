<?php
/**
 * pages/convocatorias/index.php
 * Todas las convocatorias: activas primero con estilo prominente,
 * inactivas después con texto tenue.
 * Lee contenido desde content/convocatorias/*.json
 */
$pageTitle       = 'Convocatorias — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Convocatorias abiertas y cerradas de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

$convocatorias = [];
$dirConvocatorias = __DIR__ . '/../../content/convocatorias/';
if (is_dir($dirConvocatorias)) {
    foreach (glob($dirConvocatorias . '*.json') as $archivo) {
        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            $convocatorias[] = $datos;
        }
    }
    usort($convocatorias, function ($a, $b) {
        $aActiva = !empty($a['activa']);
        $bActiva = !empty($b['activa']);
        if ($aActiva !== $bActiva) {
            return $bActiva <=> $aActiva;
        }
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });
}

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Convocatorias</h1>

    <?php if (!empty($convocatorias)): ?>
      <div class="row g-4">
        <?php foreach ($convocatorias as $conv): ?>
          <?php $esActiva = !empty($conv['activa']); ?>
          <div class="col-md-6">
            <div class="surface-card h-100 <?= $esActiva ? 'convocatoria-activa' : 'convocatoria-inactiva' ?>">
              <div class="d-flex align-items-center gap-2 mb-3">
                <?php if ($esActiva): ?>
                  <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activa</span>
                <?php else: ?>
                  <span class="badge bg-secondary"><i class="bi bi-clock-history me-1"></i>Cerrada</span>
                <?php endif; ?>
                <span class="news-date mb-0">
                  <i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($conv['fecha']) ?>
                </span>
              </div>
              <h3 class="h5 <?= $esActiva ? '' : 'text-muted' ?>"><?= htmlspecialchars($conv['titulo']) ?></h3>
              <?php if (!empty($conv['resumen'])): ?>
                <p class="<?= $esActiva ? '' : 'text-muted' ?> mb-2"><?= htmlspecialchars($conv['resumen']) ?></p>
              <?php endif; ?>
              <?php if (!empty($conv['contenido'])): ?>
                <p class="small <?= $esActiva ? '' : 'text-muted' ?> mb-2"><?= htmlspecialchars($conv['contenido']) ?></p>
              <?php endif; ?>
              <?php if (!empty($conv['cierre'])): ?>
                <p class="small text-muted mb-0">
                  <i class="bi bi-calendar-x me-1"></i>Fecha de cierre: <?= htmlspecialchars($conv['cierre']) ?>
                </p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="surface-card">
        <p class="mb-0 text-muted">No hay convocatorias publicadas aun.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
