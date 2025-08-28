<?php

namespace App\Controller;

use App\Core\S3Manager;
use Exception;
use SimpleXMLElement;

class S3Controller
{

    public function listAllFiles($path = null)
    {
        // Verifica che l'utente sia loggato nell'hub
        HubController::checkAuth();
        
        try {
            $baseS3Folder = config('s3_folder') ?? '';
            $prefix = $path;
            $breadcrumbs = [];
            $error = null;

            // Se la cartella base non è definita, genera un errore
            if (empty($baseS3Folder)) {
                throw new \InvalidArgumentException("La cartella S3 's3_folder' non è configurata.");
            }

            // Normalizza il prefisso per la richiesta S3
            if ($prefix !== null) {
                $prefix = urldecode($prefix);
                if (strpos($prefix, '..') !== false) {
                    throw new \InvalidArgumentException('Percorso non valido.');
                }
                $prefix = rtrim($prefix, '/');
            }
            
            $fullPrefix = $baseS3Folder;
            if (!empty($prefix)) {
                $fullPrefix .= '/' . $prefix;
            }
            if ($fullPrefix !== '' && substr($fullPrefix, -1) !== '/') {
                $fullPrefix .= '/';
            }

            $s3 = new S3Manager();
            $files = $s3->listObjects($fullPrefix);
            
            // Se la cartella iniziale è vuota, mostro un errore
            if (empty($files) && (empty($path) || $path === $baseS3Folder)) {
                 $error = "La cartella S3 '{$baseS3Folder}' non esiste o è vuota.";
            }

            // Costruisce i breadcrumbs e i percorsi dei file per la vista
            $pathSegments = array_filter(explode('/', $prefix ?? ''));
            $currentPath = '';
            foreach ($pathSegments as $segment) {
                $currentPath .= $segment . '/';
                $breadcrumbs[$segment] = rtrim($currentPath, '/');
            }
            if (!empty($breadcrumbs)) {
                $last = array_key_last($breadcrumbs);
                $breadcrumbs[$last] = null;
            }
            
            // Rimuovi il prefisso della cartella base dai percorsi per la vista
            foreach ($files as &$file) {
                $file['path'] = ltrim(substr($file['path'], strlen($baseS3Folder)), '/');
            }
            unset($file);

            render('s3_list', [
                'files'           => $files,
                'bucket'          => $s3->getBucketName(),
                'breadcrumbs'     => $breadcrumbs,
                'current_prefix'  => $prefix,
                'error'           => $error
            ]);

        } catch (\Throwable $e) {
            render('s3_list', [
                'error'       => $e->getMessage(),
                'bucket'      => '',
                'breadcrumbs' => []
            ]);
        }
    }
    
    /**
     * Recupera e restituisce il contenuto di un file da S3.
     * @param string $path La chiave (percorso) del file su S3.
     */
    public function getFileContent(string $path)
    {
        try {
            $baseS3Folder = config('s3_folder') ?? '';

            // Decodifica il percorso e aggiunge la cartella base
            $fullPath = $baseS3Folder . '/' . urldecode($path);
            
            if (strpos($fullPath, '..') !== false) {
                http_response_code(403);
                exit('Percorso non valido.');
            }
    
            $s3 = new S3Manager();
            $content = $s3->getFile($fullPath);
    
            if ($content === null) {
                http_response_code(404);
                exit('File non trovato.');
            }
    
            $extension = $this->getExtension($fullPath);
            $mimeType = match($extension) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'mp4' => 'video/mp4',
                default => 'text/plain',
            };
    
            header('Content-Type: ' . $mimeType);
            echo $content;
            exit;
    
        } catch (\Throwable $e) {
            http_response_code(500);
            exit('Errore interno del server: ' . $e->getMessage());
        }
    }

    /**
     * Restituisce l'estensione di un file.
     * @param string $path
     * @return string
     */
    private function getExtension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }
}