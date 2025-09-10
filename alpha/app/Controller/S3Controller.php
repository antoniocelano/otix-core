<?php

// Dichiarazione del namespace per la classe del controller.
namespace App\Controller;

// Importa le classi necessarie: S3Manager per l'interazione con Amazon S3
// e HubController per la gestione dell'autenticazione.
use App\Core\S3Manager;
use App\Controller\HubController;

/**
 * Controller per la gestione dei file su Amazon S3.
 * Include funzionalità per listare, caricare, eliminare, copiare,
 * rinominare e spostare file.
 */
class S3Controller
{
    /**
     * Mostra la lista dei file e delle cartelle presenti nel bucket S3.
     *
     * @param string|null $path Il percorso della cartella da visualizzare. Se nullo, mostra la root.
     * @return void
     */
    public function listAllFiles($path = null)
    {
        // Verifica che l'utente sia autenticato come utente dell'hub.
        HubController::checkAuth();
        
        try {
            // Ottiene la cartella base S3 dalla configurazione.
            $baseS3Folder = config('s3_folder') ?? '';
            $prefix = $path;
            $breadcrumbs = [];
            $error = null;

            // Se la cartella base non è definita nella configurazione, solleva un'eccezione.
            if (empty($baseS3Folder)) {
                throw new \InvalidArgumentException("La cartella S3 's3_folder' non è configurata.");
            }

            // Normalizza il prefisso (il percorso) per la richiesta S3.
            if ($prefix !== null) {
                $prefix = urldecode($prefix);
                // Previene attacchi di directory traversal.
                if (strpos($prefix, '..') !== false) {
                    throw new \InvalidArgumentException('Percorso non valido.');
                }
                $prefix = rtrim($prefix, '/');
            }
            
            // Costruisce il prefisso completo da usare con l'API S3.
            $fullPrefix = $baseS3Folder;
            if (!empty($prefix)) {
                $fullPrefix .= '/' . $prefix;
            }
            // Aggiunge uno slash finale al prefisso se non è vuoto.
            if ($fullPrefix !== '' && substr($fullPrefix, -1) !== '/') {
                $fullPrefix .= '/';
            }

            // Istanzia il gestore S3 e recupera la lista degli oggetti (file e cartelle).
            $s3 = new S3Manager();
            $files = $s3->listObjects($fullPrefix);
            
            // Se la cartella iniziale è vuota, imposta un messaggio di errore.
            if (empty($files) && (empty($path) || $path === $baseS3Folder)) {
                 $error = "La cartella S3 '{$baseS3Folder}' non esiste o è vuota.";
            }

            // Costruisce la logica per i breadcrumbs (navigazione del percorso).
            $pathSegments = array_filter(explode('/', $prefix ?? ''));
            $currentPath = '';
            foreach ($pathSegments as $segment) {
                $currentPath .= $segment . '/';
                $breadcrumbs[$segment] = rtrim($currentPath, '/');
            }
            if (!empty($breadcrumbs)) {
                $last = array_key_last($breadcrumbs);
                $breadcrumbs[$last] = null; // Il percorso dell'ultimo breadcrumb è nullo (pagina corrente).
            }
            
            // Rimuove il prefisso della cartella base dai percorsi dei file per renderli relativi alla vista.
            foreach ($files as &$file) {
                $file['path'] = ltrim(substr($file['path'], strlen($baseS3Folder)), '/');
            }
            unset($file);

            // Recupera l'elenco di tutte le cartelle nel bucket per la selezione della destinazione.
            $allFolders = $this->getAllFolders($s3);

            // Renderizza la vista 's3_list' passando tutti i dati necessari.
            render('s3_list', [
                'files'           => $files,
                'bucket'          => config('s3_folder'),
                'breadcrumbs'     => $breadcrumbs,
                'current_prefix'  => $prefix,
                'error'           => $error,
                'all_folders'     => $allFolders // Passa tutte le cartelle alla vista
            ]);

        } catch (\Throwable $e) {
            // In caso di eccezione, renderizza la vista con un messaggio di errore.
            render('s3_list', [
                'error'       => $e->getMessage(),
                'bucket'      => '',
                'breadcrumbs' => []
            ]);
        }
    }

    /**
     * Recupera una lista di tutte le cartelle (prefissi) dal bucket S3.
     *
     * @param S3Manager $s3 L'istanza di S3Manager.
     * @return array Un array di stringhe che rappresentano i percorsi delle cartelle.
     */
    private function getAllFolders(S3Manager $s3): array
    {
        $allFolders = [];
        $baseS3Folder = config('s3_folder') ?? '';
        $continuationToken = null;

        // Esegue una scansione paginata del bucket.
        do {
            $params = [
                'prefix' => $baseS3Folder . '/',
                'continuation-token' => $continuationToken
            ];
            
            // Chiamata all'API S3 per ottenere un elenco piatto di tutti gli oggetti nel prefisso.
            $response = $s3->getS3Client()->getBucket($s3->getBucketName(), [], $params);
            
            // Se c'è un errore nella risposta, restituisce un array vuoto.
            if ($response->error) {
                return [];
            }
            
            $xml = $response->body;
            
            // Estrae i percorsi delle cartelle dai nomi dei file.
            foreach ($xml->Contents as $content) {
                $key = (string) $content->Key;
                $relativeKey = substr($key, strlen($baseS3Folder) + 1); // Rimuove la cartella base.
                $pathParts = explode('/', $relativeKey);
                
                // Ignora i file nella radice e l'ultimo elemento (il nome del file stesso).
                $folderPath = '';
                for ($i = 0; $i < count($pathParts) - 1; $i++) {
                    $folderPath .= $pathParts[$i] . '/';
                    $allFolders[$folderPath] = true; // Usa un array associativo per evitare duplicati.
                }
            }

            // Ottiene il token per la prossima pagina di risultati.
            $continuationToken = (string) $xml->NextContinuationToken;
            
        } while (!empty($continuationToken)); // Continua finché ci sono risultati.

        // Estrae le chiavi (i percorsi delle cartelle) e le ordina.
        $finalFolders = array_keys($allFolders);
        sort($finalFolders);

        return $finalFolders;
    }

    /**
     * Carica un file nella cartella S3 corrente.
     *
     * @return void
     */
    public function uploadFile()
    {
        // Controlla l'autenticazione.
        HubController::checkAuth();
        
        // Imposta il percorso di reindirizzamento.
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }

        // Verifica che il file sia stato caricato senza errori.
        if (empty($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['upload_error'] = 'Errore nel caricamento del file.';
            header("Location: {$redirectPath}");
            return 0;
        }

        try {
            $baseS3Folder = config('s3_folder');
            $fileName = basename($_FILES['fileToUpload']['name']);
            
            // Costruisce la chiave di destinazione per S3.
            $destinationKey = $baseS3Folder;
            if ($currentPath !== '') {
                $destinationKey .= '/' . $currentPath;
            }
            $destinationKey .= '/' . $fileName;

            $s3 = new S3Manager();
            // Tenta di caricare il file.
            if ($s3->putFile($destinationKey, $_FILES['fileToUpload']['tmp_name'])) {
                $_SESSION['upload_success'] = "File '{$fileName}' caricato con successo.";
            } else {
                $_SESSION['upload_error'] = "Errore durante il caricamento del file su S3.";
            }

        } catch (\Throwable $e) {
            // Gestisce le eccezioni e imposta un messaggio di errore.
            $_SESSION['upload_error'] = 'Errore interno del server: ' . $e->getMessage();
        }

        // Reindirizza l'utente alla lista dei file.
        header("Location: {$redirectPath}");
        return 0;
    }

    /**
     * Elimina un file da S3.
     *
     * @return void
     */
    public function deleteFile()
    {
        // Controlla l'autenticazione.
        HubController::checkAuth();

        // Imposta il percorso di reindirizzamento.
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }

        // Verifica che il percorso del file da eliminare sia stato specificato.
        if (empty($_POST['file_path'])) {
            $_SESSION['delete_error'] = 'Percorso file non specificato.';
            header("Location: {$redirectPath}");
            return 0;
        }

        try {
            $baseS3Folder = config('s3_folder');
            // Costruisce la chiave completa del file.
            $fullPath = $baseS3Folder . '/' . $_POST['file_path'];

            $s3Manager = new S3Manager();
            $s3Client = $s3Manager->getS3Client();
            $bucketName = $s3Manager->getBucketName();

            // Chiama la funzione di eliminazione diretta dell'oggetto S3.
            $response = $s3Client->deleteObject($bucketName, $fullPath);
            
            // Controlla il successo dell'operazione.
            if (!$response->error) {
                $_SESSION['delete_success'] = "File '" . basename($_POST['file_path']) . "' eliminato con successo.";
            } else {
                $_SESSION['delete_error'] = "Errore durante l'eliminazione del file: " . $response->error['message'];
            }

        } catch (\Throwable $e) {
            // Gestisce le eccezioni.
            $_SESSION['delete_error'] = 'Errore interno del server: ' . $e->getMessage();
        }

        // Reindirizza alla lista dei file.
        header("Location: {$redirectPath}");
        return 0;
    }

    /**
     * Copia un file da una posizione a un'altra all'interno del bucket S3.
     *
     * @return void
     */
    public function copyFile()
    {
        // Controlla l'autenticazione.
        HubController::checkAuth();
        
        // Imposta il percorso di reindirizzamento e recupera i dati dal form.
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }
        
        $sourceFilePath = $_POST['source_file_path'] ?? '';
        $destinationFolderPath = $_POST['destination_folder_path'] ?? '';
        $newFileName = $_POST['new_file_name'] ?? '';

        // Se il nuovo nome non è specificato, usa il nome originale.
        if (empty($newFileName)) {
            $newFileName = basename($sourceFilePath);
        }
    
        // Controlla la validità degli input.
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
            
            // Costruisce le chiavi di origine e destinazione per S3.
            $sourceKey = $baseS3Folder . '/' . $sourceFilePath;
            
            $destinationKey = $baseS3Folder;
            if ($destinationFolderPath !== '') {
                $destinationKey .= '/' . $destinationFolderPath;
            }
            $destinationKey .= '/' . $newFileName;
            
            $s3Manager = new S3Manager();
            // Verifica se un file con lo stesso nome esiste già nella destinazione.
            $existingFile = $s3Manager->getFile($destinationKey);
            if ($existingFile !== null) {
                $_SESSION['copy_error'] = "Il file '" . $newFileName . "' esiste già nella destinazione.";
                header("Location: {$redirectPath}");
                return 0;
            }
            
            // Tenta di copiare il file.
            if ($s3Manager->copyFile($sourceKey, $destinationKey)) {
                $_SESSION['copy_success'] = "File '" . basename($sourceFilePath) . "' copiato con successo come '" . $newFileName . "' in '" . ($destinationFolderPath ?: 'radice') . "'.";
            } else {
                $_SESSION['copy_error'] = "Errore durante la copia del file su S3.";
            }
    
        } catch (\Throwable $e) {
            // Gestisce le eccezioni.
            $_SESSION['copy_error'] = 'Errore interno del server: ' . $e->getMessage();
        }
    
        // Reindirizza.
        header("Location: {$redirectPath}");
        return 0;
    }

    /**
     * Rinomina un file all'interno della stessa cartella.
     *
     * @return void
     */
    public function renameFile()
    {
        // Controlla l'autenticazione.
        HubController::checkAuth();
        
        // Imposta il percorso di reindirizzamento e recupera i dati.
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }
        
        $sourceFilePath = $_POST['source_file_path'] ?? '';
        $newFileName = $_POST['new_file_name'] ?? '';

        // Controlla la validità degli input.
        if (empty($sourceFilePath) || empty($newFileName)) {
            $_SESSION['rename_error'] = 'Percorso del file di origine e nuovo nome sono obbligatori.';
            header("Location: {$redirectPath}");
            return 0;
        }
        
        try {
            $baseS3Folder = config('s3_folder') ?? '';
            $sourceKey = $baseS3Folder . '/' . $sourceFilePath;

            // Determina la cartella di destinazione.
            $destinationFolder = dirname($sourceFilePath);
            $destinationKey = $baseS3Folder . '/' . ($destinationFolder !== '.' ? $destinationFolder . '/' : '') . $newFileName;
            
            $s3Manager = new S3Manager();

            // Verifica che il nuovo nome non esista già.
            if ($s3Manager->getFile($destinationKey) !== null) {
                $_SESSION['rename_error'] = "Il file '{$newFileName}' esiste già.";
                header("Location: {$redirectPath}");
                return 0;
            }
            
            // Utilizza la funzione 'moveFile' che è una combinazione di copia ed eliminazione.
            if ($s3Manager->moveFile($sourceKey, $destinationKey)) {
                $_SESSION['rename_success'] = "File '" . basename($sourceFilePath) . "' rinominato con successo in '{$newFileName}'.";
            } else {
                $_SESSION['rename_error'] = "Errore durante la rinomina del file su S3.";
            }
    
        } catch (\Throwable $e) {
            // Gestisce le eccezioni.
            $_SESSION['rename_error'] = 'Errore interno del server: ' . $e->getMessage();
        }
    
        // Reindirizza.
        header("Location: {$redirectPath}");
        return 0;
    }


    /**
     * Sposta un file da una cartella a un'altra all'interno del bucket S3.
     *
     * @return void
     */
    public function moveFile()
    {
        // Controlla l'autenticazione.
        HubController::checkAuth();
        
        // Imposta il percorso di reindirizzamento e recupera i dati.
        $redirectPath = '/s3/list';
        $currentPath = $_POST['current_path'] ?? '';
        if ($currentPath !== '') {
            $redirectPath .= '/' . $currentPath;
        }
        
        $sourceFilePath = $_POST['source_file_path'] ?? '';
        $destinationFolderPath = $_POST['destination_folder_path'] ?? '';
        $newFileName = $_POST['new_file_name'] ?? '';

        // Se il nuovo nome non è specificato, usa quello originale.
        if (empty($newFileName)) {
            $newFileName = basename($sourceFilePath);
        }
    
        // Controlla la validità degli input.
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
            
            // Costruisce le chiavi di origine e destinazione per S3.
            $sourceKey = $baseS3Folder . '/' . $sourceFilePath;
            
            $destinationKey = $baseS3Folder;
            if ($destinationFolderPath !== '') {
                $destinationKey .= '/' . $destinationFolderPath;
            }
            $destinationKey .= '/' . $newFileName;
            
            $s3Manager = new S3Manager();
            // Verifica se un file con lo stesso nome esiste già nella destinazione.
            $existingFile = $s3Manager->getFile($destinationKey);
            if ($existingFile !== null) {
                $_SESSION['move_error'] = "Il file '" . $newFileName . "' esiste già nella destinazione.";
                header("Location: {$redirectPath}");
                return 0;
            }
            
            // Tenta di spostare il file (copia + elimina).
            if ($s3Manager->moveFile($sourceKey, $destinationKey)) {
                $_SESSION['move_success'] = "File '" . basename($sourceFilePath) . "' spostato con successo in '" . ($destinationFolderPath ?: 'radice') . "' come '" . $newFileName . "'.";
            } else {
                $_SESSION['move_error'] = "Errore durante lo spostamento del file su S3.";
            }
    
        } catch (\Throwable $e) {
            // Gestisce le eccezioni.
            $_SESSION['move_error'] = 'Errore interno del server: ' . $e->getMessage();
        }
    
        // Reindirizza.
        header("Location: {$redirectPath}");
        return 0;
    }
    
    /**
     * Recupera e restituisce il contenuto di un file da S3.
     *
     * @param string $path La chiave (percorso) del file su S3.
     * @return void
     */
    public function getFileContent(string $path)
    {
        try {
            $baseS3Folder = config('s3_folder') ?? '';

            // Decodifica il percorso e aggiunge la cartella base.
            $fullPath = $baseS3Folder . '/' . urldecode($path);
            
            // Previene attacchi di directory traversal.
            if (strpos($fullPath, '..') !== false) {
                http_response_code(403);
                exit('Percorso non valido.');
            }
    
            $s3 = new S3Manager();
            // Ottiene il contenuto del file.
            $content = $s3->getFile($fullPath);
    
            // Se il file non è trovato, restituisce 404.
            if ($content === null) {
                http_response_code(404);
                exit('File non trovato.');
            }
    
            // Determina il tipo MIME in base all'estensione del file.
            $extension = $this->getExtension($fullPath);
            $mimeType = match($extension) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'mp4' => 'video/mp4',
                default => 'text/plain', // Default per i file di testo.
            };
    
            // Imposta l'header Content-Type e stampa il contenuto.
            header('Content-Type: ' . $mimeType);
            echo $content;
            return 0;
    
        } catch (\Throwable $e) {
            // Gestisce le eccezioni interne.
            http_response_code(500);
            exit('Errore interno del server: ' . $e->getMessage());
        }
    }

    /**
     * Restituisce l'estensione di un file.
     *
     * @param string $path Il percorso del file.
     * @return string L'estensione del file in minuscolo.
     */
    private function getExtension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }
}