<?php
// controllers/BookingController.php

require_once __DIR__ . '/../models/Database.php';

class BookingController {
    
    public static function create() {
        // Check if user is logged in
        header('Content-Type: application/json');
        
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

    public static function getBookedSlots() {
        // Get booked time slots for a service
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['service_id']) || !isset($input['date'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing service_id or date']);
            exit();
        }

        $serviceId = intval($input['service_id']);
        $date = $input['date']; // Format: YYYY-MM-DD

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid date format']);
            exit();
        }

        $db = new Database();
        $conn = $db->getConnection();

        try {
            $stmt = $conn->prepare("
                SELECT appointment_time 
                FROM appointments 
                WHERE service_id = :service_id 
                AND appointment_date = :appointment_date
                AND status IN ('pending', 'confirmed')
                ORDER BY appointment_time ASC
            ");

            $stmt->execute([
                ':service_id' => $serviceId,
                ':appointment_date' => $date
            ]);

            $booked = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $booked[] = $row['appointment_time'];
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'booked_times' => $booked
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching booked slots: ' . $e->getMessage()]);
        }
    }
}
?>
