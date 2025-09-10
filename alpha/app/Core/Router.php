<?php

namespace App\Core;

/**
 * Classe Router per la gestione delle rotte dell'applicazione.
 * Si occupa di mappare le richieste HTTP (metodo e URI) ai rispettivi
 * controller e metodi, gestendo anche parametri dinamici e validazioni di sicurezza.
 */
class Router
{
    /**
     * @var array Un array di rotte registrate. Ogni rotta è un array contenente il
     * metodo HTTP, il pattern dell'URI e il gestore (handler).
     */
    private array $routes = [];

    /**
     * Costruttore del Router.
     *
     * @param array $routes Un array opzionale di rotte da inizializzare.
     */
    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
    }

    /**
     * Aggiunge una nuova rotta all'elenco.
     *
     * @param string $method Il metodo HTTP (es. 'GET', 'POST').
     * @param string $pattern Il pattern dell'URI (es. '/users/{id}').
     * @param array $handler Il gestore, tipicamente un array con [NomeController::class, 'nomeMetodo'].
     */
    public function add($method, $pattern, $handler)
    {
        $this->routes[] = [$method, $pattern, $handler];
    }

    /**
     * Esegue il dispatch della richiesta, trovando e eseguendo la rotta corrispondente.
     *
     * @param string $method Il metodo HTTP della richiesta corrente.
     * @param string $uri L'URI della richiesta corrente.
     */
    public function dispatch(string $method, string $uri)
    {
        // Estrae il percorso (path) dall'URI per la corrispondenza.
        $path = parse_url($uri, PHP_URL_PATH);

        // Itera su ogni rotta registrata.
        foreach ($this->routes as [$verb, $pattern, $handler]) {
            // Salta le rotte che non corrispondono al metodo HTTP corrente.
            if ($verb !== $method) {
                continue;
            }

            // Costruisce l'espressione regolare (regex) a partire dal pattern della rotta.
            // Supporta i parametri dinamici nella forma {nome} o {nome:regex}.
            $paramPatterns = [];
            $regex = preg_replace_callback(
                '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#',
                function ($m) use (&$paramPatterns) {
                    $name = $m[1]; // Nome del parametro (es. 'id').
                    // Se non è specificata una regex, usa il default che accetta tutto tranne '/'.
                    $pattern = isset($m[2]) ? $m[2] : '[^/]+';
                    $paramPatterns[$name] = $pattern;
                    // Crea un gruppo con nome per la regex (es. `(?P<id>[^/]+)`).
                    return '(?P<' . $name . '>' . $pattern . ')';
                },
                $pattern
            );
            $regex = '#^' . $regex . '$#'; // Aggiunge gli ancoraggi di inizio e fine stringa.

            // Tenta di trovare una corrispondenza tra l'URI e la regex della rotta.
            if (preg_match($regex, $path, $matches)) {
                // Filtra i risultati per ottenere solo i parametri con nome.
                $params = array_filter($matches, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY);

                // --- Validazione di sicurezza per i parametri ---
                // Previene attacchi di tipo Directory Traversal e blocca caratteri non sicuri.
                foreach ($params as $key => $value) {
                    // Previene il Directory Traversal bloccando '..'.
                    if (strpos($value, '..') !== false) {
                        (new \App\Controller\ErrorController())->code('ERR001');
                        return;
                    }
                    // Blocca caratteri non consentiti per i parametri.
                    if (!preg_match('/^[a-zA-Z0-9_\.\/-]+$/', $value)) {
                        (new \App\Controller\ErrorController())->code('ERR001');
                        return;
                    }
                    // Assicura che i parametri non contengano '/' se il pattern non lo permette.
                    $original = $paramPatterns[$key] ?? '[^/]+';
                    $allowsSlash = ($original === '.*') || strpos($original, '/') !== false;
                    if (!$allowsSlash && strpos($value, '/') !== false) {
                        (new \App\Controller\ErrorController())->code('ERR001');
                        return;
                    }
                }
                // --- Fine validazione ---

                // Esegue il gestore della rotta.
                [$controller, $action] = $handler;
                // Crea un'istanza del controller e chiama il metodo, passando i parametri dinamici.
                return (new $controller())->{$action}(...array_values($params));
            }
        }

        // Se nessuna rotta corrisponde, viene gestito l'errore '404 - Not Found'.
        $errorController = new \App\Controller\ErrorController();
        $errorController->code('ERR001'); // Codice per l'errore "not found".
    }

    // Metodi statici helper per definire le rotte in modo più leggibile (stile Laravel).
    // Usano una variabile globale per aggiungere le rotte.

    /**
     * Aggiunge una rotta GET.
     *
     * @param string $pattern Il pattern dell'URI.
     * @param array $handler Il gestore.
     */
    public static function get($pattern, $handler)
    {
        global $routes;
        $routes[] = ['GET', $pattern, $handler];
    }

    /**
     * Aggiunge una rotta POST.
     *
     * @param string $pattern Il pattern dell'URI.
     * @param array $handler Il gestore.
     */
    public static function post($pattern, $handler)
    {
        global $routes;
        $routes[] = ['POST', $pattern, $handler];
    }
}

/**
 * Funzione helper globale per registrare una rotta.
 *
 * @param string $method Il metodo HTTP.
 * @param string $pattern Il pattern dell'URI.
 * @param array $handler Il gestore.
 */
function route($method, $pattern, $handler)
{
    global $routes;
    $routes[] = [strtoupper($method), $pattern, $handler];
}