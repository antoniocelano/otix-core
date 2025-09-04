<?php
namespace App\Controller;

use App\Core\HubDatabase;
use App\Middleware\CheckRequest;
use App\Core\Session; // Importa la classe Session

class HubController
{
    /**
     * Mostra la dashboard dell'Hub.
     * Controlla se l'utente è autenticato prima di mostrare la pagina.
     */
    public function index()
    {
        if (!Session::has('hub_user_id')) {
            header('Location: /' . current_lang() . '/hub/login');
            return 0;
        }
        render('hub/index');
    }

    /**
     * Mostra una pagina generica dell'Hub se l'utente è loggato e la vista esiste.
     * @param string $page Il nome della vista da caricare dalla cartella 'hub/pages'.
     */
    public function showPage(string $page)
    {
        // Sicurezza: controlla sempre se l'utente è loggato
        if (!Session::has('hub_user_id')) {
            header('Location: /' . current_lang() . '/hub/login');
            return 0;
        }

        // Pulisce il nome della pagina per sicurezza
        $safePage = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
        $viewPath = "hub/pages/{$safePage}";
        $fullPath = __DIR__ . "/../../resources/views/" . THEME_DIR . "/{$viewPath}.php";

        // Controlla se il file della vista esiste
        if (file_exists($fullPath)) {
            render($viewPath);
        } else {
            // Se la vista non esiste, mostra un errore 404
            (new \App\Controller\ErrorController())->code('ERR001');
        }
    }

    /**
     * Mostra il form di login per l'Hub.
     */
    public function showLoginForm()
    {
        render('hub/login');
    }

    /**
     * Gestisce il processo di login per l'Hub.
     */
    public function login()
    {
        // Utilizza la funzione helper http() per accedere ai dati POST
        $http = http();
        $email = sanitize_input($http['post']['email'], 'email');
        $password = sanitize_input($http['post']['password'], 'str');

        if (!$email || !$password) {
            Session::set('hub_error_message', 'Email e password sono obbligatori.');
            header('Location: /hub/login');
            return 0;
        }

        try {
            $db = new HubDatabase();
            $user = $db->select('users', ['email' => $email]);

            if (!empty($user) && password_verify($password, $user[0]['password'])) {
                session_regenerate_id(true);
                Session::set('hub_user_id', $user[0]['id']);
                Session::set('hub_user_name', $user[0]['name']);
                
                Session::remove('hub_error_message');
                header('Location: /hub');
                return 0;
            } else {
                Session::set('hub_error_message', 'Credenziali non valide.');
                header('Location: /hub/login');
                return 0;
            }
        } catch (\PDOException $e) {
            Session::set('hub_error_message', 'Errore del database. Riprova più tardi.');
            header('Location: /hub/login');
            return 0;
        }
    }

    public static function checkAuth()
    {
        if (!Session::has('hub_user_id')) {
            header('Location: /hub/login');
            return 0;
        }
    }

    /**
     * Esegue il logout dalla sezione Hub.
     */
    public function logout()
    {
        Session::remove('hub_user_id');
        Session::remove('hub_user_name');
        header('Location: /' . current_lang() . '/login');
        return 0;
    }
}