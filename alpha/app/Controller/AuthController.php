<?php
namespace App\Controller;

use App\Core\Database;
use App\Core\Mailer;

class AuthController
{
    /**
     * Mostra il form di login.
     */
    public function showLoginForm()
    {
        if(isset($_SESSION['user_id']) && config('is_site') === true){
            header('Location: /index');
            exit;
        }else{
            render('login');
        }
    }

    /**
     * Mostra il form di registrazione in base allo step corrente.
     */
    public function showRegisterForm()
    {
        $step = isset($_SESSION['otp_email']) && isset($_SESSION['otp_expires_at']) && time() < $_SESSION['otp_expires_at'] ? 2 : 1;
        
        if ($step === 2 && time() >= $_SESSION['otp_expires_at']) {
            unset($_SESSION['otp'], $_SESSION['otp_expires_at'], $_SESSION['otp_email']);
            $step = 1;
            $_SESSION['error_message'] = 'Codice OTP scaduto. Richiedine uno nuovo.';
        }

        render('register', [
            'step' => $step,
            'email_for_otp' => $_SESSION['otp_email'] ?? ''
        ]);
    }

    /**
     * Invia il codice OTP all'utente e prepara lo step 2.
     */
    public function sendOtp()
    {
        $email = sanitize_input($_POST['email'], 'email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = 'Indirizzo email non valido.';
            header('Location: /register');
            exit;
        }

        $db = new Database();
        $user = $db->select('users', ['email' => $email]);
        if (!empty($user)) {
            $_SESSION['error_message'] = 'Questo indirizzo email è già registrato.';
            header('Location: /register');
            exit;
        }

        $otp = random_int(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;
        $_SESSION['otp_expires_at'] = time() + 600;

        $mailer = new Mailer();
        $subject = 'Il tuo codice di verifica';
        $template = 'otp_code';
        $data = ['otp' => $otp];

        if ($mailer->send($email, $subject, $template, $data)) {
            $_SESSION['success_message'] = 'Codice OTP inviato alla tua email.';
        } else {
            $_SESSION['error_message'] = 'Impossibile inviare l\'email. Riprova più tardi.';
            unset($_SESSION['otp'], $_SESSION['otp_expires_at'], $_SESSION['otp_email']);
        }

        header('Location: /register');
        exit;
    }

    /**
     * Gestisce la logica di registrazione finale con OTP.
     */
    public function register()
    {
        $name = sanitize_input($_POST['name'], 'str');
        $password = sanitize_input($_POST['password'], 'str');
        $otp_submitted = sanitize_input($_POST['otp'], 'int');
        $email_from_session = $_SESSION['otp_email'];

        if (!$email_from_session || !isset($_SESSION['otp']) || !isset($_SESSION['otp_expires_at'])) {
            $_SESSION['error_message'] = 'Per favore, richiedi prima un codice OTP.';
            header('Location: /register');
            exit;
        }

        if (time() > $_SESSION['otp_expires_at']) {
            $_SESSION['error_message'] = 'Codice OTP scaduto. Richiedine uno nuovo.';
            unset($_SESSION['otp'], $_SESSION['otp_expires_at'], $_SESSION['otp_email']);
            header('Location: /register');
            exit;
        }

        if (!$otp_submitted || (int)$otp_submitted !== $_SESSION['otp']) {
            $_SESSION['error_message'] = 'Codice OTP non valido.';
            header('Location: /register');
            exit;
        }

        if (!$name || !$password) {
            $_SESSION['error_message'] = 'Nome e password sono obbligatori.';
            header('Location: /register');
            exit;
        }
        
        $db = new Database();

        try {
            $db->begin();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $db->insert('users', [
                'name' => $name,
                'email' => $email_from_session,
                'password' => $hashedPassword,
            ]);
            $db->commit();
            
            unset($_SESSION['otp'], $_SESSION['otp_expires_at'], $_SESSION['otp_email']);

            $_SESSION['success_message'] = 'Registrazione avvenuta con successo! Ora puoi effettuare il login.';
            header('Location: /login');
            exit;

        } catch (\PDOException $e) {
            $db->rollback();
            if ($e->errorInfo[1] == 1062) {
                $_SESSION['error_message'] = 'Questa email è già stata registrata.';
            } else {
                $_SESSION['error_message'] = 'Errore durante la registrazione. Riprova più tardi.';
            }
            unset($_SESSION['otp'], $_SESSION['otp_expires_at'], $_SESSION['otp_email']);
            header('Location: /register');
            exit;
        }
    }

    public function forgotPassword()
    {
        $email = sanitize_input($_POST['email'], 'email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = 'Indirizzo email non valido.';
            header('Location: /login');
            exit;
        }

        $db = new Database();
        $user = $db->select('users', ['email' => $email]);

        if (empty($user)) {
            $_SESSION['success_message'] = 'Se l\'indirizzo email è corretto, riceverai un link per il recupero della password.';
            header('Location: /login');
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 ora di validità

        $db->update('users', ['reset_token' => $token, 'reset_token_expires_at' => $expires], ['email' => $email]);

        $mailer = new Mailer();
        $subject = 'Recupero Password';
        $template = 'password_reset';
        $data = ['token' => $token];

        if ($mailer->send($email, $subject, $template, $data)) {
            $_SESSION['success_message'] = 'Se l\'indirizzo email è corretto, riceverai un link per il recupero della password.';
        } else {
            $_SESSION['error_message'] = 'Impossibile inviare l\'email. Riprova più tardi.';
        }

        header('Location: /login');
        exit;
    }

    /**
     * Mostra il form per il reset della password.
     */
    public function showResetForm($token)
    {
        $db = new Database();
        $user = $db->select('users', ['reset_token' => $token]);

        if (empty($user) || time() > strtotime($user[0]['reset_token_expires_at'])) {
            $_SESSION['error_message'] = 'Token non valido o scaduto.';
            header('Location: /login');
            exit;
        }

        render('reset_password', ['token' => $token]);
    }

    /**
     * Gestisce il reset della password.
     */
    public function resetPassword()
    {
        $token = sanitize_input($_POST['token'], 'str');
        $password = sanitize_input($_POST['password'], 'str');

        if (!$token || !$password) {
            $_SESSION['error_message'] = 'Token e password sono obbligatori.';
            header('Location: /password/reset/' . $token);
            exit;
        }

        $db = new Database();
        $user = $db->select('users', ['reset_token' => $token]);

        if (empty($user) || time() > strtotime($user[0]['reset_token_expires_at'])) {
            $_SESSION['error_message'] = 'Token non valido o scaduto.';
            header('Location: /login');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $db->update(
            'users',
            ['password' => $hashedPassword, 'reset_token' => null, 'reset_token_expires_at' => null],
            ['id' => $user[0]['id']]
        );

        $_SESSION['success_message'] = 'Password aggiornata con successo! Ora puoi effettuare il login.';
        header('Location: /login');
        exit;
    }

    public function login()
    {
        $email = sanitize_input($_POST['email'] , 'email');
        $password = sanitize_input($_POST['password'] , 'str');

        if (!$email || !$password) {
            $_SESSION['error_message'] = 'Email e password sono obbligatori.';
            header('Location: /login');
            exit;
        }

        try {
            $db = new Database();
            $user = $db->select('users', ['email' => $email]);

            if (!empty($user) && password_verify($password, $user[0]['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user[0]['id'];
                $_SESSION['user_name'] = $user[0]['name'];
                
                unset($_SESSION['error_message']);

                if(config('is_site') === true){
                    header('Location: /index');
                } else {
                    header('Location: /admin');
                }
                exit;
            } else {
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