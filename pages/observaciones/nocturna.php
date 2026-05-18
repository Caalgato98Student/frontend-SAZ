<?php
$pageTitle       = 'Observacion Nocturna — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Sesiones de observacion nocturna de planetas, nebulosas y galaxias.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/observaciones.php';

$observacion = get_observacion_por_slug('nocturna');

ob_start();
include __DIR__ . '/../../templates/observacion.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
