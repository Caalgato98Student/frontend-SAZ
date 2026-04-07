<?php
$pageTitle       = 'Semana Mundial del Espacio — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Celebracion internacional de la Semana Mundial del Espacio organizada por la SAZ.';
$basePath        = '../../';
$eventoSlug      = 'semana-espacio';

ob_start();
include __DIR__ . '/../../templates/evento.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
