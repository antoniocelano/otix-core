<?php
namespace App\Controller;

class GetPublicFileController
{
    public function file(...$segments)
    {
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

        $relativePath = implode('/', $segments);
        
        // Il percorso di base è la cartella /public
        $basePublicPath = BASE_PATH . '/public/';
        $fullPath = $basePublicPath . $relativePath;
        
        // --- Controlli di sicurezza ---
        $realBasePath = realpath($basePublicPath);
        $realFullPath = realpath($fullPath);
        
        if ($realFullPath === false || strpos($realFullPath, $realBasePath) !== 0) {
            http_response_code(404);
            return 0;
        }
        
        // 2. Controlla che il tipo di file sia permesso
        $extension = strtolower(pathinfo($realFullPath, PATHINFO_EXTENSION));
        if (!array_key_exists($extension, $allowedMimeTypes)) {
            http_response_code(403); // Forbidden
            return 0;
        }

        // --- Servi il file ---
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header("Content-Type: " . $allowedMimeTypes[$extension]);
        header("Content-Length: " . filesize($realFullPath));
        
        readfile($realFullPath);
        return 0;
    }
}