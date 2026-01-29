<?php

// controllers/UserController.php
// Requirements: MVC Pattern and English comments
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../models/user.php';

class UserController
{

    public function register()
    {
        require_once __DIR__ . '/../models/user.php';
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $role = $_POST['role'] ?? 'user';
                $data = [
                    'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                    'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                    'password' => isset($_POST['password']) ? $_POST['password'] : '',
                    'role' => $role,
                    'business_name' => isset($_POST['business_name']) ? trim($_POST['business_name']) : null,
                    'address' => isset($_POST['address']) ? trim($_POST['address']) : null,
                    'postal_code' => isset($_POST['postal_code']) ? trim($_POST['postal_code']) : null
                ];

                if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                    throw new Exception("All common fields are required.");
                }

                $user = new User();
                $result = $user->create($data); // This will throw an exception if the user exists

                if ($result) {
                    // Database registration was successful, now we try to login for the session
                    $createdUser = $user->login($data['username'], $data['password']);

                    if ($createdUser) {
                        $_SESSION['user_id'] = $createdUser['id'];
                        $_SESSION['username'] = $createdUser['username'];
                        $_SESSION['role'] = $createdUser['role'];
                    }

                    // EMAIL SENDING ATTEMPT
                    try {
                        $msg = "<h1>Welcome to EasyPoint!</h1><p>Your account has been created successfully.</p>";
                        $this->sendEmail($data['email'], "Welcome to EasyPoint", $msg);
                    } catch (Exception $e) {
                        error_log("Email skipped due to network: " . $e->getMessage());
                    }
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => "Registration successful!", 'role' => $createdUser['role']]);
                        exit();
                    }

                    header("Location: index.php");
                    exit();
                }
            } catch (Exception $e) {
                // Here we capture errors like "user already exists" or empty fields
                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(400); // Bad Request
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                    exit();
                }
                // If it's not AJAX, you could set a variable for the view
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }

    public function login()
    {
        $error_message = "";
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

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

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'role' => $userData['role']]);
                        exit();
                    }
                    header("Location: index.php");
                    exit();
                    //  }
                } else {
                    $error_message = "Invalid username/email or password.";
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        http_response_code(401);
                        echo json_encode(['success' => false, 'message' => $error_message]);
                        exit();
                    }
                }
            } else {
                $error_message = "Please fill in all fields.";
                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $error_message]);
                    exit();
                }
            }
        }

    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }

        $userModel = new User();
        $userData = $userModel->getFullProfile($_SESSION['user_id']);

        // Make sure role is available in the view
        $userRole = $_SESSION['role'] ?? $userData['role'] ?? 'admin';

        require_once __DIR__ . '/../public/dashboard.php';
    }

  public function updateSchedule()
{
    // 1. Leer el JSON crudo enviado por fetch()
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($_SESSION['user_id']) && isset($data['schedule'])) {
        $userModel = new User();
        
        // 2. Convertir el array a string JSON para guardarlo en la DB
        $scheduleString = json_encode($data['schedule']);

        if ($userModel->saveOpeningHours($_SESSION['user_id'], $scheduleString)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
        }
        exit();
    }
}


    // public function dashboard() {
    //     if (!isset($_SESSION['user_id'])) {
    //         header("Location: index.php?action=login");
    //         exit();
    //     }

    //     $userModel = new User();
    //     // CHANGE: We use the new method that brings data from both tables
    //     $userData = $userModel->getFullProfile($_SESSION['user_id']);

    //     require_once __DIR__ . '/../views/dashboard.php';
    // }

public function updateBusinessInfo()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
        $userModel = new User(); // Definimos el modelo al inicio
        $uploadDir = __DIR__ . '/../public/assets/uploads/';
        
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $handleUpload = function ($fileKey) use ($uploadDir) {
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
                $filename = uniqid('img_') . '.' . $ext;
                if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadDir . $filename)) {
                    return 'assets/uploads/' . $filename;
                }
            }
            return null;
        };

        try {
          $data = [
    'business_name' => $_POST['business_name'] ?? '',
    'phone'         => $_POST['phone'] ?? '',
    'address'       => $_POST['address'] ?? '',
    'city'          => $_POST['city'] ?? '',
    'postal_code'   => $_POST['postal_code'] ?? '',
    'description'   => $_POST['description'] ?? '',
    'is_public'     => isset($_POST['is_public']) ? 1 : 0, 
    'logo_url'      => $handleUpload('logo'),
    'banner_url'    => $handleUpload('banner')
];

            // 1. Actualizar perfil base
            $userModel->updateBusinessProfile($_SESSION['user_id'], $data);

            // 2. Procesar Galería (Gallery)
            if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                $galleryPaths = [];
                foreach ($_FILES['gallery']['name'] as $key => $val) {
                    if ($_FILES['gallery']['error'][$key] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($_FILES['gallery']['name'][$key], PATHINFO_EXTENSION);
                        $filename = uniqid('gal_') . '.' . $ext;
                        if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $uploadDir . $filename)) {
                            $galleryPaths[] = 'assets/uploads/' . $filename;
                        }
                    }
                }

                if (!empty($galleryPaths)) {
                    // Obtenemos el profile_id necesario para la tabla business_gallery
                    $profile = $userModel->getFullProfile($_SESSION['user_id']);
                    if (isset($profile['profile_id'])) {
                        $userModel->addGalleryImages($profile['profile_id'], $galleryPaths);
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        }
    }
}


   public function viewBusiness() {
    $businessId = $_GET['id'] ?? null;
    if (!$businessId) { header("Location: index.php"); exit(); }

    $userModel = new User();
    // 1. Obtenemos los datos del perfil
    $businessData = $userModel->getFullProfile($businessId);

    if (!$businessData) { header("Location: index.php"); exit(); }

    // 2. Obtenemos las imágenes de la galería específicamente
    $galleryImages = $userModel->getBusinessGallery($businessData['user_id']);

    // 3. Cargamos la vista (las variables $businessData y $galleryImages estarán disponibles allí)
    require_once __DIR__ . '/../views/business-service.php';
}


    // Function to send email using PHPMailer
    private function sendEmail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
         
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io'; 
        $mail->SMTPAuth = true;
        $mail->Port = 2525; 
        $mail->Username = '83b8fc135d6989'; 
        $mail->Password = 'f5a90f6cf9f62a'; 
        
        $mail->Timeout = 3; // seting a timeout of 3 seconds
        
        $mail->setFrom('support@easypoint.com', 'EasyPoint Support');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
        } catch (Exception $e) {
            // We save the error in the log but allow the user to see their success on the web
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}


