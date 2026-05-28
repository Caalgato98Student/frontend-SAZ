<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo  = get_pdo();
$id   = intval($_GET['id'] ?? 0);
$item = $pdo->prepare("SELECT * FROM colaboradores WHERE id=? LIMIT 1");
$item->execute([$id]); $item=$item->fetch();
if (!$item) { header('Location: index.php'); exit; }

$pageTitle = 'Editar colaborador';
$basePath  = '../../';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $nombre    = sanitize_text($_POST['nombre']??'', 255);
    $profesion = sanitize_text($_POST['profesion']??'', 255);
    $redNombre = sanitize_text($_POST['red_nombre']??'', 100);
    $urlRed    = sanitize_text($_POST['url_red']??'', 500);
    $orden     = intval($_POST['orden'] ?? 0);
    $activo    = isset($_POST['activo']) ? 1 : 0;
    if (!$nombre) $errors[] = 'El nombre es obligatorio.';

    $imagen = $item['imagen'];
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $slug = trim(strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT',$nombre))),'-');
        $dir  = __DIR__.'/../../assets/img/colaboradores/';
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $imagen = $slug.'-'.$id.'.'.$ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir.$imagen);
    }
    if (isset($_POST['eliminar_imagen']) && $imagen) { @unlink(__DIR__.'/../../assets/img/colaboradores/'.$imagen); $imagen=null; }

    if (!$errors) {
        $pdo->prepare("UPDATE colaboradores SET nombre=?,profesion=?,red_nombre=?,url_red=?,imagen=?,activo=?,orden=? WHERE id=?")
            ->execute([$nombre,$profesion,$redNombre,$urlRed,$imagen,$activo,$orden,$id]);
        header('Location: index.php?msg=editado'); exit;
    }
    $item=array_merge($item,compact('nombre','profesion','redNombre','urlRed','orden','activo'));
    $item['imagen']=$imagen;
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Colaboradores</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar colaborador</h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <div class="row g-3">
          <div class="col-12"><label class="form-label" for="nombre">Nombre *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?=htmlspecialchars($item['nombre'])?>" required></div>
          <div class="col-12"><label class="form-label" for="profesion">Profesión</label>
            <input type="text" class="form-control" id="profesion" name="profesion" value="<?=htmlspecialchars($item['profesion']??'')?>"></div>
          <div class="col-md-4"><label class="form-label" for="red_nombre">Red social</label>
            <input type="text" class="form-control" id="red_nombre" name="red_nombre" value="<?=htmlspecialchars($item['red_nombre']??'')?>"></div>
          <div class="col-md-8"><label class="form-label" for="url_red">URL perfil</label>
            <input type="url" class="form-control" id="url_red" name="url_red" value="<?=htmlspecialchars($item['url_red']??'')?>"></div>
        </div>
      </div>
      <div class="adm-card">
        <h2 class="adm-card-title">Foto de perfil</h2>
        <?php if($item['imagen']): ?>
          <img src="../../assets/img/colaboradores/<?=htmlspecialchars($item['imagen'])?>"
               style="width:80px;height:80px;object-fit:cover;border-radius:50%;margin-bottom:.75rem">
          <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="eli" name="eliminar_imagen">
            <label class="form-check-label" for="eli" style="color:#f87171;font-size:.875rem">Eliminar foto</label></div>
        <?php endif; ?>
        <input type="file" class="form-control" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 3 MB</small>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <div class="mb-3"><label class="form-label" for="orden">Orden</label>
          <input type="number" class="form-control" id="orden" name="orden" value="<?=$item['orden']?>" min="0"></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="activo" name="activo" <?=$item['activo']?'checked':''?>>
          <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activo</label></div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Guardar cambios</button>
        <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
