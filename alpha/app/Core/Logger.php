<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Session;

/**
 * Classe per la gestione dei log personalizzati nel database.
 * Questo logger è statico e non necessita di istanza per essere utilizzato.
 * Salva i messaggi di log in una tabella 'logs'.
 */
class Logger
{
    /**
     * Registra un messaggio di log nel database.
     * Questo metodo cerca di aggiornare un log esistente (se appena creato da un middleware)
     * o di inserirne uno nuovo.
     *
     * @param string $message Il messaggio descrittivo del log.
     * @param string $level Il livello di criticità del log (es. 'INFO', 'WARNING', 'ERROR'). Il valore predefinito è 'INFO'.
     */
    public static function set(string $message, string $level = 'INFO')
    {
        // Crea una nuova istanza della classe Database per interagire con il DB.
        $db = new Database();
        
        // Recupera l'indirizzo IP del client o lo imposta su 'UNKNOWN' se non disponibile.
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        // Tenta di ottenere l'ID dell'ultima riga inserita dal database.
        // Questo ID potrebbe essere stato generato da un middleware che ha già registrato la richiesta.
        $lastLog = $db->lastInsertId();

        // Controlla se è stato recuperato un ID valido dall'ultima operazione di inserimento.
        if (!empty($lastLog)) {
            // Se un ID esiste, aggiorna il record esistente con il messaggio e il livello di log.
            // Questo scenario si verifica tipicamente quando un middleware inserisce
            // un record base, che viene poi arricchito da un'altra parte dell'applicazione.
            $db->update(
                'logs',
                ['log_message' => $message, 'log_level' => strtoupper($level)],
                ['id' => $lastLog]
            );
        } else {
             // Se non c'è un ID di inserimento recente, significa che il middleware non ha ancora
             // registrato la richiesta, quindi inserisce un nuovo record.
            
            // Recupera l'email dell'utente dalla sessione se è loggato, altrimenti imposta null.
            $userEmail = Session::has('user_id') ? Session::get('user_email') : null;

            // Inserisce un nuovo record completo nella tabella 'logs'.
            $db->insert('logs', [
                'user_email'  => $userEmail,
                'ip_address'  => $ip,
                'http_method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                'uri'         => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
                'log_message' => $message,
                'log_level'   => strtoupper($level)
            ]);
        }
    }
}