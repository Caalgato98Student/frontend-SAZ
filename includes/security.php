<?php
/**
 * includes/security.php
 * ─────────────────────────────────────────────────────────────────
 * Módulo central de seguridad — Sociedad Astronómica de Zacatecas
 *
 * Provee:
 *   1. Cabeceras HTTP de seguridad (CSP, X-Frame-Options, etc.)
 *   2. Generación y verificación de tokens CSRF
 *   3. Saneamiento y validación de inputs
 *   4. Rate-limiting básico por sesión
 * ─────────────────────────────────────────────────────────────────
 */

// Iniciar sesión de forma segura si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,   // JS no puede leer la cookie de sesión
        'cookie_samesite' => 'Strict', // Previene CSRF vía cookie
        'use_strict_mode' => true,   // Rechaza IDs de sesión no iniciados por el servidor
    ]);
}

// ═══════════════════════════════════════════════════════════════
// 1. CABECERAS HTTP DE SEGURIDAD
// ═══════════════════════════════════════════════════════════════

/**
 * Emite todas las cabeceras de seguridad HTTP.
 * Llamar ANTES de cualquier salida HTML.
 */
function send_security_headers(): void
{
    // Previene que el sitio sea incrustado en iframes (clickjacking)
    header('X-Frame-Options: DENY');

    // El navegador no debe adivinar el tipo MIME de los recursos
    header('X-Content-Type-Options: nosniff');

    // Controla qué información de referencia se envía en las peticiones
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Restringe el acceso a APIs del navegador
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // ── Content Security Policy ──────────────────────────────────
    // Permite exactamente los recursos externos que usa el sitio:
    //   - Bootstrap 5.3 CSS/JS (cdn.jsdelivr.net)
    //   - Bootstrap Icons (cdn.jsdelivr.net)
    //   - Google Fonts (fonts.googleapis.com + fonts.gstatic.com)
    //   - Google Maps iframe (maps.google.com / www.google.com)
    //   - 'self' para todos los recursos propios
    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
        "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net",
        "img-src 'self' data: https:",
        "frame-src https://www.google.com",
        "connect-src 'self'",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
        "upgrade-insecure-requests",
    ]);
    header("Content-Security-Policy: $csp");
}

// Emitir cabeceras inmediatamente al incluir este archivo
send_security_headers();


// ═══════════════════════════════════════════════════════════════
// 2. TOKENS CSRF
// ═══════════════════════════════════════════════════════════════

/**
 * Genera (o reutiliza) un token CSRF y lo guarda en la sesión.
 *
 * @return string Token hexadecimal de 64 caracteres
 */
function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica que el token CSRF enviado coincida con el de la sesión.
 * Si falla, termina la ejecución con HTTP 403.
 *
 * @param string $token  Token recibido en el POST
 */
function verify_csrf_token(string $token): void
{
    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {
        http_response_code(403);
        exit('Solicitud no válida. Por favor recarga la página e inténtalo de nuevo.');
    }
    // Regenerar token tras uso exitoso (one-time token)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// ═══════════════════════════════════════════════════════════════
// 3. SANEAMIENTO Y VALIDACIÓN DE INPUTS
// ═══════════════════════════════════════════════════════════════

/**
 * Limpia un string de entrada: elimina espacios extra y caracteres peligrosos.
 *
 * @param  string $value  Valor crudo del formulario
 * @param  int    $maxLen Longitud máxima permitida
 * @return string         Valor limpio
 */
function sanitize_text(string $value, int $maxLen = 255): string
{
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return mb_substr($value, 0, $maxLen, 'UTF-8');
}

/**
 * Valida y limpia una dirección de correo electrónico.
 *
 * @param  string $email  Correo crudo
 * @return string|false   Correo limpio o false si es inválido
 */
function sanitize_email(string $email): string|false
{
    $email = trim($email);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

/**
 * Valida y limpia un número de teléfono (solo dígitos, espacios, +, -, ()).
 *
 * @param  string $phone  Teléfono crudo
 * @return string         Teléfono limpio (vacío si inválido)
 */
function sanitize_phone(string $phone): string
{
    $phone = trim($phone);
    // Permitir: dígitos, espacios, guiones, paréntesis, signo +
    $phone = preg_replace('/[^\d\s\+\-\(\)]/', '', $phone);
    return mb_substr($phone, 0, 20, 'UTF-8');
}

/**
 * Detecta si el campo honeypot fue rellenado (bot).
 *
 * @param  string $honeypotValue  Valor del campo oculto
 * @return bool   true si es un bot
 */
function is_bot(string $honeypotValue): bool
{
    return $honeypotValue !== '';
}


// ═══════════════════════════════════════════════════════════════
// 4. RATE-LIMITING POR SESIÓN
// ═══════════════════════════════════════════════════════════════

/**
 * Aplica un límite de envíos por formulario usando la sesión PHP.
 * Máximo $maxAttempts envíos en $windowSeconds segundos.
 *
 * @param  string $formKey        Clave única del formulario (ej. 'contacto', 'suscribirse')
 * @param  int    $maxAttempts    Número máximo de envíos permitidos (default: 3)
 * @param  int    $windowSeconds  Ventana de tiempo en segundos (default: 300 = 5 min)
 * @return array  ['allowed' => bool, 'seconds_left' => int]
 */
function check_rate_limit(
    string $formKey,
    int $maxAttempts = 3,
    int $windowSeconds = 300
): array {
    $sessionKey = "rate_limit_{$formKey}";
    $now        = time();

    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = ['count' => 0, 'window_start' => $now];
    }

    $data = &$_SESSION[$sessionKey];

    // Reiniciar ventana si ya expiró
    if ($now - $data['window_start'] > $windowSeconds) {
        $data = ['count' => 0, 'window_start' => $now];
    }

    if ($data['count'] >= $maxAttempts) {
        $secondsLeft = $windowSeconds - ($now - $data['window_start']);
        return ['allowed' => false, 'seconds_left' => max(0, $secondsLeft)];
    }

    $data['count']++;
    return ['allowed' => true, 'seconds_left' => 0];
}
