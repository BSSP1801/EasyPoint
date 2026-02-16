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
            // Evaluamos exactamente qué campo está duplicado
            $checkQuery = "SELECT username, email FROM " . $this->table_name . " WHERE username = :username OR email = :email LIMIT 1";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(":username", $data['username']);
            $checkStmt->bindParam(":email", $data['email']);
            $checkStmt->execute();

            if ($row = $checkStmt->fetch(PDO::FETCH_ASSOC)) {
                if (strtolower($row['email']) === strtolower($data['email'])) {
                    throw new Exception("email:This email is already registered.");
                } else {
                    throw new Exception("username:This username is already taken.");
                }
            }

            $token = bin2hex(random_bytes(16));

            $query = "INSERT INTO " . $this->table_name . " 
              (username, email, password, role, business_name, address, postal_code, is_confirmed, token) 
              VALUES (:username, :email, :password, :role, :business_name, :address, :postal_code, 0, :token)";

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
            $stmt->bindParam(":token", $token);
        

            if ($stmt->execute()) {
                return $token; 
            }
        return false;

        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            throw new Exception("A database error occurred.");
        }
    }


//Token Validation
public function confirmAccount($token)
{
    try {
        // Buscamos si existe un usuario con ese token y que no esté confirmado
        $query = "UPDATE " . $this->table_name . " 
                  SET is_confirmed = 1, token = NULL 
                  WHERE token = :token AND is_confirmed = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        // Retorna true si se modificó alguna fila
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error confirmando cuenta: " . $e->getMessage());
        return false;
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
        $query = "SELECT u.*, 
                     bp.id as business_profile_id, 
                     bp.description, bp.business_type, bp.logo_url, bp.banner_url, 
                     bp.opening_hours, bp.is_public,
                     bp.website, bp.instagram_link, bp.facebook_link, bp.twitter_link, bp.tiktok_link
              FROM " . $this->table_name . " u 
              LEFT JOIN business_profiles bp ON u.id = bp.user_id 
              WHERE u.id = :id LIMIT 1";
        // ... resto de la función igual
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Helper para obtener solo el ID del perfil de negocio (útil para subidas rápidas)
    public function getBusinessProfileByUserId($userId)
    {
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

            // ... (El update de la tabla users se mantiene igual) ...
            $queryUser = "UPDATE users SET 
                      business_name = :bname, phone = :phone, address = :address, city = :city, postal_code = :zip 
                      WHERE id = :id";
            // ... (binding de users igual) ...
            $stmtU = $this->conn->prepare($queryUser);
            $stmtU->bindParam(':bname', $data['business_name']);
            $stmtU->bindParam(':phone', $data['phone']);
            $stmtU->bindParam(':address', $data['address']);
            $stmtU->bindParam(':city', $data['city']);
            $stmtU->bindParam(':zip', $data['postal_code']);
            $stmtU->bindParam(':id', $userId);
            $stmtU->execute();

            // Actualizar business_profiles incluyendo business_type
            $queryProfile = "INSERT INTO business_profiles 
                 (user_id, description, business_type, logo_url, banner_url, is_public, website, instagram_link, facebook_link, twitter_link, tiktok_link) 
                 VALUES (:uid, :desc, :type, :logo, :banner, :is_public, :web, :insta, :fb, :tw, :tk) 
                 ON DUPLICATE KEY UPDATE 
                 description = :desc,
                 business_type = :type, 
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
            $stmtP->bindParam(':type', $data['business_type']); // Nuevo campo
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
    public function getRecommendedStores($category = null)
    {
        try {
            $query = "SELECT u.id, u.business_name, u.address, u.city, u.postal_code, 
                         bp.logo_url, bp.business_type,
                         COALESCE(AVG(r.rating), 0) as avg_rating, 
                         COUNT(r.id) as review_count
              FROM users u 
              INNER JOIN business_profiles bp ON u.id = bp.user_id 
              LEFT JOIN reviews r ON bp.id = r.business_profile_id
              WHERE u.role = 'store' AND bp.is_public = 1";

            if ($category) {
                $query .= " AND bp.business_type = :category";
            }

            // CAMBIO: Ordenar por valoración media descendente y luego por cantidad de reseñas
            $query .= " GROUP BY u.id ORDER BY avg_rating DESC, review_count DESC LIMIT 8";

            $stmt = $this->conn->prepare($query);

            if ($category) {
                $stmt->bindParam(':category', $category);
            }

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
        $db = new Database();
        $conn = $db->getConnection();

        // Obtenemos la cita, los detalles del servicio y el nombre del negocio (store)
        $stmt = $conn->prepare("
        SELECT a.id, a.appointment_date, a.appointment_time, a.status,
               s.name as service_name, s.duration, s.price,
               u_store.business_name as store_name, u_store.address as store_address,
               u_store.phone as store_phone
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        JOIN users u_store ON s.user_id = u_store.id
        WHERE a.user_id = :user_id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");

        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAppointmentStatus($appointmentId, $newStatus, $userId)
    {
        try {
            // Permitimos la actualización si quien lo solicita es el dueño del 
            // servicio (tienda: s.user_id) O el cliente dueño de la cita (a.user_id)
            $query = "UPDATE appointments a 
                      INNER JOIN services s ON a.service_id = s.id 
                      SET a.status = :status 
                      WHERE a.id = :id AND (s.user_id = :user_id OR a.user_id = :user_id)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $appointmentId);
            $stmt->bindParam(':user_id', $userId);

            if ($stmt->execute()) {
                // Verificar si realmente se modificó alguna fila
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error updating appointment: " . $e->getMessage());
            return false;
        }
    }

    public function getAllStores($category = null, $searchQuery = null)
    {
        try {
            $sql = "SELECT u.id, u.business_name, u.address, u.city, u.postal_code, bp.logo_url, bp.business_type 
              FROM users u 
              INNER JOIN business_profiles bp ON u.id = bp.user_id 
              WHERE u.role = 'store' AND bp.is_public = 1";

            // Filtro por categoría
            if ($category && $category !== 'All') {
                $sql .= " AND bp.business_type = :category";
            }

            // Filtro por texto (búsqueda por nombre o ciudad)
            if ($searchQuery) {
                $sql .= " AND (u.business_name LIKE :search OR u.city LIKE :search)";
            }

            $sql .= " ORDER BY u.created_at DESC"; // Sin LIMIT

            $stmt = $this->conn->prepare($sql);

            if ($category && $category !== 'All') {
                $stmt->bindParam(':category', $category);
            }
            if ($searchQuery) {
                $term = "%" . $searchQuery . "%";
                $stmt->bindParam(':search', $term);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all stores: " . $e->getMessage());
            return [];
        }
    }


    // Búsqueda de tiendas por servicio, nombre o ubicación
    // Búsqueda de tiendas por servicio, nombre o ubicación - CON MEDIA Y CONTEO
    public function searchStores($searchTerm = null, $location = null, $category = null)
    {
        // Usamos GROUP BY en lugar de DISTINCT para poder usar funciones de agregación (AVG, COUNT)
        $query = "SELECT u.id, u.business_name, u.address, u.city, u.postal_code, u.created_at, 
                         bp.logo_url, bp.business_type, bp.description,
                         COALESCE(AVG(r.rating), 5) as avg_rating, 
                         COUNT(DISTINCT r.id) as review_count
                  FROM users u 
                  INNER JOIN business_profiles bp ON u.id = bp.user_id 
                  LEFT JOIN services s ON u.id = s.user_id 
                  LEFT JOIN reviews r ON bp.id = r.business_profile_id
                  WHERE u.role = 'store' AND bp.is_public = 1";

        $params = [];

        // Filtro por texto
        if (!empty($searchTerm)) {
            $query .= " AND (s.name LIKE :search OR u.business_name LIKE :business_search OR bp.business_type LIKE :type_search)";
            $params[':search'] = "%" . $searchTerm . "%";
            $params[':business_search'] = "%" . $searchTerm . "%";
            $params[':type_search'] = "%" . $searchTerm . "%";
        }

        // Filtro por ubicación
        if (!empty($location)) {
            $query .= " AND u.city LIKE :location";
            $params[':location'] = "%" . $location . "%";
        }

        // Filtro por categoría
        if (!empty($category) && $category !== 'All') {
            $query .= " AND bp.business_type = :category";
            $params[':category'] = $category;
        }

        // Agrupamos por ID de usuario para calcular la media única por tienda
        $query .= " GROUP BY u.id ORDER BY u.created_at DESC";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePassword($userId, $newHash)
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $newHash);
            $stmt->bindParam(':id', $userId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    public function getReviews($businessProfileId)
    {
        try {
            $query = "SELECT r.*, u.username 
                      FROM reviews r 
                      JOIN users u ON r.user_id = u.id 
                      WHERE r.business_profile_id = :pid 
                      ORDER BY r.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':pid', $businessProfileId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching reviews: " . $e->getMessage());
            return [];
        }
    }

}
?>