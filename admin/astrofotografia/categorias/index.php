<?php
/**
 * admin/astrofotografia/categorias/index.php — Listado y creación rápida de categorías de astrofotografía.
 */
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db.php';

$pageTitle = 'Categorías de Astrofotografía';
$basePath  = '../../../';
$pdo       = get_pdo();
$errors    = [];
$msg       = $_GET['msg'] ?? '';

// Crear categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $nombre      = sanitize_text($_POST['nombre'] ?? '', 100);
    $icono       = sanitize_text($_POST['icono'] ?? '', 100) ?: 'bi-camera';
    $color       = sanitize_text($_POST['color'] ?? '', 20) ?: '#818cf8';
    $descripcion = sanitize_text($_POST['descripcion'] ?? '', 255);

    if (!$nombre) {
        $errors[] = 'El nombre es obligatorio.';
    } else {
        $slug = trim(preg_replace('/[^a-z0-9]+/', '-', mb_strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $nombre))), '-');
        if (!$slug) {
            $slug = 'categoria';
        }

        // Verificar unicidad
        $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM astrofoto_categorias WHERE slug = ? OR nombre = ?");
        $stmtChk->execute([$slug, $nombre]);
        if ($stmtChk->fetchColumn() > 0) {
            $errors[] = 'Ya existe una categoría con ese nombre o slug.';
        } else {
            $pdo->prepare("INSERT INTO astrofoto_categorias (nombre, slug, icono, color, descripcion) VALUES (?, ?, ?, ?, ?)")
                ->execute([$nombre, $slug, $icono, $color, $descripcion]);
            header("Location: index.php?msg=creado"); exit;
        }
    }
}

// Cargar todas las categorías
$categorias = $pdo->query("SELECT * FROM astrofoto_categorias ORDER BY nombre")->fetchAll();

$csrf = generate_csrf_token();
ob_start(); ?>

<?php if ($msg === 'creado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Categoría creada correctamente.</div>
<?php elseif ($msg === 'editado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Categoría actualizada correctamente.</div>
<?php elseif ($msg === 'eliminado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Categoría eliminada correctamente.</div>
<?php elseif ($msg === 'error_eliminar'): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> No se puede eliminar la categoría porque contiene fotografías asociadas.</div>
<?php endif; ?>

<?php foreach ($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="../index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Astrofotografía</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Categorías</span>
</div>

<div class="row g-3">
  <!-- Listado -->
  <div class="col-lg-8">
    <div class="adm-card">
      <h2 class="adm-card-title">Categorías de astrofotografía registradas</h2>
      <?php if ($categorias): ?>
        <div class="table-responsive">
          <table class="adm-table">
            <thead>
              <tr>
                <th>Color</th>
                <th>Nombre</th>
                <th>Icono</th>
                <th>Slug (URL)</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categorias as $cat): ?>
                <tr>
                  <td>
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: <?= htmlspecialchars($cat['color']) ?>; border: 1px solid rgba(255,255,255,0.1)"></div>
                  </td>
                  <td class="fw-semibold">
                    <?= htmlspecialchars($cat['nombre']) ?><br>
                    <?php if ($cat['descripcion']): ?>
                      <small style="color: var(--adm-muted); font-weight: normal; font-size: .78rem;"><?= htmlspecialchars($cat['descripcion']) ?></small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <i class="bi <?= htmlspecialchars($cat['icono']) ?>" style="font-size: 1.2rem; color: <?= htmlspecialchars($cat['color']) ?>"></i>
                  </td>
                  <td style="color:var(--adm-muted)"><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                  <td>
                    <div class="d-flex gap-2">
                      <a href="editar.php?id=<?= $cat['id'] ?>" class="btn btn-sm"
                         style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form method="POST" action="eliminar.php" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm"
                                style="padding:.25rem .65rem"
                                data-confirm="¿Eliminar la categoría «<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>»? Solo se podrá eliminar si no tiene astrofotografías asociadas.">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="mb-0" style="color:var(--adm-muted)">No hay categorías registradas aún.</p>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Crear rápida -->
  <div class="col-lg-4">
    <div class="adm-card">
      <h2 class="adm-card-title">Nueva categoría</h2>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="crear">
        
        <div class="mb-3">
          <label class="form-label" for="nombre">Nombre de la categoría *</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Nebulosas">
        </div>
        
        <div class="mb-3">
          <label class="form-label" for="icono">Icono (Bootstrap Icons) *</label>
          <input type="text" class="form-control" id="icono" name="icono" required placeholder="Ej: bi-stars" value="bi-camera">
          <small style="color:var(--adm-muted);font-size:.72rem">Ver íconos en <a href="https://icons.getbootstrap.com" target="_blank" style="color:var(--adm-accent)">icons.getbootstrap.com</a></small>
        </div>

        <div class="mb-3">
          <label class="form-label" for="color">Color distintivo *</label>
          <input type="color" class="form-control form-control-color w-100" id="color" name="color" value="#818cf8" title="Elige un color para esta categoría">
        </div>

        <div class="mb-3">
          <label class="form-label" for="descripcion">Descripción</label>
          <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Breve descripción..."></textarea>
        </div>
        
        <button type="submit" class="btn-adm-primary btn w-100"><i class="bi bi-plus-lg me-1"></i> Crear categoría</button>
      </form>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../../base_admin.php';
