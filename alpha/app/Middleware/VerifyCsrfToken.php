<?php

// Ignora la verifica per le richieste "safe" (che non modificano dati)
$safeMethods = ['GET', 'HEAD', 'OPTIONS'];
if (in_array($http['method'], $safeMethods, true)) {
    return; // Non fare nulla
}

// Per tutte le altre richieste (POST, PUT, DELETE, etc.), verifica il token.
$sessionToken = $_SESSION['csrf_token'] ?? null;
$postToken = $http['post']['csrf_token'] ?? null;

// Se il token manca o non corrisponde, blocca la richiesta.
// hash_equals() previene attacchi di tipo "timing attack".
if (!$sessionToken || !$postToken || !hash_equals($sessionToken, $postToken)) {
    (new \App\Controller\ErrorController())->code('ERR002'); // Errore 403 Forbidden
    exit;
}
