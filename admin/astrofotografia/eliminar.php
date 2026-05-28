<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
if ($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: index.php');exit;}
verify_csrf_token($_POST['csrf_token'] ?? '');
$id = intval($_POST['id'] ?? 0);
$pdo = get_pdo();
$row = $pdo->prepare("SELECT imagen FROM astrofotografia WHERE id=? LIMIT 1");
$row->execute([$id]);
$row = $row->fetch();
if ($row) {
    if (!empty($row['imagen'])) @unlink(__DIR__.'/../../assets/img/astrofotografia/'.$row['imagen']);
    $pdo->prepare("DELETE FROM astrofotografia WHERE id=?")->execute([$id]);
}
header('Location: index.php?msg=eliminado'); exit;
