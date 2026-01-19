<?php
// models/Database.php
// Requirement: All comments in English [cite: 39]
require_once dirname(__DIR__) . '/config/config.php';
class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        // Data from config.php 
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
    }

    public function getConnection()
    {
        $this->conn = null;
        try {
            // Using PDO as required [cite: 59]
      
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Requirement: Error handling [cite: 92]
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}