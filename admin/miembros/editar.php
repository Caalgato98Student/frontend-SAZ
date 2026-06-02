<?php
/**
 * admin/miembros/editar.php — Editar miembro existente.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/miembros.php';

$pdo = get_pdo();
$id  = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM miembros WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$m = $stmt->fetch();
if (!$m) { header('Location: index.php'); exit; }

$pageTitle = 'Editar: ' . $m['nombre'];
$basePath  = '../../';
$errors    = [];
$msgItem   = $_GET['msgitem'] ?? '';

$cargos = get_cargos();

$stmtF = $pdo->prepare("SELECT id, descripcion FROM miembro_formacion WHERE miembro_id = ? ORDER BY orden, id");
$stmtF->execute([$id]);
$formacion = $stmtF->fetchAll();

$stmtD = $pdo->prepare("SELECT id, descripcion FROM miembro_divulgacion WHERE miembro_id = ? ORDER BY orden, id");
$stmtD->execute([$id]);
$divulgacion = $stmtD->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $action = $_POST['action'];

    if ($action === 'save_miembro') {
        $nombre       = sanitize_text($_POST['nombre'] ?? '', 255);
        $especialidad = sanitize_text($_POST['especialidad'] ?? '', 255);
        $cargoId      = intval($_POST['cargo_id'] ?? 0) ?: null;
        $correo       = sanitize_text($_POST['correo'] ?? '', 255);
        $ubicacion    = sanitize_text($_POST['ubicacion'] ?? '', 255);
        $distincion   = sanitize_text($_POST['distincion'] ?? '', 255);
        $generalidades = trim($_POST['generalidades'] ?? '');
        $activo       = isset($_POST['activo']) ? 1 : 0;
        $mesa         = isset($_POST['en_mesa_directiva']) ? 1 : 0;
        $orden        = intval($_POST['orden'] ?? 0);

        if (!$nombre) $errors[] = 'El nombre es obligatorio.';

        // Validar cargo si se especificó
        if ($cargoId !== null) {
            $validCargo = false;
            foreach ($cargos as $cg) {
                if ($cg['id'] == $cargoId) {
                    $validCargo = true;
                    break;
                }
            }
            if (!$validCargo) $errors[] = 'El cargo seleccionado no es válido.';
        }

        $imagen = $m['imagen'];
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $ext   = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','webp'];
            if (!in_array($ext, $allow)) {
                $errors[] = 'Formato de imagen no permitido.';
            } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
                $errors[] = 'La imagen supera el limite de 5 MB.';
            } else {
                $dir = __DIR__ . '/../../assets/img/miembros/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $imagen = $m['slug'] . '.' . $ext;
                move_uploaded_file($_FILES['imagen']['tmp_name'], $dir . $imagen);
            }
        }

        if (!$errors) {
            $pdo->prepare(
                "UPDATE miembros SET nombre=?,especialidad=?,cargo_id=?,correo=?,ubicacion=?,
                 distincion=?,imagen=?,generalidades=?,activo=?,en_mesa_directiva=?,orden=?
                 WHERE id=?"
            )->execute([$nombre,$especialidad,$cargoId,$correo,$ubicacion,$distincion,$imagen,$generalidades,$activo,$mesa,$orden,$id]);
            header("Location: editar.php?id={$id}&msg=editado"); exit;
        }
    } elseif ($action === 'add_formacion') {
        $desc = trim($_POST['descripcion'] ?? '');
        if ($desc) {
            $stmtMax = $pdo->prepare("SELECT MAX(orden) FROM miembro_formacion WHERE miembro_id = ?");
            $stmtMax->execute([$id]);
            $maxOrd = intval($stmtMax->fetchColumn()) + 1;
            
            $pdo->prepare("INSERT INTO miembro_formacion (miembro_id,descripcion,orden) VALUES (?,?,?)")->execute([$id,$desc,$maxOrd]);
        }
        header("Location: editar.php?id={$id}&msgitem=added"); exit;
    } elseif ($action === 'del_formacion') {
        $itemId = intval($_POST['item_id'] ?? 0);
        $pdo->prepare("DELETE FROM miembro_formacion WHERE id=? AND miembro_id=?")->execute([$itemId,$id]);
        header("Location: editar.php?id={$id}&msgitem=deleted"); exit;
    } elseif ($action === 'add_divulgacion') {
        $desc = trim($_POST['descripcion'] ?? '');
        if ($desc) {
            $stmtMax = $pdo->prepare("SELECT MAX(orden) FROM miembro_divulgacion WHERE miembro_id = ?");
            $stmtMax->execute([$id]);
            $maxOrd = intval($stmtMax->fetchColumn()) + 1;
            
            $pdo->prepare("INSERT INTO miembro_divulgacion (miembro_id,descripcion,orden) VALUES (?,?,?)")->execute([$id,$desc,$maxOrd]);
        }
        header("Location: editar.php?id={$id}&msgitem=added"); exit;
    } elseif ($action === 'del_divulgacion') {
        $itemId = intval($_POST['item_id'] ?? 0);
        $pdo->prepare("DELETE FROM miembro_divulgacion WHERE id=? AND miembro_id=?")->execute([$itemId,$id]);
        header("Location: editar.php?id={$id}&msgitem=deleted"); exit;
    }
}

$csrf = generate_csrf_token();
ob_start();
?>
<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem"><i class="bi bi-chevron-left"></i> Miembros</a>
  <span style="color:var(--adm-border)">/</span><span style="font-size:.9rem">Editar</span>
</div>
<h1 class="h4 fw-bold mb-4">Editar: <?= htmlspecialchars($m['nombre']) ?></h1>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'editado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Miembro actualizado.</div>
<?php endif; ?>
<?php if ($msgItem === 'added'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Item agregado.</div>
<?php elseif ($msgItem === 'deleted'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Item eliminado.</div>
<?php endif; ?>
<?php foreach ($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="adm-card mb-3">
      <h2 class="adm-card-title">Datos generales</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="save_miembro">
        <div class="mb-3"><label class="form-label" for="nombre">Nombre *</label>
          <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($m['nombre']) ?>" required></div>
        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label" for="cargo_id">Cargo</label>
            <select class="form-select" id="cargo_id" name="cargo_id">
              <option value="">— Sin cargo —</option>
              <?php foreach ($cargos as $cg): ?>
                <option value="<?= $cg['id'] ?>" <?= $m['cargo_id'] == $cg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cg['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-6"><label class="form-label" for="especialidad">Especialidad</label>
            <input type="text" class="form-control" id="especialidad" name="especialidad" value="<?= htmlspecialchars($m['especialidad'] ?? '') ?>"></div>
        </div>
        <div class="mb-3"><label class="form-label" for="correo">Correo</label>
          <input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($m['correo'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label" for="ubicacion">Ubicacion</label>
          <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($m['ubicacion'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label" for="distincion">Distincion</label>
          <input type="text" class="form-control" id="distincion" name="distincion" value="<?= htmlspecialchars($m['distincion'] ?? '') ?>"></div>
        <div class="mb-3"><label class="form-label" for="generalidades">Generalidades
            <small style="color:var(--adm-muted)">(HTML: &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;)</small></label>
          <textarea class="form-control" id="generalidades" name="generalidades" rows="5"><?= htmlspecialchars($m['generalidades'] ?? '') ?></textarea></div>
        <div class="mb-3"><label class="form-label" for="imagen">Cambiar foto</label>
          <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
          <small style="color:var(--adm-muted);font-size:.78rem">Dejar vacio para conservar la foto actual</small></div>
        <div class="row g-2 mb-3">
          <div class="col-6"><label class="form-label" for="orden">Orden</label>
            <input type="number" class="form-control" id="orden" name="orden" value="<?= $m['orden'] ?>" min="0"></div>
          <div class="col-6 d-flex flex-column gap-2 justify-content-end pb-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="activo" name="activo" <?= $m['activo'] ? 'checked' : '' ?>>
              <label class="form-check-label" for="activo" style="color:var(--adm-text);font-size:.875rem">Activo</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="mesa" name="en_mesa_directiva" <?= $m['en_mesa_directiva'] ? 'checked' : '' ?>>
              <label class="form-check-label" for="mesa" style="color:var(--adm-text);font-size:.875rem">Mesa directiva</label>
            </div>
          </div>
        </div>
        <button type="submit" class="btn-adm-primary btn w-100"><i class="bi bi-save me-1"></i> Guardar</button>
      </form>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="adm-card mb-3">
      <h2 class="adm-card-title">Formacion academica</h2>
      <?php foreach ($formacion as $fi): ?>
        <div class="d-flex align-items-start justify-content-between gap-2 mb-2 pb-2" style="border-bottom:1px solid var(--adm-border)">
          <span style="font-size:.875rem"><?= htmlspecialchars($fi['descripcion']) ?></span>
          <form method="POST" style="flex-shrink:0">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <input type="hidden" name="action" value="del_formacion">
            <input type="hidden" name="item_id" value="<?= $fi['id'] ?>">
            <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm" style="padding:.2rem .55rem" data-confirm="Eliminar este item?"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      <?php endforeach; ?>
      <?php if (empty($formacion)): ?><p style="color:var(--adm-muted);font-size:.875rem">Sin items de formacion.</p><?php endif; ?>
      <form method="POST" class="mt-2">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        <input type="hidden" name="action" value="add_formacion">
        <div class="input-group">
          <input type="text" class="form-control" name="descripcion" placeholder="Ej: Doctorado en Astrofisica, UAZ, 2010" required>
          <button type="submit" class="btn-adm-primary btn" style="border-radius:0 8px 8px 0"><i class="bi bi-plus-lg"></i></button>
        </div>
      </form>
    </div>

    <div class="adm-card">
      <h2 class="adm-card-title">Divulgacion cientifica</h2>
      <?php foreach ($divulgacion as $di): ?>
        <div class="d-flex align-items-start justify-content-between gap-2 mb-2 pb-2" style="border-bottom:1px solid var(--adm-border)">
          <span style="font-size:.875rem"><?= htmlspecialchars($di['descripcion']) ?></span>
          <form method="POST" style="flex-shrink:0">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <input type="hidden" name="action" value="del_divulgacion">
            <input type="hidden" name="item_id" value="<?= $di['id'] ?>">
            <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm" style="padding:.2rem .55rem" data-confirm="Eliminar este item?"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      <?php endforeach; ?>
      <?php if (empty($divulgacion)): ?><p style="color:var(--adm-muted);font-size:.875rem">Sin items de divulgacion.</p><?php endif; ?>
      <form method="POST" class="mt-2">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        <input type="hidden" name="action" value="add_divulgacion">
        <div class="input-group">
          <input type="text" class="form-control" name="descripcion" placeholder="Ej: Conferencia: El universo, 2023" required>
          <button type="submit" class="btn-adm-primary btn" style="border-radius:0 8px 8px 0"><i class="bi bi-plus-lg"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../base_admin.php';