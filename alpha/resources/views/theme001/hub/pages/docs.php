<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otix Core - Documentazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            z-index: 100;
            padding: 2rem 1rem;
            background-color: var(--bs-tertiary-bg);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            overflow-y: auto;
        }
        main {
            flex-grow: 1;
            padding: 2rem;
            margin-left: 280px;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                padding: 1rem;
                box-shadow: none;
            }
            main {
                margin-left: 0;
                padding: 1rem;
            }
        }
        .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            color: var(--bs-body-color);
        }
        .nav-link.active {
            font-weight: 600;
            color: var(--bs-primary);
        }
        section {
            display: none;
        }
        section.active {
            display: block;
        }
        code {
            background-color: var(--bs-secondary-bg);
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
    </style>
</head>
<body>
    <aside class="sidebar">
        <h4 class="mb-3">Otix Core</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-section-id="panoramica">Panoramica</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="architettura">Architettura</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="controller">Controller</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="middleware">Middleware</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="router">Router</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="database">Database</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="mailer">Mailer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="s3manager">S3Manager</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-section-id="cli">Tool a riga di comando (`php do`)</a>
            </li>
        </ul>
    </aside>

    <main>
        <div class="container-fluid">
            <h1 class="display-5 fw-bold">Otix Core: Documentazione Ufficiale</h1>
            <p class="fs-4">Benvenuto nella documentazione del framework Otix Core.</p>

            <hr class="my-4">

            <section id="panoramica" class="active">
                <h2 class="pb-2 border-bottom">Panoramica</h2>
                <p><strong>Otix Core</strong> è un **micro-framework PHP** leggero e modulare, progettato per lo sviluppo rapido di applicazioni web. La sua filosofia si concentra sulla **semplicità** e sull'**efficienza**, fornendo solo gli strumenti essenziali per la creazione di applicazioni robuste e scalabili, senza la complessità e il carico dei framework monolitici. Con una struttura basata su componenti indipendenti come Router, Database, Mailer e S3Manager, offre la flessibilità di utilizzare solo ciò di cui hai bisogno per il tuo progetto. È la scelta ideale per sviluppatori che cercano il controllo totale sull'architettura, la massima velocità e un codice pulito e minimale.</p>

                <h3 class="mt-4">Requisiti minimi</h3>
                <p>Per eseguire Otix Core, assicurati che il tuo ambiente di sviluppo o server soddisfi i seguenti requisiti:</p>
                <ul>
                    <li>**PHP 8.2 o superiore**: Il framework è costruito per sfruttare le ultime funzionalità e ottimizzazioni di PHP.</li>
                    <li>**Composer**: Gestore di dipendenze PHP, necessario per installare e gestire i pacchetti del framework.</li>
                    <li>**Estensioni PHP**:
                        <ul>
                            <li><code>pdo_mysql</code> (o l'estensione corrispondente al tuo database)</li>
                            <li><code>mbstring</code></li>
                            <li><code>openssl</code></li>
                        </ul>
                    </li>
                    <li>**Server web**: Apache o Nginx configurato con <code>mod_rewrite</code> (o un'alternativa equivalente per Nginx) per reindirizzare tutte le richieste a <code>public/index.php</code>.</li>
                </ul>
                <p>Una corretta configurazione del server è fondamentale per il funzionamento del Front Controller del framework.</p>

                <div class="d-flex justify-content-end mt-5">
                    <button class="btn btn-primary next-btn" data-section-id="architettura">Avanti &raquo;</button>
                </div>
            </section>

            <section id="architettura">
                <h2 class="pb-2 border-bottom">Architettura: Approccio Front Controller</h2>
                <p>Il framework si basa sul design pattern <strong>Front Controller</strong>. Tutte le richieste HTTP sono incanalate attraverso un unico punto di ingresso, il file <code>public/index.php</code>. Questo approccio centralizzato semplifica la gestione delle rotte, l'applicazione di middleware e la configurazione globale.</p>
                
                <h3 class="mt-4">Il Flusso della Richiesta</h3>
                <p>Ogni richiesta HTTP inviata alla tua applicazione segue un percorso ben definito all'interno del framework. Questo "ciclo di vita della richiesta" assicura che ogni passaggio, dalla gestione della sessione al dispatching del router, avvenga in un ordine logico e sicuro. Il processo è gestito dal file <code>public/index.php</code> ed è composto dai seguenti passaggi:</p>
                
                <ol>
                    <li><strong>Definizione delle Costanti</strong>: Viene definita la costante <code>BASE_PATH</code> che punta alla directory radice del framework.</li>
                    <li><strong>Autoloader e Sessione</strong>: Vengono caricati l'autoloader di Composer, gli helper e viene inizializzata la sessione tramite la classe <code>App\Core\Session</code>.</li>
                    <li><strong>Gestione della Richiesta</strong>: La richiesta HTTP viene inizializzata attraverso il middleware <code>CheckRequest</code>, che acquisisce i dati della richiesta (URI, metodo, cookies, ecc.).</li>
                    <li><strong>Variabili d'Ambiente</strong>: Le variabili d'ambiente dal file <code>.env</code> vengono caricate e rese disponibili.</li>
                    <li><strong>Header di Sicurezza</strong>: Vengono impostati degli header HTTP globali per migliorare la sicurezza dell'applicazione, come <code>X-Frame-Options</code> e <code>X-XSS-Protection</code>.</li>
                    <li><strong>Esecuzione dei Middleware</strong>: I middleware definiti nel file <code>config/MiddlewareOrder.php</code> vengono eseguiti sequenzialmente. Questo permette di applicare logiche pre-routing come l'autenticazione, la validazione CSRF o la gestione della lingua prima che la richiesta raggiunga il controller.</li>
                    <li><strong>Gestione della Lingua</strong>: Il framework verifica l'URI della richiesta per determinare la lingua corrente dell'utente e imposta un valore globale <code>$GLOBALS['current_lang']</code> per le successive operazioni.</li>
                    <li><strong>Dispatch del Router</strong>: Il router, dopo aver caricato le rotte da <code>app/Routes.php</code>, cerca una corrispondenza con l'URI e il metodo HTTP della richiesta. Una volta trovata una rotta, istanzia il controller appropriato e chiama il metodo specificato, passando i parametri dinamici.</li>
                    <li><strong>Invio della Risposta</strong>: Il framework invia la risposta generata dal controller. Se la risposta è un array, la formatta come JSON, altrimenti invia il contenuto HTML o testuale.</li>
                    <li><strong>Gestione delle Eccezioni</strong>: Se si verifica un errore non gestito durante il processo, viene attivata la gestione globale delle eccezioni che si occupa di visualizzare una pagina di errore.</li>
                </ol>

                <h3 class="mt-4">Esempio di Flusso di una Richiesta</h3>
                <p>Quando un utente naviga su <code>tuo-sito.com/lang/login</code>, il framework gestisce la richiesta in questo modo:</p>
                <pre><code>
// 1. La richiesta arriva a public/index.php
// 2. Vengono caricati i middleware, tra cui `AuthMiddleware` e `VerifyCsrfToken`
// 3. Il router prende la richiesta e cerca una rotta corrispondente:
//    Router::get('/login', [AuthController::class, 'showLoginForm']);
// 4. Il router invoca il metodo `showLoginForm` del `AuthController`.
// 5. Il metodo del controller restituisce la vista del form di login.
// 6. Il contenuto della vista viene inviato al browser dell'utente come risposta.
                </code></pre>
                
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="panoramica">&laquo; Indietro</button>
                    <button class="btn btn-primary next-btn" data-section-id="controller">Avanti &raquo;</button>
                </div>
            </section>

            <section id="controller">
                <h2 class="pb-2 border-bottom">Controller</h2>
                <p>I Controller contengono la logica di business dell'applicazione. Ricevono la richiesta dal Router, interagiscono con il database o altri servizi e preparano la risposta da inviare al client. I controller inclusi nel framework sono situati nella cartella <code>app/Controller</code>.</p>
                <p>I controller inclusi sono:</p>
                <ul>
                    <li><strong><code>AuthController</code></strong>: Gestisce le funzionalità di autenticazione come login, registrazione e reset password.</li>
                    <li><strong><code>ErrorController</code></strong>: Definisce le pagine di errore standard, come 403 (Forbidden) e 404 (Not Found).</li>
                    <li><strong><code>HubController</code></strong>: Contiene la logica specifica per la sezione "hub" del sito, che è un'area amministrativa o riservata.</li>
                    <li><strong><code>S3Controller</code></strong>: Gestisce le interazioni con il servizio di storage S3 per il caricamento e la visualizzazione dei file.</li>
                    <li><strong><code>SiteController</code></strong>: Gestisce le rotte principali e le pagine pubbliche del sito, come la home page.</li>
                </ul>
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="architettura">&laquo; Indietro</button>
                    <button class="btn btn-primary next-btn" data-section-id="middleware">Avanti &raquo;</button>
                </div>
            </section>

            <section id="middleware">
                <h2 class="pb-2 border-bottom">Middleware</h2>
                <p>I Middleware sono componenti che si inseriscono tra la richiesta e il controller per ispezionare, modificare o terminare la richiesta. Sono ideali per gestire l'autenticazione, i permessi e la validazione CSRF. I middleware sono situati nella cartella <code>app/Middleware</code>.</p>
                <p>I middleware inclusi sono:</p>
                <ul>
                    <li><strong><code>AuthMiddleware</code></strong>: Verifica se l'utente è autenticato e lo reindirizza al login se necessario.</li>
                    <li><strong><code>HubAuthMiddleware</code></strong>: Middleware specifico per l'autenticazione della sezione "hub".</li>
                    <li><strong><code>VerifyCsrfToken</code></strong>: Controlla e valida il token CSRF per proteggere l'applicazione dalle richieste POST malevole.</li>
                    <li><strong><code>SetLang</code></strong>: Definisce la lingua corrente dell'applicazione in base ai cookie o alla configurazione predefinita.</li>
                    <li><strong><code>SetDomain</code></strong>: Imposta il dominio corrente per la gestione di siti multi-dominio.</li>
                    <li><strong><code>CheckRequest</code></strong>: Un middleware generico che ispeziona e sanifica i dettagli della richiesta HTTP.</li>
                </ul>
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="controller">&laquo; Indietro</button>
                    <button class="btn btn-primary next-btn" data-section-id="router">Avanti &raquo;</button>
                </div>
            </section>

            <section id="router">
                <h2 class="pb-2 border-bottom">Router</h2>
                <p>Il componente **Router** (<code>App\Core\Router</code>) è responsabile della mappatura degli URL alle azioni dei controller. Le rotte sono definite nel file <code>app/Routes.php</code>.</p>
                <h3>Definire le rotte</h3>
                <p>Le rotte sono definite utilizzando i metodi statici <code>Router::get()</code> e <code>Router::post()</code>. Il primo parametro è il pattern dell'URL, mentre il secondo è un array che specifica il controller e il metodo da eseguire.</p>
                <pre><code>// Rotta per la home page
use App\Controller\SiteController;
Router::get('/', [SiteController::class, 'index']);

// Rotta con parametro dinamico
Router::get('/hub/{page}', [HubController::class, 'showPage']);</code></pre>
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="middleware">&laquo; Indietro</button>
                    <button class="btn btn-primary next-btn" data-section-id="database">Avanti &raquo;</button>
                </div>
            </section>

            <section id="database">
                <h2 class="pb-2 border-bottom">Database</h2>
                <p>La classe <strong>Database</strong> (<code>App\Core\Database</code>) fornisce un'astrazione sicura per interagire con il database, prevenendo attacchi SQL injection tramite l'uso di prepared statements.</p>
                <h3>Esempi di utilizzo</h3>
                <pre><code>use App\Core\Database;

$db = new Database();

// Inserimento di una nuova riga
$db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);

// Selezione di un utente
$user = $db->select('users', ['id' => 1]);</code></pre>
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="router">&laquo; Indietro</button>
                    <button class="btn btn-primary next-btn" data-section-id="mailer">Avanti &raquo;</button>
                </div>
            </section>

            <section id="mailer">
                <h2 class="pb-2 border-bottom">Mailer</h2>
                <p>La classe <strong>Mailer</strong> (<code>App\Core\Mailer</code>) semplifica l'invio di email transazionali e notifiche utilizzando template e le configurazioni del file <code>.env</code>.</p>
                <h3>Invio di un'email</h3>
                <pre><code>use App\Core\Mailer;

$mailer = new Mailer();

$mailer->send(
    'destinatario@email.com',
    'Oggetto della mail',
    'email_template', // Nome del file del template in `views/theme001/emails`
    ['nome' => 'Mario Rossi']
);</code></pre>
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="database">&laquo; Indietro</button>
                    <button class="btn btn-primary next-btn" data-section-id="s3manager">Avanti &raquo;</button>
                </div>
            </section>

            <section id="s3manager">
                <h2 class="pb-2 border-bottom">S3Manager</h2>
                <p>Il componente <strong>S3Manager</strong> (<code>App\Core\S3Manager</code>) fornisce un'interfaccia per interagire con i servizi di storage compatibili con S3.</p>
                <h3>Esempi di utilizzo</h3>
                <pre><code>use App\Core\S3Manager;

$s3 = new S3Manager();

// Carica un file
$s3->putFile('docs/documento.pdf', '/percorso/locale/documento.pdf');

// Legge il contenuto di un file
$fileContent = $s3->getFile('docs/documento.pdf');</code></pre>
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="mailer">&laquo; Indietro</button>
                    <button class="btn btn-primary next-btn" data-section-id="cli">Avanti &raquo;</button>
                </div>
            </section>

            <section id="cli">
                <h2 class="pb-2 border-bottom">Tool a riga di comando (`php do`)</h2>
                <p>Il tool <code>do</code> semplifica le attività di sviluppo e manutenzione, come la gestione delle migrazioni del database e la creazione di utenti.</p>
                <h3>Comandi comuni</h3>
                <pre><code>// Crea un nuovo file di migrazione
php do make:migration create_users_table

// Esegue le migrazioni pendenti
php do migrate

// Annulla l'ultimo batch di migrazioni
php do rollback

// Mostra lo stato delle migrazioni
php do status

// Pulisce la cache delle viste
php do cache:clear

// Crea un nuovo utente
php do make:user &lt;nome&gt; &lt;email&gt; &lt;password&gt;
</code></pre>
                <div class="d-flex justify-content-start mt-5">
                    <button class="btn btn-secondary prev-btn" data-section-id="s3manager">&laquo; Indietro</button>
                </div>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            const sections = document.querySelectorAll('main section');
            const nextBtns = document.querySelectorAll('.next-btn');
            const prevBtns = document.querySelectorAll('.prev-btn');

            function showSection(sectionId) {
                sections.forEach(section => {
                    if (section.id === sectionId) {
                        section.classList.add('active');
                    } else {
                        section.classList.remove('active');
                    }
                });
            }

            function updateActiveLink(sectionId) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('data-section-id') === sectionId) {
                        link.classList.add('active');
                    }
                });
            }

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionId = this.getAttribute('data-section-id');
                    showSection(sectionId);
                    updateActiveLink(sectionId);
                });
            });

            nextBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const nextSectionId = this.getAttribute('data-section-id');
                    showSection(nextSectionId);
                    updateActiveLink(nextSectionId);
                });
            });

            prevBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const prevSectionId = this.getAttribute('data-section-id');
                    showSection(prevSectionId);
                    updateActiveLink(prevSectionId);
                });
            });

            showSection('panoramica');
            updateActiveLink('panoramica');
        });
    </script>
</body>
</html>