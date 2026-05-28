<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Observaciones';
$basePath  = '../../';
$pdo       = get_pdo();
$items     = $pdo->query("SELECT id,slug,titulo,icono,activo,orden FROM observaciones ORDER BY orden")->fetchAll();
$msg       = $_GET['msg'] ?? '';
ob_start(); ?>

<?php if($msg==='editado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Observación actualizada.</div><?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 fw-bold mb-0">Observaciones</h1>
</div>

<div class="adm-card mb-3" style="font-size:.85rem;color:var(--adm-muted)">
  <i class="bi bi-info-circle me-1"></i>
  Los tipos de observación (Diurna, Nocturna, Solar…) se editan aquí. Sus ítems de contenido se administran al presionar "Editar".
</div>

<div class="adm-card">
  <?php if($items): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Icono</th><th>Título</th><th>Slug</th><th>Activa</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach($items as $o): ?>
            <tr>
              <td><i class="<?=htmlspecialchars($o['icono'])?>" style="font-size:1.25rem;color:var(--adm-accent)"></i></td>
              <td><?=htmlspecialchars($o['titulo'])?></td>
              <td><code style="color:var(--adm-muted);font-size:.78rem"><?=htmlspecialchars($o['slug'])?></code></td>
              <td><?=$o['activo']?'<i class="bi bi-check-circle-fill" style="color:#4ade80"></i>':'<i class="bi bi-dash-circle" style="color:var(--adm-border)"></i>'?></td>
              <td>
                <a href="editar.php?id=<?=$o['id']?>" class="btn btn-sm" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem"><i class="bi bi-pencil"></i> Editar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="color:var(--adm-muted)">No hay observaciones en la base de datos.</p>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
