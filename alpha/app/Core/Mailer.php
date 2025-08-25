<?php
namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Includi manualmente i file di PHPMailer poiché non è gestito da un autoloader globale.
require_once BASE_PATH . '/public/phpmailer/Exception.php';
require_once BASE_PATH . '/public/phpmailer/PHPMailer.php';
require_once BASE_PATH . '/public/phpmailer/SMTP.php';

class Mailer
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // Configurazione del server
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'] ?? 'localhost';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USERNAME'] ?? '';
        $this->mailer->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $_ENV['MAIL_PORT'] ?? 587;
        
        // Impostazioni di default del mittente
        $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Mailer';
        $this->mailer->setFrom($fromAddress, $fromName);
    }

    /**
     * Invia un'email utilizzando un template.
     *
     * @param string|array $to      L'indirizzo o gli indirizzi dei destinatari.
     * @param string       $subject L'oggetto dell'email.
     * @param string       $template Il nome del file del template (senza .php).
     * @param array        $data    I dati da passare al template.
     * @return bool True se l'invio ha successo, altrimenti false.
     */
    public function send($to, string $subject, string $template, array $data = []): bool
    {
        try {
            // Destinatari
            if (is_array($to)) {
                foreach ($to as $address) {
                    $this->mailer->addAddress($address);
                }
            } else {
                $this->mailer->addAddress($to);
            }

            // Contenuto dell'email
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $this->renderTemplate($template, $data);
            $this->mailer->AltBody = strip_tags($this->mailer->Body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            // Logga l'errore o gestiscilo come preferisci
            error_log("Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    /**
     * Renderizza il template dell'email.
     *
     * @param string $template
     * @param array  $data
     * @return string
     */
    private function renderTemplate(string $template, array $data = []): string
    {
        $templatePath = BASE_PATH . "/resources/views/" . THEME_DIR . "/emails/{$template}.php";

        if (!file_exists($templatePath)) {
            throw new \Exception("Template email non trovato: {$templatePath}");
        }

        ob_start();
        extract($data);
        require $templatePath;
        return ob_get_clean();
    }
}
