<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guida Operativa alla Classe Database</title>
    <link rel="stylesheet" href="/public/assets/css/bootstrap.min.css">
    <style>
        body {
            padding: 2rem;
        }
        .card {
            margin-bottom: 1.5rem;
        }
        code {
            background-color: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.9em;
        }
        pre > code {
            display: block;
            padding: 1rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .terminal {
            background-color: #212529;
            color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            font-family: monospace;
        }
        .terminal .prompt {
            color: #0d6efd;
        }
        .terminal .output {
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Guida Operativa alla Classe Database e Migrazioni</h1>
        <p class="lead">Questa è una guida pratica per utilizzare la classe <code>App\Core\Database</code> e gestire lo schema del database in modo controllato tramite il tool da riga di comando <code>do</code>.</p>

        <hr class="my-4">

        <div class="card"><div class="card-header"><h2>⚙️ 1. Connessione al Database</h2></div><div class="card-body"><p>Per prima cosa, devi ottenere un'istanza della classe <code>Database</code>. La connessione viene gestita automaticamente nel costruttore.</p><pre><code>use App\Core\Database;\n\n// Crea l'oggetto che si connette al DB\n$db = new Database();</code></pre><p class="mt-3">Da questo momento in poi, utilizzerai la variabile <code>$db</code> per chiamare tutti gli altri metodi.</p></div></div>
        <div class="card"><div class="card-header"><h2>💾 2. Eseguire un `INSERT`</h2></div><div class="card-body"><p>Per inserire una nuova riga in una tabella.</p><h4>Sintassi</h4><pre><code>$db->insert(string $table, array $data): bool</code></pre><h4>Parametri</h4><ul><li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li><li><code>$data</code> <strong>(array)</strong>: Un array <strong>associativo</strong> (chiave => valore).</li></ul><h4>Valore di Ritorno</h4><p><strong><code>bool</code></strong>: Ritorna <code>true</code> se l'inserimento ha avuto successo.</p></div></div>
        <div class="card"><div class="card-header"><h3>💾 2.1 Ottenere l'Ultimo ID Inserito (`lastInsertId`)</h3></div><div class="card-body"><p>Subito dopo un <code>INSERT</code>, puoi recuperare l'ID auto-generato.</p><h4>Sintassi</h4><pre><code>$db->lastInsertId(): string</code></pre><h4>Valore di Ritorno</h4><p><strong><code>string</code></strong>: L'ID dell'ultima riga inserita.</p></div></div>
        <div class="card"><div class="card-header"><h2>🔄 3. Eseguire un `UPDATE`</h2></div><div class="card-body"><p>Per modificare righe esistenti.</p><h4>Sintassi</h4><pre><code>$db->update(string $table, array $data, array $where): int</code></pre><h4>Parametri</h4><ul><li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li><li><code>$data</code> <strong>(array)</strong>: Array associativo con i nuovi dati.</li><li><code>$where</code> <strong>(array)</strong>: Array associativo per la clausola `WHERE`.</li></ul><h4>Valore di Ritorno</h4><p><strong><code>int</code></strong>: Il numero di righe modificate.</p></div></div>
        <div class="card"><div class="card-header"><h2>🗑️ 4. Eseguire una `DELETE`</h2></div><div class="card-body"><p>Per eliminare righe da una tabella.</p><h4>Sintassi</h4><pre><code>$db->delete(string $table, array $where): int</code></pre><h4>Parametri</h4><ul><li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li><li><code>$where</code> <strong>(array)</strong>: Array associativo per la clausola `WHERE`.</li></ul><h4>Valore di Ritorno</h4><p><strong><code>int</code></strong>: Il numero di righe eliminate.</p></div></div>
        <div class="card"><div class="card-header"><h2>🔍 5. Eseguire una `SELECT`</h2></div><div class="card-body"><p>Per leggere e recuperare dati.</p><h4>Sintassi</h4><pre><code>$db->select(string $table, array $where = [], $columns = '*'): array</code></pre><h4>Parametri</h4><ul><li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li><li><code>$where</code> <strong>(array, opzionale)</strong>: Condizione `WHERE`.</li><li><code>$columns</code> <strong>(string|array, opzionale)</strong>: Colonne da selezionare.</li></ul><h4>Valore di Ritorno</h4><p><strong><code>array</code></strong>: Un array di array associativi con i risultati.</p></div></div>
        <div class="card"><div class="card-header"><h2>🚀 6. Eseguire una Query Libera (`query`)</h2></div><div class="card-body"><p>Per i casi in cui è necessaria una query complessa o per comandi che modificano la struttura del database (DDL).</p><h4>Sintassi</h4><pre><code>$db->query(string $sql, array $params = []): array</code></pre><h4>Esempi</h4><pre><code>// SELECT complessa con join e parametri
$results = $db->query(
    'SELECT u.*, p.product_name FROM users u JOIN purchases p ON u.id = p.user_id WHERE u.id = ?',
    [1]
);

// Comando DDL (Data Definition Language)
// Non restituisce risultati, ma modifica lo schema del DB
$db->query('CREATE TABLE logs (id INT PRIMARY KEY, message TEXT)');
</code></pre></div></div>
        <div class="card"><div class="card-header"><h2>🔁 7. Gestione delle Transazioni</h2></div><div class="card-body"><p>Per eseguire una serie di operazioni come un unico blocco atomico. Essenziale per operazioni complesse.</p><h4>Metodi</h4><ul><li><code>beginTransaction()</code>: Avvia una nuova transazione.</li><li><code>commit()</code>: Salva permanentemente le modifiche.</li><li><code>rollback()</code>: Annulla tutte le modifiche.</li></ul><h4>Esempio</h4><pre><code>try {
    $db->beginTransaction();

    $db->insert('ordini', ['utente_id' => 1, 'prodotto' => 'Libro']);
    $db->update('prodotti', ['quantita' => 9], ['id' => 123]);

    $db->commit();
    echo "Operazione completata!";
} catch (\PDOException $e) {
    $db->rollback();
    echo "Operazione fallita e annullata.";
}</code></pre></div></div>

        <hr class="my-5">

        <h1 class="mb-4">Schema Versioning con le Migrazioni</h1>
        <p class="lead">Le migrazioni ti permettono di definire e condividere la struttura del database della tua applicazione in modo programmatico. Sono come un sistema di controllo versione per il tuo database.</p>
        
        <div class="card">
            <div class="card-header">
                <h2>🧑‍💻 9. DO CLI: Il Tuo Tool da Console</h2>
            </div>
            <div class="card-body">
                <p>Lo script <code>do</code> è la tua interfaccia a riga di comando per gestire le migrazioni. Tutti i comandi vanno eseguiti dalla cartella radice.</p>
                
                <hr>

                <h4>Comando: <code>make:migration</code></h4>
                <p>Crea un nuovo file di migrazione vuoto nella cartella <code>database/migrations/</code>.</p>
                <div class="terminal">
                    <span class="prompt">$</span> php do make:migration create_products_table
                    <br>
                    <span class="output">Migrazione creata con successo: 2025_07_28_173000_create_products_table.php</span>
                </div>
                <p class="mt-3">Il file generato conterrà una classe con due metodi: <code>up()</code> per applicare la modifica e <code>down()</code> per annullarla.</p>

                <hr>

                <h4>Comando: <code>migrate</code></h4>
                <p>Esegue tutte le migrazioni "pendenti", cioè quelle che non sono ancora state eseguite sul database corrente.</p>
                <div class="terminal">
                    <span class="prompt">$</span> php do migrate
                    <br>
                    <span class="output">In esecuzione in corso: 2025_07_28_173000_create_products_table.php</span>
                    <br>
                    <span class="output">Eseguita           : 2025_07_28_173000_create_products_table.php</span>
                    <br>
                    <span class="output">Migrazioni eseguite con successo.</span>
                </div>

                <hr>

                <h4>Comando: <code>rollback</code></h4>
                <p>Annulla l'ultimo "batch" di migrazioni eseguite. È utile per tornare indietro velocemente se hai commesso un errore.</p>
                 <div class="terminal">
                    <span class="prompt">$</span> php do rollback
                    <br>
                    <span class="output">Annullamento in corso: 2025_07_28_173000_create_products_table.php</span>
                    <br>
                    <span class="output">Annullata            : 2025_07_28_173000_create_products_table.php</span>
                     <br>
                    <span class="output">Rollback completato con successo.</span>
                </div>
                
                <hr>
                
                <h4>Comando: <code>status</code></h4>
                <p>Mostra lo stato di tutte le migrazioni, indicando quali sono state eseguite e quali sono ancora pendenti.</p>
                 <div class="terminal">
                    <span class="prompt">$</span> php do status
                    <br>
                    <span class="output">Stato delle Migrazioni</span>
                    <br>
                    <span class="output">-----------------------</span>
                    <br>
                    <span class="output">[ ✓ Eseguita ] 2025_07_28_142519_create_users_table.php</span>
                    <br>
                    <span class="output">[ ✗ Pendente ] 2025_07_28_173000_create_products_table.php</span>
                </div>

                <hr>

                <h4>Comando: <code>cache:clean</code></h4>
                <p>Cancella i file di cache delle viste (.html) in <code>/storage/cache/views/</code></p>
                 <div class="terminal">
                    <span class="prompt">$</span> php do cache:clear
                    <br>
                    <span class="output">Cache delle viste pulita con successo. Rimossi {count} file.</span>
                </div>

            </div>
        </div>


        <div class="alert alert-warning" role="alert">
          <h4 class="alert-heading">⚠️ Gestione degli Errori</h4>
          <p>Ricorda: tutti i metodi della classe <code>Database</code> lanciano un'eccezione <code>\PDOException</code> se il database restituisce un errore. <strong>È obbligatorio racchiudere sempre le chiamate in un blocco <code>try...catch</code></strong> per gestire i fallimenti in modo pulito.</p>
        </div>

    </div>
</body>
</html>