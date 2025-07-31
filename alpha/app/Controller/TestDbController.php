<?php
namespace App\Controller;

use App\Core\Database;
use PDOException;
use InvalidArgumentException;

class TestDbController
{
    /**
     * Mostra la pagina di test con il form di inserimento.
     */
    public function index()
    {
        render('test/form_test', [
            'message' => 'Compila il form per inserire un nuovo utente di test.',
            'status' => 'info'
        ]);
    }

    /**
     * Riceve i dati dal form (POST) e tenta di inserirli nel database.
     */
    public function processInsert()
    {
        // Controlla se i dati sono stati inviati
        if (empty($_POST['name']) || empty($_POST['email'])) {
            render('test/form_test', [
                'message' => 'Tutti i campi sono obbligatori.',
                'status' => 'danger'
            ]);
            return;
        }

        $message = '';
        $status = 'danger';

        try {
            $db = new Database();
            
            // Prende i dati direttamente dal form
            $userData = [
                'name'              => $_POST['name'],
                'email'             => $_POST['email'],
                'created_at' => date('Y-m-d H:i:s') 
            ];

            if ($db->insert('test_users', $userData)) {
                $message = "Inserimento avvenuto con successo! Hai inserito: <br><b>Nome:</b> " . htmlspecialchars($userData['name']) . "<br><b>Email:</b> " . htmlspecialchars($userData['email']);
                $status = 'success';
            } else {
                $message = "L'inserimento è fallito per un motivo sconosciuto.";
            }

        } catch (PDOException $e) {
            $message = "Errore durante l'inserimento: " . $e->getMessage();
        } catch (InvalidArgumentException $e) {
            $message = "Errore negli argomenti passati: " . $e->getMessage();
        }

        render('test/form_test', compact('message', 'status'));
    }

    public function processUpdate()
    {
        if (empty($_POST['id']) || empty($_POST['name']) || empty($_POST['email'])) {
            render('test/form_test', [
                'message' => 'Per aggiornare, tutti i campi (ID, Nome, Email) sono obbligatori.',
                'status' => 'danger'
            ]);
            return;
        }

        $message = '';
        $status = 'danger';

        try {
            $db = new Database();
            
            $updateData = [
                'name'  => $_POST['name'],
                'email' => $_POST['email']
            ];
            
            $whereCondition = [
                'id' => filter_var($_POST['id'], FILTER_VALIDATE_INT)
            ];

            if ($whereCondition['id'] === false) {
                 throw new InvalidArgumentException("L'ID fornito non è un numero intero valido.");
            }

            $affectedRows = $db->update('test_users', $updateData, $whereCondition);

            if ($affectedRows > 0) {
                $message = "Aggiornamento riuscito! {$affectedRows} riga/e modificata/e per l'ID " . htmlspecialchars($_POST['id']) . ".";
                $status = 'success';
            } else {
                $message = "Operazione eseguita, ma nessuna riga è stata modificata. L'ID potrebbe non esistere o i dati inseriti erano identici a quelli già presenti.";
                $status = 'info';
            }

        } catch (PDOException $e) {
            $message = "Errore durante l'aggiornamento: " . $e->getMessage();
        } catch (InvalidArgumentException $e) {
            $message = "Errore negli argomenti passati: " . $e->getMessage();
        }

        render('test/form_test', compact('message', 'status'));
    }

    /**
     * Riceve un ID dal form (POST) e tenta di eliminare il record corrispondente.
     */
    public function processDelete()
    {
        if (empty($_POST['id'])) {
            render('test/form_test', [
                'message' => 'Per eliminare un record, il campo ID è obbligatorio.',
                'status' => 'danger'
            ]);
            return;
        }

        $message = '';
        $status = 'danger';

        try {
            $db = new Database();
            
            $whereCondition = [
                'id' => filter_var($_POST['id'], FILTER_VALIDATE_INT)
            ];

            if ($whereCondition['id'] === false) {
                 throw new InvalidArgumentException("L'ID fornito non è un numero intero valido.");
            }

            $affectedRows = $db->delete('test_users', $whereCondition);

            if ($affectedRows > 0) {
                $message = "Eliminazione riuscita! {$affectedRows} riga/e eliminata/e per l'ID " . htmlspecialchars($_POST['id']) . ".";
                $status = 'success';
            } else {
                $message = "Operazione eseguita, ma nessuna riga è stata eliminata. L'ID potrebbe non esistere.";
                $status = 'info';
            }

        } catch (PDOException $e) {
            $message = "Errore durante l'eliminazione: " . $e->getMessage();
        } catch (InvalidArgumentException $e) {
            $message = "Errore negli argomenti passati: " . $e->getMessage();
        }

        render('test/form_test', compact('message', 'status'));
    }

    public function processSelect()
    {
        if (empty($_POST['id'])) {
            render('test/form_test', [
                'message' => 'Per cercare un record, il campo ID è obbligatorio.',
                'status' => 'danger'
            ]);
            return;
        }

        $message = '';
        $status = 'danger';
        $results = [];

        try {
            $db = new Database();
            $whereCondition = ['id' => filter_var($_POST['id'], FILTER_VALIDATE_INT)];

            if ($whereCondition['id'] === false) {
                throw new InvalidArgumentException("L'ID fornito non è valido.");
            }

            $results = $db->select('test_users', $whereCondition);

            if (!empty($results)) {

                $expectedColumns = ['id', 'name', 'email', 'created_at'];
                $firstRowKeys = array_keys($results[0]);


                foreach ($expectedColumns as $column) {
                    if (!in_array($column, $firstRowKeys)) {
                        $results = []; 
                        throw new Exception("Dati recuperati, ma manca la colonna attesa: '{$column}'. Controlla che il nome della colonna nella tabella `test_users` sia corretto.");
                    }
                }

              
                // --- FINE BLOCCO DI VALIDAZIONE ---

                $message = "Record trovato per l'ID " . htmlspecialchars($_POST['id']) . ".";
                $status = 'success';
            } else {
                $message = "Nessun record trovato con ID " . htmlspecialchars($_POST['id']) . ".";
                $status = 'info';
            }

        } catch (PDOException | InvalidArgumentException | Exception $e) { // Aggiunto Exception
            $message = "Errore durante la ricerca: " . $e->getMessage();
        }

        render('test/form_test', compact('message', 'status', 'results'));
    }

    public function processFindLast()
    {
        $message = '';
        $status = 'danger';
        $results = [];
    
        try {
            $db = new Database();
            
            $lastRecord = $db->findLast('test_users'); 
          
            if ($lastRecord !== null) {
                $results[] = $lastRecord;
                $message = "Ultimo record trovato nella tabella `test_users`.";
                $status = 'success';
            } else {
                $message = "La tabella `test_users` è vuota.";
                $status = 'info';
            }
    
        } catch (PDOException | InvalidArgumentException $e) {
            $message = "Errore durante la ricerca dell'ultimo record: " . $e->getMessage();
        }
    
        render('test/form_test', compact('message', 'status', 'results'));
    }

    public function processRawQuery()
    {
        if (empty($_POST['raw_sql'])) {
            render('test/form_test', [
                'message' => 'Il campo della query SQL non può essere vuoto.',
                'status' => 'danger'
            ]);
            return;
        }

        $message = '';
        $status = 'danger';
        $results = [];
        $rawSql = $_POST['raw_sql'];

        try {
            $db = new Database();
            $results = $db->query($rawSql);

            if (isset($results)) {
                 $message = "Query eseguita con successo. Trovati **" . count($results) . "** record.";
                 $status = 'success';
            }

        } catch (PDOException | InvalidArgumentException $e) {
            $message = "Errore durante l'esecuzione della query: " . $e->getMessage();
        }

        render('test/form_test', compact('message', 'status', 'results', 'rawSql'));
    }
}