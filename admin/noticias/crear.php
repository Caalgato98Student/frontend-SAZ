<?php
/**
 * admin/noticias/crear.php — Formulario para crear una nueva noticia.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Nueva noticia';
$basePath  = '../../';
$pdo       = get_pdo();

// Categorías disponibles
$categorias = $pdo->query("SELECT id, nombre FROM categorias_noticias ORDER BY nombre")->fetchAll();

$errors = [];
$vals   = ['titulo'=>'','resumen'=>'','contenido'=>'','autor'=>'','fecha'=>date('Y-m-d'),
           'categoria_id'=>'','estado'=>'borrador','visible_en_principal'=>1,'fijado'=>0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    // Recoger y validar
    $titulo    = sanitize_text($_POST['titulo'] ?? '', 255);
    $resumen   = trim($_POST['resumen'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $autor     = sanitize_text($_POST['autor'] ?? '', 150);
    $fecha     = $_POST['fecha'] ?? date('Y-m-d');
    $catId     = intval($_POST['categoria_id'] ?? 0) ?: null;
    $estado    = in_array($_POST['estado'] ?? '', ['borrador','publicado','archivado'])
                   ? $_POST['estado'] : 'borrador';
    $visible   = isset($_POST['visible_en_principal']) ? 1 : 0;
    $fijado    = isset($_POST['fijado']) ? 1 : 0;

    if (!$titulo)  $errors[] = 'El título es obligatorio.';
    if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) $errors[] = 'La fecha no es válida.';

    // Generar slug
    if (!$errors) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT', $titulo)));
        $slug = trim($slug, '-') . '-' . date('Ymd');

        // Subir imagen si hay
        $imagen = null;
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $ext   = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Formato de imagen no permitido. Usa JPG, PNG o WebP.';
            } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
                $errors[] = 'La imagen supera el límite de 5 MB.';
            } else {
                $dir = __DIR__ . '/../../assets/img/noticias/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $imagen = $slug . '.' . $ext;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $imagen);
            }
        }

        if (!$errors) {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO noticias
                     (slug,titulo,resumen,contenido,imagen,autor,categoria_id,fecha,estado,visible_en_principal,fijado)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?)"
                );
                $stmt->execute([$slug,$titulo,$resumen,$contenido,$imagen,$autor,$catId,$fecha,$estado,$visible,$fijado]);
                header('Location: index.php?msg=creado');
                exit;
            } catch (\PDOException $e) {
                $errors[] = 'Error al guardar: ' . $e->getMessage();
            }
        }
    }

    // Re-poblar
    $vals = compact('titulo','resumen','contenido','autor','fecha','catId','estado','visible','fijado');
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem">
    <i class="bi bi-chevron-left"></i> Noticias
  </a>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem">Nueva</span>
</div>
<h1 class="h4 fw-bold mb-4">Nueva noticia</h1>

<?php foreach ($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card mb-3">
        <div class="mb-3">
          <label class="form-label" for="titulo">Título *</label>
          <input type="text" class="form-control" id="titulo" name="titulo"
                 value="<?= htmlspecialchars($vals['titulo']) ?>" required placeholder="Ej: Expo Ciencias UAZ 2025">
        </div>
        <div class="mb-3">
          <label class="form-label" for="resumen">Resumen <small style="color:var(--adm-muted)">(para tarjetas y previews)</small></label>
          <textarea class="form-control" id="resumen" name="resumen" rows="3"
                    placeholder="Breve descripción de la noticia..."><?= htmlspecialchars($vals['resumen']) ?></textarea>
        </div>
        <div class="mb-0">
          <label class="form-label" for="contenido">Contenido completo</label>
          <textarea class="form-control" id="contenido" name="contenido" rows="10"
                    placeholder="Cuerpo completo del artículo..."><?= htmlspecialchars($vals['contenido']) ?></textarea>
        </div>
      </div>

      <div class="adm-card">
        <h2 class="adm-card-title">Imagen de portada</h2>
        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
        <small style="color:var(--adm-muted);font-size:.78rem">JPG, PNG o WebP · máx. 5 MB</small>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Publicación</h2>
        <div class="mb-3">
          <label class="form-label" for="estado">Estado</label>
          <select class="form-select" id="estado" name="estado">
            <option value="borrador"  <?= ($vals['estado']==='borrador')  ? 'selected':'' ?>>Borrador</option>
            <option value="publicado" <?= ($vals['estado']==='publicado') ? 'selected':'' ?>>Publicado</option>
            <option value="archivado" <?= ($vals['estado']==='archivado') ? 'selected':'' ?>>Archivado</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label" for="fecha">Fecha</label>
          <input type="date" class="form-control" id="fecha" name="fecha"
                 value="<?= htmlspecialchars($vals['fecha']) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label" for="autor">Autor</label>
          <input type="text" class="form-control" id="autor" name="autor"
                 value="<?= htmlspecialchars($vals['autor']) ?>" placeholder="Nombre del autor">
        </div>
        <div class="mb-3">
          <label class="form-label" for="categoria_id">Categoría</label>
          <select class="form-select" id="categoria_id" name="categoria_id">
            <option value="">— Sin categoría —</option>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($vals['categoria_id'] == $cat['id']) ? 'selected':'' ?>>
                <?= htmlspecialchars($cat['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="visible_en_principal"
                 name="visible_en_principal" <?= $vals['visible_en_principal'] ? 'checked':'' ?>>
          <label class="form-check-label" for="visible_en_principal" style="color:var(--adm-text);font-size:.875rem">
            Visible en página principal
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="fijado"
                 name="fijado" <?= $vals['fijado'] ? 'checked':'' ?>>
          <label class="form-check-label" for="fijado" style="color:var(--adm-text);font-size:.875rem">
            Fijar en home <i class="bi bi-pin-fill" style="color:#f59e0b"></i>
          </label>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn">
          <i class="bi bi-save me-1"></i> Guardar noticia
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
