<?php
declare(strict_types=1);
$GLOBALS['view_cache'] = true;
/**
 * --------------------------------------------------------------------------
 * Definisci Costanti di Base
 * --------------------------------------------------------------------------
 *
 * Definisce il percorso radice dell'applicazione per garantire che i percorsi
 * dei file siano sempre corretti, indipendentemente da dove viene eseguito lo script.
 */
define('BASE_PATH', __DIR__ . '/..');

/**
 * --------------------------------------------------------------------------
 * Includi Autoloader e Helper
 * --------------------------------------------------------------------------
 *
 * Carica l'autoloader delle classi (standard PSR-4) e le funzioni helper
 * globali definite in autoload.php.
 */
require BASE_PATH . '/autoload.php';

use App\Core\Router;
use App\Middleware\CheckRequest;

try {
    /**
     * ----------------------------------------------------------------------
     * 1. Inizializzazione della Richiesta
     * ----------------------------------------------------------------------
     *
     * CheckRequest è l'UNICO punto dell'applicazione che accede alle
     * variabili superglobali (es. $_SERVER, $_GET, $_POST, $_COOKIE).
     * Istanziamo questa classe per ottenere un array '$http' sicuro e sanificato.
     * Da questo momento in poi, il resto dell'applicazione utilizzerà solo $http.
     */
    $checkRequest = new CheckRequest();
    $http = $checkRequest->getHTTP();

    /**
     * ----------------------------------------------------------------------
     * 2. Caricamento delle Variabili d'Ambiente
     * ----------------------------------------------------------------------
     *
     * Carica il file .env principale. Questo file non dovrebbe essere
     * accessibile pubblicamente. Altri file .env specifici per dominio
     * verranno caricati dal middleware SetDomain.
     */
    loadEnv(BASE_PATH . '/.env'); //

    /**
     * ----------------------------------------------------------------------
     * 3. Configurazione della Sessione
     * ----------------------------------------------------------------------
     *
     * Imposta i parametri della sessione in modo sicuro prima di avviarla.
     * Utilizza lo schema (http/https) ottenuto da $http per impostare
     * il cookie come 'secure'.
     */
    session_name('hybris');
    $secureCookie = ($http['scheme'] === 'https');
    session_set_cookie_params([
        'lifetime' => $_ENV['SESSION_LIFETIME'] ?? 0,
        'path'     => '/',
        'secure'   => $secureCookie,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    if (isset($_ENV['SESSION_LIFETIME'])) {
        ini_set('session.gc_maxlifetime', (int) $_ENV['SESSION_LIFETIME']);
    }
    session_start();

    /**
     * ----------------------------------------------------------------------
     * 4. Header di Sicurezza Globali
     * ----------------------------------------------------------------------
     *
     * Invia alcuni header HTTP di base per migliorare la sicurezza
     * contro attacchi comuni come clickjacking e XSS.
     */
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: no-referrer');
    
    /**
     * ----------------------------------------------------------------------
     * 5. Esecuzione dei Middleware
     * ----------------------------------------------------------------------
     *
     * Esegue in sequenza i middleware definiti in config.php.
     * Ogni middleware riceve l'array $http e può modificarlo o
     * eseguire azioni come redirect (es. SetLang).
     */
    $middlewares = require BASE_PATH . '/app/Middleware/config.php'; //
    foreach ($middlewares as $alias => $relativePath) {
        if ($alias === 'CheckRequest') {
            continue; // Già istanziato.
        }
        $fullPath = BASE_PATH . '/' . ltrim($relativePath, '/');
        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Middleware non trovato: {$alias}");
        }
        $result = (function() use ($fullPath, &$http) {
            return require $fullPath;
        })();
        if (is_array($result)) {
            $http = array_merge($http, $result);
        }
    }
    
    /**
     * ----------------------------------------------------------------------
     * 6. Gestione della Lingua per la Richiesta Corrente
     * ----------------------------------------------------------------------
     *
     * Dopo che i middleware hanno agito (es. redirect di SetLang),
     * questo blocco determina la lingua effettiva per la richiesta corrente,
     * la rimuove dall'URI per il router e la rende disponibile globalmente.
     */
    $config   = require USER_SOURCES_PATH . '/config.php';
    $langs    = $config['langs'] ?? ['it'];
    $default  = $config['default_lang'] ?? $langs[0];
    $isSite   = $config['is_site'] ?? true;

    $uri      = $http['uri'];
    $segs     = array_values(array_filter(explode('/', trim($uri, '/'))));

    if (isset($segs[0]) && in_array($segs[0], $langs, true)) {
        $lang = array_shift($segs);
        $newPath = '/' . implode('/', $segs);
        $http['uri'] = $newPath === '/' ? '/' : rtrim($newPath, '/');
    } else {
        $lang = $http['cookies']['otxlang'] ?? $default;
    }
    
    // Rende la lingua disponibile globalmente per le view e i controller
    $GLOBALS['current_lang'] = $lang;
    function current_lang(): string {
        return $GLOBALS['current_lang'] ?? 'it';
    }
    
    /**
     * ----------------------------------------------------------------------
     * 7. Dispatch del Router
     * ----------------------------------------------------------------------
     *
     * Carica le rotte e le passa al Router. Il router confronta l'URI
     * della richiesta con le rotte definite e invoca il controller
     * e il metodo appropriati.
     */
    $routes = [];
    require BASE_PATH . '/app/Routes.php'; //
    $router   = new Router($routes);
    $response = $router->dispatch($http['method'], $http['uri']);

    /**
     * ----------------------------------------------------------------------
     * 8. Invio della Risposta
     * ----------------------------------------------------------------------
     *
     * Invia la risposta generata dal controller al browser.
     * Gestisce sia risposte HTML che JSON.
     */
    if (is_array($response)) {
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo $response;
    }

} catch (\Throwable $e) {
    /**
     * ----------------------------------------------------------------------
     * Gestione delle Eccezioni Globali
     * ----------------------------------------------------------------------
     *
     * Se in qualsiasi punto del processo si verifica un'eccezione,
     * viene catturata qui per mostrare una pagina di errore generica.
     * In produzione, l'errore dovrebbe essere loggato su file.
     */
    // Esempio di logging: error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    (new \App\Controller\ErrorController())->code('ERR001'); //
    exit;
}
?>