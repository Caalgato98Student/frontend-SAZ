<?php
/**
 * admin/observaciones/crear.php — Crear nueva sesión de observación.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Nueva observación';
$basePath  = '../../';
$pdo       = get_pdo();
$errors    = [];
$vals      = ['titulo'=>'','slug'=>'','icono'=>'','descripcion_intro'=>'','recomendaciones'=>'','activo'=>1,'orden'=>0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $titulo      = sanitize_text($_POST['titulo'] ?? '', 255);
    $slug        = sanitize_text($_POST['slug'] ?? '', 100);
    $icono       = sanitize_text($_POST['icono'] ?? '', 100);
    $desc        = trim($_POST['descripcion_intro'] ?? '');
    $recoms      = trim($_POST['recomendaciones'] ?? '');
    $activo      = isset($_POST['activo']) ? 1 : 0;
    $orden       = intval($_POST['orden'] ?? 0);

    if (!$titulo) $errors[] = 'El título es obligatorio.';
    if (!$slug)   $errors[] = 'El slug es obligatorio.';

    if (!$slug && $titulo) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', iconv('UTF-8','ASCII//TRANSLIT', $titulo)));
        $slug = trim($slug, '-');
    }

    if (!$errors) {
        try {
            $pdo->prepare(
                "INSERT INTO observaciones (slug,titulo,icono,descripcion_intro,recomendaciones,activo,orden)
                 VALUES (?,?,?,?,?,?,?)"
            )->execute([$slug,$titulo,$icono,$desc,$recoms,$activo,$orden]);
            header('Location: index.php?msg=editado'); exit;
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Ya existe una observación con ese slug.';
            } else {
                $errors[] = 'Error al guardar: ' . $e->getMessage();
            }
        }
    }
    $vals = compact('titulo','slug','icono','descripcion_intro','recomendaciones','activo','orden');
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Observaciones</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Nueva</span>
</div>
<h1 class="h4 fw-bold mb-4">Nueva observación</h1>

<?php foreach($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?=htmlspecialchars($e)?></div>
<?php endforeach; ?>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?=$csrf?>">
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card">
        <div class="mb-3"><label class="form-label" for="titulo">Título *</label>
          <input type="text" class="form-control" id="titulo" name="titulo"
                 value="<?=htmlspecialchars($vals['titulo'])?>" required
                 placeholder="Ej: Observación nocturna" oninput="autoSlug(this.value)"></div>
        <div class="mb-3"><label class="form-label" for="slug">Slug (URL) *</label>
          <input type="text" class="form-control" id="slug" name="slug"
                 value="<?=htmlspecialchars($vals['slug'])?>" required
                 placeholder="Ej: nocturna" pattern="[a-z0-9\-]+"></div>
        <div class="mb-3">
          <label class="form-label" for="icono">Clase Bootstrap Icons</label>
          <div class="input-group">
            <span class="input-group-text" style="background:var(--adm-dark);border:1px solid var(--adm-border);border-right:none">
              <i id="iconPrev" class="<?=htmlspecialchars($vals['icono'])?>"></i>
            </span>
            <input type="text" class="form-control" id="icono" name="icono"
                   value="<?=htmlspecialchars($vals['icono'])?>"
                   placeholder="bi bi-moon-stars"
                   oninput="document.getElementById('iconPrev').className=this.value">
          </div>
        </div>
        <div class="mb-3"><label class="form-label" for="descripcion_intro">Descripción introductoria</label>
          <textarea class="form-control" id="descripcion_intro" name="descripcion_intro" rows="4"
                    placeholder="Descripción de este tipo de observación..."><?=htmlspecialchars($vals['descripcion_intro']??'')?></textarea></div>
        <div class="mb-0"><label class="form-label" for="recomendaciones">Recomendaciones al pie
            <small style="color:var(--adm-muted)">(HTML básico: &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;)</small></label>
          <textarea class="form-control" id="recomendaciones" name="recomendaciones" rows="4"
                    placeholder="Recomendaciones para los asistentes..."><?=htmlspecialchars($vals['recomendaciones']??'')?></textarea></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Configuración</h2>
        <div class="mb-3"><label class="form-label" for="orden">Orden</label>
          <input type="number" class="form-control" id="orden" name="orden" value="0" min="0"></div>
        <div class="form-check"><input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
          <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activa</label></div>
      </div>
      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Crear observación</button>
        <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Cancelar</a>
      </div>
    </div>
  </div>
</form>
<script>
function autoSlug(v) {
  const s = document.getElementById('slug');
  if (!s.dataset.manual) {
    s.value = v.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'')
      .replace(/[^a-z0-9\s-]/g,'').trim().replace(/\s+/g,'-');
  }
}
document.getElementById('slug').addEventListener('input', () => { document.getElementById('slug').dataset.manual='1'; });
</script>
<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';