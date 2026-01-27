<?php
// models/User.php
require_once __DIR__ . '/Database.php';

class User
{
    private $conn;
    private $table_name = "users";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Function to create a new user
    public function create($data)
    {
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
    public function login($username_or_email, $password)
    {
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


    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveOpeningHours($userId, $jsonSchedule)
    {
        try {

            // Intenta insertar una nueva fila en business_profiles con el user_id y el horario.
            //  Si el user_id ya existe (DUPLICATE KEY), simplemente actualiza el campo opening_hours.
            $query = "INSERT INTO business_profiles (user_id, opening_hours) 
                  VALUES (:user_id, :opening_hours) 
                  ON DUPLICATE KEY UPDATE opening_hours = :opening_hours_update";

            $stmt = $this->conn->prepare($query);


            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":opening_hours", $jsonSchedule);
            $stmt->bindParam(":opening_hours_update", $jsonSchedule);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("DB Error saving schedule: " . $e->getMessage());
            return false;
        }

    }

    // En models/User.php

    // 1. Obtener datos combinados de Usuario y Perfil de Negocio
    public function getFullProfile($userId)
    {

        $query = "SELECT u.*, 
                     bp.description, bp.logo_url, bp.banner_url, bp.website, bp.instagram_link,
                     bp.opening_hours 
              FROM " . $this->table_name . " u 
              LEFT JOIN business_profiles bp ON u.id = bp.user_id 
              WHERE u.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Actualizar información del negocio
    public function updateBusinessProfile($userId, $data)
    {
        try {
            $this->conn->beginTransaction();

            // A. Actualizar datos básicos en tabla USERS
            $queryUser = "UPDATE users SET 
                      business_name = :bname, 
                      phone = :phone, 
                      address = :address, 
                      city = :city, 
                      postal_code = :zip 
                      WHERE id = :id";

            $stmtU = $this->conn->prepare($queryUser);
            $stmtU->bindParam(':bname', $data['business_name']);
            $stmtU->bindParam(':phone', $data['phone']);
            $stmtU->bindParam(':address', $data['address']);
            $stmtU->bindParam(':city', $data['city']);
            $stmtU->bindParam(':zip', $data['postal_code']);
            $stmtU->bindParam(':id', $userId);
            $stmtU->execute();

            // B. Actualizar perfil extendido (Description, Images) en tabla BUSINESS_PROFILES
            // Usamos "INSERT ... ON DUPLICATE KEY UPDATE" por si el perfil aún no existe
            $queryProfile = "INSERT INTO business_profiles (user_id, description, logo_url, banner_url, is_public) 
                 VALUES (:uid, :desc, :logo, :banner, :is_public) 
                 ON DUPLICATE KEY UPDATE 
                 description = :desc,
                 logo_url = COALESCE(:logo, logo_url),
                 banner_url = COALESCE(:banner, banner_url),
                 is_public = :is_public";

            $stmtP = $this->conn->prepare($queryProfile);
            $stmtP->bindParam(':is_public', $data['is_public']);
        

            $stmtP->bindParam(':uid', $userId);
            $stmtP->bindParam(':desc', $data['description']);

            // Manejo de nulos para imágenes
            $logo = !empty($data['logo_url']) ? $data['logo_url'] : null;
            $banner = !empty($data['banner_url']) ? $data['banner_url'] : null;

            $stmtP->bindParam(':logo', $logo);
            $stmtP->bindParam(':banner', $banner);
            $stmtP->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }
    // En models/User.php

    public function getRecommendedStores()
    {
        try {

            // Usamos LEFT JOIN para traer la tienda aunque aún no haya configurado su perfil completo (logo)
            $query = "SELECT u.id, u.business_name, u.address, u.city, u.postal_code, bp.logo_url 
                  FROM users u 
                  INNER JOIN business_profiles bp ON u.id = bp.user_id 
                  WHERE u.role = 'store' AND bp.is_public = 1 
                  ORDER BY u.created_at DESC LIMIT 8";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching stores: " . $e->getMessage());
            return [];
        }
    }

public function saveOpeningHours($userId, $jsonSchedule) {
    try {
      
        // Intenta insertar una nueva fila en business_profiles con el user_id y el horario.
        //  Si el user_id ya existe (DUPLICATE KEY), simplemente actualiza el campo opening_hours.
        $query = "INSERT INTO business_profiles (user_id, opening_hours) 
                  VALUES (:user_id, :opening_hours) 
                  ON DUPLICATE KEY UPDATE opening_hours = :opening_hours_update";
        
        $stmt = $this->conn->prepare($query);
        
 
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":opening_hours", $jsonSchedule);
        $stmt->bindParam(":opening_hours_update", $jsonSchedule);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("DB Error saving schedule: " . $e->getMessage());
        return false;
    }

}

// En models/User.php

// 1. Obtener datos combinados de Usuario y Perfil de Negocio
public function getFullProfile($userId) {
    $query = "SELECT u.*, 
                     bp.description, bp.logo_url, bp.banner_url, bp.website, bp.instagram_link 
              FROM " . $this->table_name . " u 
              LEFT JOIN business_profiles bp ON u.id = bp.user_id 
              WHERE u.id = :id LIMIT 1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $userId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 2. Actualizar información del negocio
public function updateBusinessProfile($userId, $data) {
    try {
        $this->conn->beginTransaction();

        // A. Actualizar datos básicos en tabla USERS
        $queryUser = "UPDATE users SET 
                      business_name = :bname, 
                      phone = :phone, 
                      address = :address, 
                      city = :city, 
                      postal_code = :zip 
                      WHERE id = :id";
        
        $stmtU = $this->conn->prepare($queryUser);
        $stmtU->bindParam(':bname', $data['business_name']);
        $stmtU->bindParam(':phone', $data['phone']);
        $stmtU->bindParam(':address', $data['address']);
        $stmtU->bindParam(':city', $data['city']);
        $stmtU->bindParam(':zip', $data['postal_code']);
        $stmtU->bindParam(':id', $userId);
        $stmtU->execute();

        // B. Actualizar perfil extendido (Description, Images) en tabla BUSINESS_PROFILES
        // Usamos "INSERT ... ON DUPLICATE KEY UPDATE" por si el perfil aún no existe
        $queryProfile = "INSERT INTO business_profiles (user_id, description, logo_url, banner_url) 
                         VALUES (:uid, :desc, :logo, :banner) 
                         ON DUPLICATE KEY UPDATE 
                         description = :desc,
                         logo_url = COALESCE(:logo, logo_url), -- Si no se sube nueva imagen, mantener la anterior
                         banner_url = COALESCE(:banner, banner_url)";

        $stmtP = $this->conn->prepare($queryProfile);
        $stmtP->bindParam(':uid', $userId);
        $stmtP->bindParam(':desc', $data['description']);
        
        // Manejo de nulos para imágenes
        $logo = !empty($data['logo_url']) ? $data['logo_url'] : null;
        $banner = !empty($data['banner_url']) ? $data['banner_url'] : null;
        
        $stmtP->bindParam(':logo', $logo);
        $stmtP->bindParam(':banner', $banner);
        $stmtP->execute();

        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        $this->conn->rollBack();
        error_log("Error updating profile: " . $e->getMessage());
        return false;
    }
}

}