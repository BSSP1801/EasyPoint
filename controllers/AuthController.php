<?php
// controllers/AuthController.php

session_start();

require_once __DIR__ . '/../models/user.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($action === 'login') {
        handleLogin($input);
    } elseif ($action === 'register') {
        handleRegister($input);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

function handleLogin($input) {
    if (!isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password required']);
        return;
    }

    $email = trim($input['email']);
    $password = $input['password'];

    $userModel = new User();
    $userData = $userModel->login($email, $password);

    if ($userData) {
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['role'] = $userData['role'];

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'role' => $userData['role']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
}

function handleRegister($input) {
    if (!isset($input['username']) || !isset($input['email']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username, email, and password required']);
        return;
    }

    $username = trim($input['username']);
    $email = trim($input['email']);
    $password = $input['password'];

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        return;
    }

    $userModel = new User();
    
    $data = [
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'role' => 'user'
    ];

    try {
        if ($userModel->create($data)) {
            // Auto-login after registration
            $userData = $userModel->login($email, $password);
            if ($userData) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['role'] = $userData['role'];
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error registering user']);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
