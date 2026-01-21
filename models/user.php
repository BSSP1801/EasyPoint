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
 public function create($data) {
    try {
       
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE username = :username OR email = :email";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":username", $data['username']);
        $checkStmt->bindParam(":email", $data['email']);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception("The username or email is already registered.");
        }

        
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, role, business_name, address, postal_code, is_confirmed) 
                  VALUES (:username, :email, :password, :role, :business_name, :address, :postal_code, 0)";
        
        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($data['username']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $role = $data['role'] ?? 'user';
        $hashed_password = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $business_name = !empty($data['business_name']) ? htmlspecialchars(strip_tags($data['business_name'])) : null;
        $address = !empty($data['address']) ? htmlspecialchars(strip_tags($data['address'])) : null;
        $postal_code = !empty($data['postal_code']) ? htmlspecialchars(strip_tags($data['postal_code'])) : null;

        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":business_name", $business_name);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":postal_code", $postal_code);

        return $stmt->execute();

    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        throw new Exception("A database error occurred.");
    }
}
public function login($username_or_email, $password) {
    // We check both username or email for better UX
    $query = "SELECT id, username, email, password, role, is_confirmed 
              FROM " . $this->table_name . " 
              WHERE username = :identifier OR email = :identifier LIMIT 1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":identifier", $username_or_email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify the hashed password
        if (password_verify($password, $row['password'])) {
            return $row; // Return user data if password is correct
        }
    }
    return false; // Login failed
}


public function getById($id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}