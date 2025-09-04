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

// --- 4. Logica di reindirizzamento per la lingua ---
if (count($segments) === 1 && in_array($segments[0], $langs, true)) {
    $lang = $segments[0];
    header("Location: {$scheme}://{$host}/{$lang}/index", true, 302);
    return 0;
}
if (!isset($segments[0]) || !in_array($segments[0], $langs, true)) {
    $langToRedirect = $cookieLang ?: $defaultLang;
    $originalPath = $path;
    $targetPath = ($originalPath === '/') ? "/{$langToRedirect}/index" : "/{$langToRedirect}" . rtrim($originalPath, '/');
    header("Location: {$scheme}://{$host}{$targetPath}", true, 302);
    return 0;
}

// --- 5. Aggiorna il cookie della lingua ---
$currentLangInUrl = $segments[0];
if ($cookieLang !== $currentLangInUrl) {
    setcookie('otxlang', $currentLangInUrl, [
        'expires' => time() + 30 * 24 * 3600,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}