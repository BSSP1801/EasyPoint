<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/service.php';
require_once __DIR__ . '/../models/user.php';

// Verificar que tenemos los parámetros necesarios
if (!isset($_GET['service_id']) || !isset($_GET['store_id'])) {
    header("Location: index.php");
    exit();
}

$serviceId = (int)$_GET['service_id'];
$storeId = (int)$_GET['store_id'];

$serviceModel = new Service();
$userModel = new User();

// Obtener datos del servicio
$service = $serviceModel->getServiceById($serviceId);
if (!$service || $service['user_id'] != $storeId) {
    header("Location: index.php");
    exit();
}

// Obtener datos de la tienda y su horario
$store = $userModel->getFullProfile($storeId);
if (!$store) {
    header("Location: index.php");
    exit();
}

// Parsear horario de apertura
$openingHours = [];
if (!empty($store['opening_hours'])) {
    $openingHours = json_decode($store['opening_hours'], true);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - EasyPoint</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="public/css/styles-book-service.css">
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
                            <a href="index.php?action=logout" class="dropdown-item">
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
                            <a href="index.php?action=logout" class="dropdown-item">
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
                            <a href="index.php?action=logout" class="dropdown-item">
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
                            <a href="index.php?action=logout" class="dropdown-item">
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
    </header>

    <div class="booking-container">
        <div class="booking-wrapper">
            <!-- LEFT PANEL: DATE AND TIME SELECTION -->
            <div class="booking-left-panel">
                <h2 class="booking-title">Select date and time</h2>

                <!-- Month Navigation -->
                <div class="month-navigation">
                    <button id="prevMonth" class="month-btn"><i class="fas fa-chevron-left"></i></button>
                    <span id="currentMonth" class="current-month">February 2026</span>
                    <button id="nextMonth" class="month-btn"><i class="fas fa-chevron-right"></i></button>
                </div>

                <!-- Calendar -->
                <div class="calendar-container">
                    <div class="weekdays">
                        <div class="weekday">Mon</div>
                        <div class="weekday">Tue</div>
                        <div class="weekday">Wed</div>
                        <div class="weekday">Thu</div>
                        <div class="weekday">Fri</div>
                        <div class="weekday">Sat</div>
                        <div class="weekday">Sun</div>
                    </div>
                    <div class="days-grid" id="daysGrid">
                        <!-- Generated by JavaScript -->
                    </div>
                </div>

                <!-- Legend -->
                <div class="calendar-legend">
                    <div class="legend-item">
                        <span class="legend-color available"></span>
                        <span>Available</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color unavailable"></span>
                        <span>Unavailable</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color selected"></span>
                        <span>Selected</span>
                    </div>
                </div>

                <!-- Time Slots -->
                <div class="time-slots-section" style="display: none;" id="timeSlotsSection">
                    <div class="time-slots-wrapper">
                        <div class="time-column">
                            <h4>Morning</h4>
                            <div class="time-slots" id="morningSlots">
                                <!-- Generated by JavaScript -->
                            </div>
                        </div>
                        <div class="time-column">
                            <h4>Afternoon</h4>
                            <div class="time-slots" id="afternoonSlots">
                                <!-- Generated by JavaScript -->
                            </div>
                        </div>
                        <div class="time-column">
                            <h4>Evening</h4>
                            <div class="time-slots" id="eveningSlots">
                                <!-- Generated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: ORDER SUMMARY -->
            <div class="booking-right-panel">
                <div class="order-summary">
                    <h3 class="summary-title">Your order</h3>

                    <!-- Service Card -->
                    <div class="service-card">
                        <div class="service-header">
                            <h4><?php echo htmlspecialchars($service['name']); ?></h4>
                        </div>
                        <div class="service-details">
                            <span class="service-duration">
                                <i class="fas fa-clock"></i>
                                <?php echo htmlspecialchars($service['duration']); ?> min
                            </span>
                        </div>
                        <div class="service-price">
                            <strong><?php echo number_format($service['price'], 2); ?> €</strong>
                        </div>
                    </div>

                    <!-- Selected Date & Time Display -->
                    <div class="selected-info" id="selectedInfo" style="display: none;">
                        <div class="info-row">
                            <span class="label">Date:</span>
                            <span class="value" id="selectedDate">-</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Time:</span>
                            <span class="value" id="selectedTime">-</span>
                        </div>
                    </div>

                    <!-- Employee Selection -->
                    <div class="employee-section" id="employeeSection" style="display: none;">
                        <h4 class="section-subtitle">Available professionals</h4>
                        <div class="employee-grid" id="employeeGrid">
                            <!-- Generated by JavaScript -->
                        </div>
                    </div>

                    <!-- Total Price -->
                    <div class="price-section">
                        <div class="price-row">
                            <span class="label">Total</span>
                            <span class="price-value" id="totalPrice"><?php echo number_format($service['price'], 2); ?> €</span>
                        </div>
                    </div>

                    <!-- Continue Button -->
                    <button class="continue-btn" id="continueBtn" onclick="proceedToConfirmation()">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CONFIRMATION MODAL -->
    <div id="confirmationModal" class="modal-overlay">
        <div class="modal-box booking-modal">
            <button class="close-btn" id="closeConfirmBtn" onclick="closeConfirmationModal()">×</button>

            <div class="modal-content">
                <h2 class="modal-title">Booking Details</h2>

                <div class="confirmation-details">
                    <div class="detail-block">
                        <h4>Service</h4>
                        <p id="confirmServiceName"><?php echo htmlspecialchars($service['name']); ?></p>
                    </div>

                    <div class="detail-block">
                        <h4>Business</h4>
                        <p id="confirmBusinessName"><?php echo htmlspecialchars($store['business_name']); ?></p>
                    </div>

                    <div class="detail-block">
                        <h4>Date & Time</h4>
                        <p id="confirmDateTime">-</p>
                    </div>

                    <div class="detail-block">
                        <h4>Duration</h4>
                        <p id="confirmDuration"><?php echo htmlspecialchars($service['duration']); ?> minutes</p>
                    </div>

                    <div class="detail-block total">
                        <h4>Total Price</h4>
                        <p id="confirmPrice" class="price"><?php echo number_format($service['price'], 2); ?> €</p>
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="btn-confirm" id="confirmBookingBtn" onclick="confirmBooking()">
                        Confirm
                    </button>
                    <button class="btn-cancel" onclick="cancelBooking()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CANCEL CONFIRMATION MODAL -->
    <div id="cancelConfirmModal" class="modal-overlay">
        <div class="modal-box booking-modal">
            <h2 class="modal-title">Cancel Booking?</h2>
            <p class="cancel-message">Are you sure you want to cancel this booking?</p>
            <div class="modal-actions">
                <button class="btn-confirm" onclick="confirmCancel()">
                    Yes, cancel
                </button>
                <button class="btn-cancel" onclick="continueProceedBooking()">
                    Continue booking
                </button>
            </div>
        </div>
    </div>

    <!-- AUTH MODAL -->
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

    <!-- LOADING SPINNER -->
    <div id="loadingSpinner" class="loading-spinner" style="display: none;">
        <div class="spinner"></div>
        <p>Registering your appointment...</p>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="toast"></div>

    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h2 class="footer-logo">EasyPoint</h2>
                <p class="footer-desc">The easiest way to look and feel your best. Book appointments with top
                    professionals near you.</p>
                <div class="social-icons">
                    <a href="https://facebook.com/"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://instagram.com/"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://x.com/"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://tiktok.com/"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>

            <div class="footer-links-group">
                <div class="footer-column">
                    <h3>Company</h3>
                    <a href="index.php?action=company">About Us</a>
                    <a href="index.php?action=company#contact">Contact</a>
                </div>

                <div class="footer-column">
                    <h3>For Business</h3>
                    <a href="index.php?action=business">Partner with us</a>
                    <a href="index.php?action=business#support">Support</a>
                </div>

                <div class="footer-column">
                    <h3>Legal</h3>
                    <a href="index.php?action=legal">Privacy Policy</a>
                    <a href="index.php?action=legal#terms">Terms of Service</a>
                    <a href="#">Cookies Settings</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 EasyPoint. All rights reserved.</p>
        </div>
    </footer>

    <!-- Data Storage for JavaScript -->
    <script>
        const SERVICE_DATA = {
            id: <?php echo $serviceId; ?>,
            store_id: <?php echo $storeId; ?>,
            name: '<?php echo addslashes($service['name']); ?>',
            price: <?php echo $service['price']; ?>,
            duration: <?php echo $service['duration']; ?>
        };

        const STORE_DATA = {
            id: <?php echo $storeId; ?>,
            business_name: '<?php echo addslashes($store['business_name']); ?>',
            opening_hours: <?php echo json_encode($openingHours); ?>
        };

        const USER_LOGGED = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const USER_ID = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    </script>

    <script src="public/js/script.js"></script>
    <script src="public/js/script-book-service.js"></script>
</body>

</html>
