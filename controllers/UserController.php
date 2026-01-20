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
            // Common fields
            $data = [
                'username'      => trim($_POST['username'] ?? ''),
                'email'         => trim($_POST['email'] ?? ''),
                'password'      => $_POST['password'] ?? '',
                'role'          => $_POST['role'] ?? 'user',
                // Store specific fields (will be null for 'user' role)
                'business_name' => trim($_POST['business_name'] ?? null),
                'address'       => trim($_POST['address'] ?? null),
                'postal_code'   => trim($_POST['postal_code'] ?? null)
            ];

            // Basic validation for common fields
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception("All common fields are required.");
            }

            // Specific validation if the role is 'store'
            if ($data['role'] === 'store') {
                if (empty($data['business_name']) || empty($data['address'])) {
                    throw new Exception("Business name and Address are required for stores.");
                }
            } else {
                // Ensure store fields are null if it's a regular user
                $data['business_name'] = $data['address'] = $data['postal_code'] = null;
            }

            $user = new User();
            // Important: Pass the whole $data array to the model
            if ($user->create($data)) {
                $success_message = "Registration as " . $data['role'] . " successful! Please check your email.";
                header("Location: index.php?action=login");
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
    // Pass variables to the view
    require_once __DIR__ . '/../views/register.php';
}

public function login() {
    $error_message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($identifier) && !empty($password)) {
            $userModel = new User();
            $userData = $userModel->login($identifier, $password);

            if ($userData) {
                // Check if account is confirmed (Requirement DWES Obj. 7)
             //   if ($userData['is_confirmed'] == 0) {
             //       $error_message = "Please confirm your account via email first."; uncomment when email confirmation is implemented
             //   } else {
                    // Set session variables
                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['role'] = $userData['role']; // admin or user
                    
                   header("Location: index.php");
                    exit();
              //  }
            } else {
                $error_message = "Invalid username/email or password.";
            }
        } else {
            $error_message = "Please fill in all fields.";
        }
    }
    require_once __DIR__ . '/../views/login.php';
}

public function dashboard() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?action=login");
        exit();
    }

    $userModel = new User();
    $userData = $userModel->getById($_SESSION['user_id']);

    require_once __DIR__ . '/../views/dashboard.php';
}


}