<?php

// Importa le classi necessarie per interagire con il database e la sessione.
use App\Core\Database;
use App\Core\Session;

// Richiama una funzione helper 'http()' (non definita in questo snippet)
// che probabilmente restituisce un oggetto o un array contenente i dati della richiesta HTTP
// (come metodo, URI, e indirizzo IP) già sanificati.
$http = http();
// Crea un'istanza della classe Database per interagire con il database.
$db = new Database();

// --- Raccolta e preparazione dei dati per il log ---

// Estrae l'indirizzo IP remoto, il metodo HTTP e l'URI dalla richiesta sanificata.
$ip = $http['remote'];
$method = $http['method'];
$uri = $http['uri'];
// Controlla se la sessione ha un 'user_id' e, in caso affermativo,
// recupera l'email dell'utente dalla sessione. Altrimenti, il valore è null.
$userEmail = Session::has('user_id') ? Session::get('user_email') : null;

// --- Inserimento del log nel database ---

// Salva un record nella tabella 'logs'.
// Questo log registra che una richiesta è stata ricevuta, includendo
// i dettagli dell'utente, l'IP, il metodo, l'URI e un messaggio predefinito.
$db->insert('logs', [
    // L'email dell'utente, se loggato.
    'user_email'  => $userEmail,
    // L'indirizzo IP del client che ha effettuato la richiesta.
    'ip_address'  => $ip,
    // Il metodo HTTP (es. GET, POST).
    'http_method' => $method,
    // L'URI (Uniform Resource Identifier) richiesto.
    'uri'         => $uri,
    // Un messaggio fisso che descrive l'evento.
    'log_message' => 'Richiesta ricevuta',
    // Il livello di criticità del log, in questo caso 'INFO' per informazione.
    'log_level'   => 'INFO'
]);

// --- Passaggio del controllo al prossimo step ---

// Restituisce l'array $http, consentendo ad altri middleware o al router
// di accedere ai dati della richiesta sanificati senza doverli rielaborare.
return $http;