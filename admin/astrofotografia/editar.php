<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/astrofotografia.php';

$pdo = get_pdo();
$id  = intval($_GET['id'] ?? 0);
$foto = $pdo->prepare("SELECT * FROM astrofotografia WHERE id = ? LIMIT 1");
$foto->execute([$id]);
$foto = $foto->fetch();
if (!$foto) { header('Location: index.php'); exit; }

$pageTitle = 'Editar fotografía';
$basePath  = '../../';
$errors    = [];

$categorias = get_astrofoto_categorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $fotografo = sanitize_text($_POST['fotografo'] ?? '', 150);
    $titulo    = sanitize_text($_POST['titulo'] ?? '', 255);
    $fecha     = $_POST['fecha'] ?? '';
    $catId     = intval($_POST['categoria_id'] ?? 0);
    $visible   = isset($_POST['visible']) ? 1 : 0;
    $destacada = isset($_POST['destacada']) ? 1 : 0;

    if (!$fotografo) $errors[] = 'El fotógrafo es obligatorio.';
    if (!$fecha)     $errors[] = 'La fecha es obligatoria.';

    // Validar categoría
    $validCat = false;
    foreach ($categorias as $c) {
        if ($c['id'] == $catId) {
            $validCat = true;
            break;
        }
    }
    if (!$validCat) $errors[] = 'La categoría seleccionada no es válida.';

    $imagen = $foto['imagen'];
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $errors[] = 'Formato no permitido.'; }
        elseif ($_FILES['imagen']['size'] > 10*1024*1024) { $errors[] = 'Imagen supera 10 MB.'; }
        else {
            $dir = __DIR__ . '/../../assets/img/astrofotografia/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $imagen = $foto['slug'] . '.' . $ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $imagen);
        }
    }
    if (isset($_POST['eliminar_imagen']) && $foto['imagen']) {
        @unlink(__DIR__ . '/../../assets/img/astrofotografia/' . $foto['imagen']);
        $imagen = null;
    }

    if (!$errors) {
        $pdo->prepare(
            "UPDATE astrofotografia SET titulo=?,fotografo=?,lugar=?,fecha=?,descripcion=?,coordenadas=?,imagen=?,
             categoria_id=?,telescopio=?,montura=?,camara=?,integracion=?,iso_gain=?,filtros=?,
             post_procesamiento=?,visible=?,destacada=? WHERE id=?"
        )->execute([
            $titulo, $fotografo,
            sanitize_text($_POST['lugar']??'',255), $fecha,
            trim($_POST['descripcion']??''),
            sanitize_text($_POST['coordenadas']??'',255),
            $imagen, $catId,
            sanitize_text($_POST['telescopio']??'',255),
            sanitize_text($_POST['montura']??'',255),
            sanitize_text($_POST['camara']??'',255),
            sanitize_text($_POST['integracion']??'',255),
            sanitize_text($_POST['iso_gain']??'',100),
            sanitize_text($_POST['filtros']??'',255),
            sanitize_text($_POST['post_procesamiento']??'',500),
            $visible, $destacada, $id
        ]);
        header('Location: index.php?msg=editado'); exit;
    }
    $foto = array_merge($foto, [
        'titulo' => $titulo,
        'fotografo' => $fotografo,
        'lugar' => sanitize_text($_POST['lugar']??'',255),
        'fecha' => $fecha,
        'descripcion' => trim($_POST['descripcion']??''),
        'coordenadas' => sanitize_text($_POST['coordenadas']??'',255),
        'categoria_id' => $catId,
        'telescopio' => sanitize_text($_POST['telescopio']??'',255),
        'montura' => sanitize_text($_POST['montura']??'',255),
        'camara' => sanitize_text($_POST['camara']??'',255),
        'integracion' => sanitize_text($_POST['integracion']??'',255),
        'iso_gain' => sanitize_text($_POST['iso_gain']??'',100),
        'filtros' => sanitize_text($_POST['filtros']??'',255),
        'post_procesamiento' => sanitize_text($_POST['post_procesamiento']??'',500),
        'visible' => $visible,
        'destacada' => $destacada,
        'imagen' => $imagen
    ]);
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Astrofotografía</a>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar fotografía</h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Datos básicos</h2>
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label" for="titulo">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($foto['titulo']??'')?>">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="categoria_id">Categoría *</label>
            <select class="form-select" id="categoria_id" name="categoria_id">
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?=$foto['categoria_id'] == $cat['id'] ? 'selected' : ''?>><?=htmlspecialchars($cat['nombre'])?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="fotografo">Fotógrafo *</label>
            <input type="text" class="form-control" id="fotografo" name="fotografo" value="<?=htmlspecialchars($foto['fotografo'])?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="fecha">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" value="<?=htmlspecialchars($foto['fecha'])?>">
          </div>
          <div class="col-md-3">
            <label class="form-label" for="lugar">Lugar</label>
            <input type="text" class="form-control" id="lugar" name="lugar" value="<?=htmlspecialchars($foto['lugar']??'')?>">
          </div>
          <div class="col-12">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?=htmlspecialchars($foto['descripcion']??'')?></textarea>
          </div>
        </div>
      </div>

      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Ficha técnica</h2>
        <div class="row g-3">
          <?php
          $campos = ['telescopio'=>'Telescopio','montura'=>'Montura','camara'=>'Cámara',
                     'integracion'=>'Integración total','iso_gain'=>'ISO / Ganancia',
                     'filtros'=>'Filtros','coordenadas'=>'Coordenadas (RA/Dec)'];
          foreach($campos as $k=>$l): ?>
            <div class="col-md-6">
              <label class="form-label" for="<?=$k?>"><?=$l?></label>
              <input type="text" class="form-control" id="<?=$k?>" name="<?=$k?>" value="<?=htmlspecialchars($foto[$k]??'')?>">
            </div>
          <?php endforeach; ?>
          <div class="col-12">
            <label class="form-label" for="post_procesamiento">Post-procesamiento</label>
            <input type="text" class="form-control" id="post_procesamiento" name="post_procesamiento" value="<?=htmlspecialchars($foto['post_procesamiento']??'')?>">
          </div>
        </div>
      </div>

      <div class="adm-card">
        <h2 class="adm-card-title">Imagen</h2>
        <?php if (!empty($foto['imagen'])): ?>
          <img src="../../assets/img/astrofotografia/<?=htmlspecialchars($foto['imagen'])?>"
               style="max-height:180px;max-width:100%;border-radius:8px;margin-bottom:.75rem">
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="eliminar_imagen" name="eliminar_imagen">
            <label class="form-check-label" for="eliminar_imagen" style="color:#f87171;font-size:.875rem">Eliminar imagen actual</label>
          </div>
        <?php endif; ?>
        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 10 MB</small>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Visibilidad</h2>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="visible" name="visible" <?=$foto['visible']?'checked':''?>>
          <label class="form-check-label" for="visible" style="color:var(--adm-text);font-size:.875rem">Visible en galería</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="destacada" name="destacada" <?=$foto['destacada']?'checked':''?>>
          <label class="form-check-label" for="destacada" style="color:var(--adm-text);font-size:.875rem">Destacada en home</label>
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Guardar cambios</button>
        <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
