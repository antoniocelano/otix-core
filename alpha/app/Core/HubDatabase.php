<?php
namespace App\Core;

use PDO;
use PDOException;
use InvalidArgumentException;

/**
 * Classe dedicata alla gestione della connessione con un database "Hub".
 * Estende la classe 'Database' per ereditare tutte le funzionalità CRUD e di gestione delle transazioni.
 * Si connette a un database specifico utilizzando le credenziali con prefisso 'HUB_' dal file .env.
 */
class HubDatabase extends Database
{
    /** @var PDO|null La singola istanza della connessione PDO per l'Hub. */
    private static ?PDO $hubConnection = null;

    /**
     * Costruttore della classe.
     * Al momento della creazione di un'istanza, inizializza la connessione al database Hub.
     */
    public function __construct()
    {
        $this->initHubConnection();
    }

    /**
     * Inizializza la connessione al database Hub.
     * Questo metodo garantisce che esista una sola connessione attiva (pattern Singleton) per l'Hub.
     * Legge le credenziali dalle variabili d'ambiente HUB_HOST, HUB_PORT, ecc.
     */
    private function initHubConnection(): void
    {
        // Controlla se l'istanza della connessione all'Hub esiste già.
        if (self::$hubConnection === null) {
            try {
                // Costruisce la stringa DSN (Data Source Name) per la connessione.
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    $_ENV['HUB_HOST'] ?? 'localhost',
                    $_ENV['HUB_PORT'] ?? '3306',
                    $_ENV['HUB_DATABASE'] ?? ''
                );

                // Crea una nuova istanza PDO per il database Hub.
                self::$hubConnection = new PDO(
                    $dsn,
                    $_ENV['HUB_USERNAME'] ?? '',
                    $_ENV['HUB_PASSWORD'] ?? '',
                    [
                        // Imposta la gestione degli errori per lanciare eccezioni.
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Imposta la modalità di recupero predefinita su array associativo.
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Disabilita l'emulazione delle prepare statements per una maggiore sicurezza.
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
                
                // Assegna la nuova connessione alla classe genitore 'Database'.
                // Questo passaggio è fondamentale per consentire ai metodi ereditati
                // (come insert, update, select) di operare su questa specifica connessione.
                parent::setConnection(self::$hubConnection);

            } catch (PDOException $e) {
                // Cattura le eccezioni di PDO e le rilancia con un messaggio più specifico.
                throw new PDOException("Errore di connessione al database HUB: " . $e->getMessage(), (int)$e->getCode());
            }
        } else {
            // Se la connessione esiste già, la ri-assegna alla classe genitore.
            // Questo assicura che qualsiasi istanza di HubDatabase lavori con la stessa connessione.
            parent::setConnection(self::$hubConnection);
        }
    }
}