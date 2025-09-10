<?php

use App\Core\Database;

class Migration_2025_09_08_083009_create_logs_table
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function up()
    {
        $this->db->query("
            CREATE TABLE logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_email VARCHAR(255) NULL,
                ip_address VARCHAR(45) NOT NULL,
                http_method VARCHAR(10) NOT NULL,
                uri TEXT NOT NULL,
                logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                log_message TEXT NULL,
                log_level ENUM('INFO', 'MEDIUM', 'HIGH', 'CRITICAL') NULL
            )
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS logs");
    }
}