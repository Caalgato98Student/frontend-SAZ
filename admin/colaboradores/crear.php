<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Nuevo colaborador';
$basePath  = '../../';
$pdo       = get_pdo();
$errors    = [];
$vals = ['nombre'=>'','profesion'=>'','red_nombre'=>'','url_red'=>'','orden'=>0,'activo'=>1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $nombre    = sanitize_text($_POST['nombre']??'', 255);
    $profesion = sanitize_text($_POST['profesion']??'', 255);
    $redNombre = sanitize_text($_POST['red_nombre']??'', 100);
    $urlRed    = sanitize_text($_POST['url_red']??'', 500);
    $orden     = intval($_POST['orden'] ?? 0);
    $activo    = isset($_POST['activo']) ? 1 : 0;

    if (!$nombre) $errors[] = 'El nombre es obligatorio.';

    $imagen = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) $errors[] = 'Formato imagen no permitido.';
        elseif ($_FILES['imagen']['size'] > 3*1024*1024) $errors[] = 'Imagen supera 3 MB.';
    }

    if (!$errors) {
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $slug = trim(strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT',$nombre))),'-').'-'.time();
            $dir  = __DIR__.'/../../assets/img/colaboradores/';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $imagen = $slug.'.'.$ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $dir.$imagen);
        }
        $pdo->prepare("INSERT INTO colaboradores (nombre,profesion,imagen,activo,orden) VALUES(?,?,?,?,?)")
            ->execute([$nombre,$profesion,$imagen,$activo,$orden]);
        $nuevoId = $pdo->lastInsertId();

        if (!empty($redNombre) && !empty($urlRed)) {
            $pdo->prepare("INSERT INTO colaborador_redes (colaborador_id, nombre, url, orden) VALUES (?,?,?,0)")
                ->execute([$nuevoId, $redNombre, $urlRed]);
        }
        header('Location: index.php?msg=creado'); exit;
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Colaboradores</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Nuevo</span>
</div>
<h1 class="h4 fw-bold mb-4">Nuevo colaborador</h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label" for="nombre">Nombre completo *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?=htmlspecialchars($vals['nombre'])?>" required placeholder="Dr. Juan García">
          </div>
          <div class="col-12">
            <label class="form-label" for="profesion">Profesión / Descripción</label>
            <input type="text" class="form-control" id="profesion" name="profesion" value="<?=htmlspecialchars($vals['profesion'])?>" placeholder="Astrónomo observacional">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="red_nombre">Red social</label>
            <input type="text" class="form-control" id="red_nombre" name="red_nombre" value="<?=htmlspecialchars($vals['red_nombre'])?>" placeholder="ResearchGate">
          </div>
          <div class="col-md-8">
            <label class="form-label" for="url_red">URL del perfil</label>
            <input type="url" class="form-control" id="url_red" name="url_red" value="<?=htmlspecialchars($vals['url_red'])?>" placeholder="https://...">
          </div>
        </div>
      </div>
      <div class="adm-card">
        <h2 class="adm-card-title">Foto de perfil</h2>
        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 3 MB</small>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <div class="mb-3">
          <label class="form-label" for="orden">Orden de aparición</label>
          <input type="number" class="form-control" id="orden" name="orden" value="<?=$vals['orden']?>" min="0">
          <small style="color:var(--adm-muted);font-size:.78rem">Menor número = aparece primero</small>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
          <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activo (visible en el sitio)</label>
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
