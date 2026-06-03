<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Astrofotografía';
$basePath  = '../../';
$pdo       = get_pdo();

$filtro = $_GET['cat'] ?? '';
$where  = $filtro ? "WHERE c.slug = " . $pdo->quote($filtro) : '';
$fotos  = $pdo->query(
    "SELECT a.id, a.slug, a.titulo, a.fotografo, a.fecha, c.nombre AS categoria, a.visible, a.destacada
     FROM astrofotografia a
     JOIN astrofoto_categorias c ON a.categoria_id = c.id
     {$where}
     ORDER BY a.fecha DESC"
)->fetchAll();

$categoriasFiltro = $pdo->query("SELECT nombre, slug FROM astrofoto_categorias ORDER BY nombre")->fetchAll();

$msg = $_GET['msg'] ?? '';
ob_start(); ?>

<?php if ($msg === 'creado'):?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Fotografía agregada.</div>
<?php elseif($msg==='editado'):?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Fotografía actualizada.</div>
<?php elseif($msg==='eliminado'):?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Fotografía eliminada.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Astrofotografía</h1>
  <div class="d-flex gap-2">
    <a href="categorias/index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-text);border-radius:8px">
      <i class="bi bi-tags me-1"></i> Categorías
    </a>
    <a href="crear.php" class="btn-adm-primary btn">
      <i class="bi bi-plus-lg me-1"></i> Subir fotografía
    </a>
  </div>
</div>

<div class="adm-card mb-3">
  <div class="d-flex gap-2 flex-wrap">
    <a href="?" class="btn btn-sm" style="border-radius:20px;font-size:.8rem;
       background:<?=$filtro===''?'rgba(59,130,246,.25)':'rgba(255,255,255,.05)'?>;
       border:1px solid <?=$filtro===''?'rgba(59,130,246,.5)':'var(--adm-border)'?>;
       color:<?=$filtro===''?'#60a5fa':'var(--adm-muted)'?>">
      Todas
    </a>
    <?php foreach ($categoriasFiltro as $c): ?>
      <a href="?cat=<?=$c['slug']?>" class="btn btn-sm" style="border-radius:20px;font-size:.8rem;
         background:<?=$filtro===$c['slug']?'rgba(59,130,246,.25)':'rgba(255,255,255,.05)'?>;
         border:1px solid <?=$filtro===$c['slug']?'rgba(59,130,246,.5)':'var(--adm-border)'?>;
         color:<?=$filtro===$c['slug']?'#60a5fa':'var(--adm-muted)'?>">
        <?=htmlspecialchars($c['nombre'])?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="adm-card">
  <?php if ($fotos): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead><tr><th>Imagen</th><th>Título / Fotógrafo</th><th>Categoría</th><th>Fecha</th><th>Visible</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($fotos as $f): ?>
          <tr>
            <td style="width:60px">
              <?php if ($f['imagen'] ?? null): ?>
                <img src="../../assets/img/astrofotografia/<?=htmlspecialchars($f['imagen'])?>"
                     style="width:50px;height:40px;object-fit:cover;border-radius:6px">
              <?php else: ?>
                <div style="width:50px;height:40px;background:var(--adm-dark);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--adm-border)">
                  <i class="bi bi-camera"></i>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:500"><?=htmlspecialchars($f['titulo']??'Sin título')?></div>
              <div style="font-size:.78rem;color:var(--adm-muted)"><?=htmlspecialchars($f['fotografo'])?></div>
            </td>
            <td><span style="font-size:.8rem;color:var(--adm-muted)"><?=htmlspecialchars($f['categoria'])?></span></td>
            <td style="color:var(--adm-muted);font-size:.8rem"><?=$f['fecha']?></td>
            <td><?=$f['visible']?'<i class="bi bi-check-circle-fill" style="color:#4ade80"></i>':'<i class="bi bi-dash-circle" style="color:var(--adm-border)"></i>'?></td>
            <td>
              <div class="d-flex gap-2">
                <a href="editar.php?id=<?=$f['id']?>" class="btn btn-sm" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem"><i class="bi bi-pencil"></i></a>
                <form method="POST" action="eliminar.php" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?=generate_csrf_token()?>">
                  <input type="hidden" name="id" value="<?=$f['id']?>">
                  <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm"
                          style="padding:.25rem .65rem"
                          data-confirm="¿Eliminar «<?=htmlspecialchars($f['titulo']??'esta foto',ENT_QUOTES)?>»?">
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
    <p style="color:var(--adm-muted)">No hay fotografías. <a href="crear.php" style="color:var(--adm-accent)">Subir la primera</a>.</p>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); include __DIR__.'/../base_admin.php';
