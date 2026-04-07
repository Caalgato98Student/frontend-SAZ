<?php
$pageTitle       = 'Equinoccio de Primavera — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Evento de divulgacion sobre el equinoccio de primavera y observacion solar.';
$basePath        = '../../';
$eventoSlug      = 'equinoccio';

ob_start();
include __DIR__ . '/../../templates/evento.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
