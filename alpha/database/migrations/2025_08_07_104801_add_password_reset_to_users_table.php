<?php

use App\Core\Database;

class Migration_2025_08_07_104801_add_password_reset_to_users_table
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
            ADD COLUMN reset_token VARCHAR(255) NULL DEFAULT NULL,
            ADD COLUMN reset_token_expires_at TIMESTAMP NULL DEFAULT NULL
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
            DROP COLUMN reset_token,
            DROP COLUMN reset_token_expires_at
        ");
    }
}