<?php
namespace App\Core;

use PDO;
use PDOException;
use InvalidArgumentException;

/**
 * Classe per la gestione del database tramite PDO.
 * Implementa il pattern Singleton per la connessione e offre metodi sicuri
 * per le operazioni CRUD, transazioni, e caching delle query.
 */
class Database
{
    /** @var PDO|null La singola istanza della connessione PDO. */
    private static ?PDO $connection = null;
    /** @var bool Flag per indicare se una transazione è attiva. */
    private bool $inTransaction = false;
    /** @var array Array che tiene traccia dei savepoint attivi. */
    private array $savepoints = [];

    /** @var string Regex per validare i nomi di tabelle e colonne. */
    private const VALID_IDENTIFIER_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';

    /** @var array Cache per gli statement preparati, ottimizzando le query ripetute. */
    private array $statementCache = [];
    /** @var int Dimensione massima della cache degli statement. */
    private int $maxCacheSize = 100;

    /**
     * Costruttore della classe. Inizializza la connessione al database.
     */
    public function __construct()
    {
        $this->initConnection();
    }

    /**
     * Imposta una connessione PDO esterna. Utile per i test o per un'inizializzazione manuale.
     *
     * @param PDO $connection L'istanza di PDO da usare.
     */
    public function setConnection(PDO $connection): void
    {
        self::$connection = $connection;
    }

    /**
     * Inizializza la connessione al database usando le variabili d'ambiente.
     * Assicura che ci sia una sola connessione attiva (Singleton).
     *
     * @throws PDOException Se la connessione fallisce.
     */
    private function initConnection(): void
    {
        if (self::$connection === null) {
            try {
                // Costruisce la stringa DSN (Data Source Name)
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    $_ENV['DB_HOST'] ?? 'localhost',
                    $_ENV['DB_PORT'] ?? '3306',
                    $_ENV['DB_DATABASE'] ?? ''
                );

                // Crea una nuova istanza di PDO con le opzioni di configurazione
                self::$connection = new PDO(
                    $dsn,
                    $_ENV['DB_USERNAME'] ?? '',
                    $_ENV['DB_PASSWORD'] ?? '',
                    [
                        // Modalità di gestione degli errori: lancia eccezioni
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        // Modalità di recupero dei risultati: array associativo
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // Disabilita l'emulazione delle prepare, per query sicure
                        PDO::ATTR_EMULATE_PREPARES => false,
                        // Connessioni persistenti per riutilizzo
                        PDO::ATTR_PERSISTENT => true,
                        // Comando iniziale per impostare il set di caratteri e la collation
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]
                );
            } catch (PDOException $e) {
                throw new PDOException("Errore di connessione al database: " . $e->getMessage(), (int)$e->getCode());
            }
        }
    }

    /**
     * Valida un identificatore (nome tabella o colonna) per prevenire SQL Injection.
     *
     * @param string $identifier La stringa da validare.
     * @throws InvalidArgumentException Se l'identificatore contiene caratteri non validi.
     */
    private function validate(string $identifier): void
    {
        if (!preg_match(self::VALID_IDENTIFIER_PATTERN, $identifier)) {
            throw new InvalidArgumentException("Identificatore non valido: $identifier");
        }
    }

    /**
     * Ottiene un prepared statement dalla cache per ottimizzare le prestazioni.
     * Se non è presente, lo crea e lo aggiunge alla cache.
     *
     * @param string $sql La query SQL.
     * @return \PDOStatement Lo statement PDO.
     */
    private function getStatement(string $sql): \PDOStatement
    {
        $hash = md5($sql);

        // Controlla se lo statement è già in cache
        if (!isset($this->statementCache[$hash])) {
            // Se la cache è piena, rimuove il più vecchio (FIFO)
            if (count($this->statementCache) >= $this->maxCacheSize) {
                array_shift($this->statementCache);
            }
            // Prepara un nuovo statement e lo aggiunge alla cache
            $this->statementCache[$hash] = self::$connection->prepare($sql);
        }

        return $this->statementCache[$hash];
    }

    /**
     * Inizia una transazione o crea un savepoint.
     *
     * @param string|null $savepointName Se specificato, crea un savepoint.
     * @return bool True se l'operazione ha avuto successo, false altrimenti.
     * @throws PDOException Se un savepoint viene richiesto senza una transazione attiva.
     */
    public function begin(?string $savepointName = null): bool
    {
        try {
            if ($savepointName !== null) {
                if (!$this->inTransaction) {
                    throw new PDOException('Impossibile creare un savepoint senza una transazione attiva');
                }
                $this->validate($savepointName);
                self::$connection->exec("SAVEPOINT `$savepointName`");
                $this->savepoints[] = $savepointName;
                return true;
            }

            // Se una transazione è già attiva, non ne avvia una nuova
            if ($this->inTransaction) {
                return false;
            }

            $this->inTransaction = self::$connection->beginTransaction();
            return $this->inTransaction;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Effettua il commit di una transazione o rilascia un savepoint.
     *
     * @param string|null $savepointName Se specificato, rilascia un savepoint.
     * @return bool True se l'operazione ha avuto successo, false altrimenti.
     * @throws PDOException In caso di errore durante il commit, esegue un rollback.
     */
    public function commit(?string $savepointName = null): bool
    {
        try {
            if ($savepointName !== null) {
                if (!in_array($savepointName, $this->savepoints)) {
                    throw new PDOException("Savepoint '$savepointName' non trovato");
                }
                self::$connection->exec("RELEASE SAVEPOINT `$savepointName`");
                // Rimuove il savepoint dall'array
                $this->savepoints = array_filter($this->savepoints, fn($sp) => $sp !== $savepointName);
                return true;
            }

            // Se non c'è una transazione attiva, non fa nulla
            if (!$this->inTransaction) {
                return false;
            }

            $result = self::$connection->commit();
            $this->inTransaction = false;
            $this->savepoints = []; // Cancella i savepoint alla fine della transazione
            return $result;
        } catch (PDOException $e) {
            // In caso di errore, esegue il rollback per garantire la coerenza
            if ($this->inTransaction) {
                $this->rollback();
            }
            throw $e;
        }
    }

    /**
     * Effettua il rollback di una transazione o di un savepoint.
     *
     * @param string|null $savepointName Se specificato, effettua il rollback fino al savepoint.
     * @return bool True se l'operazione ha avuto successo, false altrimenti.
     */
    public function rollback(?string $savepointName = null): bool
    {
        try {
            if ($savepointName !== null) {
                if (!in_array($savepointName, $this->savepoints)) {
                    throw new PDOException("Savepoint '$savepointName' non trovato");
                }
                self::$connection->exec("ROLLBACK TO SAVEPOINT `$savepointName`");
                return true;
            }

            // Se non c'è una transazione attiva, non fa nulla
            if (!$this->inTransaction) {
                return false;
            }

            $result = self::$connection->rollBack();
            $this->inTransaction = false;
            $this->savepoints = []; // Cancella i savepoint al rollback
            return $result;
        } catch (PDOException $e) {
            // Gestione in caso di errore critico durante il rollback
            $this->inTransaction = false;
            $this->savepoints = [];
            throw $e;
        }
    }

    /**
     * Inserisce un record in una tabella.
     *
     * @param string $table Il nome della tabella.
     * @param array $data Dati da inserire (array associativo).
     * @param bool $ignore Se true, usa INSERT IGNORE.
     * @param array $onDuplicateUpdate Se specificato, esegue un UPSERT.
     * @return bool True in caso di successo.
     * @throws InvalidArgumentException Se i dati o la tabella non sono validi.
     * @throws PDOException Se l'esecuzione della query fallisce.
     */
    public function insert(string $table, array $data, bool $ignore = false, array $onDuplicateUpdate = []): bool
    {
        if (empty($table) || empty($data) || !$this->isAssociativeArray($data)) {
            throw new InvalidArgumentException('Tabella e dati (array associativo) sono obbligatori');
        }

        $this->validate($table);
        foreach (array_keys($data) as $column) {
            $this->validate($column);
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn($key) => ":$key", $columns);
        $ignoreClause = $ignore ? 'IGNORE' : '';

        $sql = sprintf(
            "INSERT %s INTO `%s` (`%s`) VALUES (%s)",
            $ignoreClause,
            $table,
            implode('`, `', $columns),
            implode(', ', $placeholders)
        );

        // Aggiunge la clausola ON DUPLICATE KEY UPDATE se specificata
        if (!empty($onDuplicateUpdate)) {
            foreach (array_keys($onDuplicateUpdate) as $column) {
                $this->validate($column);
            }

            $updateClauses = array_map(
                fn($key) => "`$key` = :update_$key",
                array_keys($onDuplicateUpdate)
            );
            $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $updateClauses);
        }

        try {
            $stmt = $this->getStatement($sql);

            // Binda i valori per la query di inserimento
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value, $this->getPdoType($value));
            }

            // Binda i valori per l'update in caso di duplicato
            foreach ($onDuplicateUpdate as $key => $value) {
                $stmt->bindValue(":update_$key", $value, $this->getPdoType($value));
            }

            $result = $stmt->execute();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Errore nell'operazione di inserimento: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Aggiorna record in una tabella.
     *
     * @param string $table Il nome della tabella.
     * @param array $data Dati da aggiornare (array associativo).
     * @param array $conditions Condizioni WHERE (array associativo).
     * @param string $operator Operatore logico per le condizioni ('AND' o 'OR').
     * @return int Il numero di righe aggiornate.
     * @throws InvalidArgumentException Se i parametri non sono validi.
     * @throws PDOException Se l'esecuzione della query fallisce.
     */
    public function update(string $table, array $data, array $conditions, string $operator = 'AND'): int
    {
        if (empty($table) || empty($data) || empty($conditions) ||
            !$this->isAssociativeArray($data) || !$this->isAssociativeArray($conditions)) {
            throw new InvalidArgumentException('Tabella, dati e condizioni (array associativi) sono obbligatori');
        }

        $this->validate($table);
        foreach (array_keys($data) as $column) {
            $this->validate($column);
        }
        foreach (array_keys($conditions) as $column) {
            $this->validate($column);
        }

        $operator = strtoupper($operator);
        if (!in_array($operator, ['AND', 'OR'])) {
            throw new InvalidArgumentException('Operatore deve essere AND o OR');
        }

        $setPlaceholders = array_map(fn($key) => "`$key` = :set_$key", array_keys($data));
        $wherePlaceholders = array_map(fn($key) => "`$key` = :where_$key", array_keys($conditions));

        $sql = sprintf(
            "UPDATE `%s` SET %s WHERE %s",
            $table,
            implode(', ', $setPlaceholders),
            implode(" $operator ", $wherePlaceholders)
        );

        try {
            $stmt = $this->getStatement($sql);

            // Binda i valori per la clausola SET
            foreach ($data as $key => $value) {
                $stmt->bindValue(":set_$key", $value, $this->getPdoType($value));
            }

            // Binda i valori per la clausola WHERE
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":where_$key", $value, $this->getPdoType($value));
            }

            $stmt->execute();
            $rowCount = $stmt->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            throw new PDOException("Errore nell'operazione di aggiornamento: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Elimina record da una tabella.
     *
     * @param string $table Il nome della tabella.
     * @param array $conditions Condizioni WHERE (array associativo).
     * @param string $operator Operatore logico per le condizioni ('AND' o 'OR').
     * @param int|null $limit Limite di righe da eliminare.
     * @return int Il numero di righe eliminate.
     * @throws InvalidArgumentException Se i parametri non sono validi.
     * @throws PDOException Se l'esecuzione della query fallisce.
     */
    public function delete(string $table, array $conditions, string $operator = 'AND', ?int $limit = null): int
    {
        if (empty($table) || empty($conditions) || !$this->isAssociativeArray($conditions)) {
            throw new InvalidArgumentException('Tabella e condizioni (array associativo) sono obbligatori');
        }

        $this->validate($table);
        foreach (array_keys($conditions) as $column) {
            $this->validate($column);
        }

        $operator = strtoupper($operator);
        if (!in_array($operator, ['AND', 'OR'])) {
            throw new InvalidArgumentException('Operatore deve essere AND o OR');
        }

        $wherePlaceholders = array_map(fn($key) => "`$key` = :where_$key", array_keys($conditions));

        $sql = sprintf(
            "DELETE FROM `%s` WHERE %s",
            $table,
            implode(" $operator ", $wherePlaceholders)
        );

        if ($limit !== null && $limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }

        try {
            $stmt = $this->getStatement($sql);

            // Binda i valori per la clausola WHERE
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":where_$key", $value, $this->getPdoType($value));
            }

            $stmt->execute();
            $rowCount = $stmt->rowCount();
            return $rowCount;
        } catch (PDOException $e) {
            throw new PDOException("Errore nell'operazione di eliminazione: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Esegue una query di selezione e restituisce i risultati.
     *
     * @param string $table Il nome della tabella.
     * @param array $conditions Condizioni WHERE.
     * @param string|array $columns Colonne da selezionare ('*' o un array di nomi).
     * @param array $joins Array di join.
     * @param array $orderBy Array per l'ordinamento.
     * @param int|null $limit Limite di righe.
     * @param int|null $offset Offset per la paginazione.
     * @param string $operator Operatore per le condizioni.
     * @return array I risultati della query.
     * @throws InvalidArgumentException Se i parametri non sono validi.
     * @throws PDOException Se l'esecuzione della query fallisce.
     */
    public function select(
        string $table,
        array $conditions = [],
        $columns = '*',
        array $joins = [],
        array $orderBy = [],
        ?int $limit = null,
        ?int $offset = null,
        string $operator = 'AND'
    ): array {
        if (empty($table)) {
            throw new InvalidArgumentException('Il nome della tabella è obbligatorio');
        }

        $this->validate($table);

        // Prepara la clausola SELECT delle colonne
        if (is_array($columns)) {
            foreach ($columns as $column) {
                $this->validate($column);
            }
            $columnClause = implode(', ', array_map(fn($col) => "`$col`", $columns));
        } else {
            $columnClause = '*';
        }

        $sql = "SELECT {$columnClause} FROM `{$table}`";

        // Gestisce le clausole JOIN
        foreach ($joins as $join) {
            if (!isset($join['table'], $join['on'])) {
                throw new InvalidArgumentException('JOIN deve avere "table" e "on"');
            }
            $this->validate($join['table']);
            $joinType = strtoupper($join['type'] ?? 'INNER');
            $sql .= " {$joinType} JOIN `{$join['table']}` ON {$join['on']}";
        }

        // Gestisce la clausola WHERE
        if (!empty($conditions)) {
            foreach (array_keys($conditions) as $column) {
                $this->validate($column);
            }

            $operator = strtoupper($operator);
            if (!in_array($operator, ['AND', 'OR'])) {
                throw new InvalidArgumentException('Operatore deve essere AND o OR');
            }

            $wherePlaceholders = array_map(fn($key) => "`$key` = :where_$key", array_keys($conditions));
            $sql .= " WHERE " . implode(" $operator ", $wherePlaceholders);
        }

        // Gestisce la clausola ORDER BY
        if (!empty($orderBy)) {
            $orderClauses = [];
            foreach ($orderBy as $column => $direction) {
                $this->validate($column);
                $direction = strtoupper($direction);
                if (!in_array($direction, ['ASC', 'DESC'])) {
                    throw new InvalidArgumentException('La direzione dell\'ordinamento deve essere ASC o DESC');
                }
                $orderClauses[] = "`$column` $direction";
            }
            $sql .= " ORDER BY " . implode(', ', $orderClauses);
        }

        // Gestisce LIMIT e OFFSET
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }

        try {
            $stmt = $this->getStatement($sql);

            // Binda i valori delle condizioni
            if (!empty($conditions)) {
                foreach ($conditions as $key => $value) {
                    $stmt->bindValue(":where_$key", $value, $this->getPdoType($value));
                }
            }

            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch (PDOException $e) {
            throw new PDOException("Errore nell'operazione di selezione: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Esegue una query SQL generica.
     *
     * @param string $sql La query SQL.
     * @param array $params I parametri per la query (solo per query non DDL).
     * @return array I risultati della query (solo per SELECT), altrimenti un array vuoto.
     * @throws InvalidArgumentException Se la query è vuota o ha parametri con query DDL.
     * @throws PDOException Se l'esecuzione della query fallisce.
     */
    public function query(string $sql, array $params = []): array
    {
        $trimmedSql = trim($sql);
        if (empty($trimmedSql)) {
            throw new InvalidArgumentException('La query SQL non può essere vuota');
        }

        $operation = strtoupper(strtok($trimmedSql, ' '));
        $isDDL = in_array($operation, ['CREATE', 'DROP', 'ALTER', 'TRUNCATE']);

        // Le query DDL non supportano i parametri
        if ($isDDL && !empty($params)) {
            throw new InvalidArgumentException("I parametri non sono supportati per le operazioni DDL come '$operation'.");
        }

        try {
            // Usa exec() per le operazioni DDL
            if ($isDDL) {
                self::$connection->exec($trimmedSql);
                return [];
            }

            // Usa prepare/execute per le altre query
            $stmt = $this->getStatement($trimmedSql);
            $stmt->execute($params);

            // Restituisce i risultati solo per le query SELECT
            if ($operation === 'SELECT') {
                $results = $stmt->fetchAll();
                return $results;
            }

            return [];
        } catch (PDOException $e) {
            throw new PDOException("Errore nella query SQL: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Trova e restituisce l'ultima riga inserita in una tabella, ordinando per l'ID in ordine decrescente.
     *
     * @param string $table Il nome della tabella.
     * @param string $idColumn La colonna ID.
     * @return array|null L'array della riga, o null se non trovata.
     * @throws InvalidArgumentException Se i parametri non sono validi.
     */
    public function findLast(string $table, string $idColumn = 'id'): ?array
    {
        $this->validate($table);
        $this->validate($idColumn);

        $sql = "SELECT * FROM `{$table}` ORDER BY `{$idColumn}` DESC LIMIT 1";
        $stmt = self::$connection->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false ? $result : null;
    }

    /**
     * Ottiene l'ID dell'ultima riga inserita.
     *
     * @return string L'ID generato.
     */
    public function lastInsertId(): string
    {
        return self::$connection->lastInsertId();
    }

    /**
     * Controlla se un array è associativo.
     *
     * @param array $array L'array da controllare.
     * @return bool True se è associativo, false altrimenti.
     */
    private function isAssociativeArray(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Determina il tipo di parametro PDO appropriato in base al tipo del valore.
     *
     * @param mixed $value Il valore da tipizzare.
     * @return int La costante PDO::PARAM_*.
     */
    private function getPdoType($value): int
    {
        return match (gettype($value)) {
            'boolean' => PDO::PARAM_BOOL,
            'integer' => PDO::PARAM_INT,
            'NULL' => PDO::PARAM_NULL,
            default => PDO::PARAM_STR
        };
    }

    /**
     * Pulisce la cache degli statement preparati.
     * Utile per liberare memoria se lo script è a lunga esecuzione.
     */
    public function clearStatementCache(): void
    {
        $this->statementCache = [];
    }

    /**
     * Ottiene statistiche sullo stato interno della classe.
     *
     * @return array Un array con il numero di statement in cache, lo stato della transazione e dei savepoint.
     */
    public function getStats(): array
    {
        return [
            'cached_statements' => count($this->statementCache),
            'in_transaction' => $this->inTransaction,
            'active_savepoints' => count($this->savepoints)
        ];
    }
}