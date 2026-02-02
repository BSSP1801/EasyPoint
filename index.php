<?php
// public/index.php


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/models/Service.php';

session_start();

$action = $_GET['action'] ?? 'home';
$controller = new UserController();
$userModel = new User();
// --- LOGIC SECTION: Process actions before any HTML is sent ---
// This allows header("Location: ...") to work correctly
// Initialize an empty array for stores
$stores = [];
if ($action === 'home') {
    $stores = $userModel->getRecommendedStores();
}
//Switch case to handle different actions
switch ($action) {
    case 'register':
        $controller->register();
        exit();
        break;
    case 'login':
        $controller->login();
        exit();
        break;
    case 'dashboard':
        $controller->dashboard();
        exit();
        break;
    case 'update_schedule':
        $controller->updateSchedule();
        exit();
        break;
    case 'update_business_info':
        $controller->updateBusinessInfo();
        exit();
    case 'view_business':
        $controller->viewBusiness();
        exit();
    case 'view_business':
        $controller->viewBusiness();
        exit();
    case 'add_service':
        $controller->addService();
        exit();
    case 'delete_service':
        $controller->deleteService();
        exit();
    case 'logout':
        session_destroy();
        $_SESSION = array(); // Clear the session array
        header("Location: index.php");
        exit();
        break;
}

// --- VIEW SECTION: Start HTML output after all logic is done ---
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint - Appointment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<?php

?>

<body>
    <div class="sticky-header">
        <div class="sticky-container">
            <div class="sticky-logo"><a href="/index.php">EasyPoint</a></div>

            <div class="sticky-search-bar">
                <div class="search-field">
                    <span class="search-icon">🔍</span>
                    <input type="text" placeholder="Search services">
                </div>
                <div class="search-field border-left">
                    <span class="search-icon">📍</span>
                    <input type="text" placeholder="Where?">
                </div>
                <button class="sticky-search-btn">Search</button>
            </div>

            <div class="sticky-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <a href="index.php?action=dashboard" class="dashboard-link">Dashboard</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <a href="index.php?action=dashboard" class="dashboard-link">Dashboard</a> 
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <header>
        <nav class="navigation-bar">
            <div class="logo"><a href="/index.php">EasyPoint</a></div>
            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <a href="index.php?action=dashboard">
                        <span class="user-link">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span></a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <span class="user-link">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                    <a href="index.php?action=dashboard" class="dashboard-link">Dashboard</a>
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="central-content">
            <h1 class="main-title">Believe in yourself</h1>
            <p class="subtitle">Discover and book an appointment with beauty and wellness professionals near you</p>

            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Search services or businesses">
            </div>

            <ul class="category-list">
                <li>Hair Salon</li>
                <li>Barbershop</li>
                <li>Nail Salon</li>
                <li>Hair Removal</li>
                <li>Eyebrows & Lashes</li>
                <li>Skincare</li>
                <li>Massage</li>
                <li>Makeup</li>
            </ul>
        </div>
    </header>

    <section class="recommended-section">
        <h2 class="section-title">Recommended</h2>

        <div class="carousel-container">
            <button class="arrow-button left-arrow">
                <i class="fa-solid fa-arrow-left"></i>
            </button>

            <div class="shops-grid">
    <?php if (empty($stores)): ?>
        <p style="padding: 20px;">No stores available yet. Be the first to join!</p>
    <?php else: ?>
        <?php foreach ($stores as $store): ?>
            <?php 
                // Prepare data (no changes)
                $name = !empty($store['business_name']) ? htmlspecialchars($store['business_name']) : 'Unnamed Business';
                
                $addressParts = [];
                if (!empty($store['address'])) $addressParts[] = htmlspecialchars($store['address']);
                if (!empty($store['postal_code'])) $addressParts[] = htmlspecialchars($store['postal_code']);
                if (!empty($store['city'])) $addressParts[] = htmlspecialchars($store['city']);
                $fullAddress = implode(', ', $addressParts);
                
                $image = !empty($store['logo_url']) ? 'public/' . htmlspecialchars($store['logo_url']) : 'public/assets/images/tienda-1.png';
            ?>

            <a href="index.php?action=view_business&id=<?php echo $store['id']; ?>" style="text-decoration: none; color: inherit;">
                <article class="shop-card">
                    <div class="image-container">
                        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" class="shop-image">
                        <div class="rating-label">
                            5.0 <span class="reviews-text">New</span>
                        </div>
                    </div>
                    <div class="shop-info">
                        <h3 class="shop-name"><?php echo $name; ?></h3>
                        <p class="shop-address"><?php echo $fullAddress; ?></p>
                        <span class="sponsored-text">Recommended</span>
                    </div>
                </article>
            </a>

        <?php endforeach; ?>
    <?php endif; ?>
</div>

            <button class="arrow-button right-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </section>




    <section class="features-section">
        <div class="features-container">
            <div class="features-image-wrapper">
                <img src="public/assets/images/img-resource-1.jpeg" alt="Beauty Experience" class="features-image">
            </div>
            <div class="features-content">
                <h2 class="features-title">Book with top professionals near you</h2>
                <p class="features-text">
                    Navigate through our platform to discover the finest health and beauty businesses available on
                    EasyPoint. We curate the best local professionals to ensure high-quality service.
                </p>
                <p class="features-text">
                    Check out business profiles and read verified reviews from other users to make an informed decision.
                    You can also explore their portfolios to see the real results of their work before you book.
                </p>
                <p class="features-text">
                    Save time and leave the stress behind. With EasyPoint, booking your next beauty appointment is free,
                    easy, and fast, giving you more time to focus on yourself.
                </p>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="features-container">
            <div class="features-content">
                <h2 class="features-title">Discover and book with the best local talent</h2>

                <p class="features-text">
                    Explore our platform to find the highest-rated health and beauty businesses available on EasyPoint.
                    We carefully select top-tier professionals to ensure you receive only quality service.
                </p>

                <p class="features-text">
                    Make the right choice by checking out business profiles and reading verified reviews from real
                    clients. You can also browse their portfolios to see the results of their work before you commit.
                </p>

                <p class="features-text">
                    Save time and skip the stress. With EasyPoint, securing your next appointment is simple, free, and
                    instant, leaving you more time to focus on what matters: you.
                </p>
            </div>

            <div class="features-image-wrapper">
                <img src="public/assets/images/img-resource-2.jpeg" alt="Booking Illustration" class="features-image">
            </div>
        </div>
    </section>


    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h2 class="footer-logo">EasyPoint</h2>
                <p class="footer-desc">The easiest way to look and feel your best. Book appointments with top
                    professionals near you.</p>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

            <div class="footer-links-group">
                <div class="footer-column">
                    <h3>Company</h3>
                    <a href="#">About Us</a>
                    <a href="#">Careers</a>
                    <a href="#">Press</a>
                    <a href="#">Contact</a>
                </div>

                <div class="footer-column">
                    <h3>For Business</h3>
                    <a href="#">Partner with us</a>
                    <a href="#">Business App</a>
                    <a href="#">Support</a>
                </div>

                <div class="footer-column">
                    <h3>Legal</h3>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookies Settings</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 EasyPoint. All rights reserved.</p>
        </div>
    </footer>


    <div id="auth-modal" class="modal-overlay">
        <div class="modal-box">
            <span class="close-modal">&times;</span>

            <div id="login-view">
                <h2 class="modal-title">Welcome Back</h2>
                <p class="modal-subtitle">Log in to book your next appointment</p>
                <div id="login-error" style="color: red; margin-bottom: 10px; display: none;"></div>
                <form id="login-form">
                    <div class="form-group">
                        <label>Email or Username</label>
                        <input type="text" name="identifier" required class="modal-input">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required class="modal-input">
                    </div>
                    <button type="submit" class="modal-btn">Log In</button>
                </form>
                <div class="switch-form">
                    Don't have an account? <span id="go-to-register">Sign up</span>
                </div>
            </div>

            <div id="register-view" class="hidden">
                <h2 class="modal-title">Create Account</h2>
                <p class="modal-subtitle">Join EasyPoint today</p>
                <div id="register-error" style="color: red; margin-bottom: 10px; display: none;"></div>
                <div id="register-success" style="color: green; margin-bottom: 10px; display: none;"></div>
                <form id="register-form">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required class="modal-input">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required class="modal-input">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required class="modal-input">
                    </div>
                    <button type="submit" class="modal-btn">Sign Up</button>
                </form>
                <div class="switch-form">
                    Already have an account? <span id="go-to-login">Log In</span>
                </div>
            </div>
        </div>
    </div>

    <div id="store-modal" class="modal-overlay">
        <div class="modal-box">
            <span class="close-store-modal"
                style="position: absolute; top: 15px; right: 20px; font-size: 28px; font-weight: bold; color: #aaa; cursor: pointer;">&times;</span>

            <h2 class="modal-title">Register your Business</h2>
            <p class="modal-subtitle">List your store on EasyPoint</p>
            <div id="store-error" style="color: red; margin-bottom: 10px; display: none;"></div>
            <div id="store-success" style="color: green; margin-bottom: 10px; display: none;"></div>
            <form id="store-register-form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required class="modal-input">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required class="modal-input">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required class="modal-input">
                </div>
                <div class="form-group">
                    <label>Business Name</label>
                    <input type="text" name="business_name" required class="modal-input">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" required class="modal-input">
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" class="modal-input">
                </div>
                <button type="submit" class="modal-btn">Create Business Account</button>
            </form>
        </div>
    </div>

    <script src="public/js/script.js"></script>
</body>

</html>