<?php
/**
 * pages/suscribirse/index.php
 * Formulario de suscripción con:
 *   - Token CSRF
 *   - Campo honeypot anti-bot
 *   - Validación y saneamiento server-side completo
 *   - Rate-limiting por sesión (máx. 3 envíos / 5 min)
 *   - Feedback visual de éxito o error
 */

// ── Configuración de página ──────────────────────────────────────
$pageTitle       = 'Suscribirse — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Únete a la Sociedad Astronómica de Zacatecas. Completa el formulario de suscripción.';
$basePath        = '../../';

// security.php ya fue cargado por base.php, pero si se accede
// directamente lo cargamos aquí también para garantizar protección.
if (!function_exists('generate_csrf_token')) {
    require_once __DIR__ . '/../../includes/security.php';
}

// ── Flag: formulario activo (false = sin backend conectado aún) ─
$formActivo = false;

// ── Variables de estado del formulario ──────────────────────────
$success  = false;
$errors   = [];
$formData = [
    'nombre'   => '',
    'correo'   => '',
    'telefono' => '',
    'interes'  => '',
    'mensaje'  => '',
];

$interesesValidos = ['Divulgacion', 'Astrofotografia', 'Observacion', 'Investigacion', 'Educacion'];

// ── Procesamiento del POST ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Verificar token CSRF
    $tokenRecibido = $_POST['csrf_token'] ?? '';
    verify_csrf_token($tokenRecibido);

    // 2. Honeypot — si tiene contenido, es un bot (rechazar silenciosamente)
    if (!is_bot($_POST['website'] ?? '')) {

        // 3. Rate-limiting
        $rateLimit = check_rate_limit('suscribirse');
        if (!$rateLimit['allowed']) {
            $minutos = ceil($rateLimit['seconds_left'] / 60);
            $errors[] = "Has enviado demasiadas solicitudes. Por favor espera {$minutos} minuto(s) e inténtalo de nuevo.";
        } else {

            // 4. Sanear y validar cada campo
            $nombre   = sanitize_text($_POST['nombre']   ?? '', 100);
            $correo   = sanitize_email($_POST['correo']  ?? '');
            $telefono = sanitize_phone($_POST['telefono'] ?? '');
            $interesRaw = sanitize_text($_POST['interes'] ?? '', 50);
            $mensaje  = sanitize_text($_POST['mensaje']  ?? '', 1000);

            // Validaciones de negocio
            if (mb_strlen($nombre) < 2) {
                $errors[] = 'El nombre debe tener al menos 2 caracteres.';
            }
            if ($correo === false) {
                $errors[] = 'El correo electrónico no es válido.';
            }
            if ($interesRaw !== '' && !in_array($interesRaw, $interesesValidos, true)) {
                $errors[] = 'El área de interés seleccionada no es válida.';
            }

            // Preservar valores para repintar el formulario si hay error
            $formData = [
                'nombre'   => $nombre,
                'correo'   => $correo !== false ? $correo : '',
                'telefono' => $telefono,
                'interes'  => $interesRaw,
                'mensaje'  => $mensaje,
            ];

            if (empty($errors)) {
                // ── TODO: Conectar con base de datos / enviar email ──────────
                // Cuando el backend esté listo, descomentar y conectar:
                // guardar_suscriptor($nombre, $correo, $telefono, $interesRaw, $mensaje);
                // $success = true;
                // $formData = array_fill_keys(array_keys($formData), '');
            }
        }
    }
    // Si es bot: no hacer nada (respuesta silenciosa para no dar pistas)
}

// ── Generar token CSRF para el formulario ───────────────────────
$csrfToken = generate_csrf_token();

// ── Capturar el contenido HTML ───────────────────────────────────
ob_start();
?>

<section class="py-5">
  <div class="container">
    <div class="col-lg-8 mx-auto">

      <!-- Encabezado -->
      <div class="text-center mb-5">
        <i class="bi bi-envelope-plus text-primary" style="font-size: 3rem;"></i>
        <h1 class="section-title mt-3">Suscribirse</h1>
        <p class="lead">Únete a la SAZ y recibe información sobre eventos, actividades y oportunidades de colaboración.</p>
      </div>

      <!-- Aviso: formulario próximamente disponible -->
      <?php if (!$formActivo): ?>
      <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="status" id="sub-aviso-pronto">
        <i class="bi bi-clock-history flex-shrink-0" aria-hidden="true"></i>
        <div>
          <strong>Próximamente disponible.</strong> Estamos preparando el sistema de registro. ¡Vuelve pronto para unirte a la SAZ!
        </div>
      </div>
      <?php endif; ?>

      <!-- Alertas de error -->
      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger mb-4" role="alert" id="sub-alerta-error">
        <div class="d-flex align-items-center gap-2 mb-2">
          <i class="bi bi-exclamation-triangle-fill flex-shrink-0" aria-hidden="true"></i>
          <strong>Por favor corrige los siguientes errores:</strong>
        </div>
        <ul class="mb-0 ps-3">
          <?php foreach ($errors as $err): ?>
          <li><?= $err /* Ya está escapado por sanitize_text */ ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- Formulario -->
      <div class="surface-card">
        <form class="row g-3" method="post" action="" novalidate id="form-suscribirse">

          <!-- Token CSRF (oculto) -->
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

          <!-- Honeypot: invisible para humanos, visible para bots -->
          <div style="display:none;" aria-hidden="true">
            <label for="sub-website">Página web (no rellenar)</label>
            <input type="text" id="sub-website" name="website" tabindex="-1" autocomplete="off" value="">
          </div>

          <!-- Nombre -->
          <div class="col-md-6">
            <label for="sub-nombre" class="form-label">Nombre completo <span class="text-danger" aria-hidden="true">*</span></label>
            <input
              type="text"
              class="form-control <?= !empty($errors) && mb_strlen($formData['nombre']) < 2 ? 'is-invalid' : '' ?>"
              id="sub-nombre"
              name="nombre"
              placeholder="Tu nombre completo"
              value="<?= htmlspecialchars($formData['nombre']) ?>"
              required
              maxlength="100"
              autocomplete="name">
            <div class="invalid-feedback">Por favor ingresa tu nombre completo.</div>
          </div>

          <!-- Correo -->
          <div class="col-md-6">
            <label for="sub-correo" class="form-label">Correo electrónico <span class="text-danger" aria-hidden="true">*</span></label>
            <input
              type="email"
              class="form-control"
              id="sub-correo"
              name="correo"
              placeholder="correo@ejemplo.com"
              value="<?= htmlspecialchars($formData['correo']) ?>"
              required
              maxlength="254"
              autocomplete="email">
            <div class="invalid-feedback">Por favor ingresa un correo electrónico válido.</div>
          </div>

          <!-- Teléfono -->
          <div class="col-md-6">
            <label for="sub-telefono" class="form-label">Teléfono</label>
            <input
              type="tel"
              class="form-control"
              id="sub-telefono"
              name="telefono"
              placeholder="(000) 000 0000"
              value="<?= htmlspecialchars($formData['telefono']) ?>"
              maxlength="20"
              autocomplete="tel">
          </div>

          <!-- Área de interés -->
          <div class="col-md-6">
            <label for="sub-interes" class="form-label">Área de interés</label>
            <select id="sub-interes" name="interes" class="form-select">
              <option value="" <?= $formData['interes'] === '' ? 'selected' : '' ?> disabled>Selecciona una opción</option>
              <?php foreach ($interesesValidos as $opcion): ?>
              <option value="<?= htmlspecialchars($opcion) ?>"
                <?= $formData['interes'] === $opcion ? 'selected' : '' ?>>
                <?= htmlspecialchars($opcion) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Mensaje -->
          <div class="col-12">
            <label for="sub-mensaje" class="form-label">Mensaje</label>
            <textarea
              id="sub-mensaje"
              name="mensaje"
              class="form-control"
              rows="4"
              placeholder="Cuéntanos por qué deseas unirte o cualquier información adicional"
              maxlength="1000"><?= htmlspecialchars($formData['mensaje']) ?></textarea>
            <div class="form-text text-end" id="sub-contador">0 / 1000</div>
          </div>

          <!-- Botón de envío -->
          <div class="col-12">
            <button type="submit" class="btn btn-primary" id="sub-btn-enviar" <?= !$formActivo ? 'disabled aria-disabled="true" title="Registro próximamente disponible"' : '' ?>>
              <i class="bi bi-send me-1"></i>Enviar solicitud
            </button>
            <?php if (!$formActivo): ?>
            <p class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>El registro estará habilitado próximamente.</p>
            <?php endif; ?>
          </div>

        </form>
      </div>

    </div>
  </div>
</section>

<script>
// Contador de caracteres para el campo mensaje
(function () {
  const textarea = document.getElementById('sub-mensaje');
  const counter  = document.getElementById('sub-contador');
  if (!textarea || !counter) return;

  function updateCount() {
    const len = textarea.value.length;
    counter.textContent = len + ' / 1000';
    counter.style.color = len > 900 ? 'var(--bs-danger)' : '';
  }
  textarea.addEventListener('input', updateCount);
  updateCount();
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
