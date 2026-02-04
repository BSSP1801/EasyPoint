<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/models/Service.php';

session_start();

$action = $_GET['action'] ?? 'home';
$controller = new UserController();
$userModel = new User();

$stores = [];

if ($action === 'home') {
    $searchTerm = $_GET['q'] ?? null;
    $locationTerm = $_GET['loc'] ?? null;
    $categoryFilter = $_GET['category'] ?? null;

    if ($searchTerm || $locationTerm) {
        $stores = $userModel->searchStores($searchTerm, $locationTerm);
    } else {
        $stores = $userModel->getRecommendedStores($categoryFilter);
    }
}

switch ($action) {
    case 'search':
        $controller->search();
        exit();
    case 'register':
        $controller->register();
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
        require_once __DIR__ . '/views/book-service.php';
        exit();
    case 'add_service':
        $controller->addService();
        exit();
    case 'delete_service':
        $controller->deleteService();
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
    case 'logout':
        session_destroy();
        $_SESSION = array();
        header("Location: index.php");
        exit();
}

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

<body>
    <div class="sticky-header">
        <div class="sticky-container">
            <div class="sticky-logo"><a href="/index.php">EasyPoint</a></div>

            <form action="index.php" method="GET" class="sticky-search-bar">
                <input type="hidden" name="action" value="search">
                <div class="search-field">
                    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" placeholder="Search services">
                </div>
                <div class="search-field border-left">
                    <span class="search-icon"><i class="fa-solid fa-location-dot"></i></span>
                    <input type="text" name="loc" placeholder="Where?">
                </div>
                <button type="submit" class="sticky-search-btn">Search</button>
            </form>

            <div class="sticky-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>

                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>

                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>

                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>

                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>

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

                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>

                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>

                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="central-content">
            <h1 class="main-title">Believe in yourself</h1>
            <p class="subtitle">Discover and book an appointment with beauty and wellness professionals near you</p>

            <form action="index.php" method="GET" class="hero-search-bar" style="
    background-color: rgba(235, 230, 210, 0.1); 
    border: 1px solid rgba(165, 134, 104, 0.3);
    border-radius: 50px; 
    padding: 5px; 
    display: flex; 
    align-items: center; 
    box-shadow: 0 4px 20px rgba(0,0,0,0.2); 
    max-width: 750px; 
    margin: 30px auto; 
    backdrop-filter: blur(5px);">

                <input type="hidden" name="action" value="search">

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 10px 20px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 15px; font-size: 18px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" placeholder="Search services"
                        value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 16px; background: transparent; color: #ebe6d2;">
                </div>

                <div style="width: 1px; height: 30px; background-color: rgba(165, 134, 104, 0.3);"></div>

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 10px 20px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 15px; font-size: 18px;">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <input type="text" name="loc" placeholder="Where?"
                        value="<?php echo htmlspecialchars($_GET['loc'] ?? ''); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 16px; background: transparent; color: #ebe6d2;">
                </div>

                <button type="submit" class="sticky-search-btn" style="
        background-color: #a58668; 
        color: #2b201e; 
        border: none; 
        padding: 12px 30px; 
        border-radius: 30px; 
        cursor: pointer; 
        font-weight: bold; 
        margin-left: 5px; 
        font-size: 16px;">
                    Search
                </button>
            </form>
            <ul class="category-list">
                <li><a href="index.php?action=view_all_stores&category=Hair Salon" class="cat-link">Hair Salon</a></li>
                <li><a href="index.php?action=view_all_stores&category=Barbershop" class="cat-link">Barbershop</a></li>
                <li><a href="index.php?action=view_all_stores&category=Nail Salon" class="cat-link">Nail Salon</a></li>
                <li><a href="index.php?action=view_all_stores&category=Hair Removal" class="cat-link">Hair Removal</a>
                </li>
                <li><a href="index.php?action=view_all_stores&category=Eyebrows & Lashes" class="cat-link">Eyebrows &
                        Lashes</a></li>
                <li><a href="index.php?action=view_all_stores&category=Skincare" class="cat-link">Skincare</a></li>
                <li><a href="index.php?action=view_all_stores&category=Massage" class="cat-link">Massage</a></li>
                <li><a href="index.php?action=view_all_stores&category=Makeup" class="cat-link">Makeup</a></li>
                <?php if (isset($_GET['category'])): ?>
                    <li><a href="index.php?action=view_all_stores" class="cat-link" style="color: #d9534f;">Clear
                            Filters</a></li>
                <?php else: ?>
                    <li><a href="index.php?action=view_all_stores" class="cat-link" style="font-weight: bold;">View All
                            Stores</a></li>
                <?php endif; ?>

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
                        $name = !empty($store['business_name']) ? htmlspecialchars($store['business_name']) : 'Unnamed Business';

                        $addressParts = [];
                        if (!empty($store['address']))
                            $addressParts[] = htmlspecialchars($store['address']);
                        if (!empty($store['postal_code']))
                            $addressParts[] = htmlspecialchars($store['postal_code']);
                        if (!empty($store['city']))
                            $addressParts[] = htmlspecialchars($store['city']);
                        $fullAddress = implode(', ', $addressParts);

                        $image = !empty($store['logo_url']) ? 'public/' . htmlspecialchars($store['logo_url']) : 'public/assets/images/tienda-1.png';
                        ?>

                        <a href="index.php?action=view_business&id=<?php echo $store['id']; ?>"
                            style="text-decoration: none; color: inherit;">
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