<?php
// controllers/UserController.php
// Requirements: MVC Pattern and English comments

require_once __DIR__ . '/../models/user.php';

class UserController
{

    public function register()
    {
        $error_message = "";
        $success_message = "";
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get role from POST or set default to 'user'
                $role = $_POST['role'] ?? 'user';

                // Common fields - safely get and trim values
                $data = [
                    'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                    'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                    'password' => isset($_POST['password']) ? $_POST['password'] : '',
                    'role' => $role,
                    // Store specific fields (will be null for 'user' role)
                    'business_name' => isset($_POST['business_name']) ? trim($_POST['business_name']) : null,
                    'address' => isset($_POST['address']) ? trim($_POST['address']) : null,
                    'postal_code' => isset($_POST['postal_code']) ? trim($_POST['postal_code']) : null
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

                error_log("Attempting to create user with role: " . $data['role']);

                $user = new User();
                // Important: Pass the whole $data array to the model
                $result = $user->create($data);

                error_log("Create result: " . ($result ? "true" : "false"));

                if ($result) {
                    // Get the created user data to set session
                    $createdUser = $user->login($data['username'], $data['password']);

                    if ($createdUser) {
                        // Automatically set session after registration
                        $_SESSION['user_id'] = $createdUser['id'];
                        $_SESSION['username'] = $createdUser['username'];
                        $_SESSION['role'] = $createdUser['role'];
                    }

                    $success_message = "Registration successful!";

                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => true, 'message' => $success_message, 'role' => $createdUser['role'] ?? null]);
                        exit();
                    }

                    header("Location: index.php");
                    exit();
                } else {
                    throw new Exception("Failed to create user. Database operation returned false.");
                }
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                error_log("Registration error: " . $error_message);

                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => $error_message]);
                    exit();
                }
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
        // 1. Seguridad: Verificar método POST y sesión activa
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        // 2. Obtener el JSON enviado por JS
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['schedule'])) {
            // Convertimos el array de PHP de vuelta a string JSON para guardarlo en la BBDD
            $scheduleJson = json_encode($input['schedule']);

            $userModel = new User();

            if ($userModel->saveOpeningHours($_SESSION['user_id'], $scheduleJson)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Schedule saved successfully']);
                exit();
            }
        }

        // Si algo falla
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }



    // public function dashboard() {
    //     if (!isset($_SESSION['user_id'])) {
    //         header("Location: index.php?action=login");
    //         exit();
    //     }

    //     $userModel = new User();
    //     // CAMBIO: Usamos el nuevo método que trae datos de ambas tablas
    //     $userData = $userModel->getFullProfile($_SESSION['user_id']);

    //     require_once __DIR__ . '/../views/dashboard.php';
    // }

    public function updateBusinessInfo()
    {
        // CORRECCIÓN: Eliminamos la carga de la vista y del modelo aquí arriba.
        // Solo procesamos si es POST y hay sesión.

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {

            // Recoger datos de texto
            $data = [
                'business_name' => $_POST['business_name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'postal_code' => $_POST['postal_code'] ?? '',
                'description' => $_POST['description'] ?? '',
                'is_public' => isset($_POST['is_public']) ? 1 : 0,
                'logo_url' => null,
                'banner_url' => null
            ];

            // Manejo de subida de archivos (Imágenes)
            $uploadDir = __DIR__ . '/../public/assets/uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0777, true);

            // Función auxiliar para subir ficheros
            $handleUpload = function ($fileKey) use ($uploadDir) {
                if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {

                    // 1. Validar tamaño del archivo (Ejemplo: 2MB máximo)
                    $maxFileSize = 2 * 1024 * 1024; // 2 Megabytes
                    if ($_FILES[$fileKey]['size'] > $maxFileSize) {
                        throw new Exception("El archivo es demasiado grande (Máximo 2MB).");
                    }

                    // 2. Validar dimensiones (Opcional, requiere extensión GD activa)
                    $imageInfo = getimagesize($_FILES[$fileKey]['tmp_name']);
                    $maxWidth = 1200;
                    $maxHeight = 1200;
                    if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
                        throw new Exception("La imagen excede las dimensiones permitidas (Máximo 1200x1200px).");
                    }

                    // 3. Validar tipo de archivo
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!in_array($imageInfo['mime'], $allowedTypes)) {
                        throw new Exception("Formato de imagen no permitido (Usa JPG, PNG o WebP).");
                    }

                    $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('img_') . '.' . $ext;

                    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $uploadDir . $filename)) {
                        return 'assets/uploads/' . $filename;
                    }
                }
                return null;
            };

            try {
                $data['logo_url'] = $handleUpload('logo');
                $data['banner_url'] = $handleUpload('banner');
                $userModel = new User();
                // Llamamos al modelo
                if ($userModel->updateBusinessProfile($_SESSION['user_id'], $data)) {
                    // Devolvemos SOLO JSON limpio
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Database error']);
                }
                exit(); // Importante: Detener ejecución aquí

            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit();
            }


        }
    }

}