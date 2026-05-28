<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Eventos';
$basePath  = '../../';
$pdo       = get_pdo();
$eventos   = $pdo->query("SELECT id,slug,titulo,activo,orden FROM eventos ORDER BY orden,titulo")->fetchAll();
$msg       = $_GET['msg'] ?? '';
ob_start(); ?>

<?php if($msg==='creado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Evento creado.</div>
<?php elseif($msg==='editado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Evento actualizado.</div>
<?php elseif($msg==='eliminado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Evento eliminado.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Eventos</h1>
  <a href="crear.php" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Nuevo evento</a>
</div>
<div class="adm-card">
  <?php if($eventos): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Título</th><th>Slug</th><th>Activo</th><th>Ediciones</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach($eventos as $ev):
            $nEd = (int) $pdo->prepare("SELECT COUNT(*) FROM evento_ediciones WHERE evento_id=?")->execute([$ev['id']])||0;
            $stE = $pdo->prepare("SELECT COUNT(*) FROM evento_ediciones WHERE evento_id=?");
            $stE->execute([$ev['id']]); $nEd=$stE->fetchColumn();
        ?>
          <tr>
            <td><?=htmlspecialchars($ev['titulo'])?></td>
            <td style="color:var(--adm-muted);font-size:.8rem"><code style="color:var(--adm-muted)"><?=htmlspecialchars($ev['slug'])?></code></td>
            <td><?=$ev['activo']?'<i class="bi bi-check-circle-fill" style="color:#4ade80"></i>':'<i class="bi bi-dash-circle" style="color:var(--adm-border)"></i>'?></td>
            <td><span style="font-size:.8rem;color:var(--adm-muted)"><?=$nEd?> edición(es)</span>
              <a href="ediciones/index.php?evento_id=<?=$ev['id']?>" style="font-size:.8rem;color:var(--adm-accent);margin-left:.5rem">ver →</a>
            </td>
            <td>
              <div class="d-flex gap-2">
                <a href="editar.php?id=<?=$ev['id']?>" class="btn btn-sm" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="eliminar.php" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
                  <input type="hidden" name="id" value="<?=$ev['id']?>">
                  <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm" style="padding:.25rem .65rem"
                          data-confirm="¿Eliminar «<?=htmlspecialchars($ev['titulo'],ENT_QUOTES)?>» y todas sus ediciones?"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="color:var(--adm-muted)">No hay eventos. <a href="crear.php" style="color:var(--adm-accent)">Crear el primero</a>.</p>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
