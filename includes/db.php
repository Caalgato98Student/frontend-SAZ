<?php
/**
 * includes/db.php — Conexión singleton a la base de datos.
 *
 * Provee una única instancia de PDO reutilizable en todo el proyecto.
 * Se configura con las constantes definidas en config.php.
 *
 * Uso:
 *   require_once __DIR__ . '/../config.php';
 *   require_once __DIR__ . '/db.php';
 *   $pdo = get_pdo();
 *   $stmt = $pdo->prepare('SELECT * FROM noticias WHERE slug = ?');
 */

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            DB_HOST,
            defined('DB_PORT') ? DB_PORT : 3306,
            DB_NAME
        );

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    return $pdo;
}
