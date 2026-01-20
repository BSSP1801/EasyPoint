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
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
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

</body>
</html>