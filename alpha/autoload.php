<?php

function dd(...$vars)
{
    echo '<pre style="background-color: #1a1a1a; color: #f1f1f1; padding: 15px; border-radius: 5px; margin: 10px;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}


spl_autoload_register(function(string $class) {
    $prefix   = 'App\\';
    $base_dir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir
          . str_replace('\\', '/', $relative_class)
          . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 *
 * @param string $path
 * @return void
 * @throws RuntimeException
 */
function loadEnv(string $path): void
{
    if (!is_readable($path)) {
        throw new RuntimeException("Impossibile leggere il file .env in $path");
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1 => '']);
        if ($val === '') {
            continue;
        }
        if (preg_match('/^([\'"])(.*)\1$/', $val, $m)) {
            $val = $m[2];
        }
        putenv("$key=$val");
        $_ENV[$key]    = $val;
        $_SERVER[$key] = $val;
    }
}

// subito dopo l’autoloader, invoca il caricamento del .env
loadEnv(__DIR__ . '/.env');

// Utility: Config
function config($key = null) {
    static $config = null;
    if ($config === null) {
        if (!defined('USER_SOURCES_PATH')) {
            throw new \RuntimeException('The config() function can only be called after the SetDomain middleware has been executed.');
        }
        $config = require USER_SOURCES_PATH . '/config.php';
    }
    if ($key === null) return $config;
    return $config[$key] ?? null;
}


// Utility: Render view
function render($view, $params = []) {
    extract($params);
    $theme = THEME_DIR;
    $viewPath = __DIR__ . "/resources/views/{$theme}/{$view}.php";
    if (!file_exists($viewPath)) {
        throw new RuntimeException("View non trovata: $viewPath");
    }
    include $viewPath;
}

// Utility: Error handling
function abort($code = 404) {
    http_response_code($code);
    if ($code === 404) {
        render('errors/404');
    } else {
        echo "Errore $code";
    }
    exit;
}

// Utility: Escaping per le view
function eq($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Utility: Includi partial del tema attivo
function partial($name) {
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/partials/{$name}.php";
}

function partialAdmin($name) {
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/admin-partials/{$name}.php";
}