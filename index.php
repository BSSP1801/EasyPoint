<?php
// public/index.php
// Main entry point for the EasyPoint application

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/controllers/UserController.php';

session_start();

$action = $_GET['action'] ?? 'home';
$controller = new UserController();

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
        <a href="index.php?action=register">Register</a>
    </nav>

    <main>
        <?php
        // Simple routing
        switch ($action) {
            case 'register':
                $controller->register();
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