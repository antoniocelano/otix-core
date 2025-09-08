<?php
namespace App\Controller;

use App\Core\Database;
use App\Core\Mailer;
use App\Core\Session;
use App\Middleware\CheckRequest;
use App\Core\Notify;

class AuthController
{
    /**
     * Mostra il form di login.
     */
    public function showLoginForm()
    {

        if (Session::has('user_id') && config('is_site') === true) {
            header('Location: /index');
            return 0;
        } else {
            render('login');
        }
    }

    /**
     * Mostra il form di registrazione in base allo step corrente.
     */
    public function showRegisterForm()
    {
        $step = Session::has('otp_email') && Session::has('otp_expires_at') && time() < Session::get('otp_expires_at') ? 2 : 1;
        
        if ($step === 2 && time() >= Session::get('otp_expires_at')) {
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
            $step = 1;
            Session::set('error_message', 'Codice OTP scaduto. Richiedine uno nuovo.');
        }

        render('register', [
            'step' => $step,
            'email_for_otp' => Session::get('otp_email', '')
        ]);
    }

    /**
     * Invia il codice OTP all'utente e prepara lo step 2.
     */
    public function sendOtp()
    {
        $http = http();
        $email = sanitize_input($http['post']['email'], 'email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::set('error_message', 'Indirizzo email non valido.');
            header('Location: /register');
            return 0;
        }

        $db = new Database();
        $user = $db->select('users', ['email' => $email]);
        if (!empty($user)) {
            Session::set('error_message', 'Questo indirizzo email è già registrato.');
            header('Location: /register');
            return 0;
        }

        $otp = random_int(100000, 999999);
        Session::set('otp', $otp);
        Session::set('otp_email', $email);
        Session::set('otp_expires_at', time() + 600);

        $mailer = new Mailer();
        $subject = 'Il tuo codice di verifica';
        $template = 'otp_code';
        $data = ['otp' => $otp];

        if ($mailer->send($email, $subject, $template, $data)) {
            Session::set('success_message', 'Codice OTP inviato alla tua email.');
        } else {
            Session::set('error_message', 'Impossibile inviare l\'email. Riprova più tardi.');
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
        }

        header('Location: /register');
        return 0;
    }

    /**
     * Gestisce la logica di registrazione finale con OTP.
     */
    public function register()
    {
        $http = http();
        $name = sanitize_input($http['post']['name'], 'str');
        $surname = sanitize_input($http['post']['surname'], 'str');
        $password = sanitize_input($http['post']['password'], 'str');
        $otp_submitted = sanitize_input($http['post']['otp'], 'int');
        $email_from_session = Session::get('otp_email');

        if (!$email_from_session || !Session::has('otp') || !Session::has('otp_expires_at')) {
            Session::set('error_message', 'Per favore, richiedi prima un codice OTP.');
            header('Location: /register');
            return 0;
        }

        if (time() > Session::get('otp_expires_at')) {
            Session::set('error_message', 'Codice OTP scaduto. Richiedine uno nuovo.');
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
            header('Location: /register');
            return 0;
        }

        if (!$otp_submitted || (int)$otp_submitted !== Session::get('otp')) {
            Session::set('error_message', 'Codice OTP non valido.');
            header('Location: /register');
            return 0;
        }

        if (!$name || !$surname || !$password) {
            Session::set('error_message', 'Nome, cognome e password sono obbligatori.');
            header('Location: /register');
            return 0;
        }
        
        $db = new Database();

        try {
            $db->begin();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $db->insert('users', [
                'name' => $name,
                'surname' => $surname,
                'email' => $email_from_session,
                'password' => $hashedPassword,
            ]);
            $db->commit();
            
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);

            Session::set('success_message', 'Registrazione avvenuta con successo! Ora puoi effettuare il login.');
            header('Location: /login');
            return 0;

        } catch (\PDOException $e) {
            $db->rollback();
            if ($e->errorInfo[1] == 1062) {
                Session::set('error_message', 'Questa email è già stata registrata.');
            } else {
                Session::set('error_message', 'Errore durante la registrazione. Riprova più tardi.');
            }
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
            header('Location: /register');
            return 0;
        }
    }

    public function forgotPassword()
    {
        $http = http();
        $email = sanitize_input($http['post']['email'], 'email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            Session::set('error_message', 'Indirizzo email non valido.');
            header('Location: /login');
            return 0;
        }

        $db = new Database();
        $user = $db->select('users', ['email' => $email]);

        if (empty($user)) {
            Session::set('email_notfound', 'L\'email inserita non appartiene a nessun account!');
            header('Location: /login');
            return 0;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 ora di validità

        $db->update('users', ['reset_token' => $token, 'reset_token_expires_at' => $expires], ['email' => $email]);

        $mailer = new Mailer();
        $subject = 'Recupero Password';
        $template = 'password_reset';
        $data = ['token' => $token];

        if ($mailer->send($email, $subject, $template, $data)) {
            Session::set('recover_ok', 'Riceverai un link per il recupero della password.');
        } else {
            Session::set('error_message', 'Impossibile inviare l\'email. Riprova più tardi.');
        }
        header('Location: /login');
        return 0;
    }

    /**
     * Mostra il form per il reset della password.
     */
    public function showResetForm($token)
    {
        $db = new Database();
        $user = $db->select('users', ['reset_token' => $token]);

        if (empty($user) || time() > strtotime($user[0]['reset_token_expires_at'])) {
            Session::set('error_message', 'Token non valido o scaduto.');
            header('Location: /login');
            return 0;
        }

        render('reset_password', ['token' => $token]);
    }

    /**
     * Gestisce il reset della password.
     */
    public function resetPassword()
    {
        $http = http();
        $token = sanitize_input($http['post']['token'], 'str');
        $password = sanitize_input($http['post']['password'], 'str');

        if (!$token || !$password) {
            Session::set('error_message', 'Token e password sono obbligatori.');
            header('Location: /password/reset/' . $token);
            return 0;
        }

        $db = new Database();
        $user = $db->select('users', ['reset_token' => $token]);

        if (empty($user) || time() > strtotime($user[0]['reset_token_expires_at'])) {
            Session::set('error_message', 'Token non valido o scaduto.');
            header('Location: /login');
            return 0;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $db->update(
            'users',
            ['password' => $hashedPassword, 'reset_token' => null, 'reset_token_expires_at' => null],
            ['id' => $user[0]['id']]
        );

        Session::set('success_message', 'Password aggiornata con successo! Ora puoi effettuare il login.');
        header('Location: /login');
        return 0;
    }

    public function login()
    {
        $http = http();
        $email = sanitize_input($http['post']['email'] , 'email');
        $password = sanitize_input($http['post']['password'] , 'str');

        if (!$email || !$password) {
            Session::set('error_message', 'Email e password sono obbligatori.');
            header('Location: /login');
            return 0;
        }

        try {
            $db = new Database();
            $user = $db->select('users', ['email' => $email]);

            if (!empty($user) && password_verify($password, $user[0]['password'])) {
                session_regenerate_id(true);
                Session::set('user_id', $user[0]['id']);
                Session::set('user_name', $user[0]['name']);
                Session::set('user_surname', $user[0]['surname']);
                
                Session::remove('error_message');

                if (config('is_site') === true) {
                    header('Location: /index');
                } else {
                    header('Location: /admin');
                }
                return 0;
            } else {
                Session::set('error_message', 'Credenziali non valide.');
                header('Location: /login');
                return 0;
            }
        } catch (\PDOException $e) {
            Session::set('error_message', 'Errore del database. Riprova più tardi.');
            header('Location: /login');
            return 0;
        }
    }

    /**
     * Esegue il logout dell'utente.
     */
    public function logout()
    {
        Session::destroy();
        header('Location: /' . current_lang() . '/login');
        return 0;
    }
}