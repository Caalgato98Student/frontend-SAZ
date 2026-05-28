<?php
/**
 * scripts/crear_admin.php
 * ─────────────────────────────────────────────────────────────────
 * Script de USO ÚNICO para crear el primer usuario administrador.
 * Ejecutar desde el navegador UNA VEZ y luego eliminar o proteger.
 *
 * URL: http://localhost/frontend-SAZ/scripts/crear_admin.php
 * ─────────────────────────────────────────────────────────────────
 */

// ── Configuración del primer admin ──────────────────────────────
// MODIFICA ESTOS VALORES antes de ejecutar el script
$ADMIN_NOMBRE   = 'Administrador SAZ';
$ADMIN_USUARIO  = 'admin';
$ADMIN_EMAIL    = 'sazac2010@gmail.com';
$ADMIN_PASSWORD = 'SAZ2025admin!';   // Cambia esto por una contraseña segura
// ────────────────────────────────────────────────────────────────

// Solo permitir desde localhost por seguridad
$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
    http_response_code(403);
    exit('<h1>403 — Solo accesible desde localhost.</h1>');
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$pdo  = get_pdo();
$hash = password_hash($ADMIN_PASSWORD, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO admin_usuarios (nombre, usuario, email, hash, activo)
         VALUES (?, ?, ?, ?, 1)"
    );
    $stmt->execute([$ADMIN_NOMBRE, $ADMIN_USUARIO, $ADMIN_EMAIL, $hash]);

    echo "<h2 style='font-family:sans-serif;color:green'>✅ Usuario admin creado exitosamente</h2>";
    echo "<pre style='font-family:monospace'>";
    echo "  Usuario:    {$ADMIN_USUARIO}\n";
    echo "  Contraseña: {$ADMIN_PASSWORD}\n";
    echo "  Email:      {$ADMIN_EMAIL}\n";
    echo "</pre>";
    echo "<p style='font-family:sans-serif'><strong>⚠️ IMPORTANTE:</strong> Elimina o protege este archivo ahora que ya lo usaste.</p>";
    echo "<p><a href='../admin/login.php'>→ Ir al panel de administración</a></p>";

} catch (\PDOException $e) {
    if ($e->getCode() === '23000') {
        echo "<h2 style='font-family:sans-serif;color:orange'>⚠️ El usuario o email ya existe en la base de datos.</h2>";
        echo "<p style='font-family:sans-serif'>Ya hay un administrador registrado. Ve directamente al <a href='../admin/login.php'>panel de administración</a>.</p>";
    } else {
        echo "<h2 style='font-family:sans-serif;color:red'>❌ Error al crear el usuario:</h2>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    }
}
