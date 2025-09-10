<?php
// Dichiarazione del namespace per la classe del controller.
namespace App\Controller;

// Importazione delle classi necessarie per la gestione del database, email, sessione.
use App\Core\Database;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Notify;
/**
 * Controller per la gestione dell'autenticazione.
 */
class AuthController
{
    /**
     * Mostra il form di login.
     */
    public function showLoginForm()
    {
        // Controlla se l'utente ha già una sessione attiva ('user_id') e se il sito è abilitato.
        if (Session::has('user_id') && config('is_site') === true) {
            // Se l'utente è già loggato e il sito è attivo, reindirizza alla pagina principale.
            header('Location: /index');
            return 0;
        } else {
            // Altrimenti, renderizza la vista del form di login.
            render('login');
        }
    }

    /**
     * Mostra il form di registrazione in base allo step corrente.
     */
    public function showRegisterForm()
    {
        // Determina lo step di registrazione. Lo step 2 è attivo se esiste un'email OTP e il codice non è scaduto.
        $step = Session::has('otp_email') && Session::has('otp_expires_at') && time() < Session::get('otp_expires_at') ? 2 : 1;
        
        // Se lo step è 2 ma il codice OTP è scaduto...
        if ($step === 2 && time() >= Session::get('otp_expires_at')) {
            // Rimuove i dati OTP dalla sessione.
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
            // Reimposta lo step a 1.
            $step = 1;
            // Imposta un messaggio di errore.
            Session::set('error_message', 'Codice OTP scaduto. Richiedine uno nuovo.');
        }

        // Renderizza la vista di registrazione passando lo step corrente e l'email per il campo OTP.
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
        // Recupera i dati HTTP e sanitizza l'email dall'input POST.
        $http = http();
        $email = sanitize_input($http['post']['email'], 'email');

        // Controlla se l'email è valida.
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Imposta un messaggio di errore e reindirizza al form di registrazione.
            Session::set('error_message', 'Indirizzo email non valido.');
            header('Location: /register');
            return 0;
        }

        // Istanzia la classe Database per interagire con il DB.
        $db = new Database();
        // Cerca un utente con l'email fornita.
        $user = $db->select('users', ['email' => $email]);
        // Se l'utente esiste già...
        if (!empty($user)) {
            // Imposta un messaggio di errore e reindirizza.
            Session::set('error_message', 'Questo indirizzo email è già registrato.');
            header('Location: /register');
            return 0;
        }

        // Genera un codice OTP casuale di 6 cifre.
        $otp = random_int(100000, 999999);
        // Salva l'OTP, l'email e l'orario di scadenza (10 minuti) nella sessione.
        Session::set('otp', $otp);
        Session::set('otp_email', $email);
        Session::set('otp_expires_at', time() + 600);

        // Istanzia il Mailer e prepara i dati per l'email.
        $mailer = new Mailer();
        $subject = 'Il tuo codice di verifica';
        $template = 'otp_code';
        $data = ['otp' => $otp];

        // Tenta di inviare l'email.
        if ($mailer->send($email, $subject, $template, $data)) {
            // In caso di successo, imposta un messaggio di successo.
            Session::set('success_message', 'Codice OTP inviato alla tua email.');
        } else {
            // In caso di fallimento, imposta un messaggio di errore e rimuove i dati OTP dalla sessione.
            Session::set('error_message', 'Impossibile inviare l\'email. Riprova più tardi.');
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
        }

        // Reindirizza sempre al form di registrazione per mostrare il secondo step.
        header('Location: /register');
        return 0;
    }

    /**
     * Gestisce la logica di registrazione finale con OTP.
     */
    public function register()
    {
        // Recupera e sanitizza i dati dal form di registrazione.
        $http = http();
        $name = sanitize_input($http['post']['name'], 'str');
        $surname = sanitize_input($http['post']['surname'], 'str');
        $password = sanitize_input($http['post']['password'], 'str');
        $otp_submitted = sanitize_input($http['post']['otp'], 'int');
        $email_from_session = Session::get('otp_email');

        // Controlla che i dati OTP siano presenti nella sessione.
        if (!$email_from_session || !Session::has('otp') || !Session::has('otp_expires_at')) {
            // In caso contrario, imposta un errore e reindirizza.
            Session::set('error_message', 'Per favore, richiedi prima un codice OTP.');
            header('Location: /register');
            return 0;
        }

        // Controlla se il codice OTP è scaduto.
        if (time() > Session::get('otp_expires_at')) {
            // Se è scaduto, imposta un errore, rimuove i dati OTP e reindirizza.
            Session::set('error_message', 'Codice OTP scaduto. Richiedine uno nuovo.');
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
            header('Location: /register');
            return 0;
        }

        // Confronta l'OTP inviato con quello salvato in sessione.
        if (!$otp_submitted || (int)$otp_submitted !== Session::get('otp')) {
            // Se non corrispondono, imposta un errore e reindirizza.
            Session::set('error_message', 'Codice OTP non valido.');
            header('Location: /register');
            return 0;
        }

        // Controlla che nome, cognome e password non siano vuoti.
        if (!$name || !$surname || !$password) {
            // Se mancano dati, imposta un errore e reindirizza.
            Session::set('error_message', 'Nome, cognome e password sono obbligatori.');
            header('Location: /register');
            return 0;
        }
        
        // Istanzia la classe Database.
        $db = new Database();

        // Utilizza un blocco try-catch per gestire le transazioni del DB.
        try {
            // Inizia una transazione per assicurare l'integrità dei dati.
            $db->begin();
            // Hash della password prima di salvarla nel DB.
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // Inserisce i dati del nuovo utente nel database.
            $db->insert('users', [
                'name' => $name,
                'surname' => $surname,
                'email' => $email_from_session,
                'password' => $hashedPassword,
            ]);
            // Conferma la transazione.
            $db->commit();
            
            // Rimuove i dati OTP dalla sessione dopo la registrazione avvenuta con successo.
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);

            // Imposta un messaggio di successo e reindirizza alla pagina di login.
            Session::set('success_message', 'Registrazione avvenuta con successo! Ora puoi effettuare il login.');
            header('Location: /login');
            return 0;

        } catch (\PDOException $e) {
            // In caso di errore, annulla la transazione.
            $db->rollback();
            // Se l'errore è un'email duplicata (codice 1062)...
            if ($e->errorInfo[1] == 1062) {
                // Imposta un messaggio di errore specifico.
                Session::set('error_message', 'Questa email è già stata registrata.');
            } else {
                // Altrimenti, imposta un errore generico.
                Session::set('error_message', 'Errore durante la registrazione. Riprova più tardi.');
            }
            // Rimuove i dati OTP e reindirizza.
            Session::remove(['otp', 'otp_expires_at', 'otp_email']);
            header('Location: /register');
            return 0;
        }
    }

    /**
     * Gestisce la richiesta di recupero password.
     */
    public function forgotPassword()
    {
        // Recupera e sanitizza l'email dall'input POST.
        $http = http();
        $email = sanitize_input($http['post']['email'], 'email');

        // Controlla la validità dell'email.
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            // Se non è valida, imposta un errore e reindirizza.
            Session::set('error_message', 'Indirizzo email non valido.');
            header('Location: /login');
            return 0;
        }

        // Cerca l'utente nel database.
        $db = new Database();
        $user = $db->select('users', ['email' => $email]);

        // Se l'utente non viene trovato...
        if (empty($user)) {
            // Imposta un messaggio di errore e reindirizza al login.
            Session::set('email_notfound', 'L\'email inserita non appartiene a nessun account!');
            header('Location: /login');
            return 0;
        }

        // Genera un token casuale e imposta la sua scadenza a 1 ora.
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 ora di validità

        // Aggiorna l'utente nel DB con il token e la data di scadenza.
        $db->update('users', ['reset_token' => $token, 'reset_token_expires_at' => $expires], ['email' => $email]);

        // Prepara e invia l'email di recupero password.
        $mailer = new Mailer();
        $subject = 'Recupero Password';
        $template = 'password_reset';
        $data = ['token' => $token];

        if ($mailer->send($email, $subject, $template, $data)) {
            // In caso di successo, imposta un messaggio di successo.
            Session::set('recover_ok', 'Riceverai un link per il recupero della password.');
        } else {
            // In caso di fallimento, imposta un messaggio di errore.
            Session::set('error_message', 'Impossibile inviare l\'email. Riprova più tardi.');
        }
        // Reindirizza al login.
        header('Location: /login');
        return 0;
    }

    /**
     * Mostra il form per il reset della password.
     */
    public function showResetForm($token)
    {
        // Cerca l'utente basandosi sul token fornito.
        $db = new Database();
        $user = $db->select('users', ['reset_token' => $token]);

        // Controlla se il token è valido e non scaduto.
        if (empty($user) || time() > strtotime($user[0]['reset_token_expires_at'])) {
            // Se non è valido, imposta un errore e reindirizza al login.
            Session::set('error_message', 'Token non valido o scaduto.');
            header('Location: /login');
            return 0;
        }

        // Renderizza la vista per il reset della password, passando il token.
        render('reset_password', ['token' => $token]);
    }

    /**
     * Gestisce il reset della password.
     */
    public function resetPassword()
    {
        // Recupera e sanitizza il token e la nuova password dall'input POST.
        $http = http();
        $token = sanitize_input($http['post']['token'], 'str');
        $password = sanitize_input($http['post']['password'], 'str');

        // Controlla se i campi sono stati inviati.
        if (!$token || !$password) {
            // In caso contrario, imposta un errore e reindirizza al form di reset con lo stesso token.
            Session::set('error_message', 'Token e password sono obbligatori.');
            header('Location: /password/reset/' . $token);
            return 0;
        }

        // Cerca l'utente basandosi sul token.
        $db = new Database();
        $user = $db->select('users', ['reset_token' => $token]);

        // Controlla nuovamente la validità e la scadenza del token.
        if (empty($user) || time() > strtotime($user[0]['reset_token_expires_at'])) {
            // Se non è valido, imposta un errore e reindirizza al login.
            Session::set('error_message', 'Token non valido o scaduto.');
            header('Location: /login');
            return 0;
        }

        // Hash della nuova password.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Aggiorna la password dell'utente e rimuove i dati del token.
        $db->update(
            'users',
            ['password' => $hashedPassword, 'reset_token' => null, 'reset_token_expires_at' => null],
            ['id' => $user[0]['id']]
        );

        // Imposta un messaggio di successo e reindirizza al login.
        Session::set('success_message', 'Password aggiornata con successo! Ora puoi effettuare il login.');
        header('Location: /login');
        return 0;
    }

    /**
     * Gestisce il processo di login.
     */
    public function login()
    {
        // Recupera e sanitizza email e password dall'input POST.
        $http = http();
        $email = sanitize_input($http['post']['email'] , 'email');
        $password = sanitize_input($http['post']['password'] , 'str');

        $uri = $http['uri']; // "/it/login"
        $parts = explode('/', trim($uri, '/')); 
        $lang = $parts[0]; // "it"

        // Controlla se i campi sono vuoti.
        if (!$email || !$password) {
            // Se lo sono, imposta un errore e reindirizza.
            Session::set('error_message', 'Email e password sono obbligatori.');
            header('Location: /login');
            return 0;
        }

        // Utilizza un blocco try-catch per gestire eventuali eccezioni del database.
        try {
            // Cerca l'utente nel DB.
            $db = new Database();
            $user = $db->select('users', ['email' => $email]);

            // Se l'utente esiste e la password corrisponde (verifica con password_verify)...
            if (!empty($user) && password_verify($password, $user[0]['password'])) {
                // Rigenera l'ID di sessione per prevenire attacchi di session fixation.
                session_regenerate_id(true);
                // Salva i dati dell'utente nella sessione.
                Session::set('user_id', $user[0]['id']);
                Session::set('user_name', $user[0]['name']);
                Session::set('user_surname', $user[0]['surname']);
                
                // Rimuove eventuali messaggi di errore precedenti.
                Session::remove('error_message');

                // Reindirizza in base alla configurazione del sito.
                if (config('is_site') === true) {
                    header('Location: /index');
                } else {
                    header('Location: '.$lang.'/admin');
                }
                return 0;
            } else {
                // Se le credenziali non sono valide, imposta un errore e reindirizza.
                Session::set('error_message', 'Credenziali non valide.');
                header('Location: /login');
                return 0;
            }
        } catch (\PDOException $e) {
            // In caso di errore del DB, imposta un messaggio generico e reindirizza.
            Session::set('error_message', 'Errore del database. Riprova più tardi.');
            header('Location: /login');
            return 0;
        }
    }

    /**
     * Metodo statico per controllare l'autenticazione.
     * Non reindirizza, restituisce solo un valore booleano.
     */
    public static function checkAuth()
    {
        // Restituisce false se non c'è un 'user_id' nella sessione.
        if (!Session::has('user_id')) {
            return false;
        }
    }

    /**
     * Esegue il logout dell'utente.
     */
    public function logout()
    {
        // Distrugge la sessione corrente.
        Session::destroy();
        // Reindirizza alla pagina di login, tenendo conto della lingua corrente.
        header('Location: /' . current_lang() . '/login');
        return 0;
    }
}