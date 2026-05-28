<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Nueva convocatoria';
$basePath  = '../../';
$pdo       = get_pdo();
$errors    = [];
$vals = ['titulo'=>'','resumen'=>'','contenido'=>'','fecha_publicacion'=>date('Y-m-d'),
         'fecha_apertura'=>'','fecha_cierre'=>'','url_externa'=>'','estado'=>'borrador'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $titulo    = sanitize_text($_POST['titulo']??'', 255);
    $resumen   = trim($_POST['resumen']??'');
    $contenido = trim($_POST['contenido']??'');
    $fechaPub  = $_POST['fecha_publicacion'] ?? date('Y-m-d');
    $fechaAp   = $_POST['fecha_apertura'] ?: null;
    $fechaCi   = $_POST['fecha_cierre'] ?: null;
    $urlExt    = sanitize_text($_POST['url_externa']??'', 1000);
    $estado    = in_array($_POST['estado']??'', ['borrador','publicada','cerrada','archivada']) ? $_POST['estado'] : 'borrador';

    if (!$titulo) $errors[] = 'El título es obligatorio.';

    $imagen = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) $errors[] = 'Formato imagen no permitido.';
        elseif ($_FILES['imagen']['size'] > 5*1024*1024) $errors[] = 'Imagen supera 5 MB.';
    }
    $pdf = null;
    if (!empty($_FILES['pdf']['tmp_name'])) {
        if (strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION)) !== 'pdf') $errors[] = 'El archivo debe ser PDF.';
        elseif ($_FILES['pdf']['size'] > 20*1024*1024) $errors[] = 'PDF supera 20 MB.';
    }

    if (!$errors) {
        $slug = trim(strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT',$titulo))),'-') . '-' . date('Ymd');

        if (!empty($_FILES['imagen']['tmp_name'])) {
            $dir = __DIR__.'/../../assets/img/convocatorias/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $imagen = $slug . '.' . $ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $dir.$imagen);
        }
        if (!empty($_FILES['pdf']['tmp_name'])) {
            $dir = __DIR__.'/../../assets/pdf/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $pdf = $slug . '.pdf';
            move_uploaded_file($_FILES['pdf']['tmp_name'], $dir.$pdf);
        }

        $pdo->prepare(
            "INSERT INTO convocatorias (slug,titulo,resumen,contenido,imagen,pdf,url_externa,fecha_publicacion,fecha_apertura,fecha_cierre,estado)
             VALUES(?,?,?,?,?,?,?,?,?,?,?)"
        )->execute([$slug,$titulo,$resumen,$contenido,$imagen,$pdf,$urlExt,$fechaPub,$fechaAp,$fechaCi,$estado]);
        header('Location: index.php?msg=creado'); exit;
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Convocatorias</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Nueva</span>
</div>
<h1 class="h4 fw-bold mb-4">Nueva convocatoria</h1>

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
          <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($vals['titulo'])?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label" for="resumen">Resumen</label>
          <textarea class="form-control" id="resumen" name="resumen" rows="3"><?=htmlspecialchars($vals['resumen'])?></textarea>
        </div>
        <div>
          <label class="form-label" for="contenido">Bases y requisitos</label>
          <textarea class="form-control" id="contenido" name="contenido" rows="10" placeholder="Detalla los requisitos, bases y condiciones..."><?=htmlspecialchars($vals['contenido'])?></textarea>
        </div>
      </div>
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Archivos</h2>
        <div class="mb-3">
          <label class="form-label">Imagen de portada</label>
          <input type="file" class="form-control" name="imagen" accept="image/jpeg,image/png,image/webp">
          <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 5 MB</small>
        </div>
        <div>
          <label class="form-label">PDF de bases (opcional)</label>
          <input type="file" class="form-control" name="pdf" accept="application/pdf">
          <small style="color:var(--adm-muted);font-size:.78rem">PDF · máx. 20 MB</small>
        </div>
      </div>
      <div class="adm-card">
        <label class="form-label" for="url_externa">Enlace externo (formulario, etc.)</label>
        <input type="url" class="form-control" id="url_externa" name="url_externa" value="<?=htmlspecialchars($vals['url_externa'])?>" placeholder="https://forms.google.com/...">
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Estado y fechas</h2>
        <div class="mb-3">
          <label class="form-label" for="estado">Estado</label>
          <select class="form-select" id="estado" name="estado">
            <option value="borrador" selected>Borrador</option>
            <option value="publicada">Publicada</option>
            <option value="cerrada">Cerrada</option>
            <option value="archivada">Archivada</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="fecha_publicacion">Fecha publicación</label>
          <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion" value="<?=$vals['fecha_publicacion']?>">
        </div>
        <div class="mb-3">
          <label class="form-label" for="fecha_apertura">Apertura de postulaciones</label>
          <input type="date" class="form-control" id="fecha_apertura" name="fecha_apertura">
        </div>
        <div>
          <label class="form-label" for="fecha_cierre">Fecha de cierre</label>
          <input type="date" class="form-control" id="fecha_cierre" name="fecha_cierre">
        </div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Guardar</button>
        <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
