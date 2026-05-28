<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo  = get_pdo();
$id   = intval($_GET['id'] ?? 0);
$act  = $pdo->prepare("SELECT * FROM actividades WHERE id=? LIMIT 1");
$act->execute([$id]); $act=$act->fetch();
if (!$act) { header('Location: index.php'); exit; }

$pageTitle = 'Editar actividad: '.$act['titulo'];
$basePath  = '../../';
$errors    = [];
$msgItem   = $_GET['msgitem'] ?? '';

// Ítems de esta actividad
$items = $pdo->query("SELECT * FROM actividad_items WHERE actividad_id={$id} ORDER BY orden")->fetchAll();

// Acciones sobre ítems
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $action = $_POST['action'];

    if ($action === 'save_actividad') {
        $titulo = sanitize_text($_POST['titulo']??'', 255);
        $desc   = trim($_POST['descripcion']??'');
        $icono  = sanitize_text($_POST['icono']??'', 100);
        $orden  = intval($_POST['orden']??0);
        $activo = isset($_POST['activo']) ? 1 : 0;
        if (!$titulo) $errors[] = 'El título es obligatorio.';
        if (!$errors) {
            $pdo->prepare("UPDATE actividades SET titulo=?,descripcion=?,icono=?,activo=?,orden=? WHERE id=?")
                ->execute([$titulo,$desc,$icono,$activo,$orden,$id]);
            header("Location: editar.php?id={$id}&msg=editado"); exit;
        }
    }
    elseif ($action === 'add_item') {
        $tItem = sanitize_text($_POST['item_titulo']??'', 255);
        $dItem = trim($_POST['item_desc']??'');
        $orden = (int) $pdo->query("SELECT COALESCE(MAX(orden),0)+1 FROM actividad_items WHERE actividad_id={$id}")->fetchColumn();
        $pdo->prepare("INSERT INTO actividad_items (actividad_id,titulo,descripcion,orden) VALUES(?,?,?,?)")
            ->execute([$id,$tItem,$dItem,$orden]);
        header("Location: editar.php?id={$id}&msgitem=added"); exit;
    }
    elseif ($action === 'delete_item') {
        $itemId = intval($_POST['item_id']??0);
        $pdo->prepare("DELETE FROM actividad_items WHERE id=? AND actividad_id=?")->execute([$itemId,$id]);
        header("Location: editar.php?id={$id}&msgitem=deleted"); exit;
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Actividades</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar: <?=htmlspecialchars($act['titulo'])?></h1>

<?php if(isset($_GET['msg']) && $_GET['msg']==='editado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Actividad actualizada.</div>
<?php endif; ?>
<?php if($msgItem==='added'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Ítem agregado.</div>
<?php elseif($msgItem==='deleted'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Ítem eliminado.</div>
<?php endif; ?>

<div class="row g-3">
  <!-- Datos de la actividad -->
  <div class="col-lg-5">
    <div class="adm-card">
      <h2 class="adm-card-title">Datos generales</h2>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?=$csrf?>">
        <input type="hidden" name="action" value="save_actividad">
        <div class="mb-3"><label class="form-label" for="titulo">Título</label>
          <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($act['titulo'])?>" required></div>
        <div class="mb-3"><label class="form-label" for="descripcion">Descripción introductoria</label>
          <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?=htmlspecialchars($act['descripcion']??'')?></textarea></div>
        <div class="mb-3"><label class="form-label" for="icono">Clase de ícono Bootstrap Icons</label>
          <div class="input-group">
            <span class="input-group-text" style="background:var(--adm-dark);border:1px solid var(--adm-border);border-right:none">
              <i id="iconPreview" class="<?=htmlspecialchars($act['icono'])?>"></i>
            </span>
            <input type="text" class="form-control" id="icono" name="icono" value="<?=htmlspecialchars($act['icono'])?>"
                   oninput="document.getElementById('iconPreview').className=this.value">
          </div>
          <small style="color:var(--adm-muted);font-size:.78rem">Ej: <code>bi bi-telescope-fill</code></small>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6"><label class="form-label" for="orden">Orden</label>
            <input type="number" class="form-control" id="orden" name="orden" value="<?=$act['orden']?>" min="0"></div>
          <div class="col-6 d-flex align-items-end">
            <div class="form-check pb-2"><input class="form-check-input" type="checkbox" id="activo" name="activo" <?=$act['activo']?'checked':''?>>
              <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activa</label></div>
          </div>
        </div>
        <button type="submit" class="btn-adm-primary btn w-100"><i class="bi bi-save me-1"></i> Guardar actividad</button>
      </form>
    </div>
  </div>

  <!-- Ítems -->
  <div class="col-lg-7">
    <div class="adm-card mb-3">
      <h2 class="adm-card-title">Ítems descriptivos</h2>
      <?php if($items): ?>
        <?php foreach($items as $item): ?>
          <div class="d-flex align-items-start justify-content-between gap-2 mb-2 pb-2" style="border-bottom:1px solid var(--adm-border)">
            <div>
              <div style="font-weight:600;font-size:.9rem"><?=htmlspecialchars($item['titulo'])?></div>
              <?php if($item['descripcion']): ?>
                <div style="font-size:.8rem;color:var(--adm-muted)"><?=htmlspecialchars(mb_substr($item['descripcion'],0,80)).'...'?></div>
              <?php endif; ?>
            </div>
            <form method="POST" style="flex-shrink:0">
              <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
              <input type="hidden" name="action" value="delete_item">
              <input type="hidden" name="item_id" value="<?=$item['id']?>">
              <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm"
                      style="padding:.2rem .55rem" data-confirm="¿Eliminar este ítem?"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color:var(--adm-muted);font-size:.875rem">No hay ítems aún.</p>
      <?php endif; ?>
    </div>

    <div class="adm-card">
      <h2 class="adm-card-title">Agregar ítem</h2>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
        <input type="hidden" name="action" value="add_item">
        <div class="mb-3"><label class="form-label" for="item_titulo">Título del ítem</label>
          <input type="text" class="form-control" id="item_titulo" name="item_titulo" required placeholder="Ej: Charlas de café astronómico"></div>
        <div class="mb-3"><label class="form-label" for="item_desc">Descripción</label>
          <textarea class="form-control" id="item_desc" name="item_desc" rows="3"></textarea></div>
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Agregar ítem</button>
      </form>
    </div>
  </div>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
