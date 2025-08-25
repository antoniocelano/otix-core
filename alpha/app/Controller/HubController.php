<?php
namespace App\Controller;

use App\Core\HubDatabase;

class HubController
{
    /**
     * Mostra la dashboard dell'Hub.
     * Controlla se l'utente è autenticato prima di mostrare la pagina.
     */
    public function index()
    {
        if (!isset($_SESSION['hub_user_id'])) {
            header('Location: /' . current_lang() . '/hub/login');
            exit;
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
        if (!isset($_SESSION['hub_user_id'])) {
            header('Location: /' . current_lang() . '/hub/login');
            exit;
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
        $email = sanitize_input($_POST['email'], 'email');
        $password = sanitize_input($_POST['password'], 'str');

        if (!$email || !$password) {
            $_SESSION['hub_error_message'] = 'Email e password sono obbligatori.';
            header('Location: /hub/login');
            exit;
        }

        try {
            $db = new HubDatabase();
            $user = $db->select('users', ['email' => $email]);

            if (!empty($user) && password_verify($password, $user[0]['password'])) {
                session_regenerate_id(true);
                $_SESSION['hub_user_id'] = $user[0]['id'];
                $_SESSION['hub_user_name'] = $user[0]['name'];
                
                unset($_SESSION['hub_error_message']);
                header('Location: /hub');
                exit;
            } else {
                $_SESSION['hub_error_message'] = 'Credenziali non valide.';
                header('Location: /hub/login');
                exit;
            }
        } catch (\PDOException $e) {
            $_SESSION['hub_error_message'] = 'Errore del database. Riprova più tardi.';
            header('Location: /hub/login');
            exit;
        }
    }

    /**
     * Esegue il logout dalla sezione Hub.
     */
    public function logout()
    {
        unset($_SESSION['hub_user_id']);
        unset($_SESSION['hub_user_name']);
        header('Location: /' . current_lang() . '/login');
        exit;
    }
}