<?php
namespace App\Core;

use PDO;
use PDOException;
use InvalidArgumentException;

class Database
{
    private static ?PDO $connection = null;
    private bool $inTransaction = false;
    private array $savepoints = [];
    
    // Configurazione logging interno
    private bool $loggingEnabled;
    private string $logFile;
    private string $logLevel;
    
    // caratteri validi per nomi tabelle/colonne
    private const VALID_IDENTIFIER_PATTERN = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
    
    // Cache per prepared statements
    private array $statementCache = [];
    private int $maxCacheSize = 100;

    public function __construct(bool $enableLogging = false, string $logFile = '', string $logLevel = 'ERROR')
    {
        $this->loggingEnabled = $enableLogging;
        $this->logFile = $logFile ?: sys_get_temp_dir() . '/database.log';
        $this->logLevel = strtoupper($logLevel);
        $this->initConnection();
    }

    /**
     * Logger semplice interno
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if (!$this->loggingEnabled) {
            return;
        }

        $levels = ['DEBUG' => 1, 'INFO' => 2, 'WARNING' => 3, 'ERROR' => 4];
        $currentLevel = $levels[$this->logLevel] ?? 4;
        $messageLevel = $levels[strtoupper($level)] ?? 4;

        if ($messageLevel < $currentLevel) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[$timestamp] $level: $message$contextStr" . PHP_EOL;
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Inizializza la connessione singleton
     */
    private function initConnection(): void
    {
        if (self::$connection === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                    $_ENV['DB_HOST'] ?? 'localhost',
                    $_ENV['DB_PORT'] ?? '3306',
                    $_ENV['DB_DATABASE'] ?? ''
                );

                self::$connection = new PDO(
                    $dsn,
                    $_ENV['DB_USERNAME'] ?? '',
                    $_ENV['DB_PASSWORD'] ?? '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_PERSISTENT => true,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]
                );
                
                $this->log('INFO', 'Connessione database stabilita');
            } catch (PDOException $e) {
                $this->log('ERROR', 'Errore connessione database', ['error' => $e->getMessage()]);
                throw new PDOException("Errore di connessione al database: " . $e->getMessage(), (int)$e->getCode());
            }
        }
    }

    /**
     * Valida un identificatore (nome tabella o colonna)
     */
    private function validate(string $identifier): void
    {
        if (!preg_match(self::VALID_IDENTIFIER_PATTERN, $identifier)) {
            throw new InvalidArgumentException("Identificatore non valido: $identifier");
        }
    }

    /**
     * Ottiene un prepared statement dalla cache o ne crea uno nuovo
     */
    private function getStatement(string $sql): \PDOStatement
    {
        $hash = md5($sql);
        
        if (!isset($this->statementCache[$hash])) {
            if (count($this->statementCache) >= $this->maxCacheSize) {
                // Rimuovi il primo elemento (FIFO)
                array_shift($this->statementCache);
            }
            $this->statementCache[$hash] = self::$connection->prepare($sql);
        }
        
        return $this->statementCache[$hash];
    }

    /**
     * Inizia una transazione o crea un savepoint
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
                $this->log('DEBUG', "Savepoint creato: $savepointName");
                return true;
            }

            if ($this->inTransaction) {
                // Non lanciare un'eccezione, ma ritorna false per indicare che non è stata avviata una nuova transazione
                return false;
            }
            
            $this->inTransaction = self::$connection->beginTransaction();
            $this->log('DEBUG', 'Transazione iniziata');
            return $this->inTransaction;
            
        } catch (PDOException $e) {
            $this->log('ERROR', 'Errore begin transaction', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Commit della transazione o rilascio di un savepoint
     */
    public function commit(?string $savepointName = null): bool
    {
        try {
            if ($savepointName !== null) {
                if (!in_array($savepointName, $this->savepoints)) {
                    throw new PDOException("Savepoint '$savepointName' non trovato");
                }
                self::$connection->exec("RELEASE SAVEPOINT `$savepointName`");
                $this->savepoints = array_filter($this->savepoints, fn($sp) => $sp !== $savepointName);
                $this->log('DEBUG', "Savepoint rilasciato: $savepointName");
                return true;
            }

            if (!$this->inTransaction) {
                return false;
            }
            
            $result = self::$connection->commit();
            $this->inTransaction = false;
            $this->savepoints = [];
            $this->log('DEBUG', 'Transazione committata');
            return $result;
            
        } catch (PDOException $e) {
            $this->log('ERROR', 'Errore commit', ['error' => $e->getMessage()]);
            if ($this->inTransaction) {
                $this->rollback();
            }
            throw $e;
        }
    }

    /**
     * Rollback della transazione o di un savepoint
     */
    public function rollback(?string $savepointName = null): bool
    {
        try {
            if ($savepointName !== null) {
                if (!in_array($savepointName, $this->savepoints)) {
                    throw new PDOException("Savepoint '$savepointName' non trovato");
                }
                self::$connection->exec("ROLLBACK TO SAVEPOINT `$savepointName`");
                $this->log('DEBUG', "Rollback al savepoint: $savepointName");
                return true;
            }

            if (!$this->inTransaction) {
                return false;
            }
            
            $result = self::$connection->rollBack();
            $this->inTransaction = false;
            $this->savepoints = [];
            $this->log('DEBUG', 'Transazione annullata');
            return $result;
            
        } catch (PDOException $e) {
            $this->inTransaction = false;
            $this->savepoints = [];
            $this->log('ERROR', 'Errore rollback', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Inserimento con supporto per UPSERT
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

        // Gestione ON DUPLICATE KEY UPDATE
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
            
            // Bind dei valori per l'inserimento
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value, $this->getPdoType($value));
            }
            
            // Bind dei valori per l'update (se presenti)
            foreach ($onDuplicateUpdate as $key => $value) {
                $stmt->bindValue(":update_$key", $value, $this->getPdoType($value));
            }

            $result = $stmt->execute();
            $this->log('DEBUG', "Insert eseguito", ['table' => $table, 'rows' => $stmt->rowCount()]);
            return $result;

        } catch (PDOException $e) {
            $this->log('ERROR', "Errore INSERT", ['table' => $table, 'error' => $e->getMessage()]);
            throw new PDOException("Errore nell'operazione di inserimento: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Update con condizioni più flessibili
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

            foreach ($data as $key => $value) {
                $stmt->bindValue(":set_$key", $value, $this->getPdoType($value));
            }

            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":where_$key", $value, $this->getPdoType($value));
            }

            $stmt->execute();
            $rowCount = $stmt->rowCount();
            $this->log('DEBUG', "Update eseguito", ['table' => $table, 'rows' => $rowCount]);
            return $rowCount;

        } catch (PDOException $e) {
            $this->log('ERROR', "Errore UPDATE", ['table' => $table, 'error' => $e->getMessage()]);
            throw new PDOException("Errore nell'operazione di aggiornamento: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Delete con condizioni flessibili
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

            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":where_$key", $value, $this->getPdoType($value));
            }

            $stmt->execute();
            $rowCount = $stmt->rowCount();
            $this->log('DEBUG', "Delete eseguito", ['table' => $table, 'rows' => $rowCount]);
            return $rowCount;

        } catch (PDOException $e) {
            $this->log('ERROR', "Errore DELETE", ['table' => $table, 'error' => $e->getMessage()]);
            throw new PDOException("Errore nell'operazione di eliminazione: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Select con supporto per JOIN, ORDER BY, LIMIT, etc.
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

        // Prepara le colonne
        if (is_array($columns)) {
            foreach ($columns as $column) {
                $this->validate($column);
            }
            $columnClause = implode(', ', array_map(fn($col) => "`$col`", $columns));
        } else {
            $columnClause = '*';
        }

        $sql = "SELECT {$columnClause} FROM `{$table}`";

        // Gestione JOIN
        foreach ($joins as $join) {
            if (!isset($join['table'], $join['on'])) {
                throw new InvalidArgumentException('JOIN deve avere "table" e "on"');
            }
            $this->validate($join['table']);
            $joinType = strtoupper($join['type'] ?? 'INNER');
            $sql .= " {$joinType} JOIN `{$join['table']}` ON {$join['on']}";
        }

        // Gestione WHERE
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

        // Gestione ORDER BY
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

        // Gestione LIMIT e OFFSET
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }

        try {
            $stmt = $this->getStatement($sql);

            if (!empty($conditions)) {
                foreach ($conditions as $key => $value) {
                    $stmt->bindValue(":where_$key", $value, $this->getPdoType($value));
                }
            }

            $stmt->execute();
            $results = $stmt->fetchAll();
            $this->log('DEBUG', "Select eseguito", ['table' => $table, 'rows' => count($results)]);
            return $results;

        } catch (PDOException $e) {
            $this->log('ERROR', "Errore SELECT", ['table' => $table, 'error' => $e->getMessage()]);
            throw new PDOException("Errore nell'operazione di selezione: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Esegue una query SQL. Gestisce SELECT (con parametri) e DDL (CREATE, DROP, ALTER).
     */
    public function query(string $sql, array $params = []): array
    {
        $trimmedSql = trim($sql);
        if (empty($trimmedSql)) {
            throw new InvalidArgumentException('La query SQL non può essere vuota');
        }

        $operation = strtoupper(strtok($trimmedSql, ' '));
        $isDDL = in_array($operation, ['CREATE', 'DROP', 'ALTER', 'TRUNCATE']);

        // Per le query DDL, i parametri non sono supportati da PDO.
        if ($isDDL && !empty($params)) {
            throw new InvalidArgumentException("I parametri non sono supportati per le operazioni DDL come '$operation'.");
        }

        try {
            // Per le DDL, usiamo exec() che è più appropriato.
            if ($isDDL) {
                self::$connection->exec($trimmedSql);
                $this->log('DEBUG', "Query DDL eseguita", ['operation' => $operation]);
                return []; // Ritorna un array vuoto per consistenza.
            }

            // Per SELECT, INSERT, UPDATE, DELETE, usiamo prepared statements.
            $stmt = $this->getStatement($trimmedSql);
            
            $stmt->execute($params);
            
            // Restituisce i risultati solo per le query SELECT.
            if ($operation === 'SELECT') {
                $results = $stmt->fetchAll();
                $this->log('DEBUG', "Query eseguita", ['operation' => $operation, 'rows' => count($results)]);
                return $results;
            }

            // Per INSERT, UPDATE, DELETE, potremmo restituire rowCount(), ma per ora un array vuoto è ok.
            return [];

        } catch (PDOException $e) {
            $this->log('ERROR', "Errore QUERY", ['sql' => $trimmedSql, 'error' => $e->getMessage()]);
            throw new PDOException("Errore nella query SQL: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Ottiene l'ultimo ID inserito
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
     * METODO RINOMINATO: Ottiene l'ID dell'ultima riga INSERITA.
     * Questo è il comportamento corretto per lastInsertId().
     */
    public function lastInsertId(): string
    {
        return self::$connection->lastInsertId();
    }

    /**
     * Controlla se un array è associativo
     */
    private function isAssociativeArray(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Determina il tipo PDO appropriato per un valore
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
     * Pulisce la cache degli statement
     */
    public function clearStatementCache(): void
    {
        $this->statementCache = [];
        $this->log('DEBUG', 'Cache statement pulita');
    }

    /**
     * Ottiene statistiche sulla connessione
     */
    public function getStats(): array
    {
        return [
            'cached_statements' => count($this->statementCache),
            'in_transaction' => $this->inTransaction,
            'active_savepoints' => count($this->savepoints)
        ];
    }

    /**
     * Abilita o disabilita il logging
     */
    public function setLogging(bool $enabled, string $logFile = '', string $logLevel = 'ERROR'): void
    {
        $this->loggingEnabled = $enabled;
        if (!empty($logFile)) {
            $this->logFile = $logFile;
        }
        if (!empty($logLevel)) {
            $this->logLevel = strtoupper($logLevel);
        }
    }

    /**
     * Ottiene il percorso del file di log corrente
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }
}
