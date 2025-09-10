<?php
// Dichiarazione del namespace per la classe del controller.
namespace App\Controller;

/**
 * Controller per la gestione e la restituzione di file statici dalla cartella 'public'.
 */
class GetPublicFileController
{
    /**
     * Restituisce un file statico dalla directory 'public'.
     *
     * @param array $segments I segmenti del percorso del file richiesto.
     * @return int Restituisce 0 dopo aver servito il file o in caso di errore.
     */
    public function file(...$segments)
    {
        // Array associativo che mappa le estensioni dei file ai rispettivi tipi MIME.
        // Questo serve a garantire che solo tipi di file specifici vengano serviti.
        $allowedMimeTypes = [
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'json'  => 'application/json',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'png'   => 'image/png',
            'gif'   => 'image/gif',
            'svg'   => 'image/svg+xml',
            'webp'  => 'image/webp',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'eot'   => 'application/vnd.ms-fontobject',
            'otf'   => 'font/otf',
        ];

        // Unisce i segmenti del percorso in una stringa, creando un percorso relativo.
        $relativePath = implode('/', $segments);
        
        // Definisce il percorso di base della directory 'public' a partire dalla costante BASE_PATH.
        $basePublicPath = BASE_PATH . '/public/';
        // Costruisce il percorso completo del file unendo il percorso di base con il percorso relativo.
        $fullPath = $basePublicPath . $relativePath;
        
        // --- Controlli di sicurezza per prevenire attacchi di directory traversal ---
        
        // Ottiene il percorso reale (canonico) e normalizzato della directory di base.
        $realBasePath = realpath($basePublicPath);
        // Ottiene il percorso reale e normalizzato del file richiesto.
        $realFullPath = realpath($fullPath);
        
        // Verifica se il percorso reale del file esiste e se è contenuto all'interno del percorso di base.
        // Questo previene l'accesso a file al di fuori della directory 'public'.
        if ($realFullPath === false || strpos($realFullPath, $realBasePath) !== 0) {
            // Se il controllo fallisce, imposta il codice di stato HTTP a 404 (Not Found).
            http_response_code(404);
            return 0;
        }
        
        // 2. Controlla che il tipo di file sia permesso
        // Estrae l'estensione del file dal percorso reale.
        $extension = strtolower(pathinfo($realFullPath, PATHINFO_EXTENSION));
        // Verifica se l'estensione del file è nell'array dei tipi MIME consentiti.
        if (!array_key_exists($extension, $allowedMimeTypes)) {
            // Se il tipo di file non è permesso, imposta il codice di stato HTTP a 403 (Forbidden).
            http_response_code(403); 
            return 0;
        }

        // --- Servi il file ---
        // Pulisce tutti i buffer di output attivi per prevenire l'invio di dati inaspettati
        // prima degli header.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Imposta l'header HTTP 'Content-Type' basato sull'estensione del file.
        header("Content-Type: " . $allowedMimeTypes[$extension]);
        // Imposta l'header HTTP 'Content-Length' con la dimensione del file.
        header("Content-Length: " . filesize($realFullPath));
        // Legge il file e lo invia direttamente al browser.
        readfile($realFullPath);
        // Termina lo script per evitare l'esecuzione di altro codice.
        exit;
    }
}