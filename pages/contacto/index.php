<?php
/**
 * pages/contacto/index.php
 * Página de contacto con:
 *   - Token CSRF
 *   - Campo honeypot anti-bot
 *   - Validación y saneamiento server-side completo
 *   - Rate-limiting por sesión (máx. 3 envíos / 5 min)
 *   - Feedback visual de éxito o error
 */

// ── Configuración de página ──────────────────────────────────────
$pageTitle       = 'Contacto — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Contacta a la Sociedad Astronómica de Zacatecas. Formulario, redes sociales y ubicación.';
$basePath        = '../../';

// Cargar módulo de seguridad si no está cargado aún
if (!function_exists('generate_csrf_token')) {
    require_once __DIR__ . '/../../includes/security.php';
}

// ── Flag: formulario activo (false = sin backend conectado aún) ─
$formActivo = false;

// ── Variables de estado del formulario ──────────────────────────
$success  = false;
$errors   = [];
$formData = [
    'nombre'  => '',
    'correo'  => '',
    'asunto'  => '',
    'mensaje' => '',
];

// ── Procesamiento del POST ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Verificar token CSRF
    $tokenRecibido = $_POST['csrf_token'] ?? '';
    verify_csrf_token($tokenRecibido);

    // 2. Honeypot — si está relleno, es un bot
    if (!is_bot($_POST['website'] ?? '')) {

        // 3. Rate-limiting
        $rateLimit = check_rate_limit('contacto');
        if (!$rateLimit['allowed']) {
            $minutos = ceil($rateLimit['seconds_left'] / 60);
            $errors[] = "Has enviado demasiados mensajes. Por favor espera {$minutos} minuto(s) e inténtalo de nuevo.";
        } else {

            // 4. Sanear y validar cada campo
            $nombre  = sanitize_text($_POST['nombre']  ?? '', 100);
            $correo  = sanitize_email($_POST['correo'] ?? '');
            $asunto  = sanitize_text($_POST['asunto']  ?? '', 150);
            $mensaje = sanitize_text($_POST['mensaje'] ?? '', 2000);

            // Validaciones de negocio
            if (mb_strlen($nombre) < 2) {
                $errors[] = 'El nombre debe tener al menos 2 caracteres.';
            }
            if ($correo === false) {
                $errors[] = 'El correo electrónico no es válido.';
            }
            if (mb_strlen(trim($mensaje)) < 10) {
                $errors[] = 'El mensaje debe tener al menos 10 caracteres.';
            }

            // Preservar valores si hay error
            $formData = [
                'nombre'  => $nombre,
                'correo'  => $correo !== false ? $correo : '',
                'asunto'  => $asunto,
                'mensaje' => $mensaje,
            ];

            if (empty($errors)) {
                // ── TODO: Conectar con backend / enviar email ──────────────
                // Cuando el backend esté listo, descomentar y conectar:
                // enviar_email_contacto($nombre, $correo, $asunto, $mensaje);
                // $success = true;
                // $formData = array_fill_keys(array_keys($formData), '');
            }
        }
    }
    // Bot: respuesta silenciosa
}

// ── Generar token CSRF ───────────────────────────────────────────
$csrfToken = generate_csrf_token();

// ── Capturar contenido HTML ──────────────────────────────────────
ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Contacto</h1>
    <div class="row g-4">

      <!-- Columna del formulario -->
      <div class="col-lg-7">
        <div class="surface-card h-100">
          <h2 class="h5 mb-3"><i class="bi bi-chat-dots me-2"></i>Enviar mensaje</h2>

          <!-- Aviso: formulario próximamente disponible -->
          <?php if (!$formActivo): ?>
          <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="status" id="ct-aviso-pronto">
            <i class="bi bi-clock-history flex-shrink-0" aria-hidden="true"></i>
            <div>
              <strong>Próximamente disponible.</strong> Estamos habilitando el formulario de contacto. Por ahora puedes escribirnos directamente a <strong>sazac2010@gmail.com</strong>
            </div>
          </div>
          <?php endif; ?>

          <!-- Alertas de error -->
          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger mb-4" role="alert" id="ct-alerta-error">
            <div class="d-flex align-items-center gap-2 mb-2">
              <i class="bi bi-exclamation-triangle-fill flex-shrink-0" aria-hidden="true"></i>
              <strong>Por favor corrige los siguientes errores:</strong>
            </div>
            <ul class="mb-0 ps-3">
              <?php foreach ($errors as $err): ?>
              <li><?= $err ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <form class="row g-3" method="post" action="" novalidate id="form-contacto">

            <!-- Token CSRF (oculto) -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Honeypot -->
            <div style="display:none;" aria-hidden="true">
              <label for="ct-website">Página web (no rellenar)</label>
              <input type="text" id="ct-website" name="website" tabindex="-1" autocomplete="off" value="">
            </div>

            <!-- Nombre -->
            <div class="col-md-6">
              <label for="ct-nombre" class="form-label">Nombre <span class="text-danger" aria-hidden="true">*</span></label>
              <input
                type="text"
                class="form-control"
                id="ct-nombre"
                name="nombre"
                placeholder="Tu nombre"
                value="<?= htmlspecialchars($formData['nombre']) ?>"
                required
                maxlength="100"
                autocomplete="name">
              <div class="invalid-feedback">Por favor ingresa tu nombre.</div>
            </div>

            <!-- Correo -->
            <div class="col-md-6">
              <label for="ct-correo" class="form-label">Correo electrónico <span class="text-danger" aria-hidden="true">*</span></label>
              <input
                type="email"
                class="form-control"
                id="ct-correo"
                name="correo"
                placeholder="correo@ejemplo.com"
                value="<?= htmlspecialchars($formData['correo']) ?>"
                required
                maxlength="254"
                autocomplete="email">
              <div class="invalid-feedback">Por favor ingresa un correo válido.</div>
            </div>

            <!-- Asunto -->
            <div class="col-12">
              <label for="ct-asunto" class="form-label">Asunto</label>
              <input
                type="text"
                class="form-control"
                id="ct-asunto"
                name="asunto"
                placeholder="Asunto del mensaje"
                value="<?= htmlspecialchars($formData['asunto']) ?>"
                maxlength="150">
            </div>

            <!-- Mensaje -->
            <div class="col-12">
              <label for="ct-mensaje" class="form-label">Mensaje <span class="text-danger" aria-hidden="true">*</span></label>
              <textarea
                id="ct-mensaje"
                name="mensaje"
                class="form-control"
                rows="5"
                placeholder="Escribe tu mensaje"
                required
                maxlength="2000"><?= htmlspecialchars($formData['mensaje']) ?></textarea>
              <div class="form-text text-end" id="ct-contador">0 / 2000</div>
              <div class="invalid-feedback">El mensaje debe tener al menos 10 caracteres.</div>
            </div>

            <!-- Botón de envío -->
            <div class="col-12">
              <button type="submit" class="btn btn-primary" id="ct-btn-enviar" <?= !$formActivo ? 'disabled aria-disabled="true" title="Formulario próximamente disponible"' : '' ?>>
                <i class="bi bi-send me-1"></i>Enviar mensaje
              </button>
              <?php if (!$formActivo): ?>
              <p class="form-text mt-2"><i class="bi bi-info-circle me-1"></i>El formulario estará habilitado próximamente.</p>
              <?php endif; ?>
            </div>

          </form>
        </div>
      </div>

      <!-- Columna de información -->
      <div class="col-lg-5">
        <div class="surface-card mb-4">
          <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2"></i>Información de contacto</h2>
          <p class="mb-2"><i class="bi bi-envelope me-2"></i><strong>Correo:</strong> sazac2010@gmail.com</p>
          <p class="mb-2"><i class="bi bi-telephone me-2"></i><strong>Teléfono:</strong> 492 123 16 39</p>
          <p class="mb-0"><i class="bi bi-geo-alt me-2"></i><strong>Dirección:</strong> Zacatecas, Zacatecas, México</p>
        </div>

        <div class="surface-card mb-4">
          <h2 class="h5 mb-3"><i class="bi bi-share me-2" aria-hidden="true"></i>Redes sociales</h2>
          <div class="d-flex flex-wrap gap-3">
            <a href="https://www.facebook.com/SAZacatecas" class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer" aria-label="Visitar el Facebook de la Sociedad Astronómica de Zacatecas">
              <i class="bi bi-facebook me-1" aria-hidden="true"></i>Facebook
            </a>
            <a href="https://www.instagram.com/sazacatecas/" class="btn btn-outline-danger" target="_blank" rel="noopener noreferrer" aria-label="Visitar el Instagram de la Sociedad Astronómica de Zacatecas">
              <i class="bi bi-instagram me-1" aria-hidden="true"></i>Instagram
            </a>
            <a href="https://x.com/ndezacmx" class="btn btn-outline-dark" target="_blank" rel="noopener noreferrer" aria-label="Visitar el perfil de X de la Sociedad Astronómica de Zacatecas">
              <i class="bi bi-twitter-x me-1" aria-hidden="true"></i>X
            </a>
          </div>
        </div>

        <div class="surface-card">
          <h2 class="h5 mb-3"><i class="bi bi-geo-alt me-2"></i>Ubicación</h2>
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29068.03782367076!2d-102.58324!3d22.7711!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86824ebbf47eaaa5%3A0x2c96536bfa1fe2ec!2sZacatecas%2C%20Zac.%2C%20Mexico!5e0!3m2!1ses!2smx!4v1680000000000!5m2!1ses!2smx"
            width="100%" height="250" style="border:0; border-radius: 0.75rem;"
            allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Ubicación de la SAZ en Zacatecas">
          </iframe>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
// Contador de caracteres para el campo mensaje
(function () {
  const textarea = document.getElementById('ct-mensaje');
  const counter  = document.getElementById('ct-contador');
  if (!textarea || !counter) return;

  function updateCount() {
    const len = textarea.value.length;
    counter.textContent = len + ' / 2000';
    counter.style.color = len > 1800 ? 'var(--bs-danger)' : '';
  }
  textarea.addEventListener('input', updateCount);
  updateCount();
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
