<?php
/**
 * admin/auth.php — Guardián de autenticación del panel.
 *
 * Incluir al inicio de CADA página del panel con:
 *   require_once __DIR__ . '/../auth.php';
 *   require_admin_auth();
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true,
    ]);
}

/**
 * Verifica que el usuario esté autenticado como admin.
 * Si no, redirige al login y termina la ejecución.
 */
function require_admin_auth(): void
{
    if (empty($_SESSION['admin_id'])) {
        $loginUrl = str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2) . 'admin/login.php';
        header('Location: ' . $loginUrl);
        exit;
    }
}

/**
 * Devuelve el nombre del admin logueado.
 */
function admin_nombre(): string
{
    return htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin');
}
