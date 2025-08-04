<?php
namespace App\Controller;

use App\Core\Database;

class AuthController
{
    /**
     * Mostra il form di login.
     */
    public function showLoginForm()
    {
        render('login');
    }

    public function login()
    {
        $email = eq($_POST['email']) ?? null;
        $password = eq($_POST['password']) ?? null;

        if (!$email || !$password) {
            // Se mancano dati, reindirizza con un errore.
            $_SESSION['error_message'] = 'Email e password sono obbligatori.';
            header('Location: /login');
            exit;
        }

        try {
            $db = new Database();
            // Cerca l'utente tramite email.
            $user = $db->select('users', ['email' => $email]);

            if (!empty($user) && password_verify($password, $user[0]['password'])) {
                // Login riuscito: rigenera la sessione per sicurezza
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user[0]['id'];
                $_SESSION['user_name'] = $user[0]['name'];
                
                // Rimuovi eventuali messaggi di errore precedenti
                unset($_SESSION['error_message']);

                if(config('is_site') === true){
                    // Reindirizza alla dashboard admin
                    header('Location: /index');
                    exit;
                }else{
                    // Reindirizza alla dashboard admin
                    header('Location: /admin');
                    exit;
                }


            } else {
                // Login fallito
                $_SESSION['error_message'] = 'Credenziali non valide.';
                header('Location: /login');
                exit;
            }
        } catch (\PDOException $e) {
            $_SESSION['error_message'] = 'Errore del database. Riprova più tardi.';
            header('Location: /login');
            exit;
        }
    }

    /**
     * Esegue il logout dell'utente.
     */
    public function logout()
    {
        session_destroy();
        header('Location: /' . current_lang() . '/login');
        exit;
    }
}