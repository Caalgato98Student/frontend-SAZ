<?php
/**
 * admin/miembros/eliminar.php — Soft delete: activo=0.
 * Solo acepta POST con CSRF.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
verify_csrf_token($_POST['csrf_token'] ?? '');

$id  = intval($_POST['id'] ?? 0);
$pdo = get_pdo();

// Soft delete: desactivar en lugar de borrar
$pdo->prepare("UPDATE miembros SET activo = 0 WHERE id = ?")->execute([$id]);

header('Location: index.php?msg=eliminado'); exit;