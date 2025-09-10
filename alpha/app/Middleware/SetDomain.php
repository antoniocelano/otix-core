<?php

$domains = require __DIR__ . '/../../config/Domains.php';

// l'host dalla richiesta.
$currentHost = $http['host']; 

// dominio per lo sviluppo
$selected = $domains['_selected'] ?? '';
if ($selected && isset($domains[$selected])) {
    $currentHost = $selected;
}

// dominio corrente
$domainConfig = $domains[$currentHost];

// accesso diretto ai valori
$theme = $domainConfig['theme'];
$domainCode = $domainConfig['usr'];
$envFile = $domainConfig['env'];

define('THEME_DIR', $theme);
define('DOMAIN_CODE', $domainCode);
define('USER_SOURCES_PATH', realpath(BASE_PATH . '/users/' . $domainCode . '/sources'));
define('USER_ROUTES_PATH', realpath(BASE_PATH . '/users/' . $domainCode . '/routes'));

// Chiama la funzione globale loadEnv() definita in autoload.php
$envPath = BASE_PATH . '/' . ltrim($envFile, '/');
loadEnv($envPath);

return [
    'domain_code' => $domainCode,
    'theme' => $theme,
    'env' => $envFile,
];