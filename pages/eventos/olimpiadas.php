<?php
$pageTitle       = 'Olimpiadas de Astronomia — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Competencia estatal de conocimientos astronomicos para estudiantes de bachillerato.';
$basePath        = '../../';
$eventoSlug      = 'olimpiadas';

ob_start();
include __DIR__ . '/../../templates/evento.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
