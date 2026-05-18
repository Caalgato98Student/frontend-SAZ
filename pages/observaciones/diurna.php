<?php
$pageTitle       = 'Observacion Diurna — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Sesiones educativas de observacion diurna para publico general.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/observaciones.php';

$observacion = get_observacion_por_slug('diurna');

ob_start();
include __DIR__ . '/../../templates/observacion.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
