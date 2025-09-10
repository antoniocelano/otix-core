<?php
// Dichiarazione del namespace per la classe del controller.
namespace App\Controller;

/**
 * Controller per la gestione degli errori.
 */
class ErrorController
{
    /**
     * Mostra la pagina di errore 404 (Not Found).
     *
     * @return void
     */
    public function notFound()
    {
        // Chiama il metodo 'code' con il codice specifico per l'errore 404.
        return $this->code('ERR001');
    }

    /**
     * Gestisce la visualizzazione di un errore specifico in base al codice.
     *
     * @param string $code Il codice dell'errore da gestire.
     * @return void
     */
    public function code($code)
    {
        // Utilizza una struttura switch per gestire diversi codici di errore.
        switch ($code) {
            case 'ERR001':
                // Imposta il codice di risposta HTTP a 404 (Not Found).
                http_response_code(404);
                // Logga l'errore.
                $this->logError("Codice $code");
                // Renderizza la vista personalizzata per l'errore 404.
                render('errors/404');
                break;
            case 'ERR002':
                // Imposta il codice di risposta HTTP a 403 (Forbidden).
                http_response_code(403);
                // Logga l'errore.
                $this->logError("Codice $code");
                // Renderizza la vista personalizzata per l'errore 403.
                render('errors/403');
                break;
            default:
                // Logga un errore sconosciuto nel file di log.
                $this->logError("Errore sconosciuto: Codice $code");
                // Imposta un codice di risposta HTTP generico per errore del client (400 Bad Request).
                http_response_code(400);
                // Stampa un messaggio di errore semplice.
                echo "Errore sconosciuto";
        }
    }

    /**
     * Scrive gli errori nel file di log.
     *
     * @param string $message Il messaggio di errore da registrare.
     * @return void
     */
    private function logError($message)
    {
        // Definisce il percorso completo del file di log.
        $logFile = BASE_PATH . '/storage/logs/logs.php';
        // Formatta il messaggio di errore con data e ora.
        $errorMessage = sprintf(
            "[%s] Errore: %s\n",
            date('Y-m-d H:i:s'),
            $message
        );
        // Scrive il messaggio formattato nel file di log in modalità append, senza sovrascrivere il contenuto esistente.
        file_put_contents($logFile, $errorMessage, FILE_APPEND);
    }
}