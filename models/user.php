<?php
// models/User.php
require_once __DIR__ . '/Database.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Function to create a new user
  public function create($username, $email, $password) {
    try {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, role, is_confirmed) 
                  VALUES (:username, :email, :password, 'user', 0)";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $clean_username = htmlspecialchars(strip_tags($username));
        $clean_email = htmlspecialchars(strip_tags($email));
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":username", $clean_username);
        $stmt->bindParam(":email", $clean_email);
        $stmt->bindParam(":password", $hashed_password);

        return $stmt->execute();

    } catch (PDOException $e) {
        // Log error for the developer (DAW requirement)
        error_log("DB Error: " . $e->getMessage());

        if ($e->getCode() == 23000) {
            // Check if it's the email or the username that is duplicated
            if (strpos($e->getMessage(), 'email') !== false) {
                throw new Exception("This email is already registered.");
            } else {
                throw new Exception("This username is already taken.");
            }
        }
        throw new Exception("A database error occurred. Please try again later.");
    }
}
}