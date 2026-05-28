<?php
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db.php';

$pdo       = get_pdo();
$eventoId  = intval($_GET['evento_id'] ?? 0);
$evento    = $pdo->prepare("SELECT id,titulo FROM eventos WHERE id=? LIMIT 1");
$evento->execute([$eventoId]); $evento=$evento->fetch();
if (!$evento) { header('Location: ../index.php'); exit; }

$pageTitle = 'Ediciones: '.$evento['titulo'];
$basePath  = '../../../';
$ediciones = $pdo->query("SELECT * FROM evento_ediciones WHERE evento_id={$eventoId} ORDER BY anio DESC")->fetchAll();
$msg       = $_GET['msg'] ?? '';
ob_start(); ?>

<?php if($msg==='creado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Edición agregada.</div>
<?php elseif($msg==='editado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Edición actualizada.</div>
<?php elseif($msg==='eliminado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Edición eliminada.</div>
<?php endif; ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="../index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Eventos</a>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem"><?=htmlspecialchars($evento['titulo'])?></span>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem">Ediciones</span>
</div>
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 fw-bold mb-0">Ediciones — <?=htmlspecialchars($evento['titulo'])?></h1>
  <a href="crear.php?evento_id=<?=$eventoId?>" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Nueva edición</a>
</div>

<div class="adm-card">
  <?php if($ediciones): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Año</th><th>Lugar</th><th>Inicio</th><th>Fin</th><th>Acciones</th></tr></thead>
        <tbody>
          <?php foreach($ediciones as $ed): ?>
            <tr>
              <td><strong><?=$ed['anio']?></strong></td>
              <td style="color:var(--adm-muted);font-size:.85rem"><?=htmlspecialchars($ed['lugar']??'—')?></td>
              <td style="color:var(--adm-muted);font-size:.8rem"><?=$ed['fecha_inicio']??'—'?></td>
              <td style="color:var(--adm-muted);font-size:.8rem"><?=$ed['fecha_fin']??'—'?></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="editar.php?id=<?=$ed['id']?>&evento_id=<?=$eventoId?>" class="btn btn-sm" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem"><i class="bi bi-pencil"></i></a>
                  <form method="POST" action="eliminar.php" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
                    <input type="hidden" name="id" value="<?=$ed['id']?>">
                    <input type="hidden" name="evento_id" value="<?=$eventoId?>">
                    <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm" style="padding:.25rem .65rem"
                            data-confirm="¿Eliminar la edición <?=$ed['anio']?>?"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="color:var(--adm-muted)">No hay ediciones. <a href="crear.php?evento_id=<?=$eventoId?>" style="color:var(--adm-accent)">Agregar la primera</a>.</p>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../../base_admin.php';
