<?php
namespace App\Middleware;

/**
 * Middleware per il controllo e la sanificazione dei dati della richiesta HTTP.
 * Questa classe si occupa di raccogliere e rendere sicuri i dati provenienti dalle
 * superglobali di PHP ($_SERVER, $_GET, $_POST, $_COOKIE) per prevenire attacchi
 * come il Cross-Site Scripting (XSS).
 */
class CheckRequest
{
    /**
     * @var array Contiene i dati della richiesta HTTP sanificati.
     */
    private array $http;

    /**
     * Costruttore della classe.
     * Al momento dell'istanziazione, analizza e sanifica i dati della richiesta.
     */
    public function __construct()
    {
        // Inizializza l'array $http con i dati della richiesta, sanificandoli immediatamente.
        $this->http = [
            // Sanifica il metodo HTTP (es. GET, POST).
            'method'  => htmlentities($_SERVER['REQUEST_METHOD'] ?? 'GET', ENT_QUOTES, 'UTF-8'),
            // Sanifica l'URI della richiesta.
            'uri'     => htmlentities($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8'),
            // Sanifica l'host.
            'host'    => htmlentities($_SERVER['HTTP_HOST'] ?? '', ENT_QUOTES, 'UTF-8'),
            // Sanifica l'indirizzo IP del client.
            'remote'  => htmlentities($_SERVER['REMOTE_ADDR'] ?? 'GET', ENT_QUOTES, 'UTF-8'),
            // Determina lo schema del protocollo (http o https).
            'scheme'  => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http',
            // Sanifica l'array dei parametri GET in modo ricorsivo.
            'get'     => $this->sanitize($_GET),
            // Sanifica l'array dei parametri POST in modo ricorsivo.
            'post'    => $this->sanitize($_POST),
            // Sanifica l'array dei cookie in modo ricorsivo.
            'cookies' => $this->sanitize($_COOKIE),
        ];
    }

    /**
     * Sanifica ricorsivamente un array di dati.
     * Utilizza htmlentities per convertire i caratteri speciali in entità HTML,
     * rendendoli sicuri da visualizzare nelle pagine web.
     *
     * @param array $data L'array di dati da sanificare.
     * @return array L'array con i dati sanificati.
     */
    private function sanitize(array $data): array
    {
        return array_map(
            // Per ogni valore nell'array...
            fn($v) => is_array($v) ?
                // Se è un array, richiama la funzione in modo ricorsivo.
                $this->sanitize($v) :
                // Altrimenti, applica la sanificazione tramite htmlentities.
                htmlentities((string)$v, ENT_QUOTES, 'UTF-8'),
            $data
        );
    }

    /**
     * Restituisce l'array dei dati HTTP sanificati.
     *
     * @return array L'array contenente i dati della richiesta.
     */
    public function getHTTP(): array
    {
        return $this->http;
    }
}