<?php
/**
 * admin/noticias/index.php — Listado de noticias.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Noticias';
$basePath  = '../../';
$pdo       = get_pdo();

// Filtro por estado
$filtroEstado = $_GET['estado'] ?? '';
$estados = ['', 'publicado', 'borrador', 'archivado'];

$where  = $filtroEstado ? "WHERE estado = " . $pdo->quote($filtroEstado) : '';
$noticias = $pdo->query(
    "SELECT n.id, n.slug, n.titulo, n.fecha, n.estado, n.fijado, n.visible_en_principal,
            c.nombre AS categoria
     FROM noticias n
     LEFT JOIN categorias c ON n.categoria_id = c.id
     {$where}
     ORDER BY n.fecha DESC"
)->fetchAll();

$msg = $_GET['msg'] ?? '';

ob_start(); ?>

<?php if ($msg === 'creado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Noticia creada correctamente.</div>
<?php elseif ($msg === 'editado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Noticia actualizada correctamente.</div>
<?php elseif ($msg === 'eliminado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Noticia eliminada.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Noticias</h1>
  <a href="crear.php" class="btn-adm-primary btn">
    <i class="bi bi-plus-lg me-1"></i> Nueva noticia
  </a>
</div>

<!-- Filtros -->
<div class="adm-card mb-3">
  <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
    <label class="form-label mb-0 me-1" style="font-size:.8rem">Filtrar:</label>
    <?php foreach ([''=>'Todas','publicado'=>'Publicadas','borrador'=>'Borradores','archivado'=>'Archivadas'] as $val => $lbl): ?>
      <a href="?estado=<?= $val ?>"
         class="btn btn-sm"
         style="border-radius:20px;font-size:.8rem;
                background:<?= $filtroEstado===$val ? 'rgba(59,130,246,.25)' : 'rgba(255,255,255,.05)' ?>;
                border:1px solid <?= $filtroEstado===$val ? 'rgba(59,130,246,.5)' : 'var(--adm-border)' ?>;
                color:<?= $filtroEstado===$val ? '#60a5fa' : 'var(--adm-muted)' ?>">
        <?= $lbl ?>
      </a>
    <?php endforeach; ?>
  </form>
</div>

<div class="adm-card">
  <?php if ($noticias): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Título</th>
            <th>Categoría</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Home</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($noticias as $n): ?>
            <tr>
              <td>
                <?php if ($n['fijado']): ?>
                  <i class="bi bi-pin-fill me-1" style="color:#f59e0b" title="Fijada"></i>
                <?php endif; ?>
                <span style="max-width:260px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:middle">
                  <?= htmlspecialchars($n['titulo']) ?>
                </span>
              </td>
              <td style="color:var(--adm-muted);font-size:.8rem"><?= htmlspecialchars($n['categoria'] ?? '—') ?></td>
              <td style="color:var(--adm-muted);font-size:.8rem;white-space:nowrap"><?= $n['fecha'] ?></td>
              <td><span class="badge-estado badge-<?= $n['estado'] ?>"><?= ucfirst($n['estado']) ?></span></td>
              <td>
                <?= $n['visible_en_principal']
                  ? '<i class="bi bi-check-circle-fill" style="color:#4ade80"></i>'
                  : '<i class="bi bi-dash-circle" style="color:var(--adm-border)"></i>' ?>
              </td>
              <td>
                <div class="d-flex gap-2">
                  <a href="editar.php?id=<?= $n['id'] ?>" class="btn btn-sm"
                     style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form method="POST" action="eliminar.php" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $n['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm"
                            style="padding:.25rem .65rem"
                            data-confirm="¿Eliminar la noticia «<?= htmlspecialchars($n['titulo'], ENT_QUOTES) ?>»? Esta acción no se puede deshacer.">
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
    <p class="mb-0" style="color:var(--adm-muted)">No hay noticias
      <?= $filtroEstado ? "con estado «{$filtroEstado}»" : 'registradas' ?>.
      <a href="crear.php" style="color:var(--adm-accent)">Crear la primera</a>.
    </p>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../base_admin.php';
