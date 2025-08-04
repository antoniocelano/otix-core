<?php

use App\Core\Database;

class Migration_2025_08_04_084013_add_remember_token_to_users_table
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Esegue la migrazione.
     * Applica le modifiche allo schema del database.
     */
    public function up()
    {
        $this->db->query("
            ALTER TABLE users
            ADD COLUMN remember_token VARCHAR(100) NULL DEFAULT NULL AFTER password
        ");
    }

    /**
     * Annulla la migrazione.
     * Rimuove le modifiche apportate dal metodo up().
     */
    public function down()
    {
        $this->db->query("
            ALTER TABLE users
            DROP COLUMN remember_token
        ");
    }
}