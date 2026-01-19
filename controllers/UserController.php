<?php
// controllers/UserController.php
// Requirements: MVC Pattern and English comments

require_once __DIR__ . '/../models/user.php';

class UserController {
    
   public function register() {
    $error_message = "";
    $success_message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($email) || empty($password)) {
                throw new Exception("All fields are required.");
            }

            $user = new User();
            if ($user->create($username, $email, $password)) {
                $success_message = "User registered successfully! Please check your email.";
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
    // Pass variables to the view
    require_once __DIR__ . '/../views/register.php';
}
}