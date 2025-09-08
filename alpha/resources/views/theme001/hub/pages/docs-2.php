<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otix Core - Documentazione API</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #2c3e50; background-color: #ecf0f1; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { color: #2980b9; text-align: center; margin-bottom: 20px; }
        h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 5px; margin-top: 30px; }
        h3 { color: #2c3e50; margin-top: 20px; }
        p { margin-bottom: 15px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 0.9em; border: 1px solid #ddd; }
        code { font-family: "Courier New", Courier, monospace; background-color: #ecf0f1; padding: 2px 5px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th, td { border: 1px solid #bdc3c7; padding: 12px; text-align: left; }
        th { background-color: #3498db; color: #fff; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Documentazione del Framework Otix Core</h1>
        <p><strong>Otix Core</strong> è un micro-framework PHP leggero e modulare, progettato per lo sviluppo rapido di applicazioni web. Il suo obiettivo è fornire gli strumenti essenziali per la creazione di applicazioni robuste e scalabili, senza la complessità dei framework monolitici. Con una struttura basata su componenti indipendenti come Router, Database, Mailer e S3Manager, offre la flessibilità di utilizzare solo ciò di cui hai bisogno.</p>

        

        <h2>Architettura: Approccio Front Controller</h2>
        <p>Il framework si basa sul design pattern <strong>Front Controller</strong>. Tutte le richieste HTTP sono incanalate attraverso un unico punto di ingresso, il file <code>public/index.php</code>. Questo file ha la responsabilità di inizializzare l'applicazione, caricare il router e il dominio corretto, e inoltrare la richiesta al controller appropriato. Questo approccio centralizzato semplifica la gestione delle rotte, l'applicazione di middleware e la configurazione globale.</p>
        <p>Il flusso della richiesta è il seguente:
        <ol>
            <li>La richiesta arriva a <code>public/index.php</code>.</li>
            <li>Il framework carica la configurazione e i servizi (es. database, S3).</li>
            <li>Viene istanziato il <strong>Router</strong>.</li>
            <li>Il <strong>Router</strong> e i <strong>Middleware</strong> gestiscono la richiesta.</li>
            <li>La richiesta viene passata al <strong>Controller</strong> e al metodo corrispondente.</li>
            <li>Il <strong>Controller</strong> elabora la logica e restituisce una risposta (es. una vista).</li>
        </ol>
        </p>

        

        <h2>Controller</h2>
        <p>I Controller contengono la logica di business dell'applicazione. Ricevono la richiesta dal Router, interagiscono con il database o altri servizi e preparano la risposta da inviare al client. I controller del framework sono situati nella cartella <code>app/Controller</code>.</p>
        <p>I controller inclusi sono:</p>
        <table>
            <thead>
                <tr>
                    <th>Controller</th>
                    <th>Descrizione</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>AuthController</code></td>
                    <td>Gestisce le funzionalità di autenticazione: login, registrazione, reset password, ecc.</td>
                </tr>
                <tr>
                    <td><code>ErrorController</code></td>
                    <td>Definisce le pagine di errore standard, come 403 (Forbidden) e 404 (Not Found).</td>
                </tr>
                <tr>
                    <td><code>HubController</code></td>
                    <td>Contiene la logica specifica per la sezione "hub" del sito, probabilmente un'area amministrativa o riservata.</td>
                </tr>
                <tr>
                    <td><code>S3Controller</code></td>
                    <td>Gestisce le interazioni con il servizio di storage S3, come il caricamento e la visualizzazione dei file.</td>
                </tr>
                <tr>
                    <td><code>SiteController</code></td>
                    <td>Gestisce le rotte principali e le pagine pubbliche del sito (es. home page).</td>
                </tr>

            </tbody>
        </table>

        

        <h2>Middleware</h2>
        <p>I Middleware sono componenti che si inseriscono tra la richiesta e il controller. Possono ispezionare, modificare o terminare la richiesta prima che raggiunga il controller finale. Sono perfetti per la gestione di autenticazione, permessi, validazione CSRF e altro. I middleware del framework sono situati nella cartella <code>app/Middleware</code>.</p>
        <p>I middleware inclusi sono:</p>
        <table>
            <thead>
                <tr>
                    <th>Middleware</th>
                    <th>Descrizione</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>AuthMiddleware</code></td>
                    <td>Verifica se l'utente è autenticato. Se non lo è, può reindirizzarlo alla pagina di login.</td>
                </tr>
                <tr>
                    <td><code>HubAuthMiddleware</code></td>
                    <td>Middleware specifico per l'autenticazione nella sezione "hub".</td>
                </tr>
                <tr>
                    <td><code>VerifyCsrfToken</code></td>
                    <td>Controlla e valida il token CSRF (Cross-Site Request Forgery) per le richieste POST, proteggendo l'applicazione da attacchi malevoli.</td>
                </tr>
                <tr>
                    <td><code>SetLang</code></td>
                    <td>Definisce la lingua corrente dell'applicazione, utile per la localizzazione.</td>
                </tr>
                <tr>
                    <td><code>SetDomain</code></td>
                    <td>Imposta il dominio corrente, cruciale per la gestione di siti multi-dominio.</td>
                </tr>
                <tr>
                    <td><code>CheckRequest</code></td>
                    <td>Probabilmente un middleware generico per ispezionare o loggare i dettagli di ogni richiesta.</td>
                </tr>
            </tbody>
        </table>

        

        <h2>Router</h2>
        <p>Il componente <strong>Router</strong> (<code>App\Core\Router</code>) è responsabile della mappatura degli URL alle azioni del controller. Le rotte sono definite in <code>app/Routes.php</code>.</p>
        <h3>Definire le rotte:</h3>
        <p>Le rotte sono definite usando i metodi statici <code>get()</code> e <code>post()</code> della classe <code>App\Core\Router</code>. Il primo parametro è il pattern dell'URL, mentre il secondo è un array che indica il controller e il metodo da eseguire.</p>
        <pre><code>use App\Core\Router;
use App\Controller\SiteController;

// Rotta per la home page
Router::get('/', [SiteController::class, 'index']);</code></pre>
        <p>Per applicare un middleware a una rotta, si possono usare dei gruppi di rotte o specificarlo direttamente nella rotta.</p>
        <pre><code>// Esempio: applicazione di middleware
Router::get('/dashboard', [App\Controller\DashboardController::class, 'index'], [AuthMiddleware::class]);</code></pre>

        

        <h2>Database</h2>
        <p>La classe <strong>Database</strong> (<code>App\Core\Database</code>) fornisce un'astrazione sicura e facile da usare per interagire con il database, utilizzando prepared statements per prevenire SQL injection.</p>
        <h3>Esempi di utilizzo:</h3>
        <pre><code>// Seleziona un utente
$user = $db->select('users', ['id' => 1]);</code></pre>

        

        <h2>Mailer</h2>
        <p>La classe <strong>Mailer</strong> (<code>App\Core\Mailer</code>) semplifica l'invio di email transazionali e notifiche utilizzando template e la configurazione di <code>.env</code>.</p>
        <h3>Invio di un'email:</h3>
        <pre><code>$mailer->send(
    'destinatario@email.com',
    'Oggetto',
    'email_template', // Nome del template nella cartella views/emails
    ['data' => 'valore']
);</code></pre>

        

        <h2>S3Manager</h2>
        <p>Il componente <strong>S3Manager</strong> (<code>App\Core\S3Manager</code>) fornisce un'interfaccia per interagire con i servizi di storage compatibili con S3.</p>
        <h3>Esempi di utilizzo:</h3>
        <pre><code>$s3 = new S3Manager();

// Carica un file
$s3->putFile('docs/documento.pdf', '/percorso/locale/documento.pdf');</code></pre>

        

        <h2>Tool a riga di comando (<code>php do</code>)</h2>
        <p>Il tool <code>do</code> semplifica le attività di sviluppo e manutenzione come la gestione delle migrazioni del database e la creazione di utenti.</p>
        <h3>Comandi comuni:</h3>
        <pre><code>// Esegue le migrazioni del database
php do migrate

// Pulisce la cache delle viste
php do cache:clear</code></pre>
    </div>
</body>
</html>