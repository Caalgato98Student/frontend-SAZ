<?php
/**
 * admin/index.php — Dashboard del panel CMS.
 */
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/auth.php';
require_admin_auth();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Dashboard';
$basePath  = '../';

$pdo = get_pdo();

// Conteos de cada módulo
function count_table(PDO $pdo, string $table, string $where = ''): int {
    $sql  = "SELECT COUNT(*) FROM `{$table}`" . ($where ? " WHERE {$where}" : '');
    return (int) $pdo->query($sql)->fetchColumn();
}

$stats = [
    'noticias_pub'   => count_table($pdo, 'noticias', "estado = 'publicado'"),
    'noticias_total' => count_table($pdo, 'noticias'),
    'astro'          => count_table($pdo, 'astrofotografia', 'visible = 1'),
    'convocatorias'  => count_table($pdo, 'convocatorias', "estado = 'publicada'"),
    'eventos'        => count_table($pdo, 'eventos', 'activo = 1'),
    'colaboradores'  => count_table($pdo, 'colaboradores', 'activo = 1'),
    'actividades'    => count_table($pdo, 'actividades', 'activo = 1'),
    'observaciones'  => count_table($pdo, 'observaciones', 'activo = 1'),
];

// Últimas 5 noticias
$ultimasNoticias = $pdo->query(
    "SELECT titulo, fecha, estado FROM noticias ORDER BY creado_en DESC LIMIT 5"
)->fetchAll();

ob_start(); ?>

<div class="row g-3 mb-4">
  <!-- Tarjetas de estadísticas -->
  <?php
  $cards = [
    ['icon'=>'bi-newspaper',      'label'=>'Noticias publicadas', 'value'=>$stats['noticias_pub'],   'sub'=>"de {$stats['noticias_total']} totales",  'url'=>'noticias/index.php',       'color'=>'#3b82f6'],
    ['icon'=>'bi-camera-fill',    'label'=>'Fotos activas',       'value'=>$stats['astro'],           'sub'=>'en galería',                              'url'=>'astrofotografia/index.php', 'color'=>'#8b5cf6'],
    ['icon'=>'bi-megaphone-fill', 'label'=>'Convocatorias',       'value'=>$stats['convocatorias'],   'sub'=>'publicadas',                              'url'=>'convocatorias/index.php',  'color'=>'#f59e0b'],
    ['icon'=>'bi-stars',          'label'=>'Eventos activos',     'value'=>$stats['eventos'],         'sub'=>'programas',                               'url'=>'eventos/index.php',        'color'=>'#22c55e'],
    ['icon'=>'bi-people-fill',    'label'=>'Colaboradores',       'value'=>$stats['colaboradores'],   'sub'=>'activos',                                 'url'=>'colaboradores/index.php',  'color'=>'#06b6d4'],
    ['icon'=>'bi-calendar-event', 'label'=>'Actividades',         'value'=>$stats['actividades'],     'sub'=>'activas',                                 'url'=>'actividades/index.php',    'color'=>'#ec4899'],
  ];
  foreach ($cards as $c): ?>
    <div class="col-6 col-lg-4">
      <a href="<?= $c['url'] ?>" class="text-decoration-none">
        <div class="adm-card h-100" style="border-color:<?= $c['color'] ?>22;transition:transform .2s;cursor:pointer"
             onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="h2 mb-0 fw-bold" style="color:<?= $c['color'] ?>"><?= $c['value'] ?></div>
              <div class="fw-semibold mt-1" style="font-size:.9rem"><?= $c['label'] ?></div>
              <div style="font-size:.78rem;color:var(--adm-muted)"><?= $c['sub'] ?></div>
            </div>
            <div style="font-size:1.75rem;color:<?= $c['color'] ?>;opacity:.7">
              <i class="bi <?= $c['icon'] ?>"></i>
            </div>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<!-- Accesos rápidos -->
<div class="row g-3 mb-4">
  <div class="col-12 col-lg-7">
    <div class="adm-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="adm-card-title mb-0">Últimas noticias</h2>
        <a href="noticias/crear.php" class="btn-adm-primary btn btn-sm" style="background:linear-gradient(135deg,#3b82f6,#6366f1);border:none;color:#fff;font-weight:600;border-radius:7px;padding:.35rem .85rem;font-size:.83rem">
          <i class="bi bi-plus-lg me-1"></i> Nueva noticia
        </a>
      </div>
      <?php if ($ultimasNoticias): ?>
        <table class="adm-table">
          <thead>
            <tr>
              <th>Título</th><th>Fecha</th><th>Estado</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ultimasNoticias as $n): ?>
              <tr>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                  <?= htmlspecialchars($n['titulo']) ?>
                </td>
                <td style="color:var(--adm-muted);font-size:.8rem"><?= $n['fecha'] ?></td>
                <td>
                  <span class="badge-estado badge-<?= $n['estado'] ?>">
                    <?= ucfirst($n['estado']) ?>
                  </span>
                </td>
                <td>
                  <a href="noticias/index.php" style="color:var(--adm-muted);font-size:.8rem">ver →</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="mb-0" style="color:var(--adm-muted);font-size:.875rem">No hay noticias registradas aún.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-12 col-lg-5">
    <div class="adm-card">
      <h2 class="adm-card-title">Accesos rápidos</h2>
      <div class="d-grid gap-2">
        <a href="noticias/crear.php" class="btn d-flex align-items-center gap-2"
           style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:8px;text-align:left">
          <i class="bi bi-plus-circle"></i> Nueva noticia
        </a>
        <a href="astrofotografia/crear.php" class="btn d-flex align-items-center gap-2"
           style="background:rgba(139,92,246,.1);border:1px solid rgba(139,92,246,.3);color:#a78bfa;border-radius:8px;text-align:left">
          <i class="bi bi-plus-circle"></i> Subir fotografía
        </a>
        <a href="convocatorias/crear.php" class="btn d-flex align-items-center gap-2"
           style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:#fbbf24;border-radius:8px;text-align:left">
          <i class="bi bi-plus-circle"></i> Nueva convocatoria
        </a>
        <a href="eventos/crear.php" class="btn d-flex align-items-center gap-2"
           style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#4ade80;border-radius:8px;text-align:left">
          <i class="bi bi-plus-circle"></i> Nuevo evento
        </a>
        <a href="colaboradores/crear.php" class="btn d-flex align-items-center gap-2"
           style="background:rgba(6,182,212,.1);border:1px solid rgba(6,182,212,.3);color:#22d3ee;border-radius:8px;text-align:left">
          <i class="bi bi-plus-circle"></i> Nuevo colaborador
        </a>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/base_admin.php';
