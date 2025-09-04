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

// --- 3. Salta i redirect per i file statici ---
if (isset($segments[0]) && in_array($segments[0], ['static', 'public'])) { 
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

// --- 5. CONTROLLO ACCESSI  ---
if ($config['is_site'] === false) {
    $area = $segments[1] ?? 'index'; // Se l'area non è specificata, è 'index'.

    // Se l'area richiesta NON è 'admin', mostra un errore 404 ed esci.
    if ($area !== 'admin') {
        (new \App\Controller\ErrorController())->notFound();
        return 0;
    }
}

// --- 6. Aggiorna il cookie della lingua ---
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