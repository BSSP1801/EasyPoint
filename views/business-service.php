<?php
// view/business-service.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/user.php';

// 1. Check if we received an ID
if (!isset($_GET['id'])) {
    // If there is no ID, redirect to home or show error
    header("Location: ../index.php");
    exit();
}

if (isset($businessData)) {
    // Asignamos $businessData a $userData para que el resto de tu HTML funcione sin cambios
    $userData = $businessData;
} else {
    // Si alguien entra directo al archivo sin pasar por el controlador, lo expulsamos
    header("Location: ../index.php");
    exit();
}

$storeId = $_GET['id'];
$userModel = new User();

// 2. Get store data using the method you already had
$store = $userModel->getFullProfile($storeId);

// If the store does not exist, redirect
if (!$store) {
    header("Location: ../index.php");
    exit();
}

$serviceModel = new Service();
$storeServices = $serviceModel->getAllByUserId($storeId);
$targetUserId = $userData['user_id'] ?? $userData['id'];
// 3. Preparar la URL del logo
// Nota: Como estamos en la carpeta 'view/', debemos salir un nivel (../) para entrar a 'public/'
// ... código anterior ...
$logoUrl = !empty($store['logo_url'])
    ? '../public/' . htmlspecialchars($store['logo_url'])
    : '../public/assets/images/tienda-1.png';

// NUEVO: Lógica para el banner
$bannerUrl = !empty($store['banner_url'])
    ? '../public/' . htmlspecialchars($store['banner_url'])
    : '../public/assets/images/img-resource-1.jpeg'; // Imagen de fondo por defecto
// ...

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
</head>

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
                    <a href="public/dashboard.php">
                        <span class="user-link">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span></a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <span class="user-link">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
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

            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Search services or businesses">
            </div>

            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <a href="../index.php?action=dashboard">
                        <span class="user-link">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span></a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <span class="user-link">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                    <a href="../index.php?action=dashboard" class="dashboard-link">Dashboard</a>
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="../index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </nav>
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
                                    <img src="/public/<?php echo htmlspecialchars($image['image_url']); ?>" 
                                         alt="Gallery Image">
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
                                style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px;">

                                <h4 style="margin: 0; font-size: 16px; color: #333; flex: 1;">
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
                                        class="book-btn"
                                        style="background-color: #000; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-block;">
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
                <i class="fab fa-instagram" style="font-size: 20px;"></i>
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
<script src="../public/js/script-business-service.js"></script>
</body>

</html>