<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otix Core - Documentazione</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #5a67d8;
            --secondary: #764ba2;
            --accent: #f093fb;
            --bg-dark: #0f0f23;
            --bg-darker: #0a0a1a;
            --text-light: #e2e8f0;
            --text-muted: #a0aec0;
            --border: #2d3748;
            --code-bg: #1a202c;
            --success: #48bb78;
            --warning: #ed8936;
            --info: #4299e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-darker) 100%);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.1;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            border-radius: 50%;
            animation: float 20s infinite linear;
            top: 20%;
            left: 10%;
        }

        .bg-animation::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            border-radius: 50%;
            animation: float 25s infinite linear reverse;
            top: 60%;
            right: 10%;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-30px) rotate(120deg); }
            66% { transform: translateY(30px) rotate(240deg); }
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-light);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Container */
        .container {
            display: flex;
            min-height: 100vh;
            padding-top: 80px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(26, 32, 44, 0.8);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border);
            position: fixed;
            height: calc(100vh - 80px);
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 900;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 2rem 1.5rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }

        .nav-list {
            padding: 0;
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-light);
            background: rgba(102, 126, 234, 0.1);
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 16px;
            opacity: 0.7;
        }

        /* Main content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .main-content.full-width {
            margin-left: 0;
        }

        /* Sections */
        .section {
            margin-bottom: 4rem;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .section:nth-child(1) { animation-delay: 0.1s; }
        .section:nth-child(2) { animation-delay: 0.2s; }
        .section:nth-child(3) { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section h1 {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            position: relative;
        }

        .section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border);
            position: relative;
        }

        .section h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
        }

        .lead {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .section p {
            margin-bottom: 1.5rem;
            color: var(--text-muted);
        }

        /* Cards */
        .card {
            background: rgba(26, 32, 44, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.1);
        }

        /* Lists */
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .feature-item {
            background: rgba(26, 32, 44, 0.6);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
        }

        .feature-item:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }

        .feature-item strong {
            color: var(--primary);
            display: block;
            margin-bottom: 0.5rem;
        }

        /* Code blocks */
        .code-block {
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            position: relative;
            overflow-x: auto;
        }

        .code-block::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, var(--success), var(--info));
        }

        .code-block pre {
            margin: 0;
            font-family: 'Fira Code', 'JetBrains Mono', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
            color: #e2e8f0;
        }

        .code-block code {
            font-family: inherit;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        /* Status badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        .badge-success {
            background: rgba(72, 187, 120, 0.2);
            color: var(--success);
            border: 1px solid rgba(72, 187, 120, 0.3);
        }

        .badge-warning {
            background: rgba(237, 137, 54, 0.2);
            color: var(--warning);
            border: 1px solid rgba(237, 137, 54, 0.3);
        }

        .badge-info {
            background: rgba(66, 153, 225, 0.2);
            color: var(--info);
            border: 1px solid rgba(66, 153, 225, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                padding: 0 1rem;
            }

            .menu-toggle {
                display: block;
            }

            .sidebar {
                width: 100%;
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .section h1 {
                font-size: 2rem;
            }

            .section h2 {
                font-size: 1.5rem;
            }

            .feature-list {
                grid-template-columns: 1fr;
            }

            .code-block {
                margin: 1rem -1rem;
                border-radius: 0;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-darker);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-cube"></i>
                Otix Core
            </div>
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <div class="container">
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">
                    <i class="fas fa-book"></i>
                    Documentazione
                </div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a class="nav-link active" href="#introduzione">
                            <i class="fas fa-home"></i>
                            Introduzione
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#requisiti">
                            <i class="fas fa-server"></i>
                            Requisiti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#installazione">
                            <i class="fas fa-download"></i>
                            Installazione
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#struttura">
                            <i class="fas fa-folder-tree"></i>
                            Struttura Cartelle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#configurazione">
                            <i class="fas fa-cog"></i>
                            Configurazione
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#routing">
                            <i class="fas fa-route"></i>
                            Routing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#controller">
                            <i class="fas fa-gamepad"></i>
                            Controller
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#viste">
                            <i class="fas fa-eye"></i>
                            Viste e Frontend
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#middleware">
                            <i class="fas fa-shield-alt"></i>
                            Middleware
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#database">
                            <i class="fas fa-database"></i>
                            Database
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="main-content" id="mainContent">
            <section id="introduzione" class="section">
                <h1>Otix Core Framework</h1>
                <p class="lead">Un framework PHP leggero, veloce e modulare progettato per essere flessibile e potente.</p>
                
                <div class="card">
                    <p>L'architettura si basa su un approccio "front controller", dove ogni richiesta passa attraverso <code>public/index.php</code>. Il framework gestisce domini multipli, temi e configurazioni utente in modo isolato, rendendolo perfetto per progetti complessi e scalabili.</p>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                        <span class="badge badge-success">
                            <i class="fas fa-check"></i> &nbsp; Leggero
                        </span>
                        <span class="badge badge-info">
                            <i class="fas fa-bolt"></i> &nbsp; Veloce
                        </span>
                        <span class="badge badge-warning">
                            <i class="fas fa-cubes"></i> &nbsp; Modulare
                        </span>
                    </div>
                </div>
            </section>

            <section id="requisiti" class="section">
                <h2>Requisiti di Sistema</h2>
                <p>Per eseguire correttamente il framework, assicurati che il tuo ambiente di sviluppo soddisfi i seguenti requisiti:</p>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <strong><i class="fab fa-php"></i> PHP 8.0+</strong>
                        Versione minima richiesta per utilizzare le funzionalità moderne del linguaggio.
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-database"></i> PDO MySQL</strong>
                        Estensione PHP PDO con driver MySQL (pdo_mysql) per la gestione del database.
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-lock"></i> OpenSSL</strong>
                        Estensione PHP OpenSSL per funzionalità di crittografia e sicurezza.
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-server"></i> Web Server</strong>
                        Server web con supporto per mod_rewrite o equivalente per URL puliti.
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-box"></i> Composer</strong>
                        Consigliato per la gestione delle dipendenze future del progetto.
                    </div>
                </div>
            </section>
            
            <section id="installazione" class="section">
                <h2>Installazione</h2>
                <p>Segui questi passaggi per configurare il tuo progetto con Otix Core:</p>
                
                <div class="card">
                    <h4><i class="fas fa-download"></i> 1. Clona il Repository</h4>
                    <p>Scarica il codice sorgente e posizionati nella cartella del progetto.</p>
                    
                    <div class="code-block">
                        <pre><code>git clone https://github.com/your-repo/otixcore.git
cd otixcore</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h4><i class="fas fa-server"></i> 2. Configura il Web Server</h4>
                    <p>Assicurati che la "document root" del tuo virtual host punti alla cartella <code>/public</code>. Questo è fondamentale per la sicurezza.</p>
                </div>

                <div class="card">
                    <h4><i class="fas fa-cog"></i> 3. Configura l'Ambiente</h4>
                    <p>Crea il file <code>.env</code> copiando il template e personalizza le variabili d'ambiente:</p>
                    
                    <div class="code-block">
                        <pre><code>cp .env.localhost .env
# Modifica le credenziali del database e altre configurazioni</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h4><i class="fas fa-database"></i> 4. Esegui le Migrazioni</h4>
                    <p>Crea le tabelle del database usando il tool CLI integrato:</p>
                    
                    <div class="code-block">
                        <pre><code>php do migrate</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h4><i class="fas fa-shield-alt"></i> 5. Verifica i Permessi</h4>
                    <p>Assicurati che le cartelle <code>storage/cache/views</code> siano scrivibili dal server web.</p>
                </div>
            </section>

            <section id="struttura" class="section">
                <h2>Struttura delle Cartelle</h2>
                <p>Il framework segue una struttura logica per separare le diverse componenti dell'applicazione:</p>
                
                <div class="code-block">
                    <pre><code>otixcore/
├── app/                    # Cuore dell'applicazione
│   ├── Core/              # Classi fondamentali (Router, Database, Mailer)
│   ├── Controller/        # Logica applicativa
│   └── Middleware/        # Filtri per le richieste
├── database/              # Migrazioni del database
├── public/                # Document root (index.php, asset pubblici)
├── resources/             # Viste HTML, file di lingua
├── storage/               # File generati (cache, log)
├── users/                 # Configurazioni specifiche per dominio/utente
└── sources/               # Configurazioni globali (domains.php)</code></pre>
                </div>
            </section>
            
            <section id="configurazione" class="section">
                <h2>Configurazione</h2>
                <p>La configurazione è gestita a più livelli per massima flessibilità:</p>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <strong><i class="fas fa-file-code"></i> .env</strong>
                        File principale per le variabili d'ambiente globali (database, mail, sessione).
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-globe"></i> sources/domains.php</strong>
                        Mappa i domini a configurazioni specifiche (tema, codice utente, env specifico).
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-user-cog"></i> users/{USR}/config.php</strong>
                        Configurazioni specifiche dell'utente/dominio (lingue, visibilità sito).
                    </div>
                </div>
            </section>
            
            <section id="routing" class="section">
                <h2>Routing</h2>
                <p>Le rotte sono definite nel file <code>app/Routes.php</code> e associano URI a metodi di controller:</p>
                
                <div class="code-block">
                    <pre><code>// app/Routes.php
use App\Core\Router;
use App\Controller\SiteController;
use App\Controller\AuthController;

// Rotta base
Router::get('/', [SiteController::class, 'index']);

// Rotta POST per login
Router::post('/login', [AuthController::class, 'login']);

// Rotta con parametro dinamico
Router::get('/user/{id}', [UserController::class, 'show']);</code></pre>
                </div>
                
                <div class="card">
                    <p>La classe <code>App\Core\Router</code> analizza l'URI della richiesta e invoca il metodo corretto, passando eventuali parametri dinamici estratti dall'URL.</p>
                </div>
            </section>

            <section id="controller" class="section">
                <h2>Controller</h2>
                <p>I controller contengono la logica applicativa e si trovano in <code>app/Controller</code>:</p>
                
                <div class="code-block">
                    <pre><code>// app/Controller/SiteController.php
namespace App\Controller;

class SiteController
{
    public function index(): void
    {
        // Recupera e elabora i dati
        $dati = [
            'titolo' => 'Benvenuto in Otix Core!',
            'versione' => '1.0.0'
        ];
        
        // Renderizza la vista
        render('index', $dati);
    }
}</code></pre>
                </div>
            </section>

            <section id="viste" class="section">
                <h2>Viste e Frontend</h2>
                <p>Il sistema di viste supporta temi, partials e asset statici per massima flessibilità:</p>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <strong><i class="fas fa-palette"></i> Sistema Temi</strong>
                        Le viste vengono cercate prima nel tema attivo, poi nella cartella base per massima personalizzazione.
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-puzzle-piece"></i> Partials Riutilizzabili</strong>
                        Usa <code>partial()</code>, <code>partialAdmin()</code>, <code>partialHub()</code> per componenti condivisi.
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-images"></i> Asset Globali (/public)</strong>
                        Asset comuni a tutti i temi gestiti da GetPublicFileController.php.
                    </div>
                    <div class="feature-item">
                        <strong><i class="fas fa-user-tag"></i> Asset Specifici (/static)</strong>
                        Asset per utente/tema specifico gestiti da GetFileUserController.php.
                    </div>
                </div>
            </section>

            <section id="middleware" class="section">
                <h2>Middleware</h2>
                <p>I middleware sono filtri eseguiti in sequenza per ogni richiesta. Ordine di esecuzione:</p>
                
                <div class="card">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; align-items: center; padding: 1rem; background: rgba(72, 187, 120, 0.1); border-radius: 8px; border-left: 4px solid var(--success);">
                            <div style="background: var(--success); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.875rem; font-weight: bold;">1</div>
                            <div>
                                <strong>CheckRequest</strong><br>
                                <small>Sanifica le variabili superglobali ($_GET, $_POST, etc.)</small>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; padding: 1rem; background: rgba(66, 153, 225, 0.1); border-radius: 8px; border-left: 4px solid var(--info);">
                            <div style="background: var(--info); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.875rem; font-weight: bold;">2</div>
                            <div>
                                <strong>SetDomain</strong><br>
                                <small>Identifica il dominio, carica .env specifico e definisce costanti</small>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; padding: 1rem; background: rgba(237, 137, 54, 0.1); border-radius: 8px; border-left: 4px solid var(--warning);">
                            <div style="background: var(--warning); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.875rem; font-weight: bold;">3</div>
                            <div>
                                <strong>SetLang</strong><br>
                                <small>Gestisce la lingua dell'utente, imposta cookie e reindirizza</small>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; padding: 1rem; background: rgba(102, 126, 234, 0.1); border-radius: 8px; border-left: 4px solid var(--primary);">
                            <div style="background: var(--primary); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.875rem; font-weight: bold;">4</div>
                            <div>
                                <strong>HubAuthMiddleware</strong><br>
                                <small>Protegge le rotte /hub richiedendo login specifico</small>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; padding: 1rem; background: rgba(118, 75, 162, 0.1); border-radius: 8px; border-left: 4px solid var(--secondary);">
                            <div style="background: var(--secondary); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.875rem; font-weight: bold;">5</div>
                            <div>
                                <strong>AuthMiddleware</strong><br>
                                <small>Gestisce autenticazione per aree riservate (/admin)</small>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; padding: 1rem; background: rgba(240, 147, 251, 0.1); border-radius: 8px; border-left: 4px solid var(--accent);">
                            <div style="background: var(--accent); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.875rem; font-weight: bold;">6</div>
                            <div>
                                <strong>VerifyCsrfToken</strong><br>
                                <small>Protegge dalle richieste CSRF per metodi non-safe (POST, PUT)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <section id="database" class="section">
                <h2>Database e Migrazioni</h2>
                <p>La classe <code>App\Core\Database</code> offre un'interfaccia sicura per interagire con il database tramite PDO:</p>
                
                <div class="card">
                    <h4><i class="fas fa-database"></i> Funzionalità Database</h4>
                    <p>Supporta operazioni complete: SELECT, INSERT, UPDATE, DELETE e gestione delle transazioni con protezione automatica contro SQL injection.</p>
                </div>
                
                <div class="card">
                    <h4><i class="fas fa-tools"></i> Sistema di Migrazioni</h4>
                    <p>Le migrazioni sono gestite tramite lo script CLI <code>do</code> per mantenere la struttura del database allineata:</p>
                    
                    <div class="code-block">
                        <pre><code># Creare una nuova migrazione
php do make:migration add_price_to_products_table

# Eseguire tutte le migrazioni pendenti
php do migrate

# Annullare l'ultimo gruppo di migrazioni
php do rollback

# Controllare lo stato delle migrazioni
php do status</code></pre>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                    <a href="#" class="btn">
                        <i class="fas fa-rocket"></i>
                        Inizia Subito
                    </a>
                    <a href="#" class="btn" style="background: linear-gradient(135deg, var(--secondary), var(--accent));">
                        <i class="fab fa-github"></i>
                        Vedi su GitHub
                    </a>
                </div>
            </section>

        </main>
    </div>

    <script>
        // Toggle sidebar for mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('show');
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Update active link
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Close sidebar on mobile after click
                    if (window.innerWidth <= 768) {
                        document.getElementById('sidebar').classList.remove('show');
                    }
                }
            });
        });

        // Highlight current section while scrolling
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.section');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let currentSection = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                const sectionHeight = section.offsetHeight;
                if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                    currentSection = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + currentSection) {
                    link.classList.add('active');
                }
            });
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });

        // Add copy functionality to code blocks
        document.querySelectorAll('.code-block').forEach(block => {
            const button = document.createElement('button');
            button.innerHTML = '<i class="fas fa-copy"></i>';
            button.style.cssText = `
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: rgba(102, 126, 234, 0.8);
                border: none;
                color: white;
                padding: 0.5rem;
                border-radius: 6px;
                cursor: pointer;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;
            
            block.style.position = 'relative';
            block.appendChild(button);
            
            block.addEventListener('mouseenter', () => button.style.opacity = '1');
            block.addEventListener('mouseleave', () => button.style.opacity = '0');
            
            button.addEventListener('click', () => {
                const code = block.querySelector('code').textContent;
                navigator.clipboard.writeText(code).then(() => {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        button.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>