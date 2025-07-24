<?php

$domains = require __DIR__ . '/../../sources/domains.php';

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
define('USER_SOURCES_PATH', BASE_PATH . '/users/' . $domainCode . '/sources');

function loadEnvFile($file) {
    if (!file_exists($file)) return;
    foreach (file($file) as $line) {
        if (preg_match('/^([A-Z0-9_]+)=(.*)$/', trim($line), $m)) {
            putenv("{$m[1]}={$m[2]}");
            $_ENV[$m[1]] = $m[2];
        }
    }
}

$envPath = __DIR__ . '/../../' . ltrim($envFile, '/');
loadEnvFile($envPath);


return [
    'domain_code' => $domainCode,
    'theme' => $theme,
    'env' => $envFile,
];