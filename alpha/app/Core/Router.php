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

            // Costruisci la regex supportando {name} e {name:regex}
            $paramPatterns = [];
            $regex = preg_replace_callback(
                '#\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}#',
                function ($m) use (&$paramPatterns) {
                    $name = $m[1];
                    $pattern = isset($m[2]) ? $m[2] : '[^/]+';
                    $paramPatterns[$name] = $pattern;
                    return '(?P<' . $name . '>' . $pattern . ')';
                },
                $pattern
            );
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, fn($k) => is_string($k), ARRAY_FILTER_USE_KEY);

                // Validazione: consenti [a-zA-Z0-9_.-/], blocca traversal
                foreach ($params as $key => $value) {
                    if (strpos($value, '..') !== false) {
                        (new \App\Controller\ErrorController())->code('ERR001');
                        return;
                    }
                    if (!preg_match('/^[a-zA-Z0-9_\.\/-]+$/', $value)) {
                        (new \App\Controller\ErrorController())->code('ERR001');
                        return;
                    }
                    // Se il pattern originale non consente '/', non deve comparire
                    $original = $paramPatterns[$key] ?? '[^/]+';
                    $allowsSlash = ($original === '.*') || strpos($original, '/') !== false;
                    if (!$allowsSlash && strpos($value, '/') !== false) {
                        (new \App\Controller\ErrorController())->code('ERR001');
                        return;
                    }
                }

                [$controller, $action] = $handler;
                return (new $controller())->{$action}(...array_values($params));
            }
        }

        // Nessuna rotta corrisponde
        $errorController = new \App\Controller\ErrorController();
        $errorController->code('ERR001');
    }

    // stile "static routes" tipo Laravel
    public static function get($pattern, $handler) {
        global $routes;
        $routes[] = ['GET', $pattern, $handler];
    }
    public static function post($pattern, $handler) {
        global $routes;
        $routes[] = ['POST', $pattern, $handler];
    }
}

// funzione helper per registrare rotte
function route($method, $pattern, $handler) {
    global $routes;
    $routes[] = [strtoupper($method), $pattern, $handler];
}
