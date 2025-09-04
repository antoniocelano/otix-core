<?php

namespace App\Controller;

use App\Core\S3Manager;

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

            // Recupera TUTTE le cartelle del bucket
            $allFolders = $this->getAllFolders($s3);

            render('s3_list', [
                'files'           => $files,
                'bucket'          => config('s3_folder'),
                'breadcrumbs'     => $breadcrumbs,
                'current_prefix'  => $prefix,
                'error'           => $error,
                'all_folders'     => $allFolders // Passa tutte le cartelle alla vista
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
     * Recupera una lista di tutte le cartelle (prefissi) dal bucket.
     * @param S3Manager $s3 L'istanza di S3Manager.
     * @return array Un array di stringhe che rappresentano i percorsi delle cartelle.
     */
    private function getAllFolders(S3Manager $s3): array
    {
        $allFolders = [];
        $baseS3Folder = config('s3_folder') ?? '';
        $continuationToken = null;

        do {
            $params = [
                'prefix' => $baseS3Folder . '/',
                'continuation-token' => $continuationToken
            ];
            
            // Chiamata all'API S3 senza il 'delimiter' per ottenere un elenco flat
            $response = $s3->getS3Client()->getBucket($s3->getBucketName(), [], $params);
            
            if ($response->error) {
                return [];
            }
            
            $xml = $response->body;
            
            // Estrai i percorsi delle cartelle dai nomi dei file
            foreach ($xml->Contents as $content) {
                $key = (string) $content->Key;
                $relativeKey = substr($key, strlen($baseS3Folder) + 1); // remove base folder
                $pathParts = explode('/', $relativeKey);
                
                // Ignora i file nella radice e l'ultimo elemento (il nome del file stesso)
                $folderPath = '';
                for ($i = 0; $i < count($pathParts) - 1; $i++) {
                    $folderPath .= $pathParts[$i] . '/';
                    $allFolders[$folderPath] = true;
                }
            }

            $continuationToken = (string) $xml->NextContinuationToken;
            
        } while (!empty($continuationToken));

        $finalFolders = array_keys($allFolders);
        sort($finalFolders);

        return $finalFolders;
    }

    /**
     * Carica un file nella cartella S3 corrente.
     */
    public function uploadFile()
    {
        HubController::checkAuth();
        
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }

        if (empty($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['upload_error'] = 'Errore nel caricamento del file.';
            header("Location: {$redirectPath}");
            return 0;
        }

        try {
            $baseS3Folder = config('s3_folder');
            $fileName = basename($_FILES['fileToUpload']['name']);
            
            $destinationKey = $baseS3Folder;
            if ($currentPath !== '') {
                $destinationKey .= '/' . $currentPath;
            }
            $destinationKey .= '/' . $fileName;

            $s3 = new S3Manager();
            if ($s3->putFile($destinationKey, $_FILES['fileToUpload']['tmp_name'])) {
                $_SESSION['upload_success'] = "File '{$fileName}' caricato con successo.";
            } else {
                $_SESSION['upload_error'] = "Errore durante il caricamento del file su S3.";
            }

        } catch (\Throwable $e) {
            $_SESSION['upload_error'] = 'Errore interno del server: ' . $e->getMessage();
        }

        header("Location: {$redirectPath}");
        return 0;
    }

    /**
     * Elimina un file da S3.
     */
    public function deleteFile()
    {
        HubController::checkAuth();

        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }

        if (empty($_POST['file_path'])) {
            $_SESSION['delete_error'] = 'Percorso file non specificato.';
            header("Location: {$redirectPath}");
            return 0;
        }

        try {
            $baseS3Folder = config('s3_folder');
            $fullPath = $baseS3Folder . '/' . $_POST['file_path'];

            $s3Manager = new S3Manager();
            $s3Client = $s3Manager->getS3Client();
            $bucketName = $s3Manager->getBucketName();

            // Usa direttamente la funzione deleteObject
            $response = $s3Client->deleteObject($bucketName, $fullPath);
            
            if (!$response->error) {
                $_SESSION['delete_success'] = "File '" . basename($_POST['file_path']) . "' eliminato con successo.";
            } else {
                $_SESSION['delete_error'] = "Errore durante l'eliminazione del file: " . $response->error['message'];
            }

        } catch (\Throwable $e) {
            $_SESSION['delete_error'] = 'Errore interno del server: ' . $e->getMessage();
        }

        header("Location: {$redirectPath}");
        return 0;
    }

    public function copyFile()
    {
        HubController::checkAuth();
        
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }
        
        $sourceFilePath = $_POST['source_file_path'] ?? '';
        $destinationFolderPath = $_POST['destination_folder_path'] ?? '';
        
        $newFileName = $_POST['new_file_name'] ?? '';

        if (empty($newFileName)) {
            $newFileName = basename($sourceFilePath);
        }
    
        if (empty($sourceFilePath)) {
            $_SESSION['copy_error'] = 'Percorso del file di origine non specificato.';
            header("Location: {$redirectPath}");
            return 0;
        }
        
        if (empty($newFileName)) {
            $_SESSION['copy_error'] = 'Il nome del file di destinazione non può essere vuoto.';
            header("Location: {$redirectPath}");
            return 0;
        }

    
        try {
            $baseS3Folder = config('s3_folder') ?? '';
            
            $sourceKey = $baseS3Folder . '/' . $sourceFilePath;
            
            $destinationKey = $baseS3Folder;
            if ($destinationFolderPath !== '') {
                $destinationKey .= '/' . $destinationFolderPath;
            }
            $destinationKey .= '/' . $newFileName;
            
            $s3Manager = new S3Manager();
            $existingFile = $s3Manager->getFile($destinationKey);
            if ($existingFile !== null) {
                $_SESSION['copy_error'] = "Il file '" . $newFileName . "' esiste già nella destinazione.";
                header("Location: {$redirectPath}");
                return 0;
            }
            
            if ($s3Manager->copyFile($sourceKey, $destinationKey)) {
                $_SESSION['copy_success'] = "File '" . basename($sourceFilePath) . "' copiato con successo come '" . $newFileName . "' in '" . ($destinationFolderPath ?: 'radice') . "'.";
            } else {
                $_SESSION['copy_error'] = "Errore durante la copia del file su S3.";
            }
    
        } catch (\Throwable $e) {
            $_SESSION['copy_error'] = 'Errore interno del server: ' . $e->getMessage();
        }
    
        header("Location: {$redirectPath}");
        return 0;
    }

    public function renameFile()
    {
        HubController::checkAuth();
        
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }
        
        $sourceFilePath = $_POST['source_file_path'] ?? '';
        $newFileName = $_POST['new_file_name'] ?? '';

        if (empty($sourceFilePath) || empty($newFileName)) {
            $_SESSION['rename_error'] = 'Percorso del file di origine e nuovo nome sono obbligatori.';
            header("Location: {$redirectPath}");
            return 0;
        }
        
        try {
            $baseS3Folder = config('s3_folder') ?? '';
            $sourceKey = $baseS3Folder . '/' . $sourceFilePath;

            // Il percorso di destinazione è la stessa cartella, ma con il nuovo nome del file
            $destinationFolder = dirname($sourceFilePath);
            $destinationKey = $baseS3Folder . '/' . ($destinationFolder !== '.' ? $destinationFolder . '/' : '') . $newFileName;
            
            $s3Manager = new S3Manager();

            // Verifica che il nuovo nome non esista già
            if ($s3Manager->getFile($destinationKey) !== null) {
                $_SESSION['rename_error'] = "Il file '{$newFileName}' esiste già.";
                header("Location: {$redirectPath}");
                return 0;
            }
            
            // Se lo spostamento (copia + elimina) ha successo, la rinomina è avvenuta
            if ($s3Manager->moveFile($sourceKey, $destinationKey)) {
                $_SESSION['rename_success'] = "File '" . basename($sourceFilePath) . "' rinominato con successo in '{$newFileName}'.";
            } else {
                $_SESSION['rename_error'] = "Errore durante la rinomina del file su S3.";
            }
    
        } catch (\Throwable $e) {
            $_SESSION['rename_error'] = 'Errore interno del server: ' . $e->getMessage();
        }
    
        header("Location: {$redirectPath}");
        return 0;
    }


    public function moveFile()
    {
        HubController::checkAuth();
        
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }
        
        $sourceFilePath = $_POST['source_file_path'] ?? '';
        $destinationFolderPath = $_POST['destination_folder_path'] ?? '';
        $newFileName = $_POST['new_file_name'] ?? '';

        if (empty($newFileName)) {
            $newFileName = basename($sourceFilePath);
        }
    
        if (empty($sourceFilePath)) {
            $_SESSION['move_error'] = 'Percorso del file di origine non specificato.';
            header("Location: {$redirectPath}");
            return 0;
        }
        
        if (empty($newFileName)) {
            $_SESSION['move_error'] = 'Il nome del file di destinazione non può essere vuoto.';
            header("Location: {$redirectPath}");
            return 0;
        }
    
        try {
            $baseS3Folder = config('s3_folder') ?? '';
            
            $sourceKey = $baseS3Folder . '/' . $sourceFilePath;
            
            $destinationKey = $baseS3Folder;
            if ($destinationFolderPath !== '') {
                $destinationKey .= '/' . $destinationFolderPath;
            }
            $destinationKey .= '/' . $newFileName;
            
            $s3Manager = new S3Manager();
            $existingFile = $s3Manager->getFile($destinationKey);
            if ($existingFile !== null) {
                $_SESSION['move_error'] = "Il file '" . $newFileName . "' esiste già nella destinazione.";
                header("Location: {$redirectPath}");
                return 0;
            }
            
            if ($s3Manager->moveFile($sourceKey, $destinationKey)) {
                $_SESSION['move_success'] = "File '" . basename($sourceFilePath) . "' spostato con successo in '" . ($destinationFolderPath ?: 'radice') . "' come '" . $newFileName . "'.";
            } else {
                $_SESSION['move_error'] = "Errore durante lo spostamento del file su S3.";
            }
    
        } catch (\Throwable $e) {
            $_SESSION['move_error'] = 'Errore interno del server: ' . $e->getMessage();
        }
    
        header("Location: {$redirectPath}");
        return 0;
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
            return 0;
    
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