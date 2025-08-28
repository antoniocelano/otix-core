<?php

define('VIEW_CACHE_ENABLED', true); // cache on / off
define('VIEW_CACHE_PATH', __DIR__ . '/storage/cache/views'); // cache folder    
define('VIEW_CACHE_LIFETIME', 3600); // 1 hour

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

// env
loadEnv(__DIR__ . '/.env');

// user config
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

function disableCache()
{
    $GLOBALS['view_cache'] = true;
}

function minify_html(string $buffer): string
{
    $search = [
        // spazi
        '/>\s+</s',
        '/(\s)+/s',
        // commenti
        '//'
    ];
    $replace = [
        '><',
        '\\1',
        ''
    ];

    return preg_replace($search, $replace, $buffer);
}

// render view
function render($view, $params = []) {
    if (VIEW_CACHE_ENABLED) {
        // key view cache
        $cacheKey = md5($view . serialize($params));
        $cacheFile = VIEW_CACHE_PATH . '/' . $cacheKey . '.html';

        // cache in cache
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < VIEW_CACHE_LIFETIME) {
            echo file_get_contents($cacheFile);
            return; // open view
        }
    }

    // generate view
    ob_start();

    extract($params);
    $theme = THEME_DIR;
    $viewPath = __DIR__ . "/resources/views/{$theme}/{$view}.php";
    
    if (!file_exists($viewPath)) {
        $baseViewPath = __DIR__ . "/resources/views/{$view}.php";
        if (!file_exists($baseViewPath)) {
            ob_end_clean();
            throw new RuntimeException("View non trovata: $viewPath");
        }
        include $baseViewPath;
    } else {
        include $viewPath;
    }
    
    $content = ob_get_clean(); // get content
    $minifiedContent = minify_html($content); // serialize html

    // save cache if not disabled
    if (VIEW_CACHE_ENABLED && empty($GLOBALS['view_cache'])) {
        if (!is_dir(VIEW_CACHE_PATH)) {
            mkdir(VIEW_CACHE_PATH, 0775, true);
        }
        file_put_contents($cacheFile, $minifiedContent);
    }
    
    // unset cache
    unset($GLOBALS['view_cache']);

    echo $minifiedContent; // show html
}

// errors
function abort($code = 404) {
    http_response_code($code);
    if ($code === 404) {
        render('errors/404');
    } else {
        echo "Errore $code";
    }
    exit;
}

// eq for views
function eq($string) {
    return htmlspecialchars((string) $string, ENT_QUOTES, 'UTF-8');
}

// partials
function partial($name) {
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/partials/{$name}.php";
}

function partialAdmin($name) {
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/admin-partials/{$name}.php";
}

function partialHub($name) {
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/hub-partials/{$name}.php";
}

function csrf_field()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}


function sanitize_input($value, string $type)
{
    switch ($type) {
        case 'str':
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        case 'int':
            return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        case 'email':
            return filter_var($value, FILTER_SANITIZE_EMAIL);
        default:
            abort(400);
            break;
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function formatDate($date) {
    return date('d/m/Y H:i:s', strtotime($date));
}

function enc_path(string $p): string {
    $p = rtrim($p, '/');
    $parts = array_filter(explode('/', $p), 'strlen');
    return implode('/', array_map('rawurlencode', $parts)) . '/';
}