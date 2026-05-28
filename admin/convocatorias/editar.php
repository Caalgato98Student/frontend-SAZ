<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo  = get_pdo();
$id   = intval($_GET['id'] ?? 0);
$item = $pdo->prepare("SELECT * FROM convocatorias WHERE id=? LIMIT 1");
$item->execute([$id]);
$item = $item->fetch();
if (!$item) { header('Location: index.php'); exit; }

$pageTitle = 'Editar convocatoria';
$basePath  = '../../';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $titulo   = sanitize_text($_POST['titulo']??'', 255);
    $resumen  = trim($_POST['resumen']??'');
    $cont     = trim($_POST['contenido']??'');
    $fechaPub = $_POST['fecha_publicacion'] ?? $item['fecha_publicacion'];
    $fechaAp  = $_POST['fecha_apertura'] ?: null;
    $fechaCi  = $_POST['fecha_cierre'] ?: null;
    $urlExt   = sanitize_text($_POST['url_externa']??'', 1000);
    $estado   = in_array($_POST['estado']??'', ['borrador','publicada','cerrada','archivada']) ? $_POST['estado'] : $item['estado'];

    if (!$titulo) $errors[] = 'El título es obligatorio.';

    $imagen = $item['imagen'];
    $pdf    = $item['pdf'];

    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $dir = __DIR__.'/../../assets/img/convocatorias/';
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $imagen = $item['slug'].'.'.$ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $dir.$imagen);
    }
    if (isset($_POST['eliminar_imagen']) && $imagen) { @unlink(__DIR__.'/../../assets/img/convocatorias/'.$imagen); $imagen=null; }

    if (!empty($_FILES['pdf']['tmp_name'])) {
        $dir = __DIR__.'/../../assets/pdf/';
        if (!is_dir($dir)) mkdir($dir,0755,true);
        $pdf = $item['slug'].'.pdf';
        move_uploaded_file($_FILES['pdf']['tmp_name'], $dir.$pdf);
    }
    if (isset($_POST['eliminar_pdf']) && $pdf) { @unlink(__DIR__.'/../../assets/pdf/'.$pdf); $pdf=null; }

    if (!$errors) {
        $pdo->prepare(
            "UPDATE convocatorias SET titulo=?,resumen=?,contenido=?,imagen=?,pdf=?,url_externa=?,
             fecha_publicacion=?,fecha_apertura=?,fecha_cierre=?,estado=? WHERE id=?"
        )->execute([$titulo,$resumen,$cont,$imagen,$pdf,$urlExt,$fechaPub,$fechaAp,$fechaCi,$estado,$id]);
        header('Location: index.php?msg=editado'); exit;
    }
    $item = array_merge($item,compact('titulo','resumen','estado'));
    $item['imagen']=$imagen; $item['pdf']=$pdf;
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Convocatorias</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar convocatoria</h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <div class="mb-3">
          <label class="form-label" for="titulo">Título *</label>
          <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($item['titulo'])?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="resumen">Resumen</label>
          <textarea class="form-control" id="resumen" name="resumen" rows="3"><?=htmlspecialchars($item['resumen']??'')?></textarea>
        </div>
        <div>
          <label class="form-label" for="contenido">Bases y requisitos</label>
          <textarea class="form-control" id="contenido" name="contenido" rows="10"><?=htmlspecialchars($item['contenido']??'')?></textarea>
        </div>
      </div>
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Archivos</h2>
        <div class="mb-3">
          <label class="form-label">Imagen de portada</label>
          <?php if($item['imagen']): ?>
            <div class="mb-2"><img src="../../assets/img/convocatorias/<?=htmlspecialchars($item['imagen'])?>" style="max-height:120px;border-radius:6px">
              <div class="form-check mt-1"><input class="form-check-input" type="checkbox" id="eli" name="eliminar_imagen"><label class="form-check-label" for="eli" style="color:#f87171;font-size:.875rem">Eliminar imagen</label></div>
            </div>
          <?php endif; ?>
          <input type="file" class="form-control" name="imagen" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="mb-3">
          <label class="form-label">PDF de bases</label>
          <?php if($item['pdf']): ?>
            <div class="mb-2 d-flex align-items-center gap-2">
              <i class="bi bi-file-pdf-fill" style="color:#f87171"></i>
              <span style="font-size:.85rem;color:var(--adm-muted)"><?=htmlspecialchars($item['pdf'])?></span>
              <div class="form-check mb-0"><input class="form-check-input" type="checkbox" id="elipdf" name="eliminar_pdf"><label class="form-check-label" for="elipdf" style="color:#f87171;font-size:.875rem">Eliminar</label></div>
            </div>
          <?php endif; ?>
          <input type="file" class="form-control" name="pdf" accept="application/pdf">
        </div>
        <div>
          <label class="form-label" for="url_externa">Enlace externo</label>
          <input type="url" class="form-control" id="url_externa" name="url_externa" value="<?=htmlspecialchars($item['url_externa']??'')?>">
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Estado y fechas</h2>
        <div class="mb-3">
          <label class="form-label" for="estado">Estado</label>
          <select class="form-select" id="estado" name="estado">
            <?php foreach(['borrador','publicada','cerrada','archivada'] as $s): ?>
              <option value="<?=$s?>" <?=$item['estado']===$s?'selected':''?>><?=ucfirst($s)?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="fecha_publicacion">Fecha publicación</label>
          <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion" value="<?=$item['fecha_publicacion']?>">
        </div>
        <div class="mb-3">
          <label class="form-label" for="fecha_apertura">Apertura</label>
          <input type="date" class="form-control" id="fecha_apertura" name="fecha_apertura" value="<?=$item['fecha_apertura']??''?>">
        </div>
        <div>
          <label class="form-label" for="fecha_cierre">Cierre</label>
          <input type="date" class="form-control" id="fecha_cierre" name="fecha_cierre" value="<?=$item['fecha_cierre']??''?>">
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
