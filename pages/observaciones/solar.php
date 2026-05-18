<?php
$pageTitle       = 'Observacion Solar — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Sesiones de observacion segura del Sol organizadas por la SAZ.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/observaciones.php';

$observacion = get_observacion_por_slug('solar');

ob_start();
include __DIR__ . '/../../templates/observacion.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
