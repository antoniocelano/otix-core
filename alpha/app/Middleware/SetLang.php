<?php

// --- 1. Carica la configurazione dell'utente ---
$config = require USER_SOURCES_PATH . '/config.php';
$langs = $config['langs'] ?? ['it'];
$defaultLang = $config['default_lang'] ?? $langs[0];

// --- 2. Ottieni i dati necessari dalla richiesta HTTP ---
$path = parse_url($http['uri'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $path), fn($s) => $s !== ''));
$cookieLang = $http['cookies']['otxlang'] ?? null;
$scheme = $http['scheme'];
$host = $http['host'];
$isSecure = ($scheme === 'https');

// --- 3. Salta i redirect per i file statici e per le rotte di sistema che non usano la lingua ---
$allowedPaths = ['static', 'public', 'bucket'];
if (isset($segments[0]) && in_array($segments[0], $allowedPaths)) { 
    return;
}