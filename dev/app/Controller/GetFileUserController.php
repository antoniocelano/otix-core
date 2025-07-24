<?php
namespace App\Controller;

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
}