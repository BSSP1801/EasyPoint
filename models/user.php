<?php
// models/user.php
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

    // Crear nuevo usuario
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

    // Login
    public function login($username_or_email, $password)
    {
        $query = "SELECT id, username, email, password, role, is_confirmed 
              FROM " . $this->table_name . " 
              WHERE username = :identifier OR email = :identifier LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":identifier", $username_or_email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Guardar Horarios
    public function saveOpeningHours($userId, $jsonSchedule)
    {
        try {
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

    // Obtener Perfil Completo (Usuario + Negocio)
    public function getFullProfile($userId)
    {
        // Unificación: Traemos todo (*) de users y los campos específicos de business_profiles
        // incluyendo el ID del perfil para la galería.
        $query = "SELECT u.*, 
                     bp.id as business_profile_id, 
                     bp.description, bp.logo_url, bp.banner_url, 
                     bp.opening_hours, bp.is_public,
                     bp.website, bp.instagram_link, bp.facebook_link, bp.twitter_link, bp.tiktok_link
              FROM " . $this->table_name . " u 
              LEFT JOIN business_profiles bp ON u.id = bp.user_id 
              WHERE u.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Helper para obtener solo el ID del perfil de negocio (útil para subidas rápidas)
    public function getBusinessProfileByUserId($userId) {
        $query = "SELECT id FROM business_profiles WHERE user_id = :uid LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar Información de Negocio
    public function updateBusinessProfile($userId, $data)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Actualizar tabla users
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

            // 2. Actualizar/Insertar tabla business_profiles
            $queryProfile = "INSERT INTO business_profiles 
                 (user_id, description, logo_url, banner_url, is_public, website, instagram_link, facebook_link, twitter_link, tiktok_link) 
                 VALUES (:uid, :desc, :logo, :banner, :is_public, :web, :insta, :fb, :tw, :tk) 
                 ON DUPLICATE KEY UPDATE 
                 description = :desc,
                 logo_url = COALESCE(:logo, logo_url),
                 banner_url = COALESCE(:banner, banner_url),
                 is_public = :is_public,
                 website = :web,
                 instagram_link = :insta,
                 facebook_link = :fb,
                 twitter_link = :tw,
                 tiktok_link = :tk";

            $stmtP = $this->conn->prepare($queryProfile);
            $stmtP->bindParam(':uid', $userId);
            $stmtP->bindParam(':desc', $data['description']);
            $stmtP->bindParam(':is_public', $data['is_public']);
            $stmtP->bindParam(':web', $data['website']);
            $stmtP->bindParam(':insta', $data['instagram']);
            $stmtP->bindParam(':fb', $data['facebook']);
            $stmtP->bindParam(':tw', $data['twitter']);
            $stmtP->bindParam(':tk', $data['tiktok']);

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

    // Tiendas recomendadas (Carrusel Home)
    public function getRecommendedStores()
    {
        try {
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

    // Galería
    public function addGalleryImages($profileId, $imagePaths)
    {
        $query = "INSERT INTO business_gallery (business_profile_id, image_url) VALUES (:pid, :url)";
        $stmt = $this->conn->prepare($query);

        foreach ($imagePaths as $path) {
            $stmt->bindParam(':pid', $profileId);
            $stmt->bindParam(':url', $path);
            $stmt->execute();
        }
    }

    public function getBusinessGallery($userId)
    {
        $query = "SELECT bg.image_url 
              FROM business_gallery bg 
              JOIN business_profiles bp ON bg.business_profile_id = bp.id 
              WHERE bp.user_id = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getStoreAppointments($storeId)
{
    try {

        
        $query = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, 
                         u.email as client_name, 
                         s.name as service_name, s.price, s.duration
                  FROM appointments a
                  JOIN services s ON a.service_id = s.id
                  JOIN users u ON a.user_id = u.id
                  WHERE s.user_id = :store_id  
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':store_id', $storeId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error fetching store appointments: " . $e->getMessage());
        return [];
    }
}

public function getUserAppointments($userId)
{
    try {
        $query = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, 
                         s.name as service_name, s.price, s.duration,
                         store.id as store_id, store.business_name as store_name, bp.logo_url as store_logo
                  FROM appointments a
                  JOIN services s ON a.service_id = s.id
                  JOIN users store ON s.user_id = store.id
                  LEFT JOIN business_profiles bp ON store.id = bp.user_id
                  WHERE a.user_id = :user_id
                  ORDER BY a.appointment_date ASC, a.appointment_time ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error fetching user appointments: " . $e->getMessage());
        return [];
    }
}

public function updateAppointmentStatus($appointmentId, $newStatus, $storeId)
    {
        try {
            // Usamos JOIN para asegurarnos de que la cita pertenece a un servicio 
            // creado por la tienda que está intentando modificarla ($storeId)
            $query = "UPDATE appointments a 
                      INNER JOIN services s ON a.service_id = s.id 
                      SET a.status = :status 
                      WHERE a.id = :id AND s.user_id = :store_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $appointmentId);
            $stmt->bindParam(':store_id', $storeId);

            if ($stmt->execute()) {
                // Verificar si realmente se modificó alguna fila (si no, es que no era su cita o el ID no existe)
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error updating appointment: " . $e->getMessage());
            return false;
        }
    }

}
?>