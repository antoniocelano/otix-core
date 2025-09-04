<?php

namespace App\Core;

class Session
{
    /**
     * Avvia la sessione se non è già attiva.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Imposta un valore nella sessione.
     *
     * @param string $key La chiave della sessione.
     * @param mixed $value Il valore da impostare.
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Recupera un valore dalla sessione.
     *
     * @param string $key La chiave della sessione.
     * @param mixed $default Il valore predefinito se la chiave non esiste.
     * @return mixed Il valore della sessione o il valore predefinito.
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Controlla se una chiave esiste nella sessione.
     *
     * @param string $key La chiave da controllare.
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Rimuove una o più chiavi dalla sessione.
     *
     * @param string|array $keys La chiave o le chiavi da rimuovere.
     */
    public static function remove($keys): void
    {
        self::start();
        foreach ((array) $keys as $key) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Distrugge l'intera sessione.
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }
}