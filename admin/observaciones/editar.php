<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo  = get_pdo();
$id   = intval($_GET['id'] ?? 0);
$obs  = $pdo->prepare("SELECT * FROM observaciones WHERE id=? LIMIT 1");
$obs->execute([$id]); $obs=$obs->fetch();
if (!$obs) { header('Location: index.php'); exit; }

$pageTitle = 'Editar observación: '.$obs['titulo'];
$basePath  = '../../';
$errors    = [];
$msgItem   = $_GET['msgitem'] ?? '';

// Ítems de esta observación
$items = $pdo->query(
    "SELECT * FROM observacion_items WHERE observacion_id={$id} ORDER BY orden"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $action = $_POST['action'];

    if ($action === 'save_obs') {
        $titulo   = sanitize_text($_POST['titulo']??'', 255);
        $desc     = trim($_POST['descripcion_intro']??'');
        $recoms   = trim($_POST['recomendaciones']??'');
        $icono    = sanitize_text($_POST['icono']??'', 100);
        $orden    = intval($_POST['orden']??0);
        $activo   = isset($_POST['activo']) ? 1 : 0;
        if (!$titulo) $errors[] = 'El título es obligatorio.';
        if (!$errors) {
            $pdo->prepare("UPDATE observaciones SET titulo=?,descripcion_intro=?,recomendaciones=?,icono=?,activo=?,orden=? WHERE id=?")
                ->execute([$titulo,$desc,$recoms,$icono,$activo,$orden,$id]);
            header("Location: editar.php?id={$id}&msg=editado"); exit;
        }
    }
    elseif ($action === 'add_item') {
        $titulo2 = sanitize_text($_POST['item_titulo']??'', 255);
        $desc2   = trim($_POST['item_desc']??'');
        $icono2  = sanitize_text($_POST['item_icono']??'', 100);
        $stmtMax = $pdo->prepare("SELECT COALESCE(MAX(orden),0)+1 FROM observacion_items WHERE observacion_id = ?");
        $stmtMax->execute([$id]);
        $orden2 = (int) $stmtMax->fetchColumn();
        $pdo->prepare("INSERT INTO observacion_items (observacion_id,titulo,descripcion,icono,orden) VALUES(?,?,?,?,?)")
            ->execute([$id,$titulo2,$desc2,$icono2,$orden2]);
        header("Location: editar.php?id={$id}&msgitem=added"); exit;
    }
    elseif ($action === 'delete_item') {
        $itemId = intval($_POST['item_id']??0);
        $pdo->prepare("DELETE FROM observacion_items WHERE id=? AND observacion_id=?")->execute([$itemId,$id]);
        header("Location: editar.php?id={$id}&msgitem=deleted"); exit;
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Observaciones</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar: <?=htmlspecialchars($obs['titulo'])?></h1>

<?php if(isset($_GET['msg']) && $_GET['msg']==='editado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Observación actualizada.</div>
<?php endif; ?>
<?php if($msgItem==='added'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Ítem agregado.</div>
<?php elseif($msgItem==='deleted'): ?><div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Ítem eliminado.</div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="adm-card">
      <h2 class="adm-card-title">Datos generales</h2>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?=$csrf?>">
        <input type="hidden" name="action" value="save_obs">
        <div class="mb-3"><label class="form-label" for="titulo">Título</label>
          <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($obs['titulo'])?>" required></div>
        <div class="mb-3"><label class="form-label" for="descripcion_intro">Descripción introductoria</label>
          <textarea class="form-control" id="descripcion_intro" name="descripcion_intro" rows="4"><?=htmlspecialchars($obs['descripcion_intro']??'')?></textarea></div>
        <div class="mb-3"><label class="form-label" for="recomendaciones">Recomendaciones al pie</label>
          <textarea class="form-control" id="recomendaciones" name="recomendaciones" rows="4"><?=htmlspecialchars($obs['recomendaciones']??'')?></textarea></div>
        <div class="mb-3"><label class="form-label" for="icono">Clase Bootstrap Icons</label>
          <div class="input-group">
            <span class="input-group-text" style="background:var(--adm-dark);border:1px solid var(--adm-border);border-right:none">
              <i id="iconPrev" class="<?=htmlspecialchars($obs['icono'])?>"></i>
            </span>
            <input type="text" class="form-control" id="icono" name="icono" value="<?=htmlspecialchars($obs['icono'])?>"
                   oninput="document.getElementById('iconPrev').className=this.value">
          </div>
        </div>
        <div class="row g-2 mb-3">
          <div class="col-6"><label class="form-label" for="orden">Orden</label>
            <input type="number" class="form-control" id="orden" name="orden" value="<?=$obs['orden']?>" min="0"></div>
          <div class="col-6 d-flex align-items-end">
            <div class="form-check pb-2"><input class="form-check-input" type="checkbox" id="activo" name="activo" <?=$obs['activo']?'checked':''?>>
              <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activa</label></div>
          </div>
        </div>
        <button type="submit" class="btn-adm-primary btn w-100"><i class="bi bi-save me-1"></i> Guardar</button>
      </form>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="adm-card mb-3">
      <h2 class="adm-card-title">Ítems de contenido</h2>
      <?php if($items): ?>
        <?php foreach($items as $item): ?>
          <div class="d-flex align-items-start justify-content-between gap-2 mb-2 pb-2" style="border-bottom:1px solid var(--adm-border)">
            <div>
              <?php if($item['icono']): ?><i class="<?=htmlspecialchars($item['icono'])?> me-1" style="color:var(--adm-accent)"></i><?php endif; ?>
              <span style="font-weight:600;font-size:.9rem"><?=htmlspecialchars($item['titulo']??'')?></span>
              <?php if($item['descripcion']): ?>
                <div style="font-size:.8rem;color:var(--adm-muted)"><?=htmlspecialchars(mb_substr($item['descripcion'],0,80))?></div>
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
        <div class="mb-3"><label class="form-label" for="item_titulo">Título</label>
          <input type="text" class="form-control" id="item_titulo" name="item_titulo" required placeholder="Ej: Telescopios disponibles"></div>
        <div class="mb-3"><label class="form-label" for="item_icono">Ícono (opcional)</label>
          <input type="text" class="form-control" id="item_icono" name="item_icono" placeholder="bi bi-telescope"></div>
        <div class="mb-3"><label class="form-label" for="item_desc">Descripción</label>
          <textarea class="form-control" id="item_desc" name="item_desc" rows="3"></textarea></div>
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Agregar ítem</button>
      </form>
    </div>
  </div>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
