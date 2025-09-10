<?php partial('head');?>
<title>Guida Database - Otix Core</title>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous">
    <style>
        .code-block {
            background-color: #f3f4f6; /* gray-100 */
        }
        pre{
            overflow:auto;
        }
    </style>
</head>

<body class="bg-gray-50 text-black font-sans antialiased min-h-screen">
    <div class="flex flex-col lg:flex-row min-h-screen">
        <aside class="lg:w-80 bg-white border-r border-black lg:fixed h-full lg:overflow-y-auto shadow-md">
            <div class="p-8">
                <div class="mb-8">
                <a href="/<?= eq(current_lang()) ?>/index"><h2 class="text-3xl font-bold text-black mb-2">Otix Core</h2></a>
                <p class="text-black text-sm">Guida Database</p>
                </div>
                <nav>
                    <ul class="space-y-2">
                        <li><a href="#connessione" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-gear mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">1. Connessione</span>
                            </a></li>
                        <li><a href="#insert" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-floppy-disk mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">2. INSERT</span>
                            </a></li>
                        <li><a href="#last-id" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-id-card-clip mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">2.1. lastInsertId</span>
                            </a></li>
                        <li><a href="#update" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-pen-to-square mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">3. UPDATE</span>
                            </a></li>
                        <li><a href="#delete" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-trash-can mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">4. DELETE</span>
                            </a></li>
                        <li><a href="#select" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-magnifying-glass mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">5. SELECT</span>
                            </a></li>
                        <li><a href="#query" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-rocket mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">6. Query Libera</span>
                            </a></li>
                        <li><a href="#transazioni" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-arrow-right-arrow-left mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">7. Transazioni</span>
                            </a></li>
                        <li><a href="#migrazioni" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-database mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">8. Migrazioni</span>
                            </a></li>
                        <li><a href="#cli" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                                <i class="fa-solid fa-terminal mr-3 transition-colors duration-300 group-hover:text-white"></i>
                                <span class="font-medium">9. DO CLI</span>
                            </a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        
        <main class="flex-1 lg:ml-80 p-6 lg:p-12">
            <div class="max-w-4xl mx-auto">
                <header class="mb-16 text-center">
                    <h1 class="text-5xl lg:text-6xl font-bold text-black mb-6">Guida Database</h1>
                    <div class="mt-8 flex justify-center">
                        <div class="h-1 w-24 bg-black rounded-full"></div>
                    </div>
                </header>
                <section id="connessione" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-gear"></i> 1. Connessione al Database
                        </h2>
                        <p class="text-gray-800">Per prima cosa, devi ottenere un'istanza della classe <code>Database</code>. La connessione viene gestita automaticamente nel costruttore.</p>
                        <pre class="bg-black text-white rounded-lg p-4 mt-4 overflow-x-auto"><code>use App\Core\Database;</code> <br> <code>//Crea l'oggetto che si connette al DB</code><br><code>$db = new Database();</code></pre>
                        <p class="mt-3 text-sm text-gray-800">Da questo momento in poi, utilizzerai la variabile <code>$db</code> per chiamare tutti gli altri metodi.</p>
                    </div>
                </section>

                <section id="insert" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> 2. Eseguire un `INSERT`
                        </h2>
                        <p class="text-gray-800">Per inserire una nuova riga in una tabella.</p>
                        <h4 class="font-semibold mt-4 text-black">Sintassi</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>$db->insert(string $table, array $data): bool</code></pre>
                        <h4 class="font-semibold mt-4 text-black">Parametri</h4>
                        <ul class="list-disc list-inside mt-2 text-gray-800">
                            <li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li>
                            <li><code>$data</code> <strong>(array)</strong>: Un array <strong>associativo</strong> (chiave => valore).</li>
                        </ul>
                        <h4 class="font-semibold mt-4 text-black">Valore di Ritorno</h4>
                        <p class="text-gray-800"><strong><code>bool</code></strong>: Ritorna <code>true</code> se l'inserimento ha avuto successo.</p>
                    </div>
                </section>
                
                <section id="last-id" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h3 class="text-xl font-bold text-black mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-id-card-clip"></i> 2.1 Ottenere l'Ultimo ID Inserito (`lastInsertId`)
                        </h3>
                        <p class="text-gray-800">Subito dopo un <code>INSERT</code>, puoi recuperare l'ID auto-generato.</p>
                        <h4 class="font-semibold mt-4 text-black">Sintassi</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>$db->lastInsertId(): string</code></pre>
                        <h4 class="font-semibold mt-4 text-black">Valore di Ritorno</h4>
                        <p class="text-gray-800"><strong><code>string</code></strong>: L'ID dell'ultima riga inserita.</p>
                    </div>
                </section>
                
                <section id="update" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square"></i> 3. Eseguire un `UPDATE`
                        </h2>
                        <p class="text-gray-800">Per modificare righe esistenti.</p>
                        <h4 class="font-semibold mt-4 text-black">Sintassi</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>$db->update(string $table, array $data, array $where): int</code></pre>
                        <h4 class="font-semibold mt-4 text-black">Parametri</h4>
                        <ul class="list-disc list-inside mt-2 text-gray-800">
                            <li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li>
                            <li><code>$data</code> <strong>(array)</strong>: Array associativo con i nuovi dati.</li>
                            <li><code>$where</code> <strong>(array)</strong>: Array associativo per la clausola `WHERE`.</li>
                        </ul>
                        <h4 class="font-semibold mt-4 text-black">Valore di Ritorno</h4>
                        <p class="text-gray-800"><strong><code>int</code></strong>: Il numero di righe modificate.</p>
                    </div>
                </section>

                <section id="delete" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-trash-can"></i> 4. Eseguire una `DELETE`
                        </h2>
                        <p class="text-gray-800">Per eliminare righe da una tabella.</p>
                        <h4 class="font-semibold mt-4 text-black">Sintassi</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>$db->delete(string $table, array $where): int</code></pre>
                        <h4 class="font-semibold mt-4 text-black">Parametri</h4>
                        <ul class="list-disc list-inside mt-2 text-gray-800">
                            <li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li>
                            <li><code>$where</code> <strong>(array)</strong>: Array associativo per la clausola `WHERE`.</li>
                        </ul>
                        <h4 class="font-semibold mt-4 text-black">Valore di Ritorno</h4>
                        <p class="text-gray-800"><strong><code>int</code></strong>: Il numero di righe eliminate.</p>
                    </div>
                </section>
                
                <section id="select" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> 5. Eseguire una `SELECT`
                        </h2>
                        <p class="text-gray-800">Per leggere e recuperare dati.</p>
                        <h4 class="font-semibold mt-4 text-black">Sintassi</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>$db->select(string $table, array $where = [], $columns = '*'): array</code></pre>
                        <h4 class="font-semibold mt-4 text-black">Parametri</h4>
                        <ul class="list-disc list-inside mt-2 text-gray-800">
                            <li><code>$table</code> <strong>(string)</strong>: Il nome della tabella.</li>
                            <li><code>$where</code> <strong>(array, opzionale)</strong>: Condizione `WHERE`.</li>
                            <li><code>$columns</code> <strong>(string|array, opzionale)</strong>: Colonne da selezionare.</li>
                        </ul>
                        <h4 class="font-semibold mt-4 text-black">Valore di Ritorno</h4>
                        <p class="text-gray-800"><strong><code>array</code></strong>: Un array di array associativi con i risultati.</p>
                    </div>
                </section>
                
                <section id="query" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-rocket"></i> 6. Eseguire una Query Libera (`query`)
                        </h2>
                        <p class="text-gray-800">Per i casi in cui è necessaria una query complessa o per comandi che modificano la struttura del database (DDL).</p>
                        <h4 class="font-semibold mt-4 text-black">Sintassi</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>$db->query(string $sql, array $params = []): array</code></pre>
                        <h4 class="font-semibold mt-4 text-black">Esempi</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>// SELECT complessa con join e parametri
$results = $db->query(
    'SELECT u.*, p.product_name FROM users u JOIN purchases p ON u.id = p.user_id WHERE u.id = ?',
    [1]
);

// Comando DDL (Data Definition Language)
// Non restituisce risultati, ma modifica lo schema del DB
$db->query('CREATE TABLE logs (id INT PRIMARY KEY, message TEXT)');</code></pre>
                    </div>
                </section>
                
                <section id="transazioni" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i> 7. Gestione delle Transazioni
                        </h2>
                        <p class="text-gray-800">Per eseguire una serie di operazioni come un unico blocco atomico. Essenziale per operazioni complesse.</p>
                        <h4 class="font-semibold mt-4 text-black">Metodi</h4>
                        <ul class="list-disc list-inside mt-2 text-gray-800">
                            <li><code>beginTransaction()</code>: Avvia una nuova transazione.</li>
                            <li><code>commit()</code>: Salva permanentemente le modifiche.</li>
                            <li><code>rollback()</code>: Annulla tutte le modifiche.</li>
                        </ul>
                        <h4 class="font-semibold mt-4 text-black">Esempio</h4>
                        <pre class="bg-black text-white rounded-lg p-4 mt-2 overflow-x-auto"><code>try {
    $db->beginTransaction();

    $db->insert('ordini', ['utente_id' => 1, 'prodotto' => 'Libro']);
    $db->update('prodotti', ['quantita' => 9], ['id' => 123]);

    $db->commit();
    echo "Operazione completata!";
} catch (\PDOException $e) {
    $db->rollback();
    echo "Operazione fallita e annullata.";
}</code></pre>
                    </div>
                </section>

                <hr class="my-8 border-black">
                
                <section id="migrazioni" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h1 class="text-3xl font-extrabold text-black mb-4">
                            Schema Versioning con le Migrazioni
                        </h1>
                        <p class="text-lg text-gray-800">
                            Le migrazioni ti permettono di definire e condividere la struttura del database della tua applicazione in modo programmatico. Sono come un sistema di controllo versione per il tuo database.
                        </p>
                    </div>
                </section>
                
                <section id="cli" class="mb-16">
                    <div class="bg-white shadow-md rounded-xl p-6 mb-8 border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-2xl font-bold text-black mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-code"></i> 9. DO CLI: Il Tuo Tool da Console
                        </h2>
                        <p class="text-gray-800">Lo script <code>do</code> è la tua interfaccia a riga di comando per gestire le migrazioni. Tutti i comandi vanno eseguiti dalla cartella radice.</p>

                        <hr class="my-6 border-black">

                        <div class="mb-6">
                            <h4 class="font-bold text-lg text-black flex items-center gap-2">
                                <i class="fa-solid fa-plus-circle"></i> Comando: <code>make:migration</code>
                            </h4>
                            <p class="mt-2 text-gray-800">Crea un nuovo file di migrazione vuoto nella cartella <code>database/migrations/</code>.</p>
                            <div class="bg-black text-white rounded-lg p-4 mt-3">
                                <span class="text-green-400 font-mono">$</span> <span class="text-gray-200 font-mono">php do make:migration create_products_table</span><br>
                                <span class="text-gray-400 font-mono">Migrazione creata con successo: 2025_07_28_173000_create_products_table.php</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="font-bold text-lg text-black flex items-center gap-2">
                                <i class="fa-solid fa-upload"></i> Comando: <code>migrate</code>
                            </h4>
                            <p class="mt-2 text-gray-800">Esegue tutte le migrazioni "pendenti".</p>
                            <div class="bg-black text-white rounded-lg p-4 mt-3">
                                <span class="text-green-400 font-mono">$</span> <span class="text-gray-200 font-mono">php do migrate</span><br>
                                <span class="text-gray-400 font-mono">In esecuzione in corso: 2025_07_28_173000_create_products_table.php</span><br>
                                <span class="text-gray-400 font-mono">Eseguita           : 2025_07_28_173000_create_products_table.php</span><br>
                                <span class="text-gray-400 font-mono">Migrazioni eseguite con successo.</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="font-bold text-lg text-black flex items-center gap-2">
                                <i class="fa-solid fa-undo"></i> Comando: <code>rollback</code>
                            </h4>
                            <p class="mt-2 text-gray-800">Annulla l'ultimo "batch" di migrazioni eseguite.</p>
                                <div class="bg-black text-white rounded-lg p-4 mt-3">
                                <span class="text-green-400 font-mono">$</span> <span class="text-gray-200 font-mono">php do rollback</span><br>
                                <span class="text-gray-400 font-mono">Annullamento in corso: 2025_07_28_173000_create_products_table.php</span><br>
                                <span class="text-gray-400 font-mono">Annullata            : 2025_07_28_173000_create_products_table.php</span><br>
                                <span class="text-gray-400 font-mono">Rollback completato con successo.</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="font-bold text-lg text-black flex items-center gap-2">
                                <i class="fa-solid fa-list-check"></i> Comando: <code>status</code>
                            </h4>
                            <p class="mt-2 text-gray-800">Mostra lo stato di tutte le migrazioni.</p>
                                <div class="bg-black text-white rounded-lg p-4 mt-3">
                                <span class="text-green-400 font-mono">$</span> <span class="text-gray-200 font-mono">php do status</span><br>
                                <span class="text-gray-400 font-mono">Stato delle Migrazioni</span><br>
                                <span class="text-gray-400 font-mono">-----------------------</span><br>
                                <span class="text-gray-400 font-mono">[ <span class="text-green-400">✓ Eseguita</span> ] 2025_07_28_142519_create_users_table.php</span><br>
                                <span class="text-gray-400 font-mono">[ <span class="text-red-400">✗ Pendente</span> ] 2025_07_28_173000_create_products_table.php</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="font-bold text-lg text-black flex items-center gap-2">
                                <i class="fa-solid fa-broom"></i> Comando: <code>cache:clean</code>
                            </h4>
                            <p class="mt-2 text-gray-800">Cancella i file di cache delle viste (.html) in <code>/storage/cache/views/</code>.</p>
                                <div class="bg-black text-white rounded-lg p-4 mt-3">
                                <span class="text-green-400 font-mono">$</span> <span class="text-gray-200 font-mono">php do cache:clear</span><br>
                                <span class="text-gray-400 font-mono">Cache delle viste pulita con successo. Rimossi {count} file.</span>
                            </div>
                        </div>

                    </div>
                </section>

                <div class="bg-white border border-black text-black px-4 py-3 rounded-xl relative shadow-md" role="alert">
                <h4 class="text-xl font-bold mb-2">⚠️ Gestione degli Errori</h4>
                <p class="text-gray-800">Ricorda: tutti i metodi della classe <code>Database</code> lanciano un'eccezione <code>\PDOException</code> se il database restituisce un errore. <strong>È obbligatorio racchiudere sempre le chiamate in un blocco <code>try...catch</code></strong> per gestire i fallimenti in modo pulito.</p>
                </div>
            </div>
        </main>
    </div>
</body>
<?php partial('footer');?>