<?php

// Importa la classe CheckRequest per l'utilizzo nella funzione http().
use App\Middleware\CheckRequest;

// --- Configurazione della cache delle viste ---
// Abilita/disabilita la cache delle viste.
define('VIEW_CACHE_ENABLED', true);
// Definisce il percorso dove verranno salvati i file di cache delle viste.
define('VIEW_CACHE_PATH', __DIR__ . '/storage/cache/views');
// Imposta la durata della cache in secondi (qui 1 ora).
define('VIEW_CACHE_LIFETIME', 3600);

/**
 * Funzione di debug che "dà un dump e muore".
 * Simile a var_dump() e die(), ma con una formattazione più leggibile.
 *
 * @param mixed ...$vars Variabili da ispezionare.
 */
function dd(...$vars)
{
    echo '<pre style="background-color: #1a1a1a; color: #f1f1f1; padding: 15px; border-radius: 5px; margin: 10px;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

/**
 * Registra una funzione di autoloading per le classi.
 * Questa funzione carica automaticamente i file delle classi basandosi sul loro namespace.
 *
 * @param string $class Il nome della classe.
 */
spl_autoload_register(function (string $class) {
    // Definisce una mappa di prefissi di namespace e i loro percorsi base corrispondenti.
    $prefixes = [
        'App\\' => __DIR__ . '/app/',
        'PHPMailer\\PHPMailer\\' => __DIR__ . '/vendor/phpmailer/',
        'App\\Vendor\\' => __DIR__ . '/vendor/App/'
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        // Calcola la lunghezza del prefisso.
        $len = strlen($prefix);
        // Se il prefisso della classe non corrisponde, passa al prossimo.
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        // Ottiene il nome della classe relativo al prefisso del namespace.
        $relative_class = substr($class, $len);
        // Costruisce il percorso del file, sostituendo i backslash con slash.
        $file = $base_dir
            . str_replace('\\', '/', $relative_class)
            . '.php';

        // Se il file esiste, lo include e termina la ricerca.
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

/**
 * Carica le variabili d'ambiente da un file .env.
 *
 * @param string $path Il percorso del file .env.
 * @return void
 * @throws RuntimeException Se il file non è leggibile.
 */
function loadEnv(string $path): void
{
    if (!is_readable($path)) {
        throw new RuntimeException("Impossibile leggere il file .env in $path");
    }

    // Legge il file riga per riga, ignorando righe vuote e i commenti (#).
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        // Separa la chiave dal valore usando '=' come delimitatore.
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1 => '']);
        if ($val === '') {
            continue;
        }
        // Rimuove le virgolette se il valore è racchiuso tra singole o doppie virgolette.
        if (preg_match('/^([\'"])(.*)\1$/', $val, $m)) {
            $val = $m[2];
        }
        // Imposta la variabile d'ambiente a livello di processo e nelle superglobali.
        putenv("$key=$val");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}
$domains = require __DIR__ . '/config/Domains.php';
$selected = $domains['_selected'] ?? '';
if ($selected && isset($domains[$selected])) {
    $currentHost = $selected;
}
$domainConfig = $domains[$currentHost];

// Carica le variabili d'ambiente dal file .env principale.
loadEnv(__DIR__ . '/'. $domainConfig['env']);

/**
 * Ottiene le configurazioni specifiche dell'utente da un file di configurazione.
 * La configurazione viene caricata una sola volta (static caching).
 *
 * @param string|null $key La chiave della configurazione da recuperare. Se null, restituisce l'intero array di configurazione.
 * @return mixed Il valore della configurazione o null se la chiave non esiste.
 */
function config($key = null)
{
    // Usa una variabile statica per memorizzare la configurazione e caricarla una sola volta.
    static $config = null;
    if ($config === null) {
        // Controlla che una costante sia definita, assicurando che un middleware precedente abbia impostato il percorso.
        if (!defined('USER_SOURCES_PATH')) {
            throw new \RuntimeException('The config() function can only be called after the SetDomain middleware has been executed.');
        }
        $config = require USER_SOURCES_PATH . '/config.php';
    }
    if ($key === null) return $config;
    return $config[$key] ?? null;
}

/**
 * Disabilita la cache delle viste per la richiesta corrente.
 * Imposta una variabile globale che il render() controllerà.
 */
function disableCache()
{
    $GLOBALS['view_cache'] = true;
}

/**
 * Minimizza il contenuto HTML rimuovendo spazi e commenti.
 *
 * @param string $buffer Il contenuto HTML da minimizzare.
 * @return string Il contenuto HTML minimizzato.
 */
function minify_html(string $buffer): string
{
    $search = [
        // Rimuove gli spazi tra i tag HTML.
        '/>\s+</s',
        // Rimuove spazi bianchi multipli.
        '/(\s)+/s',
        // Rimuove i commenti in stile C++.
        '//'
    ];
    $replace = [
        '><',
        '\\1',
        ''
    ];

    return preg_replace($search, $replace, $buffer);
}

/**
 * Renderizza una vista con i dati forniti.
 *
 * @param string $view Il nome della vista (es. 'pages/home').
 * @param array $params Un array associativo di dati da passare alla vista.
 */
function render($view, $params = [])
{
    if (VIEW_CACHE_ENABLED) {
        // Genera una chiave di cache unica per la vista e i suoi parametri.
        $cacheKey = md5($view . serialize($params));
        $cacheFile = VIEW_CACHE_PATH . '/' . $cacheKey . '.html';

        // Controlla se il file di cache esiste ed è ancora valido.
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < VIEW_CACHE_LIFETIME) {
            // Se la cache è valida, la serve direttamente.
            echo file_get_contents($cacheFile);
            return;
        }
    }

    // Avvia l'output buffering per catturare l'HTML generato.
    ob_start();

    // Estrae i parametri in variabili locali, rendendoli disponibili nella vista.
    extract($params);
    $theme = THEME_DIR;
    $viewPath = __DIR__ . "/resources/views/{$theme}/{$view}.php";

    // Cerca la vista prima nella directory del tema, poi nella directory base.
    if (!file_exists($viewPath)) {
        $baseViewPath = __DIR__ . "/resources/views/{$view}.php";
        if (!file_exists($baseViewPath)) {
            // Se la vista non viene trovata, pulisce il buffer e lancia un'eccezione.
            ob_end_clean();
            throw new RuntimeException("View non trovata: $viewPath");
        }
        include $baseViewPath;
    } else {
        include $viewPath;
    }

    // Cattura il contenuto del buffer.
    $content = ob_get_clean();
    // Minimizza il contenuto HTML.
    $minifiedContent = minify_html($content);

    // Salva il contenuto minimizzato nella cache se la cache è abilitata e non è stata disabilitata per questa richiesta.
    if (VIEW_CACHE_ENABLED && empty($GLOBALS['view_cache'])) {
        if (!is_dir(VIEW_CACHE_PATH)) {
            mkdir(VIEW_CACHE_PATH, 0775, true);
        }
        file_put_contents($cacheFile, $minifiedContent);
    }
    
    // Rimuove la variabile globale per la prossima richiesta.
    unset($GLOBALS['view_cache']);

    // Stampa il contenuto HTML finalizzato.
    echo $minifiedContent;
}

/**
 * Gestisce la risposta in caso di errore, impostando il codice di stato HTTP.
 *
 * @param int $code Il codice di stato HTTP (es. 404).
 * @return int 0.
 */
function abort($code = 404)
{
    http_response_code($code);
    if ($code === 404) {
        render('errors/404');
    } else {
        echo "Errore $code";
    }
    return 0;
}

/**
 * Funzione di "escaping" per le viste.
 * Applica htmlspecialchars per rendere sicura una stringa da visualizzare in HTML.
 *
 * @param mixed $string La stringa da sanificare.
 * @return string La stringa sanificata.
 */
function eq($string)
{
    return htmlspecialchars((string) $string, ENT_QUOTES, 'UTF-8');
}

// --- Funzioni per includere partials (frammenti di vista) ---

/**
 * Include un partial da una vista.
 *
 * @param string $name Il nome del partial.
 */
function partial($name)
{
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/partials/{$name}.php";
}

/**
 * Include un partial specifico per l'area di amministrazione.
 *
 * @param string $name Il nome del partial.
 */
function partialAdmin($name)
{
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/admin-partials/{$name}.php";
}

/**
 * Include un partial specifico per l'hub.
 *
 * @param string $name Il nome del partial.
 */
function partialHub($name)
{
    $theme = THEME_DIR;
    include BASE_PATH . "/resources/views/{$theme}/hub-partials/{$name}.php";
}

/**
 * Genera e stampa un campo di input nascosto per il token CSRF.
 * Se il token non esiste, ne genera uno nuovo e lo salva nella sessione.
 */
function csrf_field()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}


/**
 * Sanifica un input in base al tipo specificato.
 *
 * @param mixed $value Il valore da sanificare.
 * @param string $type Il tipo di sanificazione ('str', 'int', 'email').
 * @return mixed Il valore sanificato.
 */
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

/**
 * Formatta un numero di byte in una stringa leggibile (es. "10.5 MB").
 *
 * @param int $bytes Il numero di byte.
 * @param int $precision La precisione dei decimali.
 * @return string La stringa formattata.
 */
function formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Formatta una data in un formato specifico.
 *
 * @param string $date La data in un formato riconoscibile.
 * @return string La data formattata.
 */
function formatDate($date)
{
    return date('d/m/Y H:i:s', strtotime($date));
}

/**
 * Codifica un percorso URL per prevenire errori in un URL.
 *
 * @param string $p Il percorso da codificare.
 * @return string Il percorso codificato.
 */
function enc_path(string $p): string
{
    $p = rtrim($p, '/');
    $parts = array_filter(explode('/', $p), 'strlen');
    return implode('/', array_map('rawurlencode', $parts)) . '/';
}

/**
 * Restituisce l'email dell'utente loggato, se presente nella sessione.
 *
 * @return string|null L'email dell'utente o null.
 */
function user(): ?string
{
    return $_SESSION['user_email'] ?? null;
}

/**
 * Restituisce l'email dell'utente dell'hub loggato, se presente nella sessione.
 *
 * @return string|null L'email dell'utente dell'hub o null.
 */
function hubUser(): ?string
{
    return $_SESSION['hub_user_email'] ?? null;
}

/**
 * Restituisce un'istanza sanificata della richiesta HTTP.
 *
 * @return array I dati della richiesta HTTP.
 */
function http()
{
    $checkRequest = new CheckRequest();
    $http = $checkRequest->getHTTP();
    return $http;
}