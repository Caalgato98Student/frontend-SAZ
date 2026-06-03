<?php
/**
 * admin/astrofotografia/categorias/eliminar.php — Eliminar categoría de astrofotografía.
 */
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
verify_csrf_token($_POST['csrf_token'] ?? '');

$id  = intval($_POST['id'] ?? 0);
$pdo = get_pdo();

try {
    $pdo->prepare("DELETE FROM astrofoto_categorias WHERE id = ?")->execute([$id]);
    header('Location: index.php?msg=eliminado');
} catch (\PDOException $e) {
    if ($e->getCode() === '23000') {
        header('Location: index.php?msg=error_eliminar');
    } else {
        header('Location: index.php?msg=error_eliminar');
    }
}
exit;
