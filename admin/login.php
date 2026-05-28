<?php
/**
 * admin/login.php — Formulario de acceso al panel CMS.
 */
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/auth.php';

// Si ya está autenticado, redirigir al dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verificar CSRF
    verify_csrf_token($_POST['csrf_token'] ?? '');

    // 2. Rate limiting (máx. 5 intentos en 10 min)
    $rl = check_rate_limit('admin_login', 5, 600);
    if (!$rl['allowed']) {
        $mins = ceil($rl['seconds_left'] / 60);
        $error = "Demasiados intentos fallidos. Intenta de nuevo en {$mins} minuto(s).";
    } else {
        $usuario = trim($_POST['usuario'] ?? '');
        $pass    = $_POST['password'] ?? '';

        if ($usuario === '' || $pass === '') {
            $error = 'Completa todos los campos.';
        } else {
            $pdo  = get_pdo();
            $stmt = $pdo->prepare(
                "SELECT id, nombre, hash FROM admin_usuarios
                  WHERE (usuario = ? OR email = ?) AND activo = 1 LIMIT 1"
            );
            $stmt->execute([$usuario, $usuario]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($pass, $admin['hash'])) {
                // Login exitoso
                session_regenerate_id(true);
                $_SESSION['admin_id']     = $admin['id'];
                $_SESSION['admin_nombre'] = $admin['nombre'];

                // Actualizar último login
                $pdo->prepare("UPDATE admin_usuarios SET ultimo_login = NOW() WHERE id = ?")
                    ->execute([$admin['id']]);

                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        }
    }
}

$csrfToken = generate_csrf_token();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso al Panel — SAZ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="../assets/img/logo-SAZ.png">
  <style>
    :root {
      --saz-dark:    #0d1117;
      --saz-card:    #161b22;
      --saz-border:  #30363d;
      --saz-accent:  #3b82f6;
      --saz-accent2: #6366f1;
      --saz-text:    #e6edf3;
      --saz-muted:   #8b949e;
    }
    * { box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--saz-dark);
      color: var(--saz-text);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }
    .login-wrap {
      width: 100%;
      max-width: 420px;
    }
    .login-logo {
      text-align: center;
      margin-bottom: 2rem;
    }
    .login-logo img {
      width: 70px;
      height: 70px;
      object-fit: contain;
      filter: drop-shadow(0 0 12px rgba(59,130,246,.5));
    }
    .login-logo h1 {
      font-size: 1.25rem;
      font-weight: 700;
      margin-top: .75rem;
      color: var(--saz-text);
    }
    .login-logo p {
      font-size: .85rem;
      color: var(--saz-muted);
      margin: 0;
    }
    .login-card {
      background: var(--saz-card);
      border: 1px solid var(--saz-border);
      border-radius: 14px;
      padding: 2rem;
      box-shadow: 0 8px 32px rgba(0,0,0,.4);
    }
    .form-label { color: var(--saz-muted); font-size: .875rem; font-weight: 500; }
    .form-control {
      background: var(--saz-dark);
      border: 1px solid var(--saz-border);
      color: var(--saz-text);
      border-radius: 8px;
      padding: .65rem 1rem;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus {
      background: var(--saz-dark);
      border-color: var(--saz-accent);
      box-shadow: 0 0 0 3px rgba(59,130,246,.2);
      color: var(--saz-text);
    }
    .form-control::placeholder { color: var(--saz-border); }
    .btn-login {
      background: linear-gradient(135deg, var(--saz-accent), var(--saz-accent2));
      border: none;
      border-radius: 8px;
      color: #fff;
      font-weight: 600;
      padding: .75rem;
      width: 100%;
      font-size: 1rem;
      letter-spacing: .02em;
      transition: opacity .2s, transform .15s;
    }
    .btn-login:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
    .btn-login:active { transform: translateY(0); }
    .alert-danger {
      background: rgba(220,53,69,.15);
      border: 1px solid rgba(220,53,69,.4);
      color: #f87171;
      border-radius: 8px;
      font-size: .875rem;
    }
    .input-group-text {
      background: var(--saz-dark);
      border: 1px solid var(--saz-border);
      border-right: none;
      color: var(--saz-muted);
    }
    .input-group .form-control { border-left: none; }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-logo">
      <img src="../assets/img/logo-SAZ.png" alt="Logo SAZ">
      <h1>Panel CMS</h1>
      <p>Sociedad Astronómica de Zacatecas</p>
    </div>

    <div class="login-card">
      <h2 class="h5 mb-4 fw-semibold">Iniciar sesión</h2>

      <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="mb-3">
          <label for="usuario" class="form-label">Usuario o correo</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" class="form-control" id="usuario" name="usuario"
                   placeholder="admin" autocomplete="username" required
                   value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
          </div>
        </div>

        <div class="mb-4">
          <label for="password" class="form-label">Contraseña</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="••••••••" autocomplete="current-password" required>
          </div>
        </div>

        <button type="submit" id="btn-login" class="btn-login">
          <i class="bi bi-box-arrow-in-right me-2"></i>Entrar al panel
        </button>
      </form>
    </div>

    <p class="text-center mt-3" style="font-size:.8rem;color:var(--saz-muted)">
      <a href="../index.php" style="color:var(--saz-muted)">← Volver al sitio</a>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
