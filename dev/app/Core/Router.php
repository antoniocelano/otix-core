<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
    }

    public function add($method, $pattern, $handler)
    {
        $this->routes[] = [$method, $pattern, $handler];
    }

    public function dispatch(string $method, string $uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as [$verb, $pattern, $handler]) {
            if ($verb !== $method) {
                continue;
            }

            $regex = '#^' . preg_replace('#\{([^}]+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';

            if (preg_match($regex, $path, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($k) => is_string($k),
                    ARRAY_FILTER_USE_KEY
                );
                
                // Validazione parametri: Aggiunto il punto '.' per accettare i nomi dei file.
                foreach ($params as $p) {
                    if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $p)) {
                        (new \App\Controller\ErrorController())->code('ERR001');
                        return;
                    }
                }
                
                [$controller, $action] = $handler;
                return (new $controller())->{$action}(...array_values($params));
            }
        }

        // Se nessuna rotta corrisponde
        $errorController = new \App\Controller\ErrorController();
        $errorController->code('ERR001');
    }

    // rotte static tipo laravel
    public static function get($pattern, $handler) {
        global $routes;
        $routes[] = ['GET', $pattern, $handler];
    }
    public static function post($pattern, $handler) {
        global $routes;
        $routes[] = ['POST', $pattern, $handler];
    }
}

// funzione chiamata rotte
function route($method, $pattern, $handler) {
    global $routes;
    $routes[] = [strtoupper($method), $pattern, $handler];
}