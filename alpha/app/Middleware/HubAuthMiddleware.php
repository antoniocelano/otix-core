<?php
// Middleware per l'autenticazione della sezione Hub

$path = parse_url($http['uri'], PHP_URL_PATH);

// Questo middleware si attiva solo per le rotte che iniziano con /hub
if (strpos($path, '/hub') === 0) {
    
    $isHubLoginPage = ($path === '/hub/login');
    $isHubUserLoggedIn = isset($_SESSION['hub_user_id']);

    // Se l'utente non è loggato nell'hub e non sta cercando di accedere alla pagina di login,
    // lo reindirizzo alla pagina di login dell'hub.
    if (!$isHubUserLoggedIn && !$isHubLoginPage) {
        header('Location: /' . current_lang() . '/hub/login');
        exit;
    }

    // Se l'utente è già loggato nell'hub e cerca di visitare la pagina di login,
    // lo reindirizzo alla dashboard dell'hub.
    if ($isHubUserLoggedIn && $isHubLoginPage) {
        header('Location: /' . current_lang() . '/hub');
        exit;
    }
}
