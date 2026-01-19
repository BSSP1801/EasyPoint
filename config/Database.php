<?php
// Database.php - All comments in English as per requirements 

class Database {
    private static $host = 'localhost';
    private static $db_name = 'easy_point'; // Updated to match docker-compose.yml
    private static $username = 'project_user'; // Update with your DB user
    private static $password = 'project_password';     // Update with your DB password
    private static $connection = null;

    /**
     * Get the database connection using PDO 
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                // Set DSN (Data Source Name)
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                
                // Establish connection
                self::$connection = new PDO($dsn, self::$username, self::$password);
                
                // Configure PDO to throw exceptions for error handling 
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Set default fetch mode to Associative Array
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
            } catch (PDOException $exception) {
                // In production, log this error instead of showing it
                error_log("Connection Error: " . $exception->getMessage());
                // Redirect to a custom 500 error page as required by the guide 
                header("Location: /errors/500.php");
                exit;
            }
        }
        return self::$connection;
    }
}