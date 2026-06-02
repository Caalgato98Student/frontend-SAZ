<?php
/**
 * admin/categorias/editar.php — Editar categoría de noticias.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo = get_pdo();
$id  = intval($_GET['id'] ?? 0);
$cat = $pdo->prepare("SELECT * FROM categorias_noticias WHERE id = ? LIMIT 1");
$cat->execute([$id]);
$cat = $cat->fetch();
if (!$cat) { header('Location: index.php'); exit; }

$pageTitle = 'Editar Categoría: ' . $cat['nombre'];
$basePath  = '../../';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $nombre = sanitize_text($_POST['nombre'] ?? '', 100);
    $slug   = trim(preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($_POST['slug'] ?? '')), '-');

    if (!$nombre) $errors[] = 'El nombre es obligatorio.';
    if (!$slug)   $errors[] = 'El slug es obligatorio.';

    if (!$errors) {
        // Verificar unicidad excluyendo la actual
        $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM categorias_noticias WHERE (slug = ? OR nombre = ?) AND id != ?");
        $stmtChk->execute([$slug, $nombre, $id]);
        if ($stmtChk->fetchColumn() > 0) {
            $errors[] = 'Ya existe otra categoría con ese nombre o slug.';
        } else {
            $pdo->prepare("UPDATE categorias_noticias SET nombre = ?, slug = ? WHERE id = ?")
                ->execute([$nombre, $slug, $id]);
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
<h1 class="h4 fw-bold mb-4">Editar categoría</h1>

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
          <small style="color:var(--adm-muted);font-size:.78rem">Ej: <code>astrofisica</code>. Determina la URL de la categoría.</small>
        </div>
        <button type="submit" class="btn-adm-primary btn w-100"><i class="bi bi-save me-1"></i> Guardar cambios</button>
      </form>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../base_admin.php';
