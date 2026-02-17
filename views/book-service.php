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
    <link rel="icon" type="image/svg+xml" href="public/assets/images/favicon.svg">
</head>

<body>
    <?php include "views/sticky-header.php"; ?>
    <header>
        <nav class="navigation-bar">
            <div class="logo"><a href="/index.php">EasyPoint</a></div>
            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>

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
    

    

   

     <?php include "views/modals.php"; ?>

    <!-- LOADING SPINNER -->
    <div id="loadingSpinner" class="loading-spinner" style="display: none;">
        <div class="spinner"></div>
        <p>Registering your appointment...</p>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="toast"></div>


    <!-- FOOTER -->
   <?php include "views/footer.php" ?>

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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/script.js"></script>
    <script src="public/js/script-book-service.js"></script>
</body>

</html>
