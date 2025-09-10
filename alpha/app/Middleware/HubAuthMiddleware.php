<?php
// Middleware per l'autenticazione della sezione Hub

$path = parse_url($http['uri'], PHP_URL_PATH);

// Questo middleware si attiva solo per le rotte che iniziano con /hub o /s3
if (strpos($path, '/hub') === 0 || strpos($path, '/s3') === 0) {
    
    // Controlla se la rotta è la pagina di login dell'hub, indipendentemente dalla lingua
    $isHubLoginPage = strpos($path, '/hub/login') !== false;
    $isHubUserLoggedIn = isset($_SESSION['hub_user_id']);

    // Se l'utente non è loggato nell'hub e non sta cercando di accedere alla pagina di login,
    // lo reindirizzo alla pagina di login dell'hub.
    if (!$isHubUserLoggedIn && !$isHubLoginPage) {
        // La lingua viene già gestita dal middleware SetLang, quindi possiamo usare un percorso assoluto
        header('Location: /hub/login');
        return 0;
    }

    // Se l'utente è già loggato nell'hub e cerca di visitare la pagina di login,
    // lo reindirizzo alla dashboard dell'hub.
    if ($isHubUserLoggedIn && $isHubLoginPage) {
        // La lingua viene già gestita dal middleware SetLang, quindi possiamo usare un percorso assoluto
        header('Location: /hub');
        return 0;
    }
}