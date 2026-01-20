<?php
// public/index.php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/controllers/UserController.php';

session_start();

$action = $_GET['action'] ?? 'home';
$controller = new UserController();

// --- LOGIC SECTION: Process actions before any HTML is sent ---
// This allows header("Location: ...") to work correctly
if ($action === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Check if we are processing a form (POST) to handle redirects internally
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $controller->login();
        exit(); // The controller handles its own redirect or view
    }
    if ($action === 'register') {
        $controller->register();
        exit();
    }
    if ($action === 'home' || $action === '') {
        if (isset($_SESSION['user_id'])) {
            $controller->dashboard();
            exit();
        }
    }
}

// --- VIEW SECTION: Start HTML output after all logic is done ---
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint - Appointment System</title>
    <link rel="stylesheet" href="css/temp.css">
</head>

<body>
    <nav>
    <a href="index.php">Home</a> | 
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="index.php?action=dashboard" class="user-link">
            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </a> |
        <a href="index.php?action=logout">Logout</a>
    <?php else: ?>
        <a href="index.php?action=login">Login</a> |
        <a href="index.php?action=register">Register</a>
    <?php endif; ?>
</nav>
    <main>
        <?php
        switch ($action) {
            case 'register':
                $controller->register(); // If it's a GET request, it just shows the form
                break;
            case 'login':
                $controller->login(); // If it's a GET request, it just shows the form
                break;
            case 'dashboard':
                $controller->dashboard();
                break;
            default:
                echo "<h1>Welcome to EasyPoint</h1>";
                echo "<p>Start managing your appointments easily.</p>";
                break;
        }
        ?>
    </main>
</body>

</html>