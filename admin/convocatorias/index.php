<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Convocatorias';
$basePath  = '../../';
$pdo       = get_pdo();

$filtro = $_GET['estado'] ?? '';
$where  = $filtro ? "WHERE estado = ".$pdo->quote($filtro) : '';
$items  = $pdo->query("SELECT id,slug,titulo,fecha_publicacion,fecha_cierre,estado FROM convocatorias {$where} ORDER BY fecha_publicacion DESC")->fetchAll();
$msg    = $_GET['msg'] ?? '';
ob_start(); ?>

<?php if($msg==='creado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Convocatoria creada.</div>
<?php elseif($msg==='editado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Convocatoria actualizada.</div>
<?php elseif($msg==='eliminado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Convocatoria eliminada.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Convocatorias</h1>
  <a href="crear.php" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Nueva convocatoria</a>
</div>

<div class="adm-card mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <?php foreach([''=> 'Todas','borrador'=>'Borradores','publicada'=>'Publicadas','cerrada'=>'Cerradas','archivada'=>'Archivadas'] as $v=>$l): ?>
      <a href="?estado=<?=$v?>" class="btn btn-sm" style="border-radius:20px;font-size:.8rem;
         background:<?=$filtro===$v?'rgba(59,130,246,.25)':'rgba(255,255,255,.05)'?>;
         border:1px solid <?=$filtro===$v?'rgba(59,130,246,.5)':'var(--adm-border)'?>;
         color:<?=$filtro===$v?'#60a5fa':'var(--adm-muted)'?>"><?=$l?></a>
    <?php endforeach; ?>
  </div>
</div>

<div class="adm-card">
  <?php if($items): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Título</th><th>Publicada</th><th>Cierre</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach($items as $c): ?>
            <tr>
              <td><?=htmlspecialchars($c['titulo'])?></td>
              <td style="color:var(--adm-muted);font-size:.8rem"><?=$c['fecha_publicacion']?></td>
              <td style="color:var(--adm-muted);font-size:.8rem"><?=$c['fecha_cierre']??'—'?></td>
              <td><span class="badge-estado badge-<?=$c['estado']?>"><?=ucfirst($c['estado'])?></span></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="editar.php?id=<?=$c['id']?>" class="btn btn-sm" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem"><i class="bi bi-pencil"></i></a>
                  <form method="POST" action="eliminar.php" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
                    <input type="hidden" name="id" value="<?=$c['id']?>">
                    <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm" style="padding:.25rem .65rem"
                            data-confirm="¿Eliminar «<?=htmlspecialchars($c['titulo'],ENT_QUOTES)?>»?">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="color:var(--adm-muted)">No hay convocatorias. <a href="crear.php" style="color:var(--adm-accent)">Crear la primera</a>.</p>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
