<?php
/**
 * pages/eventos/semanadelaastronomia.php
 * Página específica para el evento de la Semana de la Astronomía.
 */

$pageTitle       = 'Semana de la Astronomía — SAZ';
$pageDescription = 'Celebramos 15 años de pasión por el cosmos con el evento de divulgación científica más importante de la región. Una semana intensiva de conferencias, talleres y observación.';
$basePath        = '../../';
$eventoSlug      = 'semana-astronomia';

ob_start();

include __DIR__ . '/../../templates/evento.php'; 
$content = ob_get_clean();

include __DIR__ . '/../../base.php';
