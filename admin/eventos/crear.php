<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Nuevo evento';
$basePath  = '../../';
$pdo       = get_pdo();
$errors    = [];
$vals = ['titulo'=>'','descripcion'=>'','orden'=>0,'activo'=>1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $titulo      = sanitize_text($_POST['titulo']??'', 255);
    $descripcion = trim($_POST['descripcion']??'');
    $orden       = intval($_POST['orden']??0);
    $activo      = isset($_POST['activo']) ? 1 : 0;
    if (!$titulo) $errors[] = 'El título es obligatorio.';

    $imagen = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext,['jpg','jpeg','png','webp'])) $errors[]='Formato imagen no permitido.';
        elseif ($_FILES['imagen']['size']>5*1024*1024) $errors[]='Imagen supera 5 MB.';
    }

    if (!$errors) {
        $slug = trim(strtolower(preg_replace('/[^a-z0-9]+/','-',iconv('UTF-8','ASCII//TRANSLIT',$titulo))),'-');
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $dir=__DIR__.'/../../assets/img/eventos/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $imagen=$slug.'.'.$ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'],$dir.$imagen);
        }
        try {
            $pdo->prepare("INSERT INTO eventos (slug,titulo,descripcion,imagen_principal,activo,orden) VALUES(?,?,?,?,?,?)")
                ->execute([$slug,$titulo,$descripcion,$imagen,$activo,$orden]);
            header('Location: index.php?msg=creado'); exit;
        } catch (\PDOException $e) {
            $errors[] = 'El slug ya existe o hubo un error: '.$e->getMessage();
        }
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Eventos</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Nuevo</span>
</div>
<h1 class="h4 fw-bold mb-4">Nuevo evento</h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <div class="mb-3">
          <label class="form-label" for="titulo">Nombre del evento *</label>
          <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($vals['titulo'])?>" required placeholder="Semana de Astronomía">
        </div>
        <div>
          <label class="form-label" for="descripcion">Descripción general</label>
          <textarea class="form-control" id="descripcion" name="descripcion" rows="8"
                    placeholder="Descripción del programa, qué es, objetivos..."><?=htmlspecialchars($vals['descripcion'])?></textarea>
        </div>
      </div>
      <div class="adm-card">
        <h2 class="adm-card-title">Imagen principal</h2>
        <input type="file" class="form-control" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 5 MB</small>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <div class="mb-3"><label class="form-label" for="orden">Orden de aparición</label>
          <input type="number" class="form-control" id="orden" name="orden" value="<?=$vals['orden']?>" min="0"></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
          <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activo (visible)</label></div>
      </div>
      <div class="adm-card mb-3" style="font-size:.82rem;color:var(--adm-muted)">
        <i class="bi bi-info-circle me-1"></i>
        Después de crear el evento, podrás agregar <strong style="color:var(--adm-text)">ediciones anuales</strong> (Semana 2024, Semana 2025, etc.) desde la sección de ediciones.
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Guardar</button>
        <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
