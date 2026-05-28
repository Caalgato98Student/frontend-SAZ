<?php
require_once __DIR__ . '/../../../includes/security.php';
require_once __DIR__ . '/../../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../includes/db.php';
if ($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: index.php');exit;}
verify_csrf_token($_POST['csrf_token'] ?? '');
$id=intval($_POST['id']??0);
$eventoId=intval($_POST['evento_id']??0);
$pdo=get_pdo();
$row=$pdo->prepare("SELECT imagen,pdf FROM evento_ediciones WHERE id=? LIMIT 1");
$row->execute([$id]); $row=$row->fetch();
if ($row) {
    if ($row['imagen']) @unlink(__DIR__.'/../../../assets/img/eventos/'.$row['imagen']);
    if ($row['pdf'])    @unlink(__DIR__.'/../../../assets/pdf/'.$row['pdf']);
    $pdo->prepare("DELETE FROM evento_ediciones WHERE id=?")->execute([$id]);
}
header("Location: index.php?evento_id={$eventoId}&msg=eliminado"); exit;
