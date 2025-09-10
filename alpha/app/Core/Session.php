<?php

namespace App\Core;

/**
 * Classe statica per la gestione della sessione.
 * Fornisce un'interfaccia semplice e sicura per manipolare i dati di sessione
 * senza dover interagire direttamente con la superglobale $_SESSION.
 */
class Session
{
    /**
     * Avvia la sessione se non è già attiva.
     * Questo metodo è chiamato da tutti gli altri metodi della classe per garantire
     * che la sessione sia sempre disponibile prima di qualsiasi operazione.
     */
    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Imposta un valore nella sessione.
     *
     * @param string $key La chiave per memorizzare il valore.
     * @param mixed $value Il valore da impostare. Può essere di qualsiasi tipo.
     */
    public static function set(string $key, $value): void
    {
        // Avvia la sessione se necessario.
        self::init();
        // Assegna il valore alla chiave specificata nella superglobale $_SESSION.
        $_SESSION[$key] = $value;
    }

    /**
     * Recupera un valore dalla sessione.
     *
     * @param string $key La chiave della sessione da cui recuperare il valore.
     * @param mixed $default Il valore predefinito da restituire se la chiave non esiste.
     * @return mixed Il valore della sessione o il valore predefinito.
     */
    public static function get(string $key, $default = null)
    {
        // Avvia la sessione se necessario.
        self::init();
        // Utilizza l'operatore di coalescenza a null (??) per restituire il valore della sessione
        // se esiste, altrimenti il valore predefinito.
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Controlla se una chiave esiste nella sessione.
     *
     * @param string $key La chiave da controllare.
     * @return bool True se la chiave esiste, false altrimenti.
     */
    public static function has(string $key): bool
    {
        // Avvia la sessione se necessario.
        self::init();
        // Utilizza isset() per verificare l'esistenza della chiave.
        return isset($_SESSION[$key]);
    }

    /**
     * Rimuove una o più chiavi dalla sessione.
     *
     * @param string|array $keys La chiave o un array di chiavi da rimuovere.
     */
    public static function remove($keys): void
    {
        // Avvia la sessione se necessario.
        self::init();
        // Converte l'argomento in un array per gestire sia una singola stringa che un array di chiavi.
        foreach ((array) $keys as $key) {
            // Rimuove la chiave specificata dalla sessione.
            unset($_SESSION[$key]);
        }
    }

    /**
     * Distrugge l'intera sessione.
     * Questo metodo è tipicamente usato per il logout di un utente.
     */
    public static function destroy(): void
    {
        // Avvia la sessione se necessario.
        self::init();
        // Resetta l'array della superglobale $_SESSION.
        $_SESSION = [];
        // Distrugge tutti i dati registrati nella sessione sul server.
        session_destroy();
    }
}