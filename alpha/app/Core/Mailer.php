<?php
namespace App\Core;

// Importazione delle classi necessarie da PHPMailer.
// Queste classi sono fondamentali per la gestione delle email e degli errori.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclusione dei file di PHPMailer tramite percorsi assoluti.
// BASE_PATH è una costante che punta alla directory radice del progetto.
require_once BASE_PATH . '/vendor/phpmailer/Exception.php';
require_once BASE_PATH . '/vendor/phpmailer/PHPMailer.php';
require_once BASE_PATH . '/vendor/phpmailer/SMTP.php';

/**
 * Classe per l'invio di email utilizzando PHPMailer.
 * Questa classe incapsula la logica di configurazione e invio
 * per semplificare l'utilizzo del servizio di posta.
 */
class Mailer
{
    /** @var PHPMailer L'istanza di PHPMailer per la gestione delle email. */
    private $mailer;

    /**
     * Costruttore della classe.
     * Inizializza e configura l'oggetto PHPMailer con le impostazioni SMTP.
     */
    public function __construct()
    {
        // Creazione di una nuova istanza di PHPMailer.
        // Il parametro 'true' abilita le eccezioni per una gestione degli errori più robusta.
        $this->mailer = new PHPMailer(true);

        // Configurazione del server SMTP per l'invio delle email.
        $this->mailer->isSMTP(); // Imposta il mailer per usare SMTP.
        // Recupera l'host SMTP dalle variabili d'ambiente o usa 'localhost' come fallback.
        $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'localhost';
        $this->mailer->SMTPAuth = true; // Abilita l'autenticazione SMTP.
        // Recupera le credenziali SMTP dalle variabili d'ambiente.
        $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? '';
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? '';
        // Imposta il tipo di crittografia (es. 'tls' o 'ssl').
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        // Imposta la porta SMTP.
        $this->mailer->Port = $_ENV['MAIL_PORT'] ?? 587;

        // Impostazioni di default del mittente (da chi viene inviata l'email).
        $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Mailer';
        $this->mailer->setFrom($fromAddress, $fromName);
    }

    /**
     * Invia un'email utilizzando un template.
     *
     * @param string|array $to L'indirizzo o gli indirizzi dei destinatari.
     * @param string       $subject L'oggetto dell'email.
     * @param string       $template Il nome del file del template (senza l'estensione .php).
     * @param array        $data I dati da passare al template per il rendering.
     * @return bool True se l'invio ha successo, altrimenti false.
     */
    public function send($to, string $subject, string $template, array $data = []): bool
    {
        try {
            // Gestione dei destinatari. Se $to è un array, aggiunge più destinatari.
            if (is_array($to)) {
                foreach ($to as $address) {
                    $this->mailer->addAddress($address);
                }
            } else {
                // Se è una singola stringa, aggiunge un solo destinatario.
                $this->mailer->addAddress($to);
            }

            // Impostazioni del contenuto dell'email.
            $this->mailer->isHTML(true); // Abilita il supporto per il contenuto HTML.
            $this->mailer->Subject = $subject; // Imposta l'oggetto dell'email.
            // Genera il corpo dell'email renderizzando il template.
            $this->mailer->Body = $this->renderTemplate($template, $data);
            // Crea un corpo alternativo in testo semplice per i client che non supportano l'HTML.
            $this->mailer->AltBody = strip_tags($this->mailer->Body);

            // Invia l'email.
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            // Cattura le eccezioni di PHPMailer e gestisce l'errore.
            // L'errore viene scritto nel log degli errori di PHP.
            error_log("Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    /**
     * Renderizza il template dell'email.
     *
     * @param string $template Il nome del file del template.
     * @param array  $data Dati da passare al template.
     * @return string Il contenuto HTML renderizzato.
     * @throws \Exception Se il file del template non esiste.
     */
    private function renderTemplate(string $template, array $data = []): string
    {
        // Costruisce il percorso completo del file del template.
        // Utilizza una costante per il percorso base e una per la directory del tema.
        $templatePath = BASE_PATH . "/resources/views/" . THEME_DIR . "/emails/{$template}.php";

        // Verifica l'esistenza del file del template.
        if (!file_exists($templatePath)) {
            // Se il file non esiste, lancia un'eccezione.
            throw new \Exception("Template email non trovato: {$templatePath}");
        }

        // Utilizza il buffering dell'output per catturare il contenuto del template.
        ob_start();
        // Estrae i dati dell'array in variabili locali, rendendoli disponibili nel template.
        extract($data);
        // Include il file del template, il cui output viene catturato dal buffer.
        require $templatePath;
        // Restituisce e pulisce il buffer dell'output, ottenendo il contenuto renderizzato.
        return ob_get_clean();
    }
}