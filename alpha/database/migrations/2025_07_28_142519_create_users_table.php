<?php

use App\Core\Database;

class Migration_2025_07_28_142519_create_users_table
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Esegui la migrazione.
     */

    public function up()
    {
        $this->db->query("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Annulla la migrazione.
     */
    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS users");
    }
}