<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/service.php';
require_once __DIR__ . '/../models/user.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($businessData)) {
    $userData = $businessData;
} else {
    header("Location: ../index.php");
    exit();
}

$storeId = $_GET['id'];
$userModel = new User();

$store = $userModel->getFullProfile($storeId);

if (!$store) {
    header("Location: ../index.php");
    exit();
}

$serviceModel = new Service();
$storeServices = $serviceModel->getAllByUserId($storeId);
$targetUserId = $userData['user_id'] ?? $userData['id'];

$logoUrl = !empty($store['logo_url'])
    ? '../public/' . htmlspecialchars($store['logo_url'])
    : '../public/assets/images/tienda-1.png';

$bannerUrl = !empty($store['banner_url'])
    ? '../public/' . htmlspecialchars($store['banner_url'])
    : '../public/assets/images/img-resource-1.jpeg';

$businessName = htmlspecialchars($store['business_name'] ?? 'Negocio sin nombre');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styles-business-service.css">
    <link rel="icon" type="image/svg+xml" href="public/assets/images/favicon.svg">
</head>

<body>

    <div class="sticky-header">
        <div class="sticky-container">
            <div class="sticky-logo"><a href="/index.php">EasyPoint</a></div>

            <div class="sticky-search-bar">
                <div class="search-field">
                    <span class="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" placeholder="Search services">
                </div>
                <div class="search-field border-left">
                    <span class="search-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <input type="text" placeholder="Where?">
                </div>
                <button class="sticky-search-btn">Search</button>
            </div>

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
                    <a href="../index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <header>
        <nav class="navigation-bar">
            <div class="logo"><a href="/index.php">EasyPoint</a></div>

            <div class="hero-search-bar" style="
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

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" placeholder="Search services"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>

                <div style="width: 1px; height: 25px; background-color: rgba(165, 134, 104, 0.3);"></div>

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <input type="text" placeholder="Where?"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>

                <button class="sticky-search-btn" style="
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
            </div>

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
                    <a href="../index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </nav>
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
                <li><a href="index.php?action=view_all_stores" class="cat-link" style="color: #d9534f;">Clear Filters</a>
                </li>
            <?php else: ?>
                <li><a href="index.php?action=view_all_stores" class="cat-link" style="font-weight: bold;">View All
                        Stores</a></li>
            <?php endif; ?>

        </ul>
    </header>

    <div class="main-container">

        <div class="left-panel">
            <div class="gallery-carousel-wrapper">
                <div class="carousel-main">

                    <button class="carousel-control prev" id="btnPrev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-control next" id="btnNext">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <div class="carousel-track" id="carouselTrack">
                        <?php if (!empty($galleryImages)): ?>
                            <?php foreach ($galleryImages as $image): ?>
                                <div class="carousel-slide">
                                    <img src="/public/<?php echo htmlspecialchars($image['image_url']); ?>" alt="Gallery Image">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="carousel-slide">
                                <img src="../public/assets/images/img-resource-1.jpeg" alt="Default Image">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="carousel-dots" id="carouselDots">
                    </div>
                </div>
            </div>
            <div class="business-title">
                <h1><?php echo htmlspecialchars($store['business_name']); ?></h1>

                <?php if (!empty($store['business_type']) && $store['business_type'] !== 'General'): ?>
                    <span
                        style="background-color: #a58668; color: #2b201e; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 8px;">
                        <?php echo htmlspecialchars($store['business_type']); ?>
                    </span>
                <?php endif; ?>

                <p>
                    <?php
                    $parts = [];
                    if (!empty($store['address']))
                        $parts[] = $store['address'];
                    if (!empty($store['postal_code']))
                        $parts[] = $store['postal_code'];
                    if (!empty($store['city']))
                        $parts[] = $store['city'];
                    echo htmlspecialchars(implode(', ', $parts));
                    ?>
                </p>
                <div class="stars">
                    <i class="fas fa-star"></i> 5.0 <span style="color:#b0a8a6">(New)</span>
                </div>
            </div>

            <div>
                <h2 class="section-title">Services</h2>

                <?php if (empty($storeServices)): ?>
                    <p style="color: #888; font-style: italic;">No services listed yet.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                        <?php foreach ($storeServices as $service): ?>
                            <div class="service-row"
                                style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px;">

                                <h4 style="margin: 0; font-size: 16px; color: #cbbba6; flex: 1;">
                                    <?php echo htmlspecialchars($service['name']); ?>
                                </h4>

                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="service-details" style="text-align: right;">
                                        <span class="price-tag" style="display: block; font-weight: bold; font-size: 15px;">
                                            <?php echo htmlspecialchars($service['price']); ?> €
                                        </span>
                                        <span class="duration-tag" style="font-size: 12px; color: #888;">
                                            <?php echo htmlspecialchars($service['duration']); ?> min
                                        </span>
                                    </div>

                                    <a href="../index.php?action=book&service_id=<?php echo htmlspecialchars($service['id']); ?>&store_id=<?php echo htmlspecialchars($storeId); ?>" 
                                        class="book-btn">
                                        Book
                                    </a>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="right-panel">
            <div class="map-box"
                style="padding: 0; overflow: hidden; height: 300px; border-radius: 12px; border: 1px solid #e0e0e0;">
                <?php
                $mapParts = [];
                // Limpiamos la dirección de espacios extra
                if (!empty($store['address']))
                    $mapParts[] = trim($store['address']);
                if (!empty($store['postal_code']))
                    $mapParts[] = trim($store['postal_code']);
                if (!empty($store['city']))
                    $mapParts[] = trim($store['city']);

                // AÑADIDO: Agregamos el país al final para evitar confusiones
                if (!empty($mapParts)) {
                    $addressString = implode(', ', $mapParts) . ", España";
                } else {
                    $addressString = "Valencia, España";
                }

                $encodedAddress = urlencode($addressString);
                ?>

                <iframe width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen
                    src="https://maps.google.com/maps?q=<?php echo $encodedAddress; ?>&t=&z=15&ie=UTF8&iwloc=&output=embed">
                </iframe>
            </div>

            <div>
                <h3 class="info-header">About Us</h3>
                <p style="font-size: 14px; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($store['description'] ?? 'No description available.')); ?>
                </p>
            </div>

            <div>
                <h3 class="info-header">Schedule</h3>
                <div>
                    <?php
                    $schedule = json_decode($store['opening_hours'] ?? '', true);

                    $orderedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

                    if ($schedule && is_array($schedule)):
                        foreach ($orderedDays as $day):
                            if (isset($schedule[$day])):
                                $dayData = $schedule[$day];

                                $hoursText = "Closed";

                                $isActive = !empty($dayData['active']) && $dayData['active'] == true;

                                if ($isActive) {
                                    $open = $dayData['open'] ?? '';
                                    $close = $dayData['close'] ?? '';
                                    if ($open && $close) {
                                        $hoursText = $open . ' - ' . $close;
                                    }
                                }
                                ?>
                                <div class="schedule-row">
                                    <span><?php echo htmlspecialchars(ucfirst($day)); ?></span>
                                    <span><?php echo htmlspecialchars($hoursText); ?></span>
                                </div>
                                <?php
                            endif;
                        endforeach;
                    else: ?>
                        <p style="font-size: 13px; color: #666;">Schedule not available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <h3 class="info-header">Social Media</h3>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">

                    <?php
                    $socials = [
                        'website' => ['icon' => 'fas fa-globe', 'val' => $store['website'] ?? '', 'type' => 'url'],
                        'instagram' => ['icon' => 'fab fa-instagram', 'val' => $store['instagram_link'] ?? '', 'type' => 'instagram'],
                        'facebook' => ['icon' => 'fab fa-facebook', 'val' => $store['facebook_link'] ?? '', 'type' => 'url'],
                        'tiktok' => ['icon' => 'fab fa-tiktok', 'val' => $store['tiktok_link'] ?? '', 'type' => 'tiktok'],
                        'twitter' => ['icon' => 'fab fa-twitter', 'val' => $store['twitter_link'] ?? '', 'type' => 'twitter']
                    ];

                    $hasSocial = false;

                    foreach ($socials as $key => $data):
                        $input = trim($data['val']);

                        if (!empty($input)):
                            $hasSocial = true;
                            $finalLink = $input;

                            if (!preg_match("~^(?:f|ht)tps?://~i", $input)) {

                                $cleanUser = ltrim($input, '@');

                                switch ($data['type']) {
                                    case 'tiktok':
                                        $finalLink = "https://www.tiktok.com/@" . $cleanUser;
                                        break;

                                    case 'instagram':
                                        $finalLink = "https://www.instagram.com/" . $cleanUser;
                                        break;

                                    case 'twitter':
                                        $finalLink = "https://twitter.com/" . $cleanUser;
                                        break;

                                    default:
                                        $finalLink = "https://" . $input;
                                        break;
                                }
                            }
                            ?>
                            <a href="<?php echo htmlspecialchars($finalLink); ?>" target="_blank" rel="noopener noreferrer"
                                style="color: #cbbba6; text-decoration: none; font-size: 22px; transition: color 0.3s;"
                                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#cbbba6'">
                                <i class="<?php echo $data['icon']; ?>"></i>
                            </a>
                        <?php
                        endif;
                    endforeach;

                    if (!$hasSocial): ?>
                        <p style="font-size: 13px; color: #666; font-style: italic;">No social media linked.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
    <script src="../public/js/script-business-service.js"></script>
</body>

</html>