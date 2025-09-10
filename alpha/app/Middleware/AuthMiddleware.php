<?php
use App\Core\Session;

// Estrai il percorso dall'URI per un controllo più semplice
$path = parse_url(http()['uri'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $path)));

// Recupera la lingua di fallback dalla configurazione utente
$lang_segment = config('default_lang');

// Rimuovi il segmento della lingua (es. /it, /en) se presente nell'URL
if (isset($segments[0]) && strlen($segments[0]) == 2) {
    array_shift($segments);
}
// Ricostruisci il percorso base senza lingua (es. /admin, /login, /static)
$base_path = '/' . ($segments[0] ?? '');

// Controlla se il sito è disabilitato dalla configurazione utente (USR)
if (config('is_site') === false) {
    // Se l'utente NON è loggato...
    if (!Session::has('user_id')) {
        // Definisci i percorsi sempre accessibili (login, risorse statiche e hub)
        $allowed_paths = ['/login', '/static', '/public', '/hub', '/register', '/s3', '/bucket'];

        // Se il percorso richiesto non è tra quelli consentiti,
        // reindirizzalo alla pagina di login.
        if (!in_array($base_path, $allowed_paths)) {
            header('Location: /' . $lang_segment . '/login');
            exit;
        }
    } 
    // Se l'utente È loggato...
    else {
        // ...può accedere solo ad /admin e /logout.
        // Se cerca di andare altrove, viene reindirizzato ad /admin.
        $allowed_paths_for_auth = ['/admin', '/logout', '/static', '/public'];
        if (!in_array($base_path, $allowed_paths_for_auth)) {
            header('Location: /' . $lang_segment . '/admin');
            exit;
        }
    }
} 
// Se il sito è abilitato (is_site === true), applica la regola standard
else {
    // Se l'utente cerca di accedere ad /admin ma non è loggato, reindirizzalo al login
    if ($base_path === '/admin' && !Session::has('user_id')) {
        header('Location: /' . $lang_segment . '/login');
        exit;
    }
}