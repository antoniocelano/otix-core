<?php

use App\Core\Database;

class Migration_2025_09_02_151838_AddSurnameToUsersTable
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
        $db = new Database();
        $db->query("ALTER TABLE users ADD surname VARCHAR(255) NULL AFTER name");
    }

    public function down()
    {
        $db = new Database();
        $db->query("ALTER TABLE users DROP COLUMN surname");
    }
}