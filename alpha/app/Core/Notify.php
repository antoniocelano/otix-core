<?php

namespace App\Core;

class Notify
{
    /**
     * @var string La chiave della sessione per le notifiche.
     */
    private const SESSION_KEY = 'notifications';

    /**
     * Memorizza una notifica nella sessione per il prossimo caricamento della pagina.
     *
     * @param string $message Il testo della notifica.
     * @param int $duration La durata in secondi prima che la notifica scompaia.
     * @param string $type Il tipo di notifica (success, error, warning, info).
     */
    public static function push(string $message, int $duration, string $type)
    {
        Session::set(self::SESSION_KEY, [
            'message' => $message,
            'duration' => $duration,
            'type' => $type,
        ]);
        return true;
    }

    /**
     * Recupera la notifica dalla sessione e la rimuove.
     *
     * @return array|null La notifica se presente, altrimenti null.
     */
    public static function getAndForget(): ?array
    {
        if (Session::has(self::SESSION_KEY)) {
            $notification = Session::get(self::SESSION_KEY);
            Session::remove(self::SESSION_KEY);
            return $notification;
        }
        return null;
    }
}