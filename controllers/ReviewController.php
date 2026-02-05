<?php
require_once __DIR__ . '/../models/Database.php';

class ReviewController {
    
    public static function addReview() {
        // Verificar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'No autorizado']);
            return;
        }

        // Obtener datos JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['rating']) || !isset($input['business_id'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }

        $db = new Database();
        $conn = $db->getConnection();

        try {
            $stmt = $conn->prepare("INSERT INTO reviews (user_id, business_profile_id, rating, comment) 
                                    VALUES (:uid, :bid, :rating, :comment)");
            
            $success = $stmt->execute([
                ':uid' => $_SESSION['user_id'],
                ':bid' => $input['business_id'],
                ':rating' => $input['rating'],
                ':comment' => $input['comment'] ?? ''
            ]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            // En producción no mostrar $e->getMessage() al usuario
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
?>