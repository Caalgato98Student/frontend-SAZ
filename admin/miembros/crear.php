<?php
/**
 * admin/miembros/crear.php — Crear nuevo miembro.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Nuevo miembro';
$basePath  = '../../';
$pdo       = get_pdo();
$errors    = [];
$vals      = ['nombre'=>'','especialidad'=>'','cargo'=>'','correo'=>'','ubicacion'=>'',
               'distincion'=>'','generalidades'=>'','activo'=>1,'orden'=>0,
               'en_mesa_directiva'=>0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $nombre     = sanitize_text($_POST['nombre'] ?? '', 255);
    $especialidad = sanitize_text($_POST['especialidad'] ?? '', 255);
    $cargo      = sanitize_text($_POST['cargo'] ?? '', 255);
    $correo     = sanitize_text($_POST['correo'] ?? '', 255);
    $ubicacion  = sanitize_text($_POST['ubicacion'] ?? '', 255);
    $distincion = sanitize_text($_POST['distincion'] ?? '', 255);
    $generalidades = trim($_POST['generalidades'] ?? '');
    $activo     = isset($_POST['activo']) ? 1 : 0;
    $mesa       = isset($_POST['en_mesa_directiva']) ? 1 : 0;
    $orden      = intval($_POST['orden'] ?? 0);

    if (!$nombre) $errors[] = 'El nombre es obligatorio.';

    if (!$errors) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT', $nombre)));
        $slug = trim($slug, '-');

        // Imagen
        $imagen = null;
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $ext   = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Formato de imagen no permitido.';
            } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
                $errors[] = 'La imagen supera el límite de 5 MB.';
            } else {
                $dir = __DIR__ . '/../../assets/img/miembros/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $imagen = $slug . '.' . $ext;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $imagen);
            }
        }

        if (!$errors) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO miembros
                     (slug,nombre,especialidad,cargo,correo,ubicacion,distincion,imagen,generalidades,activo,en_mesa_directiva,orden)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
                );
                $stmt->execute([$slug,$nombre,$especialidad,$cargo,$correo,$ubicacion,$distincion,$imagen,$generalidades,$activo,$mesa,$orden]);
                $nuevoId = $pdo->lastInsertId();

                // Formación (líneas separadas por \n)
                $formacion = array_filter(array_map('trim', explode("\n", $_POST['formacion'] ?? '')));
                foreach ($formacion as $item) {
                    $pdo->prepare("INSERT INTO miembro_formacion (miembro_id,descripcion) VALUES (?,?)")->execute([$nuevoId, $item]);
                }
                // Divulgación
                $divulgacion = array_filter(array_map('trim', explode("\n", $_POST['divulgacion'] ?? '')));
                foreach ($divulgacion as $item) {
                    $pdo->prepare("INSERT INTO miembro_divulgacion (miembro_id,descripcion) VALUES (?,?)")->execute([$nuevoId, $item]);
                }

                header('Location: index.php?msg=creado'); exit;
            } catch (\PDOException $e) {
                $errors[] = 'Error al guardar: ' . $e->getMessage();
            }
        }
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Miembros</a>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem">Nuevo</span>
</div>
<h1 class="h4 fw-bold mb-4">Nuevo miembro</h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Datos personales</h2>
        <div class="row g-3">
          <div class="col-md-6 mb-3">
            <label class="form-label" for="nombre">Nombre completo *</label>
            <input type="text" class="form-control" id="nombre" name="nombre"
                   value="<?=htmlspecialchars($vals['nombre'])?>" required placeholder="Ej: Dr. Juan Pérez">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="especialidad">Especialidad</label>
            <input type="text" class="form-control" id="especialidad" name="especialidad"
                   value="<?=htmlspecialchars($vals['especialidad'])?>" placeholder="Ej: Astrofísica teórica">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="cargo">Cargo en la SAZ</label>
            <input type="text" class="form-control" id="cargo" name="cargo"
                   value="<?=htmlspecialchars($vals['cargo'])?>" placeholder="Ej: Presidente">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="correo">Correo electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo"
                   value="<?=htmlspecialchars($vals['correo'])?>" placeholder="correo@ejemplo.com">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="ubicacion">Ubicación / Institución</label>
            <input type="text" class="form-control" id="ubicacion" name="ubicacion"
                   value="<?=htmlspecialchars($vals['ubicacion'])?>" placeholder="Ej: UAZ, Zacatecas">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label" for="distincion">Distinción</label>
            <input type="text" class="form-control" id="distincion" name="distincion"
                   value="<?=htmlspecialchars($vals['distincion'])?>" placeholder="Ej: Doctor en Astrofísica">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="generalidades">Generalidades
            <small style="color:var(--adm-muted)">(HTML básico permitido: &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;)</small>
          </label>
          <textarea class="form-control" id="generalidades" name="generalidades" rows="5"
                    placeholder="Descripción general del miembro..."><?=htmlspecialchars($vals['generalidades'])?></textarea>
        </div>
      </div>

      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Formación académica</h2>
        <p style="font-size:.83rem;color:var(--adm-muted)">Escribe un ítem por línea. Ej: <em>Doctorado en Astrofísica, UAZ, 2010</em></p>
        <textarea class="form-control" name="formacion" rows="4" placeholder="Doctorado en Astrofísica, UAZ, 2010&#10;Maestría en Física, UNAM, 2006"></textarea>
      </div>

      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Divulgación científica</h2>
        <p style="font-size:.83rem;color:var(--adm-muted)">Escribe un ítem por línea.</p>
        <textarea class="form-control" name="divulgacion" rows="4" placeholder="Conferencia: El universo en expansión, 2023&#10;Artículo en Astronomía Magazine, 2022"></textarea>
      </div>

      <div class="adm-card">
        <h2 class="adm-card-title">Fotografía</h2>
        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 5 MB</small>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Configuración</h2>
        <div class="mb-3">
          <label class="form-label" for="orden">Orden de aparición</label>
          <input type="number" class="form-control" id="orden" name="orden" value="0" min="0">
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
          <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Miembro activo</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="en_mesa_directiva" name="en_mesa_directiva">
          <label class="form-check-label" for="en_mesa_directiva" style="color:var(--adm-text);font-size:.875rem">
            En mesa directiva <i class="bi bi-star-fill" style="color:#f59e0b;font-size:.8rem"></i>
          </label>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn">
          <i class="bi bi-save me-1"></i> Guardar miembro
        </button>
        <a href="index.php" class="btn"
           style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">
          Cancelar
        </a>
      </div>
    </div>
  </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/../base_admin.php';