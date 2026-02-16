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
 <?php include "views/sticky-header.php"; ?>

    <header>
         <?php include "views/navigation-bar.php"; ?>
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

 <!-- FOOTER -->
    <?php include "views/footer.php" ?>

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