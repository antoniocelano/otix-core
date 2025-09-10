<?php
// Dichiarazione del namespace per la classe del controller.
namespace App\Controller;

// Importa la classe S3Manager dal core dell'applicazione.
use App\Core\S3Manager;

/**
 * Controller per la gestione e la restituzione di file statici.
 */
class GetFileUserController
{
    /**
     * Restituisce un file statico dal filesystem locale dell'utente.
     * I segmenti del percorso del file sono passati come argomenti.
     *
     * @param array $segments I segmenti del percorso del file.
     * @return int
     */
    public function file(...$segments)
    {
        // Array associativo che mappa le estensioni dei file ai rispettivi tipi MIME.
        $allowedMimeTypes = [
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'png'   => 'image/png',
            'gif'   => 'image/gif',
            'svg'   => 'image/svg+xml',
            'webp'  => 'image/webp',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
        ];

        // Ricostruisce il percorso relativo del file unendo i segmenti.
        $relativePath = implode('/', $segments);
        
        // Recupera il codice dell'utente (probabilmente da una configurazione globale o da una costante).
        $userCode = DOMAIN_CODE;

        // Costruisce il percorso di base per i file dell'utente.
        $baseUserPath = BASE_PATH . '/users/' . $userCode . '/';
        // Combina il percorso di base con il percorso relativo per ottenere il percorso completo.
        $fullPath = $baseUserPath . $relativePath;
        
        // Ottiene i percorsi reali e canonici per prevenire attacchi di directory traversal.
        $realBasePath = realpath($baseUserPath);
        $realFullPath = realpath($fullPath);
        
        // Se il percorso reale del file non esiste o non si trova all'interno della directory di base,
        // restituisce un errore 404.
        if ($realFullPath === false || strpos($realFullPath, $realBasePath) !== 0) {
            http_response_code(404);
            return 0;
        }
        
        // Ottiene l'estensione del file dal percorso reale.
        $extension = strtolower(pathinfo($realFullPath, PATHINFO_EXTENSION));
        // Se l'estensione non è presente nell'elenco dei tipi MIME consentiti, restituisce un errore 404.
        if (!array_key_exists($extension, $allowedMimeTypes)) {
            http_response_code(404);
            return 0;
        }

        // Pulisce tutti i buffer di output per evitare problemi con gli header.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Imposta l'header Content-Type basandosi sul tipo MIME consentito.
        header("Content-Type: " . $allowedMimeTypes[$extension]);
        // Imposta l'header Content-Length per specificare la dimensione del file.
        header("Content-Length: " . filesize($realFullPath));
        
        // Legge il file e lo invia direttamente allo stream di output.
        readfile($realFullPath);
        return 0;
    }

    /**
     * Recupera un file da un bucket Amazon S3 e lo restituisce al browser.
     *
     * @param string $path La chiave (percorso) del file su S3.
     * @return int
     */
    public function s3File(string $path)
    {
        // Verifica che l'utente sia autenticato (probabilmente per l'accesso a un'area riservata).
        HubController::checkAuth();
        
        try {
            // Ottiene la cartella di base per i file S3 dalla configurazione.
            $baseS3Folder = config('s3_folder') ?? '';
            // Costruisce il percorso completo del file su S3. Il percorso viene decodificato dall'URL.
            $fullPath = $baseS3Folder . '/' . urldecode($path);

            // Controlla la presenza di '..' nel percorso per prevenire attacchi di directory traversal.
            if (strpos($fullPath, '..') !== false) {
                http_response_code(403);
                exit('Percorso non valido.');
            }
            
            // Istanzia la classe S3Manager.
            $s3 = new S3Manager();
            // Tenta di recuperare il contenuto del file da S3.
            $content = $s3->getFile($fullPath);

            // Se il contenuto è nullo (file non trovato), restituisce un errore 404.
            if ($content === null) {
                http_response_code(404);
                exit('File non trovato.');
            }
            
            // Determina il tipo MIME del file in base alla sua estensione.
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $mimeType = match($extension) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'mp4' => 'video/mp4',
                'css' => 'text/css',
                'js'  => 'application/javascript',
                default => 'application/octet-stream', // Tipo MIME predefinito per i file sconosciuti.
            };

            // Pulisce i buffer di output.
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Imposta l'header Content-Type e Content-Length prima di inviare il contenuto.
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . strlen($content));
            // Invia il contenuto del file al browser.
            echo $content;
            return 0;

        } catch (\Throwable $e) {
            // Gestisce qualsiasi errore durante il processo.
            // Imposta il codice di risposta a 500 (Internal Server Error).
            http_response_code(500);
            // Logga l'errore per il debug.
            error_log('Errore S3: ' . $e->getMessage());
            // Mostra un messaggio di errore generico all'utente.
            exit('Errore interno del server.');
        }
    }
}