<?php
namespace App\Core;

use PDO;
use PDOException;
use InvalidArgumentException;

/**
 * Classe dedicata alla gestione della connessione con il database dell'Hub.
 * Legge le credenziali con prefisso HUB_ dal file .env.
 */
class HubDatabase extends Database
{
    private static ?PDO $hubConnection = null;

    public function __construct(bool $enableLogging = false, string $logFile = '', string $logLevel = 'ERROR')
    {
        // Sovrascrive il costruttore del genitore per non chiamare initConnection()
        // ma inizializza le proprietà di logging per evitare errori.
        parent::setLogging($enableLogging, $logFile, $logLevel);
        $this->initHubConnection();
    }

    private function initHubConnection(): void
    {
        if (self::$hubConnection === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    $_ENV['HUB_HOST'] ?? 'localhost',
                    $_ENV['HUB_PORT'] ?? '3306',
                    $_ENV['HUB_DATABASE'] ?? ''
                );

                self::$hubConnection = new PDO(
                    $dsn,
                    $_ENV['HUB_USERNAME'] ?? '',
                    $_ENV['HUB_PASSWORD'] ?? '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
                // Assegna la nuova connessione alla proprietà della classe genitore
                // per far funzionare i metodi ereditati (insert, select, etc.)
                parent::setConnection(self::$hubConnection);

            } catch (PDOException $e) {
                throw new PDOException("Errore di connessione al database HUB: " . $e->getMessage(), (int)$e->getCode());
            }
        } else {
            // Se la connessione esiste già, assicurati che la classe genitore la usi
            parent::setConnection(self::$hubConnection);
        }
    }
}