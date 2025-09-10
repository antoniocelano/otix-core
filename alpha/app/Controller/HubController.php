<?php
// Dichiarazione del namespace per la classe del controller.
namespace App\Controller;

// Importazione delle classi necessarie per la gestione del database specifico dell'hub,
// del middleware di controllo delle richieste e della sessione.
use App\Core\HubDatabase;
use App\Core\Session; // Importa la classe Session

/**
 * Controller per la gestione dell'autenticazione e delle pagine dell'Hub.
 */
class HubController
{
    /**
     * Mostra la dashboard dell'Hub.
     * Controlla se l'utente è autenticato prima di mostrare la pagina.
     */
    public function index()
    {
        // Verifica se la sessione contiene l'ID dell'utente dell'hub.
        if (!Session::has('hub_user_id')) {
            // Se l'utente non è loggato, reindirizza alla pagina di login dell'hub.
            header('Location: /' . current_lang() . '/hub/login');
            return 0;
        }
        // Se l'utente è autenticato, renderizza la vista della dashboard.
        render('hub/index');
    }

    /**
     * Mostra una pagina generica dell'Hub se l'utente è loggato e la vista esiste.
     * * @param string $page Il nome della vista da caricare dalla cartella 'hub/pages'.
     */
    public function showPage(string $page)
    {
        // Sicurezza: controlla sempre se l'utente è loggato.
        if (!Session::has('hub_user_id')) {
            // Se non è loggato, reindirizza alla pagina di login.
            header('Location: /' . current_lang() . '/hub/login');
            return 0;
        }

        // Pulisce il nome della pagina per sicurezza, rimuovendo caratteri non consentiti.
        $safePage = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
        // Costruisce il percorso relativo della vista.
        $viewPath = "hub/pages/{$safePage}";
        // Costruisce il percorso completo del file della vista.
        $fullPath = __DIR__ . "/../../resources/views/" . THEME_DIR . "/{$viewPath}.php";

        // Controlla se il file della vista esiste.
        if (file_exists($fullPath)) {
            // Se esiste, renderizza la vista.
            render($viewPath);
        } else {
            // Se la vista non esiste, mostra un errore 404 (Not Found)
            // utilizzando il metodo 'code' del controller degli errori.
            (new \App\Controller\ErrorController())->code('ERR001');
        }
    }

    /**
     * Mostra il form di login per l'Hub.
     */
    public function showLoginForm()
    {
        // Renderizza la vista del form di login dell'hub.
        render('hub/login');
    }

    /**
     * Gestisce il processo di login per l'Hub.
     */
    public function login()
    {
        // Utilizza la funzione helper http() per accedere ai dati POST.
        $http = http();
        // Sanitizza l'email e la password dall'input dell'utente.
        $email = sanitize_input($http['post']['email'], 'email');
        $password = sanitize_input($http['post']['password'], 'str');

        // Controlla se email o password sono vuoti.
        if (!$email || !$password) {
            // Se lo sono, imposta un messaggio di errore e reindirizza al login.
            Session::set('hub_error_message', 'Email e password sono obbligatori.');
            header('Location: /hub/login');
            return 0;
        }

        try {
            // Istanzia la classe HubDatabase per interagire con il DB dell'hub.
            $db = new HubDatabase();
            // Cerca un utente con l'email fornita.
            $user = $db->select('users', ['email' => $email]);

            // Verifica se l'utente esiste e se la password inserita corrisponde all'hash nel database.
            if (!empty($user) && password_verify($password, $user[0]['password'])) {
                // Rigenera l'ID di sessione per prevenire attacchi di session fixation.
                session_regenerate_id(true);
                // Salva i dati dell'utente nella sessione dell'hub.
                Session::set('hub_user_id', $user[0]['id']);
                Session::set('hub_user_name', $user[0]['name']);
                
                // Rimuove eventuali messaggi di errore precedenti dalla sessione.
                Session::remove('hub_error_message');
                // Reindirizza alla dashboard dell'hub.
                header('Location: /hub');
                return 0;
            } else {
                // Se le credenziali non sono valide, imposta un messaggio di errore e reindirizza.
                Session::set('hub_error_message', 'Credenziali non valide.');
                header('Location: /hub/login');
                return 0;
            }
        } catch (\PDOException $e) {
            // Cattura le eccezioni del database, imposta un messaggio di errore generico e reindirizza.
            Session::set('hub_error_message', 'Errore del database. Riprova più tardi.');
            header('Location: /hub/login');
            return 0;
        }
    }

    /**
     * Controlla l'autenticazione per l'Hub.
     * È un metodo statico che può essere chiamato da altri controller.
     */
    public static function checkAuth()
    {
        // Verifica se l'ID utente dell'hub è presente nella sessione.
        if (!Session::has('hub_user_id')) {
            // Se non è presente, reindirizza alla pagina di login dell'hub.
            header('Location: /hub/login');
            return 0;
        }
    }

    /**
     * Esegue il logout dalla sezione Hub.
     */
    public function logout()
    {
        // Rimuove specifici dati utente dalla sessione dell'hub.
        Session::remove('hub_user_id');
        Session::remove('hub_user_name');
        // Reindirizza alla pagina di login principale del sito, tenendo conto della lingua corrente.
        header('Location: /' . current_lang() . '/login');
        return 0;
    }
}