<?php
/**
 * admin/instituciones/eliminar.php — Eliminar una institución colaboradora y su logotipo.
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

// Obtener detalles para borrar imagen física
$stmt = $pdo->prepare("SELECT imagen FROM instituciones WHERE id = ?");
$stmt->execute([$id]);
$inst = $stmt->fetch();

if ($inst) {
    if ($inst['imagen']) {
        $filePath = __DIR__ . '/../../assets/img/instituciones/' . $inst['imagen'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
    
    // Eliminar registro de la base de datos
    $pdo->prepare("DELETE FROM instituciones WHERE id = ?")->execute([$id]);
}

header('Location: index.php?msg=eliminado');
exit;
