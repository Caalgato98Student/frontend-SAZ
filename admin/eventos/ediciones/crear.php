<?php
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db.php';

$pdo      = get_pdo();
$eventoId = intval($_GET['evento_id'] ?? 0);
$evento   = $pdo->prepare("SELECT id,titulo FROM eventos WHERE id=? LIMIT 1");
$evento->execute([$eventoId]); $evento=$evento->fetch();
if (!$evento) { header('Location: ../index.php'); exit; }

$pageTitle = 'Nueva edición';
$basePath  = '../../../';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $anio       = intval($_POST['anio'] ?? date('Y'));
    $lugar      = sanitize_text($_POST['lugar']??'', 255);
    $resumen    = trim($_POST['resumen']??'');
    $fInicio    = $_POST['fecha_inicio'] ?: null;
    $fFin       = $_POST['fecha_fin'] ?: null;

    if ($anio < 1990 || $anio > 2100) $errors[] = 'Año no válido.';

    $imagen = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext=strtolower(pathinfo($_FILES['imagen']['name'],PATHINFO_EXTENSION));
        if (!in_array($ext,['jpg','jpeg','png','webp'])) $errors[]='Formato imagen no permitido.';
        elseif ($_FILES['imagen']['size']>5*1024*1024) $errors[]='Imagen supera 5 MB.';
    }
    $pdf = null;
    if (!empty($_FILES['pdf']['tmp_name'])) {
        if (strtolower(pathinfo($_FILES['pdf']['name'],PATHINFO_EXTENSION))!=='pdf') $errors[]='Archivo debe ser PDF.';
    }

    if (!$errors) {
        $slug = $evento['titulo'] . '-' . $anio;
        $slug = trim(strtolower(preg_replace('/[^a-z0-9]+/','-',iconv('UTF-8','ASCII//TRANSLIT',$slug))),'-');

        if (!empty($_FILES['imagen']['tmp_name'])) {
            $dir=__DIR__.'/../../../assets/img/eventos/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $imagen=$slug.'.'.$ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'],$dir.$imagen);
        }
        if (!empty($_FILES['pdf']['tmp_name'])) {
            $dir=__DIR__.'/../../../assets/pdf/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $pdf=$slug.'.pdf';
            move_uploaded_file($_FILES['pdf']['tmp_name'],$dir.$pdf);
        }
        try {
            $pdo->prepare("INSERT INTO evento_ediciones (evento_id,anio,fecha_inicio,fecha_fin,lugar,resumen,imagen,pdf) VALUES(?,?,?,?,?,?,?,?)")
                ->execute([$eventoId,$anio,$fInicio,$fFin,$lugar,$resumen,$imagen,$pdf]);
            header("Location: index.php?evento_id={$eventoId}&msg=creado"); exit;
        } catch(\PDOException $e) {
            $errors[] = 'Ya existe una edición para el año '.$anio.' en este evento.';
        }
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="../index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Eventos</a>
  <span style="color:var(--adm-border)">/</span>
  <a href="index.php?evento_id=<?=$eventoId?>" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><?=htmlspecialchars($evento['titulo'])?></a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Nueva edición</span>
</div>
<h1 class="h4 fw-bold mb-4">Nueva edición — <?=htmlspecialchars($evento['titulo'])?></h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <div class="row g-3">
          <div class="col-md-3"><label class="form-label" for="anio">Año *</label>
            <input type="number" class="form-control" id="anio" name="anio" value="<?=date('Y')?>" min="1990" max="2100" required></div>
          <div class="col-md-9"><label class="form-label" for="lugar">Lugar / Sede</label>
            <input type="text" class="form-control" id="lugar" name="lugar" placeholder="Zacatecas, Zac."></div>
          <div class="col-md-6"><label class="form-label" for="fecha_inicio">Fecha inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"></div>
          <div class="col-md-6"><label class="form-label" for="fecha_fin">Fecha fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"></div>
          <div class="col-12"><label class="form-label" for="resumen">Descripción de esta edición</label>
            <textarea class="form-control tinymce-editor" id="resumen" name="resumen" rows="10"></textarea></div>
        </div>
      </div>
      <div class="adm-card">
        <h2 class="adm-card-title">Archivos</h2>
        <div class="mb-3"><label class="form-label">Imagen</label>
          <input type="file" class="form-control" name="imagen" accept="image/jpeg,image/png,image/webp"></div>
        <div><label class="form-label">PDF (memorias / programa)</label>
          <input type="file" class="form-control" name="pdf" accept="application/pdf"></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="d-grid gap-2 mt-2">
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Guardar edición</button>
        <a href="index.php?evento_id=<?=$eventoId?>" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<?php $content=ob_get_clean(); include __DIR__.'/../../base_admin.php';
