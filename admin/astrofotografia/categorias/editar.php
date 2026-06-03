<?php
/**
 * admin/astrofotografia/categorias/editar.php — Editar categoría de astrofotografía.
 */
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db.php';

$pdo = get_pdo();
$id  = intval($_GET['id'] ?? 0);
$cat = $pdo->prepare("SELECT * FROM astrofoto_categorias WHERE id = ? LIMIT 1");
$cat->execute([$id]);
$cat = $cat->fetch();

if (!$cat) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Editar Categoría: ' . $cat['nombre'];
$basePath  = '../../../';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $nombre      = sanitize_text($_POST['nombre'] ?? '', 100);
    $slug        = trim(preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($_POST['slug'] ?? '')), '-');
    $icono       = sanitize_text($_POST['icono'] ?? '', 100) ?: 'bi-camera';
    $color       = sanitize_text($_POST['color'] ?? '', 20) ?: '#818cf8';
    $descripcion = sanitize_text($_POST['descripcion'] ?? '', 255);

    if (!$nombre) $errors[] = 'El nombre es obligatorio.';
    if (!$slug)   $errors[] = 'El slug es obligatorio.';

    if (!$errors) {
        // Verificar unicidad excluyendo la actual
        $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM astrofoto_categorias WHERE (slug = ? OR nombre = ?) AND id != ?");
        $stmtChk->execute([$slug, $nombre, $id]);
        if ($stmtChk->fetchColumn() > 0) {
            $errors[] = 'Ya existe otra categoría con ese nombre o slug.';
        } else {
            $pdo->prepare("UPDATE astrofoto_categorias SET nombre = ?, slug = ?, icono = ?, color = ?, descripcion = ? WHERE id = ?")
                ->execute([$nombre, $slug, $icono, $color, $descripcion, $id]);
            header("Location: index.php?msg=editado"); exit;
        }
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Categorías</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar categoría: <?= htmlspecialchars($cat['nombre']) ?></h1>

<?php foreach ($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="adm-card">
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        
        <div class="mb-3">
          <label class="form-label" for="nombre">Nombre de la categoría *</label>
          <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($cat['nombre']) ?>" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label" for="slug">Slug (URL) *</label>
          <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($cat['slug']) ?>" required>
          <small style="color:var(--adm-muted);font-size:.78rem">Determina la URL de la categoría. Ej: <code>nebulosas</code></small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="icono">Icono (Bootstrap Icons) *</label>
          <input type="text" class="form-control" id="icono" name="icono" value="<?= htmlspecialchars($cat['icono']) ?>" required>
          <small style="color:var(--adm-muted);font-size:.72rem">Ver íconos en <a href="https://icons.getbootstrap.com" target="_blank" style="color:var(--adm-accent)">icons.getbootstrap.com</a></small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="color">Color distintivo *</label>
          <input type="color" class="form-control form-control-color w-100" id="color" name="color" value="<?= htmlspecialchars($cat['color']) ?>">
        </div>

        <div class="mb-3">
          <label class="form-label" for="descripcion">Descripción</label>
          <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($cat['descripcion'] ?? '') ?></textarea>
        </div>
        
        <button type="submit" class="btn-adm-primary btn w-100"><i class="bi bi-save me-1"></i> Guardar cambios</button>
      </form>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../../base_admin.php';
