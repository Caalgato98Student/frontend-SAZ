<?php
/**
 * admin/base_admin.php — Layout compartido del panel CMS.
 * Uso: al final de cada página del panel, después de ob_start() + $content = ob_get_clean().
 */
$adminNombre = htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin');

// Detectar sección activa a partir de la URL
$script = $_SERVER['PHP_SELF'];
function admin_nav_active(string $section): string {
    global $script;
    return str_contains($script, "/admin/{$section}") ? 'active' : '';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Panel Admin') ?> — SAZ CMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="/assets/img/logo-SAZ.png">
  <style>
    :root {
      --adm-dark:    #0d1117;
      --adm-sidebar: #161b22;
      --adm-card:    #1c2128;
      --adm-border:  #30363d;
      --adm-accent:  #3b82f6;
      --adm-accent2: #6366f1;
      --adm-success: #22c55e;
      --adm-danger:  #ef4444;
      --adm-warn:    #f59e0b;
      --adm-text:    #e6edf3;
      --adm-muted:   #8b949e;
      --sidebar-w:   240px;
    }
    *, *::before, *::after { box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--adm-dark);
      color: var(--adm-text);
      margin: 0;
      min-height: 100vh;
      display: flex;
    }

    /* ── Sidebar ── */
    .adm-sidebar {
      width: var(--sidebar-w);
      min-height: 100vh;
      background: var(--adm-sidebar);
      border-right: 1px solid var(--adm-border);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0; bottom: 0;
      z-index: 100;
      transition: transform .25s ease;
    }
    .adm-sidebar-brand {
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: 1.25rem 1.25rem 1rem;
      border-bottom: 1px solid var(--adm-border);
      text-decoration: none;
    }
    .adm-sidebar-brand img { width: 36px; height: 36px; object-fit: contain; }
    .adm-sidebar-brand span {
      font-size: .9rem;
      font-weight: 700;
      color: var(--adm-text);
      line-height: 1.2;
    }
    .adm-sidebar-brand small { display: block; font-weight: 400; color: var(--adm-muted); font-size: .75rem; }

    .adm-nav { flex: 1; padding: .75rem 0; overflow-y: auto; }
    .adm-nav-section {
      padding: .5rem 1.25rem .25rem;
      font-size: .7rem;
      font-weight: 600;
      color: var(--adm-muted);
      text-transform: uppercase;
      letter-spacing: .08em;
    }
    .adm-nav a {
      display: flex;
      align-items: center;
      gap: .6rem;
      padding: .55rem 1.25rem;
      color: var(--adm-muted);
      text-decoration: none;
      font-size: .875rem;
      font-weight: 500;
      border-left: 3px solid transparent;
      transition: color .15s, background .15s, border-color .15s;
    }
    .adm-nav a:hover {
      color: var(--adm-text);
      background: rgba(255,255,255,.05);
    }
    .adm-nav a.active {
      color: var(--adm-accent);
      background: rgba(59,130,246,.1);
      border-left-color: var(--adm-accent);
    }
    .adm-nav a i { font-size: 1rem; width: 20px; text-align: center; }

    .adm-sidebar-footer {
      padding: 1rem 1.25rem;
      border-top: 1px solid var(--adm-border);
      font-size: .8rem;
    }
    .adm-sidebar-footer .admin-name { font-weight: 600; color: var(--adm-text); }
    .adm-sidebar-footer .admin-role { color: var(--adm-muted); }
    .btn-logout {
      display: flex;
      align-items: center;
      gap: .5rem;
      margin-top: .5rem;
      padding: .45rem .75rem;
      background: rgba(239,68,68,.1);
      border: 1px solid rgba(239,68,68,.3);
      color: #f87171;
      border-radius: 7px;
      font-size: .8rem;
      font-weight: 500;
      text-decoration: none;
      transition: background .15s;
    }
    .btn-logout:hover { background: rgba(239,68,68,.2); color: #f87171; }

    /* ── Main area ── */
    .adm-main {
      margin-left: var(--sidebar-w);
      flex: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .adm-topbar {
      height: 56px;
      background: var(--adm-sidebar);
      border-bottom: 1px solid var(--adm-border);
      display: flex;
      align-items: center;
      padding: 0 1.5rem;
      gap: 1rem;
      position: sticky;
      top: 0;
      z-index: 50;
    }
    .adm-topbar .page-heading {
      font-size: 1rem;
      font-weight: 600;
      color: var(--adm-text);
      flex: 1;
    }
    .btn-menu-toggle {
      display: none;
      background: none;
      border: none;
      color: var(--adm-text);
      font-size: 1.3rem;
      cursor: pointer;
      padding: .25rem;
    }
    .adm-content {
      padding: 1.75rem 1.5rem;
      flex: 1;
    }

    /* ── Cards ── */
    .adm-card {
      background: var(--adm-card);
      border: 1px solid var(--adm-border);
      border-radius: 12px;
      padding: 1.5rem;
    }
    .adm-card-title {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--adm-text);
    }

    /* ── Forms ── */
    .form-label { color: var(--adm-muted); font-size: .875rem; font-weight: 500; }
    .form-control, .form-select {
      background: var(--adm-dark);
      border: 1px solid var(--adm-border);
      color: var(--adm-text);
      border-radius: 8px;
    }
    .form-control:focus, .form-select:focus {
      background: var(--adm-dark);
      border-color: var(--adm-accent);
      box-shadow: 0 0 0 3px rgba(59,130,246,.2);
      color: var(--adm-text);
    }
    .form-control::placeholder { color: var(--adm-border); }
    .form-select option { background: var(--adm-dark); color: var(--adm-text); }
    textarea.form-control { min-height: 140px; resize: vertical; }

    /* ── Buttons ── */
    .btn-adm-primary {
      background: linear-gradient(135deg, var(--adm-accent), var(--adm-accent2));
      border: none; color: #fff; font-weight: 600;
      border-radius: 8px; padding: .55rem 1.25rem;
      transition: opacity .2s, transform .15s;
    }
    .btn-adm-primary:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
    .btn-adm-danger {
      background: rgba(239,68,68,.15);
      border: 1px solid rgba(239,68,68,.4);
      color: #f87171; border-radius: 8px; font-weight: 500;
      transition: background .15s;
    }
    .btn-adm-danger:hover { background: rgba(239,68,68,.28); color: #f87171; }

    /* ── Tables ── */
    .adm-table { width: 100%; border-collapse: collapse; }
    .adm-table th {
      background: var(--adm-dark);
      color: var(--adm-muted);
      font-size: .75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      padding: .75rem 1rem;
      border-bottom: 1px solid var(--adm-border);
      text-align: left;
    }
    .adm-table td {
      padding: .85rem 1rem;
      border-bottom: 1px solid var(--adm-border);
      font-size: .875rem;
      vertical-align: middle;
      color: var(--adm-text);
    }
    .adm-table tr:last-child td { border-bottom: none; }
    .adm-table tr:hover td { background: rgba(255,255,255,.03); }

    /* ── Badges ── */
    .badge-estado {
      display: inline-flex; align-items: center; gap: .3rem;
      padding: .25rem .65rem; border-radius: 20px; font-size: .75rem; font-weight: 600;
    }
    .badge-publicado { background: rgba(34,197,94,.15); color: #4ade80; border: 1px solid rgba(34,197,94,.3); }
    .badge-borrador  { background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }
    .badge-archivado { background: rgba(139,148,158,.15); color: var(--adm-muted); border: 1px solid var(--adm-border); }
    .badge-publicada { background: rgba(34,197,94,.15); color: #4ade80; border: 1px solid rgba(34,197,94,.3); }
    .badge-cerrada   { background: rgba(239,68,68,.15); color: #f87171; border: 1px solid rgba(239,68,68,.3); }

    /* ── Alerts ── */
    .alert-adm {
      border-radius: 8px; padding: .75rem 1rem; font-size: .875rem;
      display: flex; align-items: center; gap: .5rem; margin-bottom: 1.25rem;
    }
    .alert-adm-success { background: rgba(34,197,94,.12); border: 1px solid rgba(34,197,94,.3); color: #4ade80; }
    .alert-adm-danger  { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3); color: #f87171; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .adm-sidebar { transform: translateX(-100%); }
      .adm-sidebar.open { transform: translateX(0); }
      .adm-main { margin-left: 0; }
      .btn-menu-toggle { display: block; }
    }

    /* ── Overlay mobile ── */
    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,.6);
      z-index: 99;
    }
    .sidebar-overlay.visible { display: block; }
  </style>
</head>
<body>

<!-- Overlay móvil -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── Sidebar ── -->
<aside class="adm-sidebar" id="adminSidebar">
  <a href="/admin/index.php" class="adm-sidebar-brand">
    <img src="/assets/img/logo-SAZ.png" alt="SAZ">
    <span>SAZ CMS<small>Panel de administración</small></span>
  </a>

  <nav class="adm-nav">
    <a href="/admin/index.php" class="<?= admin_nav_active('index') ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="adm-nav-section">Contenido</div>
    <a href="/admin/noticias/index.php" class="<?= admin_nav_active('noticias') ?>">
      <i class="bi bi-newspaper"></i> Noticias
    </a>
    <a href="/admin/actividades/index.php" class="<?= admin_nav_active('actividades') ?>">
      <i class="bi bi-calendar-event"></i> Actividades
    </a>
    <a href="/admin/astrofotografia/index.php" class="<?= admin_nav_active('astrofotografia') ?>">
      <i class="bi bi-camera-fill"></i> Astrofotografía
    </a>
    <a href="/admin/convocatorias/index.php" class="<?= admin_nav_active('convocatorias') ?>">
      <i class="bi bi-megaphone-fill"></i> Convocatorias
    </a>
    <a href="/admin/eventos/index.php" class="<?= admin_nav_active('eventos') ?>">
      <i class="bi bi-stars"></i> Eventos
    </a>
    <a href="/admin/observaciones/index.php" class="<?= admin_nav_active('observaciones') ?>">
      <i class="bi bi-telescope-fill"></i> Observaciones
    </a>
    <a href="/admin/miembros/index.php" class="<?= admin_nav_active('miembros') ?>">
      <i class="bi bi-person-badge-fill"></i> Miembros / Directorio
    </a>
    <a href="/admin/colaboradores/index.php" class="<?= admin_nav_active('colaboradores') ?>">
      <i class="bi bi-people-fill"></i> Colaboradores
    </a>
    <a href="/admin/instituciones/index.php" class="<?= admin_nav_active('instituciones') ?>">
      <i class="bi bi-building-fill"></i> Instituciones
    </a>

    <div class="adm-nav-section">Administración</div>
    <a href="/admin/mensajes/index.php" class="<?= admin_nav_active('mensajes') ?>">
      <i class="bi bi-envelope-fill"></i> Mensajes
    </a>
    <a href="/admin/configuracion.php" class="<?= admin_nav_active('configuracion') ?>">
      <i class="bi bi-gear-fill"></i> Ajustes
    </a>

    <div class="adm-nav-section">Sitio</div>
    <a href="/index.php" target="_blank">
      <i class="bi bi-box-arrow-up-right"></i> Ver sitio
    </a>
  </nav>

  <div class="adm-sidebar-footer">
    <div class="admin-name"><?= $adminNombre ?></div>
    <div class="admin-role">Administrador</div>
    <a href="/admin/logout.php" class="btn-logout">
      <i class="bi bi-box-arrow-left"></i> Cerrar sesión
    </a>
  </div>
</aside>

<!-- ── Main ── -->
<div class="adm-main">
  <header class="adm-topbar">
    <button class="btn-menu-toggle" id="btnMenuToggle" aria-label="Abrir menú">
      <i class="bi bi-list"></i>
    </button>
    <span class="page-heading"><?= htmlspecialchars($pageTitle ?? 'Panel') ?></span>
  </header>

  <main class="adm-content">
    <?= $content ?? '' ?>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
  // Sidebar móvil
  const sidebar  = document.getElementById('adminSidebar');
  const overlay  = document.getElementById('sidebarOverlay');
  const btnToggle = document.getElementById('btnMenuToggle');

  function toggleSidebar() {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('visible');
  }
  btnToggle?.addEventListener('click', toggleSidebar);
  overlay?.addEventListener('click', toggleSidebar);

  // Confirmar eliminaciones
  document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!confirm(btn.dataset.confirm || '¿Eliminar este registro? Esta acción no se puede deshacer.')) {
        e.preventDefault();
      }
    });
  });

  // Auto-dismiss alerts
  setTimeout(() => {
    document.querySelectorAll('.alert-adm').forEach(el => {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    });
  }, 4000);

  // Carga e inicialización dinámica de TinyMCE
  if (document.querySelector('.tinymce-editor')) {
    const tinymceScript = document.createElement('script');
    tinymceScript.src = 'https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js';
    tinymceScript.referrerPolicy = 'origin';
    tinymceScript.onload = () => {
      tinymce.init({
        selector: '.tinymce-editor',
        skin: 'oxide-dark',
        content_css: 'dark',
        height: 400,
        menubar: false,
        plugins: 'lists link code table wordcount',
        toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link table code | removeformat',
        branding: false,
        promotion: false,
        setup: function (editor) {
          editor.on('change', function () {
            editor.save(); // Sincroniza con el textarea original
          });
        }
      });
    };
    document.head.appendChild(tinymceScript);
  }
</script>
<?php if (!empty($extraScripts)) echo $extraScripts; ?>
</body>
</html>
