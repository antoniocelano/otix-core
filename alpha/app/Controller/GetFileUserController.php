<?php
namespace App\Controller;

use App\Core\S3Manager;

class GetFileUserController
{
    public function file(...$segments)
    {
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

        $relativePath = implode('/', $segments);
        
        $userCode = DOMAIN_CODE;

        $baseUserPath = BASE_PATH . '/users/' . $userCode . '/';
        $fullPath = $baseUserPath . $relativePath;
        
        $realBasePath = realpath($baseUserPath);
        $realFullPath = realpath($fullPath);
        
        if ($realFullPath === false || strpos($realFullPath, $realBasePath) !== 0) {
            http_response_code(404);
            exit;
        }
        
        $extension = strtolower(pathinfo($realFullPath, PATHINFO_EXTENSION));
        if (!array_key_exists($extension, $allowedMimeTypes)) {
            http_response_code(404);
            exit;
        }

        // Pulisce i buffer e invia il file con il Content-Type corretto
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header("Content-Type: " . $allowedMimeTypes[$extension]);
        header("Content-Length: " . filesize($realFullPath));
        
        readfile($realFullPath);
        exit;
    }

    /**
     * Recupera un file da S3 e lo restituisce al browser.
     * @param string $path La chiave (percorso) del file su S3.
     */
    public function s3File(string $path)
    {
        // Verifica che l'utente sia loggato nell'hub
        HubController::checkAuth();
        
        try {
            $baseS3Folder = config('s3_folder') ?? '';
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
            
            $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $mimeType = match($extension) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'mp4' => 'video/mp4',
                'css' => 'text/css',
                'js'  => 'application/javascript',
                default => 'application/octet-stream', // Fallback per altri tipi di file
            };

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . strlen($content));
            echo $content;
            exit;

        } catch (\Throwable $e) {
            http_response_code(500);
            error_log('Errore S3: ' . $e->getMessage());
            exit('Errore interno del server.');
        }
    }
}