<?php
switch ($action) {
    case 'company':
        require_once __DIR__ . '/company.php';
        exit();
    case 'business':
        require_once __DIR__ . '/business.php';
        exit();
    case 'legal':
        require_once __DIR__ . '/legal.php';
        exit();
    case 'search':
        $controller->search();
        exit();
    case 'register':
        $controller->register();
        exit();
    case 'confirm':
        $controller->confirm();
        exit();
    case 'login':
        $controller->login();
        exit();
    case 'dashboard':
        $controller->dashboard();
        exit();
    case 'update_schedule':
        $controller->updateSchedule();
        exit();
    case 'update_business_info':
        $controller->updateBusinessInfo();
        exit();
    case 'view_business':
        $controller->viewBusiness();
        exit();
    case 'book':
        $service_id = $_GET['service_id'] ?? null;
        $store_id = $_GET['store_id'] ?? null;
        if (!$service_id || !$store_id) {
            header("Location: index.php");
            exit();
        }
        require_once __DIR__ . '/book-service.php';
        exit();
    case 'add_service':
        $controller->addService();
        exit();
    case 'delete_service':
        $controller->deleteService();
        exit();
    case 'add_review':
        header('Content-Type: application/json');
        ReviewController::addReview();
        exit();
    case 'change_status':
        $controller->changeStatus();
        exit();
    case 'search_client_history':
        $controller->searchClientHistory();
        exit();
    case 'view_all_stores':
        $controller->viewAllStores();
        exit();
    case 'change_password':
        $controller->changePassword();
    case 'get-booked-slots':
        header('Content-Type: application/json');
        BookingController::getBookedSlots();
        exit();
    case 'create-appointment':
        header('Content-Type: application/json');
        BookingController::create();
        exit();
        case 'forgot_password':
        $controller->forgotPassword();
        exit();
    case 'reset_password_view':
        $controller->resetPasswordView();
        exit();
    case 'reset_password_action':
        $controller->resetPasswordAction();
        exit();
    case 'logout':
        session_destroy();
        $_SESSION = array();
        header("Location: index.php");
        exit();
}

?>