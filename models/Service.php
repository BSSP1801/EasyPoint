<?php
require_once __DIR__ . '/Database.php';

class Service
{
    private $conn;
    private $table_name = "services";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId, $data)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, name, price, duration) VALUES (:uid, :name, :price, :duration)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":uid", $userId);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":price", $data['price']);
        $stmt->bindParam(":duration", $data['duration']);

        return $stmt->execute();
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAllByUserId($userId)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :uid ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($serviceId, $userId)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $serviceId);
        $stmt->bindParam(":uid", $userId);
        return $stmt->execute();
    }

        public function addService()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $serviceModel = new Service();
            $data = [
                'name' => $_POST['service_name'],
                'price' => $_POST['service_price'],
                'duration' => $_POST['service_duration']
            ];
            
            $serviceModel->create($_SESSION['user_id'], $data);
            header("Location: index.php?action=dashboard"); 
            exit();
        }
    }

    public function deleteService()
    {
        if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
            $serviceModel = new Service();
            $serviceModel->delete($_GET['id'], $_SESSION['user_id']);
            header("Location: index.php?action=dashboard");
            exit();
        }
    }

    public function getServiceById($serviceId)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $serviceId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

