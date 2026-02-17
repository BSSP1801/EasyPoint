<?php
// controllers/UserController.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/service.php'; // Asegurarse de incluir el modelo de servicio

class UserController
{
    // Registro de usuario
    public function register()
    {
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
                    throw new \Exception("All common fields are required.");
                }

                $user = new User();
                // Llamamos a create() UNA SOLA VEZ y obtenemos el token generado
                $token = $user->create($data);

                if ($token) {
                
                    // 3. Respuesta para AJAX o redirección normal
                    if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'message' => "Account created! Sending verification email...",
                        'token' => $token,           // <--- Enviamos el token al JS
                        'username' => $data['username'], // <--- Enviamos el nombre al JS
                        'email' => $data['email']    // <--- Enviamos el email al JS
                    ]);
                    exit();
                }

                    header("Location: index.php?msg=check_email");
                    exit();
                }

            } catch (\Exception $e) {
                if ($isAjax) {
                    if (ob_get_level() > 0)
                        ob_clean();

                    header('Content-Type: application/json');
                    http_response_code(400);

                    $errorMsg = $e->getMessage();
                    $field = null;

                    if (strpos($errorMsg, ':') !== false) {
                        list($field, $message) = explode(':', $errorMsg, 2);
                    } else {
                        $message = $errorMsg;
                    }

                    echo json_encode(['success' => false, 'message' => $message, 'field' => $field]);
                    exit();
                }
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
    public function confirm()
    {
        $token = $_GET['token'] ?? null;

        if ($token) {
            $userModel = new User();
            // Intentamos confirmar
            if ($userModel->confirmAccount($token)) {

                header("Location: index.php?confirmed=1");
            } else {
                // Token inválido o expirado
                header("Location: index.php?error=invalid_token");
            }
        } else {
            header("Location: index.php");
        }
        exit();
    }

    // Login
    public function login()
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = trim($_POST['identifier'] ?? '');
            $password = $_POST['password'] ?? '';

            if (!empty($identifier) && !empty($password)) {
                $userModel = new User();
                $userData = $userModel->login($identifier, $password);

                if ($userData) {
                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['role'] = $userData['role'];

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'role' => $userData['role']]);
                        exit();
                    }
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid username/email or password.";
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        http_response_code(401);
                        echo json_encode(['success' => false, 'message' => $error]);
                        exit();
                    }
                }
            } else {
                $error = "Please fill in all fields.";
                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $error]);
                    exit();
                }
            }
        }
    }

    // Cargar Vista Dashboard
    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }

        $userModel = new User();
        $userData = $userModel->getFullProfile($_SESSION['user_id']);
        $userRole = $_SESSION['role'] ?? $userData['role'] ?? 'user';

        require_once __DIR__ . '/../views/dashboard.php';
    }

    // Actualizar Horario (AJAX)
    public function updateSchedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['schedule'])) {
            $scheduleJson = json_encode($input['schedule']);
            $userModel = new User();

            if ($userModel->saveOpeningHours($_SESSION['user_id'], $scheduleJson)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Schedule saved successfully']);
                exit();
            }
        }

        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }

    // Actualizar Info de Negocio e Imágenes (AJAX)
    public function updateBusinessInfo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            $userModel = new User();
            $uploadDir = __DIR__ . '/../public/assets/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Helper para subidas únicas
            $handleUpload = function ($fileKey) use ($uploadDir) {
                if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                    $maxFileSize = 5 * 1024 * 1024; // 5MB
                    if ($_FILES[$fileKey]['size'] > $maxFileSize)
                        throw new Exception("File $fileKey too large.");

                    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    $mime = mime_content_type($_FILES[$fileKey]['tmp_name']);
                    if (!in_array($mime, $allowed))
                        throw new Exception("Invalid format for $fileKey.");

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
                    'phone' => $_POST['phone'] ?? '',
                    'address' => $_POST['address'] ?? '',
                    'city' => $_POST['city'] ?? '',
                    'postal_code' => $_POST['postal_code'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'business_type' => $_POST['business_type'] ?? 'General',
                    'is_public' => isset($_POST['is_public']) ? 1 : 0,
                    'website' => $_POST['website'] ?? '',
                    'instagram' => $_POST['instagram'] ?? '',
                    'facebook' => $_POST['facebook'] ?? '',
                    'twitter' => $_POST['twitter'] ?? '',
                    'tiktok' => $_POST['tiktok'] ?? '',
                    'logo_url' => $handleUpload('logo'),
                    'banner_url' => $handleUpload('banner')

                ];

                if (!$userModel->updateBusinessProfile($_SESSION['user_id'], $data)) {
                    throw new Exception("Database error updating profile.");
                }

                // Galería
                if (isset($_FILES['gallery'])) {
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
                        $profile = $userModel->getBusinessProfileByUserId($_SESSION['user_id']);
                        if ($profile && isset($profile['id'])) {
                            $userModel->addGalleryImages($profile['id'], $galleryPaths);
                        }
                    }
                }

                echo json_encode(['success' => true]);
                exit();

            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit();
            }
        }
    }

    // Añadir Servicio (AJAX)
    public function addService()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $serviceModel = new Service();
            $data = [
                'name' => $_POST['service_name'],
                'price' => $_POST['service_price'],
                'duration' => $_POST['service_duration']
            ];

            $serviceId = $serviceModel->create($_SESSION['user_id'], $data);

            if ($serviceId) {
                $data['id'] = $serviceId; // Retornar el ID para el frontend
                echo json_encode(['success' => true, 'service' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding service']);
            }
            exit();
        }
    }

    // Borrar Servicio (AJAX)
    public function deleteService()
    {
        header('Content-Type: application/json');
        if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
            $serviceModel = new Service();
            if ($serviceModel->delete($_GET['id'], $_SESSION['user_id'])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting service']);
            }
            exit();
        }
    }

    public function viewBusiness()
    {
        $businessId = $_GET['id'] ?? null;
        if (!$businessId) {
            header("Location: index.php");
            exit();
        }

        $userModel = new User();
        $businessData = $userModel->getFullProfile($businessId);
        $galleryImages = $userModel->getBusinessGallery($businessId);

        if (!$businessData) {
            header("Location: index.php");
            exit();
        }

        require_once __DIR__ . '/../views/business-service.php';
    }

    private function sendEmail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer: $str");
        };
            $mail->isSMTP();
            $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
           
            $mail->isSMTP();
            $mail->Host = 'smtp-relay.brevo.com';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->Username = 'brunosalcedo1801@gmail.com';
            $mail->Password = 'bskBRvreynkfOv3';
            $mail->Timeout = 10;
            $mail->setFrom('brunosalcedo1801@gmail.com', 'EasyPoint Support');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            return true;
        } catch (Exception $e) {
          error_log("Mailer Error: " . $mail->ErrorInfo); 
        return false;
        }
    }


    public function changeStatus()
    {
        header('Content-Type: application/json');

        // 1. Solo verificamos si la sesión está iniciada (sin bloquear el rol todavía)
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;

            // 2. Seguridad: Si es un cliente regular ('user'), SOLAMENTE puede cancelar citas
            if ($_SESSION['role'] === 'user' && $status !== 'cancelled') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized: Users can only cancel appointments']);
                exit();
            }

            // 3. Procesar si el estado es válido
            if ($id && $status && in_array($status, ['confirmed', 'cancelled'])) {
                $userModel = new User();
                $success = $userModel->updateAppointmentStatus($id, $status, $_SESSION['user_id']);

                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Could not update status']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
            }
            exit();
        }
    }
    public function searchClientHistory()
    {
        header('Content-Type: application/json');

        // ... verificaciones de sesión (igual que antes) ...
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'store') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $email = $_GET['email'] ?? '';
        $storeId = $_SESSION['user_id'];

        // Permitir búsquedas de al menos 3 caracteres para no sobrecargar
        if (strlen($email) < 3) {
            echo json_encode(['success' => false, 'message' => 'Type at least 3 characters']);
            exit();
        }

        $db = new Database();
        $conn = $db->getConnection();

        try {
            // CAMBIO PRINCIPAL: Usamos LIKE y concatenamos '%'
            $stmt = $conn->prepare("
            SELECT a.appointment_date, a.appointment_time, a.status, s.name as service_name, u.email, u.username
            FROM appointments a
            JOIN users u ON a.user_id = u.id
            JOIN services s ON a.service_id = s.id
            WHERE u.email LIKE :email AND s.user_id = :store_id
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");

            $searchTerm = "%" . $email . "%";

            $stmt->execute([
                ':email' => $searchTerm,
                ':store_id' => $storeId
            ]);

            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'appointments' => $appointments
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function search()
    {
        // Capturamos todos los filtros posibles
        $query = $_GET['q'] ?? '';
        $location = $_GET['loc'] ?? '';
        $category = $_GET['category'] ?? '';

        $userModel = new User();

        // Llamamos a la función unificada del modelo
        $stores = $userModel->searchStores($query, $location, $category);

        // Cargar la vista
        require_once __DIR__ . '/../views/search-services.php';
    }

    public function viewAllStores()
    {
        $this->search();
    }



    public function changePassword()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Validaciones básicas
        if (empty($current) || empty($new) || empty($confirm)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit();
        }

        if ($new !== $confirm) {
            echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
            exit();
        }

        if (strlen($new) < 6) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
            exit();
        }

        $userModel = new User();
        // Obtenemos el usuario actual para verificar su contraseña hash
        $user = $userModel->getById($_SESSION['user_id']);

        if (!$user || !password_verify($current, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Incorrect current password']);
            exit();
        }

        // Hasheamos la nueva contraseña y guardamos
        $newHash = password_hash($new, PASSWORD_BCRYPT);

        if ($userModel->updatePassword($_SESSION['user_id'], $newHash)) {
            echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        exit();
    }



public function forgotPassword() {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your email']);
            exit();
        }

        $userModel = new User();
        // Generamos un token seguro
        $token = bin2hex(random_bytes(32));

        // Guardamos el token en la base de datos
        if ($userModel->saveResetToken($email, $token)) {
            
            // Construimos el enlace de recuperación
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $resetLink = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/index.php?action=reset_password_view&token=" . $token;

            // --- CAMBIO CLAVE ---
            // Devolvemos el link y el email al JavaScript para que EmailJS los use.
            echo json_encode([
                'success' => true, 
                'message' => 'Token generated',
                'reset_link' => $resetLink, // Dato para EmailJS
                'email' => $email           // Dato para EmailJS (Evita el error "recipients address empty")
            ]);
        } else {
            // Si el email no existe, enviamos un falso éxito por seguridad, 
            // pero con una marca para que el JS sepa qué hacer (o no hacer nada).
            echo json_encode(['success' => true, 'fake_success' => true]);
        }
        exit();
    }
}

// 2. Mostrar vista de cambio de contraseña (GET)
public function resetPasswordView() {
    $token = $_GET['token'] ?? null;
    $userModel = new User();
    
    // Validar token antes de cargar la vista
    if (!$token || !$userModel->getUserByResetToken($token)) {
        header("Location: index.php?error=invalid_token");
        exit();
    }
    
    // Cargar una vista específica para poner la nueva pass
    require_once __DIR__ . '/../views/reset-password.php';
}

// 3. Procesar el cambio de contraseña (POST)
public function resetPasswordAction() {
    header('Content-Type: application/json');
    
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirm) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password too short']);
        exit();
    }

    $userModel = new User();
    $user = $userModel->getUserByResetToken($token);
    
    if ($user) {
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        if ($userModel->updatePasswordByToken($user['id'], $newHash)) {
            echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
    }
    exit();
}


}
?>