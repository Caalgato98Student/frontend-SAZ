<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo  = get_pdo();
$id   = intval($_GET['id'] ?? 0);
$ev   = $pdo->prepare("SELECT * FROM eventos WHERE id=? LIMIT 1");
$ev->execute([$id]); $ev=$ev->fetch();
if (!$ev) { header('Location: index.php'); exit; }

$pageTitle = 'Editar evento';
$basePath  = '../../';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $titulo      = sanitize_text($_POST['titulo']??'', 255);
    $descripcion = trim($_POST['descripcion']??'');
    $orden       = intval($_POST['orden']??0);
    $activo      = isset($_POST['activo']) ? 1 : 0;
    if (!$titulo) $errors[] = 'El título es obligatorio.';

    $imagen = $ev['imagen_principal'];
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext=strtolower(pathinfo($_FILES['imagen']['name'],PATHINFO_EXTENSION));
        $dir=__DIR__.'/../../assets/img/eventos/';
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $imagen=$ev['slug'].'.'.$ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'],$dir.$imagen);
    }
    if (isset($_POST['eliminar_imagen'])&&$imagen){@unlink(__DIR__.'/../../assets/img/eventos/'.$imagen);$imagen=null;}

    if (!$errors) {
        $pdo->prepare("UPDATE eventos SET titulo=?,descripcion=?,imagen_principal=?,activo=?,orden=? WHERE id=?")
            ->execute([$titulo,$descripcion,$imagen,$activo,$orden,$id]);
        header('Location: index.php?msg=editado'); exit;
    }
    $ev=array_merge($ev,compact('titulo','descripcion','orden','activo'));
    $ev['imagen_principal']=$imagen;
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Eventos</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar evento: <?=htmlspecialchars($ev['titulo'])?></h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<div class="mb-3">
  <a href="ediciones/index.php?evento_id=<?=$id?>" class="btn"
     style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#4ade80;border-radius:8px;font-size:.875rem;padding:.4rem .9rem">
    <i class="bi bi-calendar3 me-1"></i> Administrar ediciones de este evento
  </a>
</div>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <div class="mb-3"><label class="form-label" for="titulo">Nombre *</label>
          <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($ev['titulo'])?>" required></div>
        <div><label class="form-label" for="descripcion">Descripción</label>
          <textarea class="form-control" id="descripcion" name="descripcion" rows="8"><?=htmlspecialchars($ev['descripcion']??'')?></textarea></div>
      </div>
      <div class="adm-card">
        <h2 class="adm-card-title">Imagen principal</h2>
        <?php if($ev['imagen_principal']): ?>
          <img src="../../assets/img/eventos/<?=htmlspecialchars($ev['imagen_principal'])?>"
               style="max-height:140px;border-radius:8px;margin-bottom:.75rem">
          <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="eli" name="eliminar_imagen">
            <label class="form-check-label" for="eli" style="color:#f87171;font-size:.875rem">Eliminar imagen</label></div>
        <?php endif; ?>
        <input type="file" class="form-control" name="imagen" accept="image/jpeg,image/png,image/webp">
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <div class="mb-3"><label class="form-label" for="orden">Orden</label>
          <input type="number" class="form-control" id="orden" name="orden" value="<?=$ev['orden']?>" min="0"></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="activo" name="activo" <?=$ev['activo']?'checked':''?>>
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
