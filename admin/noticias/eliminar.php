<?php
/**
 * admin/noticias/eliminar.php — Eliminar una noticia (POST).
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verify_csrf_token($_POST['csrf_token'] ?? '');

$id  = intval($_POST['id'] ?? 0);
$pdo = get_pdo();

$stmt = $pdo->prepare("SELECT imagen FROM noticias WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if ($noticia) {
    // Eliminar imagen asociada si existe
    if (!empty($noticia['imagen'])) {
        $imgPath = __DIR__ . '/../../assets/img/noticias/' . $noticia['imagen'];
        if (file_exists($imgPath)) unlink($imgPath);
    }
    $pdo->prepare("DELETE FROM noticias WHERE id = ?")->execute([$id]);
}

header('Location: index.php?msg=eliminado');
exit;
