<?php

namespace App\Core;

// Importa le classi necessarie per interagire con l'API S3.
// La classe S3 del vendor gestisce le richieste HTTP e la comunicazione con il servizio.
use App\Vendor\S3\S3;
use Exception;
use SimpleXMLElement;
use App\Controller\HubController;

/**
 * Gestore S3: una classe wrapper che semplifica le operazioni di base
 * con un servizio di archiviazione S3-compatibile (come AWS S3, DigitalOcean Spaces, ecc.).
 * Questa classe incapsula la logica del client S3 di terze parti,
 * fornendo un'interfaccia più pulita e specifica per le esigenze dell'applicazione.
 */
class S3Manager
{
    /** @var S3 L'istanza del client S3 per le operazioni di archiviazione. */
    private S3 $s3Client;
    /** @var string Il nome del bucket S3 configurato. */
    private string $bucket;

    /**
     * Costruttore della classe. Inizializza il client S3 e le credenziali.
     *
     * @throws Exception Se le credenziali o le configurazioni S3 non sono complete.
     */
    public function __construct()
    {
        // Carica le credenziali e le configurazioni S3 dal file delle variabili d'ambiente (.env).
        $accessKey = $_ENV['AWS_ACCESS_KEY_ID'] ?? '';
        $secretKey = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '';
        $this->bucket = $_ENV['AWS_BUCKET'] ?? '';
        $endpoint = $_ENV['AWS_ENDPOINT'] ?? '';
        $region = $_ENV['AWS_REGION'] ?? '';
        
        // Verifica che tutte le variabili d'ambiente necessarie siano state caricate.
        // Se una manca, lancia un'eccezione per prevenire il fallimento delle operazioni.
        if (empty($accessKey) || empty($secretKey) || empty($this->bucket) || empty($endpoint) || empty($region)) {
            throw new Exception("Configurazione S3 incompleta nel file .env.");
        }

        // Passa le credenziali e la regione al costruttore del client S3 di terze parti.
        $this->s3Client = new S3($accessKey, $secretKey, $endpoint, $region);
    }
    
    /**
     * Restituisce il nome del bucket S3 configurato.
     * @return string Il nome del bucket.
     */
    public function getBucketName(): string
    {
        return $this->bucket;
    }

    /**
     * Restituisce l'istanza del client S3.
     * @return S3 L'oggetto S3.
     */
    public function getS3Client(): S3
    {
        return $this->s3Client;
    }
    
    /**
     * Copia un file da un percorso S3 a un altro.
     * Questo metodo esegue l'operazione di copia scaricando prima il file e ricaricandolo.
     *
     * @param string $sourceKey La chiave (percorso) del file di origine su S3.
     * @param string $destinationKey La chiave (percorso) di destinazione su S3.
     * @return bool True se la copia ha successo, false altrimenti.
     */
    public function copyFile(string $sourceKey, string $destinationKey): bool
    {
        try {
            // 1. Recupera il contenuto del file di origine.
            $fileContent = $this->getFile($sourceKey);
    
            if ($fileContent === null) {
                // Se il file di origine non esiste o il recupero fallisce, l'operazione di copia fallisce.
                return false;
            }
    
            // 2. Carica il contenuto del file nel percorso di destinazione.
            // Utilizza un flusso di dati temporaneo in memoria (php://temp) per gestire il contenuto
            // senza doverlo salvare su disco, il che è più efficiente, specialmente con file di grandi dimensioni.
            $tempStream = fopen('php://temp', 'r+');
            fwrite($tempStream, $fileContent);
            rewind($tempStream); // Riporta il puntatore all'inizio del flusso.
    
            // Carica l'oggetto nel bucket di destinazione.
            $response = $this->s3Client->putObject($this->bucket, $destinationKey, $tempStream, [
                'x-amz-acl' => 'public-read' // Imposta la visibilità del file a "pubblica leggibile".
            ]);
    
            // Chiude il flusso temporaneo per liberare la memoria.
            fclose($tempStream);
    
            // Controlla se la risposta contiene un errore.
            if ($response->error) {
                error_log("Errore S3 durante la copia: " . $response->error['message']);
                return false;
            }
            
            return true;
        } catch (\Throwable $e) {
            // Cattura qualsiasi eccezione o errore (come TypeError) e logga il messaggio.
            error_log("Errore interno durante la copia del file: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sposta un file da un percorso S3 a un altro.
     * Questa operazione è una combinazione di copia e eliminazione.
     *
     * @param string $sourceKey La chiave (percorso) del file di origine su S3.
     * @param string $destinationKey La chiave (percorso) di destinazione su S3.
     * @return bool True se lo spostamento ha successo.
     */
    public function moveFile(string $sourceKey, string $destinationKey): bool
    {
        try {
            // 1. Copia il file di origine nella destinazione.
            $copySuccess = $this->copyFile($sourceKey, $destinationKey);
            
            if (!$copySuccess) {
                // Se la copia fallisce, l'operazione di spostamento non può procedere.
                return false;
            }
            
            // 2. Elimina il file di origine solo se la copia è andata a buon fine.
            $deleteSuccess = $this->deleteFile($sourceKey);

            if (!$deleteSuccess) {
                // Se l'eliminazione fallisce, il file originale rimane.
                // Logga l'errore per notificare l'inconsistenza.
                error_log("Errore S3: la copia ha avuto successo ma l'eliminazione del file di origine '{$sourceKey}' è fallita.");
            }
            
            // L'operazione di spostamento è considerata un successo se sia la copia che l'eliminazione riescono.
            return $copySuccess && $deleteSuccess;
        } catch (\Throwable $e) {
            // Gestione degli errori generici.
            error_log("Errore interno durante lo spostamento del file: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recupera il contenuto di un file da S3.
     *
     * @param string $key La chiave (percorso) del file su S3.
     * @return string|null Il contenuto del file come stringa, o null se non trovato o in caso di errore.
     */
    public function getFile(string $key): ?string
    {
        // Esegue la richiesta per ottenere l'oggetto.
        $response = $this->s3Client->getObject($this->bucket, $key);
        
        // Controlla se la risposta S3 indica un errore.
        if ($response->error) {
            error_log("Errore S3: " . $response->error['message']);
            return null;
        }

        // Restituisce il corpo della risposta che contiene il contenuto del file.
        return $response->body;
    }

    /**
     * Carica un file locale su S3.
     *
     * @param string $key La chiave (percorso) di destinazione su S3.
     * @param string $sourcePath Il percorso locale del file da caricare.
     * @return bool True se il caricamento ha successo, false altrimenti.
     */
    public function putFile(string $key, string $sourcePath): bool
    {
        // Apre il file locale in modalità lettura.
        $fileHandle = fopen($sourcePath, 'r');
        if (!$fileHandle) {
            error_log("Impossibile aprire il file locale: {$sourcePath}");
            return false;
        }

        // Carica l'oggetto su S3 utilizzando il "file handle" per un trasferimento efficiente.
        $response = $this->s3Client->putObject($this->bucket, $key, $fileHandle, [
            'x-amz-acl' => 'public-read' // Imposta i permessi di accesso.
        ]);

        // Chiude il "file handle" dopo il caricamento.
        fclose($fileHandle);

        // Controlla la risposta per eventuali errori.
        if ($response->error) {
            error_log("Errore S3: " . $response->error['message']);
            return false;
        }

        return true;
    }

    /**
     * Elimina un file da S3.
     *
     * @param string $key La chiave (percorso) del file su S3 da eliminare.
     * @return bool True se l'eliminazione ha successo.
     */
    public function deleteFile(string $key): bool
    {
        // Esegue la richiesta di eliminazione dell'oggetto.
        $response = $this->s3Client->deleteObject($this->bucket, $key);
        
        // Controlla la risposta per eventuali errori.
        if ($response->error) {
            error_log("Errore S3: " . $response->error['message']);
            return false;
        }

        return true;
    }
    
    /**
     * Ottiene la lista degli oggetti (file e cartelle) in un bucket S3.
     * Usa il parametro 'delimiter' per raggruppare i file per cartelle virtuali.
     *
     * @param string $prefix Il prefisso per filtrare i file (corrisponde a una "cartella" virtuale).
     * @return array|null Un array contenente la lista di file e cartelle, o null in caso di errore.
     */
    public function listObjects(?string $prefix = null): ?array
    {
        // Imposta i parametri per la richiesta getBucket.
        // 'prefix' filtra i risultati per una specifica "cartella".
        // 'delimiter' fa in modo che la risposta raggruppi i risultati in "cartelle" (CommonPrefixes).
        $params = ['prefix' => $prefix, 'delimiter' => '/'];
        $response = $this->s3Client->getBucket($this->bucket, [], $params);
        
        if ($response->error) {
            error_log("Errore nella lista degli oggetti del bucket: " . $response->error['message']);
            return null;
        }

        // Il corpo della risposta S3 è un oggetto SimpleXMLElement, che può essere attraversato come un array.
        $xml = $response->body;
        $files = [];
        
        // Aggiungi le "cartelle virtuali" alla lista.
        foreach ($xml->CommonPrefixes as $prefixItem) {
            $folderPath = (string) $prefixItem->Prefix;
            // Estrae il nome della cartella dal percorso.
            $folderName = basename($folderPath, '/');
            $files[] = [
                'name' => $folderName,
                'path' => $folderPath,
                'type' => 'folder' // Tipo 'folder' per una facile distinzione.
            ];
        }

        // Aggiungi i file alla lista.
        foreach ($xml->Contents as $content) {
            $key = (string) $content->Key;
            
            // Ignora le chiavi che terminano con '/' (sono le cartelle già gestite dal ciclo precedente).
            if (substr($key, -1) === '/') {
                continue;
            }

            // Ottiene il nome del file rimuovendo il prefisso.
            $name = $prefix ? substr($key, strlen($prefix)) : $key;

            if (empty($name)) {
                continue;
            }
            
            $files[] = [
                'name'         => $name,
                'type'         => 'file', // Tipo 'file' per la distinzione.
                'path'         => $key,
                'lastModified' => (string) $content->LastModified,
                'size'         => (int) $content->Size,
                'owner'        => (string) $content->Owner->DisplayName,
            ];
        }

        return $files;
    }
}