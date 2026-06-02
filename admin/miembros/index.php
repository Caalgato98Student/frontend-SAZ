<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Miembros';
$basePath  = '../../';
$pdo       = get_pdo();
$items     = $pdo->query("
    SELECT m.id, m.nombre, c.nombre AS cargo, m.especialidad, m.imagen, m.activo, m.orden 
    FROM miembros m 
    LEFT JOIN cargos c ON m.cargo_id = c.id 
    ORDER BY m.orden, m.nombre
")->fetchAll();
$msg       = $_GET['msg'] ?? '';
ob_start(); ?>

<?php if($msg==='creado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Miembro agregado.</div>
<?php elseif($msg==='editado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Miembro actualizado.</div>
<?php elseif($msg==='eliminado'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Miembro eliminado.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Miembros</h1>
  <a href="crear.php" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Nuevo miembro</a>
</div>

<div class="adm-card">
  <?php if($items): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Foto</th><th>Nombre</th><th>Cargo</th><th>Especialidad</th><th>Orden</th><th>Activo</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach($items as $m): ?>
          <tr>
            <td style="width:50px">
              <?php if ($m['imagen'] && file_exists(__DIR__ . '/../../assets/img/miembros/' . $m['imagen'])): ?>
                <img src="../../assets/img/miembros/<?= htmlspecialchars($m['imagen']) ?>" alt="<?= htmlspecialchars($m['nombre']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:50%">
              <?php else: ?>
                <div style="width:40px;height:40px;background:var(--adm-dark);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--adm-border)"><i class="bi bi-person"></i></div>
              <?php endif; ?>
            </td>
            <td style="font-weight:600"><?=htmlspecialchars($m['nombre'])?></td>
            <td style="color:var(--adm-muted);font-size:.8rem"><?=htmlspecialchars($m['cargo']??'—')?></td>
            <td style="color:var(--adm-muted);font-size:.8rem"><?=htmlspecialchars($m['especialidad']??'—')?></td>
            <td style="color:var(--adm-muted);font-size:.8rem"><?=$m['orden']?></td>
            <td><?=$m['activo']?'<i class="bi bi-check-circle-fill" style="color:#4ade80"></i>':'<i class="bi bi-dash-circle" style="color:var(--adm-border)"></i>'?></td>
            <td>
              <div class="d-flex gap-2">
                <a href="editar.php?id=<?=$m['id']?>" class="btn btn-sm" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="eliminar.php" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
                  <input type="hidden" name="id" value="<?=$m['id']?>">
                  <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm" style="padding:.25rem .65rem"
                          data-confirm="¿Eliminar a «<?=htmlspecialchars($m['nombre'],ENT_QUOTES)?>»? Los datos de formación y divulgación también se eliminarán."><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="color:var(--adm-muted)">No hay miembros. <a href="crear.php" style="color:var(--adm-accent)">Agregar el primero</a>.</p>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';