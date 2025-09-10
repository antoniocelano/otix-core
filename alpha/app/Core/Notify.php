<?php

namespace App\Core;

/**
 * Classe per la gestione delle notifiche temporanee tramite sessione.
 * Le notifiche vengono "pushed" nella sessione in una pagina e possono essere
 * recuperate una sola volta nella pagina successiva, per poi essere rimosse.
 * Questo pattern è comunemente usato per mostrare messaggi di feedback all'utente,
 * come "Operazione completata con successo" dopo un reindirizzamento.
 */
class Notify
{
    /**
     * @var string La chiave della sessione per memorizzare le notifiche.
     * Questo assicura che la notifica sia salvata in un'area
     * specifica e non entri in conflitto con altri dati di sessione.
     */
    private const SESSION_KEY = 'notifications';

    /**
     * Memorizza una notifica nella sessione.
     * La notifica viene salvata come un array associativo contenente
     * il messaggio, la durata e il tipo.
     *
     * @param string $message Il testo della notifica da mostrare all'utente.
     * @param int $duration La durata in secondi per cui la notifica dovrebbe rimanere visibile.
     * @param string $type Il tipo di notifica, utile per la stilizzazione (es. 'success', 'error', 'warning', 'info').
     * @return bool Ritorna sempre true se l'operazione di salvataggio nella sessione ha successo.
     */
    public static function push(string $message, int $duration, string $type)
    {
        // Salva l'array della notifica nella sessione utilizzando la chiave predefinita.
        Session::set(self::SESSION_KEY, [
            'message' => $message,
            'duration' => $duration,
            'type' => $type,
        ]);
        return true;
    }

    /**
     * Recupera una notifica dalla sessione e la rimuove immediatamente.
     * Questo metodo implementa il pattern "flash message" o "get-and-forget",
     * garantendo che la notifica venga mostrata solo una volta.
     *
     * @return array|null Restituisce l'array della notifica se presente nella sessione, altrimenti null.
     */
    public static function getAndForget(): ?array
    {
        // Controlla se una notifica esiste nella sessione.
        if (Session::has(self::SESSION_KEY)) {
            // Se la notifica è presente, la recupera.
            $notification = Session::get(self::SESSION_KEY);
            // Rimuove immediatamente la notifica dalla sessione per evitare che venga mostrata di nuovo.
            Session::remove(self::SESSION_KEY);
            // Restituisce l'array della notifica recuperata.
            return $notification;
        }
        // Se non ci sono notifiche, ritorna null.
        return null;
    }
}