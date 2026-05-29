<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Actividades';
$basePath  = '../../';
$pdo       = get_pdo();
$items     = $pdo->query("SELECT id,slug,titulo,icono,activo,orden FROM actividades ORDER BY orden,titulo")->fetchAll();
$msg       = $_GET['msg'] ?? '';
ob_start(); ?>

<?php if($msg==='editado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Actividad actualizada.</div><?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Actividades</h1>
  <a href="crear.php" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Nueva actividad</a>
</div>

<div class="adm-card mb-3" style="font-size:.85rem;color:var(--adm-muted)">
  <i class="bi bi-info-circle me-1"></i>
  Las actividades (Charlas, Conferencias, Talleres…) se editan aquí. Sus ítems descriptivos se administran al presionar "Editar".
</div>

<div class="adm-card">
  <?php if($items): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Icono</th><th>Título</th><th>Slug</th><th>Orden</th><th>Activa</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach($items as $a): ?>
            <tr>
              <td><i class="<?=htmlspecialchars($a['icono'])?>" style="font-size:1.25rem;color:var(--adm-accent)"></i></td>
              <td><?=htmlspecialchars($a['titulo'])?></td>
              <td><code style="color:var(--adm-muted);font-size:.78rem"><?=htmlspecialchars($a['slug'])?></code></td>
              <td style="color:var(--adm-muted);font-size:.85rem"><?=$a['orden']?></td>
              <td><?=$a['activo']?'<i class="bi bi-check-circle-fill" style="color:#4ade80"></i>':'<i class="bi bi-dash-circle" style="color:var(--adm-border)"></i>'?></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="editar.php?id=<?=$a['id']?>" class="btn btn-sm" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem"><i class="bi bi-pencil"></i> Editar</a>
                  <form method="POST" action="eliminar.php" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
                    <input type="hidden" name="id" value="<?=$a['id']?>">
                    <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm" style="padding:.25rem .65rem"
                            data-confirm="¿Desactivar «<?=htmlspecialchars($a['titulo'],ENT_QUOTES)?>»?"><i class="bi bi-eye-slash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="color:var(--adm-muted)">No hay actividades en la base de datos.</p>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
