<?php

namespace App\Core;

use App\Core\S3;
use Exception;
use SimpleXMLElement;

class S3Manager
{
    private S3 $s3Client;
    private string $bucket;

    public function __construct()
    {
        // Carica le credenziali S3 dal file .env
        $accessKey = $_ENV['AWS_ACCESS_KEY_ID'] ?? '';
        $secretKey = $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '';
        $this->bucket = $_ENV['AWS_BUCKET'] ?? '';
        $endpoint = $_ENV['AWS_ENDPOINT'] ?? '';
        $region = $_ENV['AWS_REGION'] ?? '';
        
        if (empty($accessKey) || empty($secretKey) || empty($this->bucket) || empty($endpoint) || empty($region)) {
            throw new Exception("Configurazione S3 incompleta nel file .env.");
        }

        // Passa la regione al costruttore della classe S3
        $this->s3Client = new S3($accessKey, $secretKey, $endpoint, $region);
    }
    
    /**
     * @return string
     */
    public function getBucketName(): string
    {
        return $this->bucket;
    }

    /**
     * Recupera il contenuto di un file da S3.
     *
     * @param string $key La chiave (percorso) del file su S3.
     * @return string|null Il contenuto del file o null se non trovato.
     */
    public function getFile(string $key): ?string
    {
        $response = $this->s3Client->getObject($this->bucket, $key);
        
        if ($response->error) {
            error_log("Errore S3: " . $response->error['message']);
            return null;
        }

        return $response->body;
    }

    /**
     * Carica un file su S3.
     *
     * @param string $key La chiave (percorso) di destinazione su S3.
     * @param string $sourcePath Il percorso locale del file da caricare.
     * @return bool True se il caricamento ha successo.
     */
    public function putFile(string $key, string $sourcePath): bool
    {
        $fileHandle = fopen($sourcePath, 'r');
        if (!$fileHandle) {
            error_log("Impossibile aprire il file locale: {$sourcePath}");
            return false;
        }

        $response = $this->s3Client->putObject($this->bucket, $key, $fileHandle, [
            'x-amz-acl' => 'public-read' // O 'private'
        ]);

        fclose($fileHandle);

        if ($response->error) {
            error_log("Errore S3: " . $response->error['message']);
            return false;
        }

        return true;
    }
    
    /**
     * Ottiene la lista degli oggetti nel bucket, raggruppandoli per cartelle virtuali.
     *
     * @param string $prefix Il prefisso per filtrare i file (cartella virtuale).
     * @return array|null Un array di oggetti o null in caso di errore.
     */
    public function listObjects(?string $prefix = null): ?array
    {
        $params = ['prefix' => $prefix, 'delimiter' => '/'];
        $response = $this->s3Client->getBucket($this->bucket, [], $params);
        
        if ($response->error) {
            error_log("Errore nella lista degli oggetti del bucket: " . $response->error['message']);
            return null;
        }

        // Il corpo della risposta è già un oggetto SimpleXMLElement
        $xml = $response->body;
        $files = [];
        
        // Aggiungi cartelle virtuali
        foreach ($xml->CommonPrefixes as $prefixItem) {
            $folderPath = (string) $prefixItem->Prefix;
            $folderName = basename($folderPath, '/');
            $files[] = [
                'name' => $folderName,
                'path' => $folderPath,
                'type' => 'folder'
            ];
        }

        // Aggiungi file
        foreach ($xml->Contents as $content) {
            $key = (string) $content->Key;
            
            // Ignora le chiavi che terminano con / (sono le cartelle già gestite)
            if (substr($key, -1) === '/') {
                continue;
            }

            // Ignora il prefisso per ottenere il nome del file
            $name = $prefix ? substr($key, strlen($prefix)) : $key;

            if (empty($name)) {
                continue;
            }
            
            $files[] = [
                'name'         => $name,
                'type'         => 'file',
                'path'         => $key,
                'lastModified' => (string) $content->LastModified,
                'size'         => (int) $content->Size,
                'owner'        => (string) $content->Owner->DisplayName,
            ];
        }

        return $files;
    }
}