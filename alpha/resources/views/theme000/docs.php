<?php partial('head');?>
<title>Documentazione - Otix Core</title>
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

<body class="text-black font-sans antialiased min-h-screen bg-gray-50">
    <div class="flex flex-col lg:flex-row min-h-screen">
        <aside class="lg:w-80 bg-white border-r border-black lg:fixed lg:h-screen lg:overflow-y-auto shadow-md">
            <div class="p-8">
                <div class="mb-8">
                <a href="/<?= eq(current_lang()) ?>/index"><h2 class="text-3xl font-bold text-black mb-2">Otix Core</h2></a>
                    <p class="text-gray-800 text-sm">Documentazione</p>
                </div>
                <nav>
                    <ul class="space-y-2">
                        <li><a href="#panoramica" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-layer-group mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Panoramica</span>
                        </a></li>
                        <li><a href="#architettura" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-sitemap mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Architettura</span>
                        </a></li>
                        <li><a href="#flusso-richiesta" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-diagram-project mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Flusso della Richiesta</span>
                        </a></li>
                        <li><a href="#router" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-route mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Il Router</span>
                        </a></li>
                        <li><a href="#controller" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-microchip mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Controller</span>
                        </a></li>
                        <li><a href="#core" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-code mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Core</span>
                        </a></li>
                        <li><a href="#middleware" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-filter mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Middleware</span>
                        </a></li>
                        <li><a href="#config" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-sliders mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Configurazione</span>
                        </a></li>
                        <li><a href="#database" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-database mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Database</span>
                        </a></li>
                        <li><a href="#public" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-globe mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Public</span>
                        </a></li>
                        <li><a href="#resources" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-folder-open mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Resources</span>
                        </a></li>
                        <li><a href="#storage" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-box mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Storage</span>
                        </a></li>
                        <li><a href="#users" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-users mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Users</span>
                        </a></li>
                        <li><a href="#vendor" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-truck-fast mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">Vendor</span>
                        </a></li>
                        <li><a href="#root" class="sidebar-link flex items-center py-3 px-4 rounded-xl hover:bg-black hover:text-white transition-all duration-300 text-black group">
                            <i class="fa-solid fa-file-code mr-3 text-black transition-all duration-300 group-hover:text-white"></i>
                            <span class="font-medium">File Principali</span>
                        </a></li>
                    </ul>
                </nav>
            </div>
        </aside>
        <main class="flex-1 lg:ml-80 p-6 lg:p-12">
            <div class="max-w-4xl mx-auto">
                <header class="mb-16 text-center">
                    <h1 class="text-5xl lg:text-6xl font-bold text-black mb-6">DOCS</h1>
                    <div class="mt-8 flex justify-center">
                        <div class="h-1 w-24 bg-black rounded-full"></div>
                    </div>
                </header>
                <section id="panoramica" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Panoramica del Framework
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6"><strong class="text-black">Otix Core</strong> è un micro-framework PHP 8.2+ progettato per la costruzione rapida e scalabile di applicazioni web. La sua filosofia si basa sulla modularità e sul <em>Separation of Concerns</em> (Separazione delle Responsabilità), con ogni componente (Router, Database, Mailer) che svolge un compito specifico e ben definito.</p>
                        <div class="bg-white rounded-2xl p-6 border border-black">
                            <h3 class="text-2xl font-bold mb-4 text-black flex items-center">
                                <i class="fa-solid fa-circle-check text-black mr-3"></i>
                                Requisiti di Sistema
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-black rounded-full mt-3 mr-4 flex-shrink-0"></span>
                                    <span class="text-gray-800"><strong class="text-black">PHP 8.2 o superiore</strong> per sfruttare le ultime funzionalità e miglioramenti delle performance.</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-black rounded-full mt-3 mr-4 flex-shrink-0"></span>
                                    <span class="text-gray-800">Estensioni PHP: <code class="bg-black text-white px-2 py-1 rounded text-sm">pdo_mysql</code>, <code class="bg-black text-white px-2 py-1 rounded text-sm">mbstring</code>, <code class="bg-black text-white px-2 py-1 rounded text-sm">openssl</code></span>
                                </li>
                                <li class="flex items-start">
                                    <span class="w-2 h-2 bg-black rounded-full mt-3 mr-4 flex-shrink-0"></span>
                                    <span class="text-gray-800">Server web (Apache o Nginx) con supporto per URL rewriting.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
                <section id="architettura" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Architettura: Il Pattern Front Controller
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed">Il cuore dell'architettura di Otix Core è il design pattern <strong class="text-black">Front Controller</strong>. A differenza di un approccio tradizionale dove ogni URL corrisponde a un file PHP, qui tutte le richieste sono instradate a un unico punto di ingresso: <code class="bg-black text-white px-3 py-1 rounded font-mono">public/index.php</code>.</p>
                    </div>
                </section>
                <section id="flusso-richiesta" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Flusso Dettagliato della Richiesta
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-8">Ogni richiesta HTTP segue un percorso ben definito all'interno del framework:</p>
                        <div class="space-y-4">
                            <div class="group flex items-start bg-white rounded-xl p-6 border-l-4 border-black transition-all duration-300 ">
                                <div class="flex-shrink-0 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm mr-4 transition-all duration-300 group-hover:bg-white group-hover:text-black">1</div>
                                <div>
                                    <h3 class="font-bold text-black mb-2 transition-all duration-300 ">Inizializzazione</h3>
                                    <p class="text-black transition-all duration-300 ">Il file <code class="bg-black text-white px-2 py-1 rounded text-sm">public/index.php</code> include l'autoloader di Composer e avvia il motore dell'applicazione.</p>
                                </div>
                            </div>
                            <div class="group flex items-start bg-white rounded-xl p-6 border-l-4 border-black transition-all duration-300 ">
                                <div class="flex-shrink-0 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm mr-4 transition-all duration-300 group-hover:bg-white group-hover:text-black">2</div>
                                <div>
                                    <h3 class="font-bold text-black mb-2 transition-all duration-300 ">Gestione del Dominio</h3>
                                    <p class="text-black transition-all duration-300 ">Il middleware <code class="bg-black text-white px-2 py-1 rounded text-sm">SetDomain</code> carica le configurazioni specifiche dal file di configurazione.</p>
                                </div>
                            </div>
                            <div class="group flex items-start bg-white rounded-xl p-6 border-l-4 border-black transition-all duration-300 ">
                                <div class="flex-shrink-0 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm mr-4 transition-all duration-300 group-hover:bg-white group-hover:text-black">3</div>
                                <div>
                                    <h3 class="font-bold text-black mb-2 transition-all duration-300 ">Sanificazione della Richiesta</h3>
                                    <p class="text-black transition-all duration-300 ">Il middleware <code class="bg-black text-white px-2 py-1 rounded text-sm">CheckRequest</code> pulisce e sanifica tutte le variabili superglobali.</p>
                                </div>
                            </div>
                            <div class="group flex items-start bg-white rounded-xl p-6 border-l-4 border-black transition-all duration-300 ">
                                <div class="flex-shrink-0 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm mr-4 transition-all duration-300 group-hover:bg-white group-hover:text-black">4</div>
                                <div>
                                    <h3 class="font-bold text-black mb-2 transition-all duration-300 ">Autenticazione e Sicurezza</h3>
                                    <p class="text-black transition-all duration-300 ">Middleware come <code class="bg-black text-white px-2 py-1 rounded text-sm">AuthMiddleware</code> e <code class="bg-black text-white px-2 py-1 rounded text-sm">VerifyCsrfToken</code> gestiscono la sicurezza.</p>
                                </div>
                            </div>
                            <div class="group flex items-start bg-white rounded-xl p-6 border-l-4 border-black transition-all duration-300 ">
                                <div class="flex-shrink-0 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm mr-4 transition-all duration-300 group-hover:bg-white group-hover:text-black">5</div>
                                <div>
                                    <h3 class="font-bold text-black mb-2 transition-all duration-300 ">Routing</h3>
                                    <p class="text-black transition-all duration-300 ">Il componente Router riceve l'URI, lo analizza e lo confronta con le rotte definite.</p>
                                </div>
                            </div>
                            <div class="group flex items-start bg-white rounded-xl p-6 border-l-4 border-black transition-all duration-300 ">
                                <div class="flex-shrink-0 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm mr-4 transition-all duration-300 group-hover:bg-white group-hover:text-black">6</div>
                                <div>
                                    <h3 class="font-bold text-black mb-2 transition-all duration-300 ">Esecuzione del Controller</h3>
                                    <p class="text-black transition-all duration-300 ">Il router invoca il metodo appropriato nel controller specificato con i parametri dall'URL.</p>
                                </div>
                            </div>
                            <div class="group flex items-start bg-white rounded-xl p-6 border-l-4 border-black transition-all duration-300 ">
                                <div class="flex-shrink-0 w-8 h-8 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm mr-4 transition-all duration-300 group-hover:bg-white group-hover:text-black">7</div>
                                <div>
                                    <h3 class="font-bold text-black mb-2 transition-all duration-300 ">Risposta</h3>
                                    <p class="text-black transition-all duration-300 ">Il controller elabora la logica di business e restituisce una vista o una risposta JSON.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="router" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Il Router
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Il router è responsabile del mapping degli URL alle azioni dei controller. Le rotte sono definite in <code class="bg-black text-white px-3 py-1 rounded font-mono">app/Routes.php</code> e supportano vari metodi HTTP.</p>
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-4 text-black">Sintassi delle Rotte</h3>
                            <p class="text-gray-800 mb-4">Il metodo principale è <code class="bg-black text-white px-2 py-1 rounded text-sm">Router::method('/percorso', [Controller::class, 'metodo'])</code></p>
                        </div>
                        <div class="bg-black text-white rounded-2xl p-6 shadow-inner">
                            <pre><code class="language-php">use App\Core\Router;
use App\Controller\SiteController;
use App\Controller\S3Controller;

// Rotta per la home page
Router::get('/', [SiteController::class, 'index']);

// Rotta per il login
Router::get('/login', [AuthController::class, 'showLoginForm']);
Router::post('/login', [AuthController::class, 'handleLogin']);

// Rotta con parametro dinamico
Router::get('/products/{id}', [ProductController::class, 'show']);

// Gruppo di rotte con middleware
Router::group(['prefix' => '/hub', 'middleware' => ['HubAuthMiddleware']], function () {
    Router::get('/', [HubController::class, 'index']);
    Router::post('/s3/upload', [S3Controller::class, 'upload']);
});</code></pre>
                        </div>
                    </div>
                </section>
                <section id="middleware" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Middleware
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-8">I middleware agiscono come "filtri" per le richieste HTTP, eseguiti in sequenza prima che la richiesta raggiunga il controller.</p>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="group bg-white rounded-2xl p-6 border border-black transition-all duration-300  hover:shadow-lg">
                                <h3 class="text-xl font-bold text-black mb-3 flex items-center transition-all duration-300 ">
                                    <i class="fa-solid fa-shield-halved mr-2"></i> CheckRequest
                                </h3>
                                <p class="text-black text-sm transition-all duration-300 ">Sanifica i dati di input, rimuovendo potenziali script malevoli e caratteri non desiderati.</p>
                            </div>
                            <div class="group bg-white rounded-2xl p-6 border border-black transition-all duration-300  hover:shadow-lg">
                                <h3 class="text-xl font-bold text-black mb-3 flex items-center transition-all duration-300 ">
                                    <i class="fa-solid fa-user-lock mr-2"></i> AuthMiddleware
                                </h3>
                                <p class="text-black text-sm transition-all duration-300 ">Verifica se l'utente è autenticato e reindirizza alla pagina di login se necessario.</p>
                            </div>
                            <div class="group bg-white rounded-2xl p-6 border border-black transition-all duration-300  hover:shadow-lg">
                                <h3 class="text-xl font-bold text-black mb-3 flex items-center transition-all duration-300 ">
                                    <i class="fa-solid fa-fingerprint mr-2"></i> VerifyCsrfToken
                                </h3>
                                <p class="text-black text-sm transition-all duration-300 ">Valida i token CSRF per proteggere dai Cross-Site Request Forgery.</p>
                            </div>
                            <div class="group bg-white rounded-2xl p-6 border border-black transition-all duration-300  hover:shadow-lg">
                                <h3 class="text-xl font-bold text-black mb-3 flex items-center transition-all duration-300 ">
                                    <i class="fa-solid fa-clipboard-list mr-2"></i> LoggerMiddleware
                                </h3>
                                <p class="text-black text-sm transition-all duration-300 ">Registra i dettagli di ogni richiesta in una tabella di log del database.</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="controller" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Controller
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">I controller contengono la logica di business principale per una specifica risorsa. Non devono contenere logica di routing o di database diretta.</p>
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-4 text-black">Esempio di un Controller</h3>
                        </div>
                        <div class="bg-black text-white rounded-2xl p-6 shadow-inner">
                            <pre><code class="language-php">use App\Core\Database;
use App\Core\Router;
use App\Core\Notify;

class SiteController
{
    /**
     * Mostra la home page del sito.
     */
    public function index()
    {
        // Ottiene un'istanza del database
        $db = new Database();
        // Recupera gli ultimi 5 articoli
        $posts = $db->select('posts', [], 'id, title, created_at', 'created_at DESC', 5);

        // Se non ci sono post, crea una notifica
        if (empty($posts)) {
            Notify::add('info', 'Nessun articolo trovato!');
        }

        // Renderizza la vista
        Router::view('index', ['posts' => $posts]);
    }
}</code></pre>
                        </div>
                    </div>
                </section>
                <section id="database" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Interazione con il Database
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-8">La classe <code class="bg-black text-white px-3 py-1 rounded font-mono">Database</code> fornisce un'interfaccia sicura per interagire con MySQL utilizzando prepared statements.</p>
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-4 text-black">Esempi CRUD (Create, Read, Update, Delete)</h3>
                        </div>
                        <div class="bg-black text-white rounded-2xl p-6 shadow-inner mb-8">
                            <pre><code class="language-php">use App\Core\Database;

$db = new Database();

// CREATE - Inserimento di un nuovo utente
$userData = [
    'first_name' => 'Mario',
    'last_name' => 'Rossi',
    'email' => 'mario.rossi@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT)
];
$newUserId = $db->insert('users', $userData);

// READ - Recupero di un utente
$user = $db->select('users', ['email' => 'mario.rossi@example.com']);

// UPDATE - Aggiornamento
$db->update('users', ['last_name' => 'Bianchi'], ['email' => 'mario.rossi@example.com']);

// DELETE - Cancellazione
$db->delete('users', ['email' => 'mario.rossi@example.com']);</code></pre>
                        </div>
                        <div class="bg-white rounded-2xl p-6 border border-black">
                            <h3 class="text-2xl font-bold mb-4 text-black flex items-center">
                                <i class="fa-solid fa-arrows-turn-to-dots mr-3"></i>
                                Transazioni
                            </h3>
                            <p class="text-gray-800 mb-4">Per operazioni che richiedono l'esecuzione di più query in modo atomico:</p>
                            <div class="bg-black text-white rounded-xl p-4">
                                <pre><code>$db->beginTransaction();
try {
    $db->insert('orders', ['user_id' => 123, 'total' => 99.99]);
    $db->update('products', ['quantita' => 9], ['id' => 456]);
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="mailer" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Mailer
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-8">La classe <code class="bg-black text-white px-3 py-1 rounded font-mono">Mailer</code> gestisce l'invio di email tramite SMTP utilizzando template PHP.</p>
                        <div class="grid md:grid-cols-2 gap-8 mb-8">
                            <div class="bg-white rounded-2xl p-6 border border-black transition-all duration-300 hover:shadow-lg">
                                <h3 class="text-2xl font-bold mb-4 text-black flex items-center">
                                    <i class="fa-solid fa-gear mr-3"></i>
                                    Configurazione
                                </h3>
                                <p class="text-gray-800 mb-4">Le impostazioni SMTP nel file <code class="bg-black text-white px-2 py-1 rounded text-sm">.env</code>:</p>
                                <div class="bg-black text-white rounded-xl p-4">
                                    <pre><code>MAIL_HOST="smtp.mailtrap.io"
MAIL_PORT=2525
MAIL_USERNAME="tuo_username"
MAIL_PASSWORD="tua_password"
MAIL_FROM_NAME="Otix App"
MAIL_FROM_EMAIL="noreply@otix.com"</code></pre>
                                </div>
                            </div>
                            <div class="bg-white rounded-2xl p-6 border border-black transition-all duration-300 hover:shadow-lg">
                                <h3 class="text-2xl font-bold mb-4 text-black flex items-center">
                                    <i class="fa-solid fa-envelope mr-3"></i>
                                    Invio Email
                                </h3>
                                <div class="bg-black text-white rounded-xl p-4">
                                    <pre><code>$mailer = new Mailer();
$templateData = [
    'userName' => 'Mario',
    'link' => 'https://example.com'
];

$mailer->send(
    'mario@example.com',
    'Conferma registrazione',
    'registration_confirmation',
    $templateData
);</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="s3manager" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            S3Manager
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-8"><code class="bg-black text-white px-3 py-1 rounded font-mono">S3Manager</code> offre un'interfaccia per gestire file su servizi di storage compatibili con S3 (AWS S3, DigitalOcean Spaces, MinIO).</p>
                        <div class="bg-black text-white rounded-2xl p-6 shadow-inner">
                            <pre><code class="language-php">use App\Core\S3Manager;

$s3 = new S3Manager();

// Caricamento di un file
$filePath = '/percorso/locale/file.txt';
$s3Key = 'cartella/documento.txt';
if ($s3->putFile($s3Key, $filePath)) {
    echo "File caricato con successo!";
}

// Generazione di un URL pubblico
$publicUrl = $s3->getPublicUrl($s3Key);

// Eliminazione del file
$s3->deleteFile($s3Key);</code></pre>
                        </div>
                    </div>
                </section>
                <section id="session-and-notify" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Sessione e Notifiche
                        </h2>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="bg-white rounded-2xl p-6 border border-black transition-all duration-300  hover:shadow-lg">
                                <h3 class="text-2xl font-bold mb-4 text-black flex items-center transition-all duration-300 ">
                                    <i class="fa-solid fa-lock mr-3"></i>
                                    Session
                                </h3>
                                <p class="text-gray-800 mb-4 transition-all duration-300 ">Gestione sicura della sessione PHP:</p>
                                <div class="bg-black text-white rounded-xl p-4">
                                    <pre><code>Session::set('user_id', 123);

// Controlla esistenza
if (Session::has('user_id')) {
    $userId = Session::get('user_id');
}

// Rimuove una chiave
Session::delete('user_id');</code></pre>
                                </div>
                            </div>
                            <div class="bg-white rounded-2xl p-6 border border-black transition-all duration-300  hover:shadow-lg">
                                <h3 class="text-2xl font-bold mb-4 text-black flex items-center transition-all duration-300 ">
                                    <i class="fa-solid fa-bell mr-3"></i>
                                    Notify
                                </h3>
                                <p class="text-gray-800 mb-4 transition-all duration-300 ">Messaggi flash per l'utente:</p>
                                <div class="bg-black text-white rounded-xl p-4">
                                    <pre><code>// Aggiungi messaggi
Notify::add('success', 'Operazione riuscita!');
Notify::add('error', 'Errore durante il salvataggio');

// Visualizza nella vista
&lt;?php foreach (Notify::get() as $message): ?&gt;
    &lt;div class="alert alert-&lt;?= $message['type'] ?&gt;"&gt;
        &lt;?= $message['text'] ?&gt;
    &lt;/div&gt;
&lt;?php endforeach; ?&gt;</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="cli" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-6 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            Tool a riga di comando (<code class="bg-black text-white px-2 rounded font-mono text-xl">php do</code>)
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Il file <code class="bg-black text-white px-2 rounded font-mono">do</code> nella radice del progetto è un potente strumento per automatizzare attività comuni come la gestione delle migrazioni del database, la pulizia della cache e la creazione di utenti. È la spina dorsale del flusso di lavoro di sviluppo.</p>
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-4 text-black">Comandi più comuni:</h3>
                        </div>
                        <div class="bg-black text-white rounded-2xl p-6 shadow-inner">
                            <pre><code class="language-plaintext"># Crea un nuovo file di migrazione con timestamp
php do make:migration CreateProductsTable

# Esegue tutte le migrazioni in sospeso
php do migrate

# Annulla l'ultima migrazione
php do rollback

# Mostra lo stato di tutte le migrazioni
php do status

# Pulisce la cache delle viste compilate
php do cache:clear

# Crea un nuovo utente amministrativo per l'hub
php do make:hub:user "Amministratore" "admin@example.com" "passwordSicura"</code></pre>
                        </div>
                    </div>
                </section>
                
                <section id="controller" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 Controller
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella contiene la logica di business principale per le diverse funzionalità dell'applicazione. Ogni file, come suggerisce il suo nome, gestisce le richieste per un'area specifica. I controller non dovrebbero contenere la logica di routing o di accesso diretto al database, ma piuttosto coordinare i vari componenti (Core, Middleware) per elaborare le richieste e generare una risposta.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">AuthController.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce le operazioni di autenticazione, come il login, il logout, la registrazione e il recupero della password. Riceve le richieste POST dai form, interagisce con il database per verificare le credenziali o creare nuovi utenti, e gestisce le sessioni.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">ErrorController.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce e mostra le pagine di errore (es. 404, 500). Viene chiamato in caso di errore di routing o di applicazione per visualizzare le viste di errore appropriate.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">GetFileUserController.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce il recupero di file privati e riservati all'utente. A seguito di un controllo di autenticazione e autorizzazione, recupera un file dallo storage dell'utente e lo invia al browser.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">GetPublicFileController.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce il recupero di file pubblici, accessibili senza autenticazione, solitamente utilizzati per le risorse statiche.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">HubController.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce la logica del pannello di amministrazione (`Hub`). Controlla l'accesso tramite `HubAuthMiddleware`, gestisce la dashboard, la visualizzazione dei dati e le interazioni specifiche per gli amministratori.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">S3Controller.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce le operazioni di interazione con Amazon S3 o servizi di storage compatibili. Utilizza la classe `S3Manager.php` per caricare, eliminare o recuperare file.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">SiteController.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce le pagine pubbliche del sito. Carica la home page e altre pagine pubbliche, interagendo con altri componenti solo se necessario.</p>
                        </div>
                    </div>
                </section>
                
                <section id="core" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 Core
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella contiene i componenti centrali del framework, ciascuno con una responsabilità specifica.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">Database.php</h3>
                            <p class="text-gray-800 mb-4">Fornisce un'interfaccia sicura e semplificata per interagire con il database MySQL. Utilizza PDO per eseguire query con prepared statements, prevenendo SQL injection. Include metodi per le operazioni CRUD (Create, Read, Update, Delete) e per la gestione delle transazioni.</p>
                            <h4 class="text-lg font-semibold text-black mb-2">Esempio:</h4>
                            <div class="bg-black text-white rounded-xl p-4">
                                <pre><code class="language-php">use App\Core\Database;

$db = new Database();

// Inserisce un nuovo record
$userId = $db->insert('users', ['name' => 'John', 'email' => 'john@example.com']);
// Recupera un utente
$user = $db->select('users', ['id' => $userId]);</code></pre>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">HubDatabase.php</h3>
                            <p class="text-gray-800 mb-4">Fornisce un'istanza del database con credenziali specifiche per l'hub amministrativo. Si connette a un database o a un utente di database dedicato, separando i privilegi dell'hub da quelli dell'applicazione principale per motivi di sicurezza.</p>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">Logger.php</h3>
                            <p class="text-gray-800 mb-4">Registra gli eventi e le richieste dell'applicazione, scrivendo informazioni su file di log o nel database. È utile per il debugging e il monitoraggio.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">Mailer.php</h3>
                            <p class="text-gray-800 mb-4">Invia email tramite un server SMTP. Si basa sulla libreria PHPMailer per connettersi e inviare email formattate utilizzando i template nella cartella `resources/views/emails`.</p>
                            <h4 class="text-lg font-semibold text-black mb-2">Esempio:</h4>
                            <div class="bg-black text-white rounded-xl p-4">
                                <pre><code class="language-php">use App\Core\Mailer;

$mailer = new Mailer();
$templateData = ['userName' => 'Mario'];
$mailer->send('mario@example.com', 'Benvenuto', 'registration_confirmation', $templateData);</code></pre>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">Notify.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce i messaggi di notifica "flash" che appaiono dopo un'azione (es. "Operazione riuscita!"). Archivia i messaggi in sessione e li rende disponibili alla vista successiva, per poi cancellarli automaticamente.</p>
                            <h4 class="text-lg font-semibold text-black mb-2">Esempio:</h4>
                            <div class="bg-black text-white rounded-xl p-4">
                                <pre><code class="language-php">use App\Core\Notify;

// Aggiunge un messaggio
Notify::add('success', 'Il profilo è stato aggiornato!');

// Nella vista
$notifications = Notify::get();</code></pre>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">Router.php</h3>
                            <p class="text-gray-800 mb-4">Mappa le URL alle azioni dei controller. Analizza l'URI della richiesta e cerca una corrispondenza nelle rotte definite in `app/Routes.php`. Quando trova una corrispondenza, invoca il metodo appropriato nel controller specificato. Supporta anche l'iniezione dei parametri dinamici dall'URL.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">S3Manager.php</h3>
                            <p class="text-gray-800 mb-4">Fornisce un'interfaccia per interagire con i servizi di storage S3. Incapsula la logica per le operazioni S3 (caricamento, download, eliminazione, ecc.).</p>
                            <h4 class="text-lg font-semibold text-black mb-2">Esempio:</h4>
                            <div class="bg-black text-white rounded-xl p-4">
                                <pre><code class="language-php">use App\Core\S3Manager;

$s3 = new S3Manager();
$s3->putFile('documenti/report.pdf', '/path/al/file/locale/report.pdf');</code></pre>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">Session.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce in modo sicuro le variabili di sessione. Fornisce un'astrazione sicura attorno alla superglobale `$_SESSION`, prevenendo attacchi come il "session fixation".</p>
                            <h4 class="text-lg font-semibold text-black mb-2">Esempio:</h4>
                            <div class="bg-black text-white rounded-xl p-4">
                                <pre><code class="language-php">use App\Core\Session;

Session::set('user_id', 123);
$id = Session::get('user_id');</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="middleware" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 Middleware
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">I middleware sono filtri che vengono eseguiti prima che una richiesta raggiunga il controller. La loro esecuzione è definita in `config/MiddlewareOrder.php`.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">AuthMiddleware.php</h3>
                            <p class="text-gray-800 mb-4">Verifica se l'utente è autenticato controllando la sessione per un ID utente valido. Se non è loggato, lo reindirizza alla pagina di login.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">CheckRequest.php</h3>
                            <p class="text-gray-800 mb-4">Sanifica e valida i dati delle variabili superglobali (`$_GET`, `$_POST`, `$_COOKIE`), pulendo l'input per prevenire attacchi XSS e CSRF.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">HubAuthMiddleware.php</h3>
                            <p class="text-gray-800 mb-4">Simile ad `AuthMiddleware`, ma specifico per l'hub amministrativo. Verifica che l'utente non solo sia autenticato, ma che abbia anche i permessi di amministratore per accedere al pannello.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">LoggerMiddleware.php</h3>
                            <p class="text-gray-800 mb-4">Registra i dettagli di ogni richiesta HTTP, catturando l'URL, l'indirizzo IP e altre informazioni prima che venga elaborata, salvandole nel sistema di log.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">SetDomain.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce il multi-dominio caricando dinamicamente il file di configurazione (`config/Domains.php`) in base al dominio della richiesta.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">SetLang.php</h3>
                            <p class="text-gray-800 mb-4">Gestisce la lingua dell'applicazione in base alle preferenze dell'utente, impostando la lingua della sessione e dell'applicazione.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">VerifyCsrfToken.php</h3>
                            <p class="text-gray-800 mb-4">Protegge dai Cross-Site Request Forgery (CSRF). Genera un token univoco per ogni sessione e lo verifica per ogni richiesta POST, garantendo che provenga dall'utente.</p>
                        </div>
                    </div>
                </section>
                
                <section id="config" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 config
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella contiene i file di configurazione dell'applicazione.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">Domains.php</h3>
                            <p class="text-gray-800 mb-4">File di configurazione per la gestione dei domini, che permette di associare diverse impostazioni a URL specifici.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">MiddlewareOrder.php</h3>
                            <p class="text-gray-800 mb-4">Definisce l'ordine di esecuzione dei middleware. L'ordine è fondamentale per il corretto funzionamento dei filtri di sicurezza e di pre-elaborazione delle richieste.</p>
                        </div>
                    </div>
                </section>
                
                <section id="database" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 database
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella contiene i file per le migrazioni del database, gestiti tramite il tool `php do`.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 migrations</h3>
                            <p class="text-gray-800 mb-4">Contiene i file che definiscono la struttura delle tabelle e le modifiche al database. Ogni file rappresenta un'operazione di migrazione, gestita in modo incrementale per tenere traccia delle modifiche dello schema.</p>
                            <h4 class="text-lg font-semibold text-black mb-2">Esempio:</h4>
                            <div class="bg-black text-white rounded-xl p-4">
                                <pre><code class="language-php">use App\Core\Database;

return new class {
    public function up(): void
    {
        $db = new Database();
        $db->query("CREATE TABLE users (id INT PRIMARY KEY, name VARCHAR(255))");
    }

    public function down(): void
    {
        $db = new Database();
        $db->query("DROP TABLE users");
    }
};</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="public" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 public
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">La cartella `public` è il punto di accesso principale dell'applicazione web. Tutti i file accessibili dal browser sono qui.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">index.php</h3>
                            <p class="text-gray-800 mb-4">Il punto di ingresso unico per tutte le richieste HTTP. Tutti i reindirizzamenti e i percorsi passano attraverso questo file.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 assets</h3>
                            <p class="text-gray-800 mb-4">Contiene tutti i file pubblici come CSS, JavaScript e immagini.</p>
                        </div>
                    </div>
                </section>

                <section id="resources" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 resources
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella contiene le risorse non pubbliche, principalmente le viste (template HTML) dell'applicazione.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 views</h3>
                            <p class="text-gray-800 mb-4">Contiene tutti i template dell'applicazione, organizzati per tema (`theme001`). Le sottocartelle separano logicamente le viste per diverse aree come email, errori e hub amministrativo.</p>
                        </div>
                    </div>
                </section>
                
                <section id="storage" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 storage
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella contiene i file generati dall'applicazione, come i file di cache o di log, che non devono essere accessibili pubblicamente.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 cache/views</h3>
                            <p class="text-gray-800 mb-4">Memorizza le versioni compilate delle viste per migliorare le performance. Quando una vista viene richiesta per la prima volta, viene compilata e salvata qui per un accesso più veloce in futuro.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 logs</h3>
                            <p class="text-gray-800 mb-4">Contiene i file di log dell'applicazione, che registrano eventi e richieste per il monitoraggio e il debugging.</p>
                        </div>
                    </div>
                </section>
                
                <section id="users" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 users
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella è la base per la gestione dei file e delle risorse specifiche per ogni utente. Ogni utente ha una cartella dedicata con un ID univoco.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 USR0000001</h3>
                            <p class="text-gray-800 mb-4">Cartella di esempio per un singolo utente, che può contenere risorse come CSS, JavaScript, rotte e file di configurazione personalizzati.</p>
                        </div>
                    </div>
                </section>
                
                <section id="vendor" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            📁 vendor
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa cartella contiene le librerie di terze parti installate tramite Composer, essenziali per il funzionamento del framework.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 App/S3</h3>
                            <p class="text-gray-800 mb-4">Una libreria interna per l'interazione con i servizi di storage S3, fornendo un'astrazione sui protocolli di comunicazione.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">📁 phpmailer</h3>
                            <p class="text-gray-800 mb-4">La libreria open-source per l'invio di email, utilizzata dal componente `Mailer.php`.</p>
                        </div>
                    </div>
                </section>
                
                <section id="root" class="mb-16">
                    <div class="bg-white rounded-3xl p-8 shadow-md border border-black transition-all duration-300 hover:shadow-xl">
                        <h2 class="text-4xl font-bold mb-8 text-black flex items-center">
                            <span class="w-3 h-8 bg-black rounded-full mr-4"></span>
                            File Principali
                        </h2>
                        <p class="text-lg text-gray-800 leading-relaxed mb-6">Questa sezione descrive i file principali che si trovano nella radice del progetto.</p>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">.env</h3>
                            <p class="text-gray-800 mb-4">Il file di configurazione principale che contiene le variabili d'ambiente. Non deve mai essere committato su un repository pubblico, ma solo gestito in locale.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">.env.localhost</h3>
                            <p class="text-gray-800 mb-4">Un file di override specifico per l'ambiente di sviluppo locale. Viene caricato al posto del file `.env` quando l'applicazione viene eseguita su localhost.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">autoload.php</h3>
                            <p class="text-gray-800 mb-4">Il file di autoload di Composer. Viene incluso dal punto di ingresso (`public/index.php`) e si occupa di caricare automaticamente le classi necessarie, senza dover usare `require` o `include`.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">do</h3>
                            <p class="text-gray-800 mb-4">Uno script a riga di comando per le operazioni di sviluppo e manutenzione. Automatizza compiti come le migrazioni, la pulizia della cache e la creazione di utenti.</p>
                        </div>
                        
                        <div class="bg-white rounded-2xl p-6 border border-black mb-6">
                            <h3 class="text-2xl font-bold mb-2 text-black">versioning.txt</h3>
                            <p class="text-gray-800 mb-4">File di testo che contiene le informazioni sulla versione corrente dell'applicazione, utile per il monitoraggio e la compatibilità.</p>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
<?php partial('footer');?>