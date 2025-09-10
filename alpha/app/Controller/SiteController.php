<?php
namespace App\Controller;

use App\Core\Session;

/**
 * Controller principale per la gestione del sito e dell'area amministrativa.
 */
class SiteController
{
    /**
     * Il punto di ingresso dell'applicazione.
     * Gestisce il reindirizzamento iniziale a seconda della configurazione e dello stato dell'utente.
     */
    public function entrypoint()
    {
        // Recupera la lingua predefinita dalla configurazione.
        $lang = config('default_lang');

        // Imposta un cookie per la lingua.
        setcookie('otxlang', $lang, [
            'expires' => time() + 30 * 24 * 3600, // Il cookie scade dopo 30 giorni.
            'path' => '/',                       // Il cookie è disponibile per tutto il sito.
            'secure' => 'https',                 // Il cookie viene inviato solo su connessioni HTTPS.
            'httponly' => true,                  // Il cookie non è accessibile tramite JavaScript.
            'samesite' => 'Strict',              // Il cookie viene inviato solo per richieste dello stesso sito.
        ]);

        // Controlla se l'applicazione è configurata come un sito web pubblico.
        if (config('is_site') === true) {
            // Se 'is_site' è true, reindirizza alla pagina index del sito.
            header("Location: /" . $lang . "/index");
            exit; // Termina lo script per evitare esecuzioni successive.
        } else {
            // Se 'is_site' è false, l'applicazione funziona come un pannello di amministrazione.
            // Controlla se l'utente ha una sessione attiva.
            if (Session::has('user_id')) {
                // Se l'utente è loggato, reindirizza alla dashboard amministrativa.
                header("Location: /" . $lang . "/admin");
                exit; // Termina lo script.
            } else {
                // Se l'utente non è loggato, reindirizza alla pagina di login.
                header("Location: /" . $lang . "/login");
                exit; // Termina lo script.
            }
        }
    }

    /**
     * Mostra la pagina principale del sito.
     */
    public function index(): void
    {
        // Ottiene la lingua corrente dell'applicazione.
        $lang = current_lang();
        // Renderizza la vista 'index' passando la variabile $lang.
        render('index', compact('lang'));
    }

    /**
     * Mostra la pagina della guida al database.
     */
    public function dbGuide(): void
    {
        // Ottiene la lingua corrente dell'applicazione.
        $lang = current_lang();
        // Renderizza la vista 'db_guide' passando la variabile $lang.
        render('db-guide', compact('lang'));
    }

    /**
     * Mostra la pagina della documentazione.
     */
    public function docs(): void
    {
        // Ottiene la lingua corrente dell'applicazione.
        $lang = current_lang();
        // Renderizza la vista 'db_guide' passando la variabile $lang.
        render('docs', compact('lang'));
    }

    /**
     * Mostra le pagine dell'area amministrativa.
     *
     * @param string|null $page Il nome della sottopagina da caricare, se presente.
     */
    public function admin(?string $page = null): void
    {
        // Ottiene la lingua corrente.
        $lang = current_lang();
        // Determina il nome della sottopagina o usa 'index' come predefinito.
        $sub = $page ?: 'index';
        // Costruisce il percorso della vista.
        $view = "admin/{$sub}";
        // Costruisce il percorso completo del file della vista.
        $viewPath = __DIR__ . "/../../resources/views/" . THEME_DIR . "/{$view}.php";

        // Controlla se il file della vista esiste nel tema corrente.
        if (!file_exists($viewPath)) {
            // Se non esiste, cerca il file nel tema predefinito.
            $viewPath = __DIR__ . "/../../resources/views/{$view}.php";
        }

        // Controlla nuovamente se il file della vista esiste dopo il secondo tentativo.
        if (!file_exists($viewPath)) {
            // Se la vista non viene trovata, gestisce l'errore chiamando il controller di errore.
            (new \App\Controller\ErrorController())->code('ERR001');
            return; // Termina l'esecuzione.
        }

        // Se la vista è stata trovata, la renderizza.
        render($view, compact('lang'));
    }
}