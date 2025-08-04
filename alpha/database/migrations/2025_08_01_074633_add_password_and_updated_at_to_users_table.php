<?php

use App\Core\Database;

class Migration_2025_08_01_074633_add_password_and_updated_at_to_users_table
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
            ADD COLUMN password VARCHAR(255) NOT NULL AFTER email,
            ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at
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
            DROP COLUMN password,
            DROP COLUMN updated_at
        ");
    }
}