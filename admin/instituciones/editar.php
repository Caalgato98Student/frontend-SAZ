<?php
/**
 * admin/instituciones/editar.php — Editar una institución colaboradora.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo = get_pdo();
$id  = intval($_GET['id'] ?? 0);
$inst = $pdo->prepare("SELECT * FROM instituciones WHERE id = ? LIMIT 1");
$inst->execute([$id]);
$inst = $inst->fetch();

if (!$inst) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Editar Institución: ' . $inst['nombre'];
$basePath  = '../../';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $nombre = sanitize_text($_POST['nombre'] ?? '', 255);
    $url    = sanitize_text($_POST['url'] ?? '', 500);
    $orden  = intval($_POST['orden'] ?? 0);
    $activo = isset($_POST['activo']) ? 1 : 0;

    if (!$nombre) {
        $errors[] = 'El nombre es obligatorio.';
    }

    if (!$errors) {
        $imagen = $inst['imagen']; // Retener actual por defecto

        // Subida de nueva imagen
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $ext   = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Formato de imagen no permitido. Usa JPG, PNG, WebP o SVG.';
            } elseif ($_FILES['imagen']['size'] > 3 * 1024 * 1024) {
                $errors[] = 'El archivo supera el límite de 3 MB.';
            } else {
                $dir = __DIR__ . '/../../assets/img/instituciones/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $cleanNombre = preg_replace('/[^a-z0-9]+/', '-', mb_strtolower(trim($nombre)));
                $cleanNombre = trim($cleanNombre, '-');
                if (!$cleanNombre) {
                    $cleanNombre = 'institucion';
                }

                $nuevoNombreImagen = $cleanNombre . '-' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $nuevoNombreImagen)) {
                    // Eliminar logo viejo si existe y es diferente
                    if ($inst['imagen']) {
                        $viejoPath = $dir . $inst['imagen'];
                        if (file_exists($viejoPath)) {
                            @unlink($viejoPath);
                        }
                    }
                    $imagen = $nuevoNombreImagen;
                } else {
                    $errors[] = 'No se pudo guardar la imagen subida.';
                }
            }
        }

        if (!$errors) {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE instituciones SET nombre = ?, imagen = ?, url = ?, orden = ?, activo = ? WHERE id = ?"
                );
                $stmt->execute([$nombre, $imagen, $url, $orden, $activo, $id]);
                header('Location: index.php?msg=editado');
                exit;
            } catch (\PDOException $e) {
                $errors[] = 'Error al actualizar en la base de datos: ' . $e->getMessage();
            }
        }
    }
}

$csrf = generate_csrf_token();
ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem">
    <i class="bi bi-chevron-left"></i> Instituciones
  </a>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar institución: <?= htmlspecialchars($inst['nombre']) ?></h1>

<?php foreach ($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  
  <div class="row g-3">
    <div class="col-lg-8">
      <div class="adm-card">
        <div class="mb-3">
          <label class="form-label" for="nombre">Nombre de la institución *</label>
          <input type="text" class="form-control" id="nombre" name="nombre" 
                 value="<?= htmlspecialchars($inst['nombre']) ?>" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label" for="url">Enlace oficial (Sitio web)</label>
          <input type="url" class="form-control" id="url" name="url" 
                 value="<?= htmlspecialchars($inst['url'] ?? '') ?>" 
                 placeholder="Ej: https://www.uaz.edu.mx/">
          <small style="color:var(--adm-muted);font-size:.78rem">Dirección web completa que se abrirá al hacer clic en el logo.</small>
        </div>

        <div class="mb-0">
          <label class="form-label" for="imagen">Cambiar Logotipo</label>
          
          <?php if ($inst['imagen'] && file_exists(__DIR__ . '/../../assets/img/instituciones/' . $inst['imagen'])): ?>
            <div class="mb-3 p-3 rounded" style="background:rgba(255,255,255,0.03); border:1px solid var(--adm-border); display:inline-block;">
              <div style="font-size:.78rem; color:var(--adm-muted); margin-bottom:.5rem;">Logotipo actual:</div>
              <img src="../../assets/img/instituciones/<?= htmlspecialchars($inst['imagen']) ?>" alt="<?= htmlspecialchars($inst['nombre']) ?>" style="height:60px; max-width:200px; object-fit:contain;">
            </div>
          <?php endif; ?>

          <input type="file" class="form-control" id="imagen" name="imagen" accept=".jpg,.jpeg,.png,.webp,.svg">
          <small style="color:var(--adm-muted);font-size:.78rem;display:block;margin-top:.2rem">Formatos admitidos: JPG, PNG, WebP o SVG. Deja vacío si deseas conservar el logo actual.</small>
        </div>
      </div>
    </div>
    
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Configuración</h2>
        <div class="mb-3">
          <label class="form-label" for="orden">Orden de visualización</label>
          <input type="number" class="form-control" id="orden" name="orden" 
                 value="<?= htmlspecialchars($inst['orden']) ?>" min="0">
          <small style="color:var(--adm-muted);font-size:.72rem">Las instituciones se muestran ordenadas de menor a mayor.</small>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="activo" name="activo" <?= $inst['activo'] ? 'checked' : '' ?>>
          <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activa (visible en el sitio)</label>
        </div>
      </div>
      
      <div class="d-grid gap-2">
        <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Guardar cambios</button>
        <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Cancelar</a>
      </div>
    </div>
  </div>
</form>

<?php $content = ob_get_clean(); include __DIR__ . '/../base_admin.php';
