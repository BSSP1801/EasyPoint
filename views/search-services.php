<?php
// views/search-services.php

$searchTerm = $_GET['q'] ?? '';
$locationTerm = $_GET['loc'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/styles-search-services.css">
    <link rel="icon" type="image/svg+xml" href="public/assets/images/favicon.svg">
</head>

<body>

    <div class="sticky-header">
        <div class="sticky-container">
            <div class="sticky-logo"><a href="/index.php">EasyPoint</a></div>

            <form action="index.php" method="GET" class="sticky-search-bar">
                <input type="hidden" name="action" value="search">

                <div class="search-field">
                    <span class="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" placeholder="Search services"
                        value="<?php echo htmlspecialchars($searchTerm); ?>">
                </div>
                <div class="search-field border-left">
                    <span class="search-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <input type="text" name="loc" placeholder="Where?"
                        value="<?php echo htmlspecialchars($locationTerm); ?>">
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
                            <a href="../index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="../index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
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
                            <a href="../index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="../index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../index.php?action=login" class="login-link" onclick="openLoginModal(event)">Log In/Sign
                        Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <header>
        <nav class="navigation-bar">
            <div class="logo"><a href="/index.php">EasyPoint</a></div>

            <form action="index.php" method="GET" class="hero-search-bar" style="
                background-color: rgba(235, 230, 210, 0.1); 
                border: 1px solid rgba(165, 134, 104, 0.3);
                border-radius: 50px; 
                padding: 4px; 
                display: flex; 
                align-items: center; 
                box-shadow: 0 4px 20px rgba(0,0,0,0.2); 
                flex: 1;
                max-width: 600px;
                margin: 0 20px;
                backdrop-filter: blur(5px);">

                <input type="hidden" name="action" value="search">

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" placeholder="Search services"
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>

                <div style="width: 1px; height: 25px; background-color: rgba(165, 134, 104, 0.3);"></div>

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <input type="text" name="loc" placeholder="Where?"
                        value="<?php echo htmlspecialchars($locationTerm); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>

                <button type="submit" class="sticky-search-btn" style="
                    background-color: #a58668; 
                    color: #2b201e; 
                    border: none; 
                    padding: 8px 24px; 
                    border-radius: 30px; 
                    cursor: pointer; 
                    font-weight: bold; 
                    margin-left: 5px; 
                    font-size: 14px;">
                    Search
                </button>
            </form>

            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>

                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
                        </span>
                        <div class="dropdown-menu">
                            <a href="../index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="../index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
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
                            <a href="../index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="../index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../index.php?action=login" class="login-link" onclick="openLoginModal(event)">Log In/Sign
                        Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </nav>

        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">

        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">

        <ul class="category-list">
            <?php
            // Definimos las categorías
            $categories = [
                'Hair Salon',
                'Barbershop',
                'Nail Salon',
                'Hair Removal',
                'Eyebrows & Lashes',
                'Skincare',
                'Massage',
                'Makeup'
            ];

           
            ?>

            <?php foreach ($categories as $cat): ?>
                <?php
                // Clase activa si es la categoría actual
                $activeClass = ($categoryFilter === $cat) ? 'font-weight: bold; text-decoration: underline;' : '';
                ?>
                <li>
                    <a href="<?php echo buildUrl($cat); ?>" class="cat-link" style="<?php echo $activeClass; ?>">
                        <?php echo $cat; ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <?php if (!empty($categoryFilter)): ?>
                <li>
                    <a href="<?php echo buildUrl(null); ?>" class="cat-link" style="color: #d9534f;">
                        Clear Filters
                    </a>
                </li>
            <?php elseif (!empty($searchTerm) || !empty($locationTerm)): ?>
                <li>
                    <a href="index.php?action=search" class="cat-link" style="font-weight: bold;">
                        View All Stores
                    </a>
                </li>
            <?php endif; ?>
        </ul>

    </header>

    <div class="main-container">

        <div class="results-header">
            <?php
            $titleText = "All Services";
            if ($searchTerm && $locationTerm) {
                $titleText = "Results for \"$searchTerm\" in \"$locationTerm\"";
            } elseif ($searchTerm) {
                $titleText = "Results for \"$searchTerm\"";
            } elseif ($locationTerm) {
                $titleText = "Services in \"$locationTerm\"";
            } elseif ($categoryFilter) {
                $titleText = "Category: $categoryFilter";
            }
            ?>
            <h1><?php echo htmlspecialchars($titleText); ?></h1>
            <p class="results-count"><?php echo count($stores); ?> results found</p>
        </div>

        <?php if (empty($stores)): ?>
            <div class="no-results">
                <i class="fa-solid fa-store-slash"></i>
                <h2>No results found</h2>
                <p>Try adjusting your search terms or location.</p>
                <a href="index.php" class="back-home-btn">Back to Home</a>
            </div>
        <?php else: ?>
            <div class="results-grid">
                <?php foreach ($stores as $store): ?>
                    <?php
                    $name = !empty($store['business_name']) ? htmlspecialchars($store['business_name']) : 'Unnamed Business';
                    $addressParts = [];
                    if (!empty($store['address']))
                        $addressParts[] = htmlspecialchars($store['address']);
                    if (!empty($store['city']))
                        $addressParts[] = htmlspecialchars($store['city']);
                    $fullAddress = implode(', ', $addressParts);
                    $image = !empty($store['logo_url']) ? 'public/' . htmlspecialchars($store['logo_url']) : 'public/assets/images/tienda-1.png';
                    $type = !empty($store['business_type']) ? htmlspecialchars($store['business_type']) : 'Service';

                    // --- NUEVO: Lógica de puntuación ---
                    $ratingVal = isset($store['avg_rating']) ? number_format($store['avg_rating'], 1) : '5.0';
                    $reviewCount = isset($store['review_count']) ? $store['review_count'] : 0;
                    
                    // Si hay reseñas mostramos (12), si no "New"
                    $reviewText = ($reviewCount > 0) ? "($reviewCount)" : "New";
                    // -----------------------------------
                    ?>
                    <a href="index.php?action=view_business&id=<?php echo $store['id']; ?>" class="shop-card">
                        <div class="image-container">
                            <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" class="shop-image">
                            
                            <div class="rating-label">
                                <i class="fas fa-star" style="margin-right: 3px;"></i> 
                                <?php echo $ratingVal; ?> 
                                <span class="reviews-text" style="margin-left: 3px;">
                                    <?php echo $reviewText; ?>
                                </span>
                            </div>

                        </div>
                        <div class="shop-info">
                            <h3 class="shop-name"><?php echo $name; ?></h3>
                            <p class="shop-address">
                                <i class="fa-solid fa-location-dot"></i>
                                <?php echo $fullAddress ?: 'No address provided'; ?>
                            </p>
                            <span class="sponsored-text">
                                <?php echo $type; ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

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
            <p>© 2026 EasyPoint. All rights reserved.</p>
        </div>
    </footer>

    <div id="auth-modal" class="modal-overlay">
        <div class="modal-box">
            <span class="close-modal">×</span>

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
                style="position: absolute; top: 15px; right: 20px; font-size: 28px; font-weight: bold; color: #aaa; cursor: pointer;">×</span>

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