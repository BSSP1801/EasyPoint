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
    <link rel="stylesheet" href="public/css/styles.css">
</head>

<body>
    <div class="sticky-header">
    <div class="sticky-container">
        <div class="sticky-logo">EasyPoint</div>
        
        <div class="sticky-search-bar">
            <div class="search-field">
                <span class="search-icon">🔍</span>
                <input type="text" placeholder="Search services">
            </div>
            <div class="search-field border-left">
                <span class="search-icon">📍</span>
                <input type="text" placeholder="Where?">
            </div>
            <div class="search-field border-left">
                <span class="search-icon">🕒</span>
                <input type="text" placeholder="When?">
            </div>
            <button class="sticky-search-btn">Search</button>
        </div>

        <div class="sticky-menu">
            <a href="#" class="sticky-login">Log In/Sign Up</a>
            <a href="#" class="sticky-business-btn">List your business</a>
        </div>
    </div>
</div>
    <header>
    <nav class="navigation-bar">
        <div class="logo">EasyPoint</div>
        <div class="user-menu">
            <a href="#" class="login-link">Log In/Sign Up</a>
            <a href="#" class="business-button">List your business</a>
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
        </button>

        <div class="shops-grid">
            
            <article class="shop-card">
                <div class="image-container">
                    <img src="public/assets/images/tienda-1.png" alt="Barbershop" class="shop-image">
                    <div class="rating-label">
                        4.9
                        <span class="reviews-text">1271 reviews</span>
                    </div>
                </div>
                <div class="shop-info">
                    <h3 class="shop-name">Javier Garcia</h3>
                    <p class="shop-address">Calle puerta nueva numero 8, 30001, Murcia</p>
                    <div class="shop-actions">
                        <div class="action-icon"></div>
                    </div>
                    <span class="sponsored-text">Sponsored </span>
                </div>
            </article>

            <article class="shop-card">
                <div class="image-container">
                    <img src="public/assets/images/tienda-1.png" alt="Barbershop" class="shop-image">
                    <div class="rating-label">
                        4.9
                        <span class="reviews-text">965 reviews</span>
                    </div>
                </div>
                <div class="shop-info">
                    <h3 class="shop-name">Barbería ISMAEL AYALA</h3>
                    <p class="shop-address">Calle Medina, N77, 11402, Jerez de la Frontera</p>
                    <div class="shop-actions">
                        <div class="action-icon"></div>
                    </div>
                    <span class="sponsored-text">Sponsored </span>
                </div>
            </article>

            <article class="shop-card">
                <div class="image-container">
                    <img src="public/assets/images/tienda-1.png" alt="Barbershop" class="shop-image">
                    <div class="rating-label">
                        5.0
                        <span class="reviews-text">497 reviews</span>
                    </div>
                </div>
                <div class="shop-info">
                    <h3 class="shop-name">Traditional BarberShop</h3>
                    <p class="shop-address">Carrer de Salvador Baroné, 86, 08840</p>
                    <div class="shop-actions">
                        <div class="action-icon"></div>
                    </div>
                    <span class="sponsored-text">Sponsored </span>
                </div>
            </article>

            <article class="shop-card">
                <div class="image-container">
                    <img src="public/assets/images/tienda-1.png" alt="Barbershop" class="shop-image">
                    <div class="rating-label">
                        4.8
                        <span class="reviews-text">531 reviews</span>
                    </div>
                </div>
                <div class="shop-info">
                    <h3 class="shop-name">Mr Mostacho ALCOY</h3>
                    <p class="shop-address">Avenida de la Alameda, 2, 03803, Alcoy</p>
                    <div class="shop-actions">
                        <div class="action-icon"></div>
                    </div>
                    <span class="sponsored-text">Sponsored </span>
                </div>
            </article>

            <article class="shop-card">
                <div class="image-container">
                    <img src="public/assets/images/tienda-1.png" alt="Barbershop" class="shop-image">
                    <div class="rating-label">
                        4.7
                        <span class="reviews-text">320 reviews</span>
                    </div>
                </div>
                <div class="shop-info">
                    <h3 class="shop-name">Lafuen Estilistas</h3>
                    <p class="shop-address">Calle ribalta, 23, Madrid</p>
                    <div class="shop-actions">
                        <div class="action-icon"></div>
                    </div>
                    <span class="sponsored-text">Sponsored </span>
                </div>
            </article>

        </div>

        <button class="arrow-button right-arrow">
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
                Navigate through our platform to discover the finest health and beauty businesses available on EasyPoint. We curate the best local professionals to ensure high-quality service.
            </p>
            <p class="features-text">
                Check out business profiles and read verified reviews from other users to make an informed decision. You can also explore their portfolios to see the real results of their work before you book.
            </p>
            <p class="features-text">
                Save time and leave the stress behind. With EasyPoint, booking your next beauty appointment is free, easy, and fast, giving you more time to focus on yourself.
            </p>
        </div>
    </div>
</section>



<div id="auth-modal" class="modal-overlay">
    <div class="modal-box">
        <span class="close-modal">&times;</span>
        
        <div id="login-view">
            <h2 class="modal-title">Welcome Back</h2>
            <p class="modal-subtitle">Log in to book your next appointment</p>
            <form action="index.php?action=login" method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required class="modal-input">
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
            <form action="index.php?action=register" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required class="modal-input">
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

<script src="public/js/script.js"></script>
</body>
</html>