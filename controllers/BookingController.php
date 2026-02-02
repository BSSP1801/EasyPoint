<?php
// controllers/BookingController.php

session_start();

require_once __DIR__ . '/../models/Database.php';

class BookingController {
    
    public static function create() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            exit();
        }

        // Check if request is JSON
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit();
        }

        // Validate input
        $serviceId = intval($input['service_id'] ?? 0);
        $appointmentDate = $input['appointment_date'] ?? '';
        $appointmentTime = $input['appointment_time'] ?? '';

        if (!$serviceId || !$appointmentDate || !$appointmentTime) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit();
        }

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid date format']);
            exit();
        }

        // Validate time format
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $appointmentTime)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid time format']);
            exit();
        }

        // Create appointment in database
        $db = new Database();
        $conn = $db->getConnection();

        try {
            $stmt = $conn->prepare("
                INSERT INTO appointments (user_id, service_id, appointment_date, appointment_time, status)
                VALUES (:user_id, :service_id, :appointment_date, :appointment_time, :status)
            ");

            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':service_id' => $serviceId,
                ':appointment_date' => $appointmentDate,
                ':appointment_time' => $appointmentTime,
                ':status' => 'pending'
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Appointment created successfully'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error creating appointment: ' . $e->getMessage()]);
        }
    }
}

// Handle the request
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    BookingController::create();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
