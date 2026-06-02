<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/astrofotografia.php';

$pageTitle = 'Nueva fotografía';
$basePath  = '../../';
$pdo       = get_pdo();
$errors    = [];

$categorias = get_astrofoto_categorias();

$vals = ['titulo'=>'','fotografo'=>'','lugar'=>'','fecha'=>date('Y-m-d'),'descripcion'=>'','coordenadas'=>'',
         'categoria_id'=>'','telescopio'=>'','montura'=>'','camara'=>'',
         'integracion'=>'','iso_gain'=>'','filtros'=>'','post_procesamiento'=>'','visible'=>1,'destacada'=>0];

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

    if (!$errors) {
        // Generar slug
        $base = $titulo ?: ($fotografo . '-' . $fecha);
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT', $base)));
        $slug = trim($slug, '-') . '-' . date('YmdHis');

        // Subir imagen
        $imagen = null;
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $ext   = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $allow)) { $errors[] = 'Formato no permitido.'; }
            elseif ($_FILES['imagen']['size'] > 10 * 1024 * 1024) { $errors[] = 'Imagen supera 10 MB.'; }
            else {
                $dir = __DIR__ . '/../../assets/img/astrofotografia/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $imagen = $slug . '.' . $ext;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $imagen);
            }
        }

        if (!$errors) {
            $pdo->prepare(
                "INSERT INTO astrofotografia
                 (slug,titulo,fotografo,lugar,fecha,descripcion,coordenadas,imagen,categoria_id,
                  telescopio,montura,camara,integracion,iso_gain,filtros,post_procesamiento,visible,destacada)
                 VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $slug, $titulo, $fotografo,
                sanitize_text($_POST['lugar']??'',255),
                $fecha,
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
                $visible, $destacada
            ]);
            header('Location: index.php?msg=creado'); exit;
        }
    }

    // Re-poblar en caso de error
    $vals = [
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
        'destacada' => $destacada
    ];
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Astrofotografía</a>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem">Nueva</span>
</div>
<h1 class="h4 fw-bold mb-4">Subir fotografía</h1>

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
            <label class="form-label" for="titulo">Título (opcional)</label>
            <input type="text" class="form-control" id="titulo" name="titulo" value="<?=htmlspecialchars($vals['titulo'])?>" placeholder="Ej: Luna llena de octubre 2024">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="categoria_id">Categoría *</label>
            <select class="form-select" id="categoria_id" name="categoria_id">
              <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?=$vals['categoria_id'] == $cat['id'] ? 'selected' : ''?>><?=htmlspecialchars($cat['nombre'])?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="fotografo">Fotógrafo *</label>
            <input type="text" class="form-control" id="fotografo" name="fotografo" value="<?=htmlspecialchars($vals['fotografo'])?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="fecha">Fecha captura *</label>
            <input type="date" class="form-control" id="fecha" name="fecha" value="<?=htmlspecialchars($vals['fecha'])?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="lugar">Lugar</label>
            <input type="text" class="form-control" id="lugar" name="lugar" value="<?=htmlspecialchars($vals['lugar'])?>" placeholder="Zacatecas">
          </div>
          <div class="col-12">
            <label class="form-label" for="descripcion">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Nota del fotógrafo sobre el objeto capturado..."><?=htmlspecialchars($vals['descripcion'])?></textarea>
          </div>
        </div>
      </div>

      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Ficha técnica <small style="font-weight:400;color:var(--adm-muted)">(opcional)</small></h2>
        <div class="row g-3">
          <?php
          $campos = ['telescopio'=>'Telescopio','montura'=>'Montura','camara'=>'Cámara',
                     'integracion'=>'Integración total','iso_gain'=>'ISO / Ganancia',
                     'filtros'=>'Filtros','coordenadas'=>'Coordenadas (RA/Dec)'];
          foreach($campos as $k=>$l): ?>
            <div class="col-md-6">
              <label class="form-label" for="<?=$k?>"><?=$l?></label>
              <input type="text" class="form-control" id="<?=$k?>" name="<?=$k?>" value="<?=htmlspecialchars($vals[$k]??'')?>">
            </div>
          <?php endforeach; ?>
          <div class="col-12">
            <label class="form-label" for="post_procesamiento">Post-procesamiento</label>
            <input type="text" class="form-control" id="post_procesamiento" name="post_procesamiento"
                   value="<?=htmlspecialchars($vals['post_procesamiento'])?>" placeholder="PixInsight, Lightroom...">
          </div>
        </div>
      </div>

      <div class="adm-card">
        <h2 class="adm-card-title">Imagen *</h2>
        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 10 MB</small>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Visibilidad</h2>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="visible" name="visible" <?=$vals['visible']?'checked':''?>>
          <label class="form-check-label" for="visible" style="color:var(--adm-text);font-size:.875rem">Visible en galería</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="destacada" name="destacada" <?=$vals['destacada']?'checked':''?>>
          <label class="form-check-label" for="destacada" style="color:var(--adm-text);font-size:.875rem">Destacada en home</label>
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
