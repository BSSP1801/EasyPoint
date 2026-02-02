<?php
// public/dashboard.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Seguridad
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// 3. Cargar datos necesarios para el HTML
require_once dirname(__DIR__) . '/models/Service.php';
require_once dirname(__DIR__) . '/models/user.php'; 

// Cargar servicios
$serviceModel = new Service();
$myServices = $serviceModel->getAllByUserId($_SESSION['user_id']);

// Cargar datos del usuario y perfil
$userModel = new User();
$userData = $userModel->getFullProfile($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | EasyPoint</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/styles-dashboard.css">
</head>

<body>

    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <i class="fas fa-calendar-check"></i>
            <span class="logo-text">EasyPoint</span>
        </div>
        <div style="padding: 0 20px; margin-bottom: 5px;">
            <a href="index.php" style="font-size: 14px; opacity: 0.8;"><i class="fas fa-arrow-left"></i> <span class="menu-text">Back to Home</span></a>
        </div>
        <nav>
            <div class="debug-info">
                <?php echo htmlspecialchars($_SESSION['username'] ?? 'N/A'); ?> <br>
            </div>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                <a href="#" class="menu-item active" onclick="switchMainView(event, 'view-calendar')">
                    <i class="far fa-calendar-alt"></i><span class="menu-text">Calendar</span>
                </a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-appointments')">
                    <i class="far fa-clock"></i><span class="menu-text">Appointments</span>
                </a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-settings')">
                    <i class="fas fa-cog"></i><span class="menu-text">Settings</span>
                </a>

            <?php elseif (isset($_SESSION['user_id']) && ($_SESSION['role'] === 'store' || $_SESSION['role'] === 'admin')): ?>
                <a href="#" class="menu-item active" onclick="switchMainView(event, 'view-dashboard')">
                    <i class="fas fa-tachometer-alt"></i><span class="menu-text">Dashboard</span>
                </a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-calendar')">
                    <i class="far fa-calendar-alt"></i><span class="menu-text">Calendar</span>
                </a>
                <?php if($_SESSION['role'] === 'store'): ?>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-clients')">
                    <i class="far fa-user"></i><span class="menu-text">Clients</span>
                </a>
                <?php endif; ?>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-settings')">
                    <i class="fas fa-cog"></i><span class="menu-text">Settings</span>
                </a>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="content">

        <div id="view-calendar" class="main-view hidden">
            <header class="header">
                <div class="welcome">Welcome back. Here is a summary of your schedule.</div>
                <div class="header-tools">
                    <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></button>
                    <input type="text" placeholder="Search..." class="search-input">
                    <i class="fas fa-bell notification-icon"></i>
                </div>
            </header>

            <section class="summary-cards">
                <div class="card">
                    <div class="card-content">
                        <h3>0</h3><p>Today</p>
                    </div>
                    <div class="card-icon card-icon-blue"><i class="far fa-calendar"></i></div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>1</h3><p>Pending</p>
                    </div>
                    <div class="card-icon card-icon-yellow"><i class="far fa-clock"></i></div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>1</h3><p>Confirmed</p>
                    </div>
                    <div class="card-icon card-icon-green"><i class="far fa-check-circle"></i></div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>2</h3><p>Total</p>
                    </div>
                    <div class="card-icon card-icon-teal"><i class="fas fa-chart-line"></i></div>
                </div>
            </section>

            <div class="main-section">
                <section class="appointments-list">
                    <h2 class="section-title">Upcoming Appointments</h2>
                    <div class="appointment">
                        <div class="appointment-info">
                            <h4>Dental Cleaning <span class="status confirmed">Confirmed</span></h4>
                            <p><i class="far fa-user"></i> Maria Garcia | <i class="far fa-clock"></i> Tue, Jan 20 - 10:00</p>
                        </div>
                    </div>
                </section>

                <aside class="right-sidebar">
                    <div class="calendar-widget">
                        <div class="calendar-header">
                            <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                            <h3 id="monthYear">January 2026</h3>
                            <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="calendar-grid" id="calendar"></div>
                    </div>
                </aside>
            </div>
        </div>

        <div id="view-dashboard" class="main-view">
            <header class="header">
                <div class="header-text">
                    <h1 class="page-title">Management</h1>
                    <p class="page-subtitle">Configure your business details and services</p>
                </div>
            </header>

            <div class="settings-tabs">
                <button class="tab-btn active" onclick="openTab(event, 'business')">
                    <i class="fas fa-store"></i> Business
                </button>
                <button class="tab-btn" onclick="openTab(event, 'schedule')">
                    <i class="far fa-clock"></i> Schedule
                </button>
                <button class="tab-btn" onclick="openTab(event, 'notifications')">
                    <i class="fas fa-cut"></i> Services
                </button>
            </div>

            <div id="business" class="tab-content active-content">
                <section class="settings-card">
                    <div class="card-header">
                        <h3>Business Information</h3>
                        <p>Public information visible to clients</p>
                    </div>

                    <form class="settings-form" id="business-form" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group half">
                                <label>Business Name</label>
                                <input type="text" name="business_name" value="<?php echo htmlspecialchars($userData['business_name'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group half">
                                <label>Phone</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label>City</label>
                                <input type="text" name="city" value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>" class="form-input">
                            </div>
                            <div class="form-group half">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code" value="<?php echo htmlspecialchars($userData['postal_code'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>" class="form-input">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="4" class="form-input"><?php echo htmlspecialchars($userData['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label>Logo</label>
                                <input type="file" name="logo" accept="image/*" class="form-input">
                                <?php if (!empty($userData['logo_url'])): ?>
                                    <small>Current: <a href="public/<?php echo $userData['logo_url']; ?>" target="_blank">View</a></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group half">
                                <label>Banner</label>
                                <input type="file" name="banner" accept="image/*" class="form-input">
                                <?php if (!empty($userData['banner_url'])): ?>
                                    <small>Current: <a href="public/<?php echo $userData['banner_url']; ?>" target="_blank">View</a></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Gallery (Multiple)</label>
                            <input type="file" name="gallery[]" accept="image/*" multiple class="form-input">
                        </div>

                        <div class="form-group">
                            <label>Visibility</label>
                            <div class="day-toggle" style="display:flex; align-items:center; gap:10px;">
                                <label class="switch">
                                    <input type="checkbox" name="is_public" value="1" <?php echo ($userData['is_public'] ?? 0) == 1 ? 'checked' : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                                <span class="day-name">Public (Visible in Home)</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save-1"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </form> 
                </section>
            </div>

            <div id="schedule" class="tab-content">
                <section class="settings-card">
                    <div class="card-header">
                        <h3>Opening Hours</h3>
                    </div>

                    <?php
                    $schedule = [];
                    if (!empty($userData['opening_hours'])) {
                        $schedule = json_decode($userData['opening_hours'], true);
                    }
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    ?>

                    <div class="schedule-container">
                        <?php foreach ($days as $day): ?>
                            <?php
                            $dayData = $schedule[$day] ?? [];
                            $isActive = !empty($dayData['active']) && $dayData['active'] == true;
                            $openTime = $dayData['open'] ?? '09:00';
                            $closeTime = $dayData['close'] ?? '20:00';
                            ?>
                            <div class="schedule-row">
                                <div class="day-toggle">
                                    <label class="switch">
                                        <input type="checkbox" id="<?php echo $day; ?>-active" onchange="toggleDay(this)" <?php echo $isActive ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="day-name"><?php echo ucfirst($day); ?></span>
                                </div>
                                <div class="time-inputs" style="display: <?php echo $isActive ? 'flex' : 'none'; ?>;">
                                    <input type="time" id="<?php echo $day; ?>-open" value="<?php echo $openTime; ?>" class="form-input time-input">
                                    <span>to</span>
                                    <input type="time" id="<?php echo $day; ?>-close" value="<?php echo $closeTime; ?>" class="form-input time-input">
                                </div>
                                <div class="closed-label" style="display: <?php echo $isActive ? 'none' : 'block'; ?>;">Closed</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-actions">
                        <button id="save-schedule-btn" class="btn-save-2"><i class="fas fa-save"></i> Save Schedule</button>
                    </div>
                </section>
            </div>

            <div id="notifications" class="tab-content">
                <h2 style="margin-bottom: 20px;">Manage Services</h2>

                <div class="service-container">
                    <h3 style="margin-top: 0; margin-bottom: 20px; color: #333;">Add New Service</h3>
                    <form id="add-service-form" onsubmit="submitService(event)">
                    <div class="add-service-grid">
                        <div class="form-field">
                            <label>Name</label>
                            <input type="text" name="service_name" placeholder="e.g. Haircut" required class="service-input">
                        </div>
                        <div class="form-field">
                            <label>Price (€)</label>
                            <input type="number" step="0.01" name="service_price" placeholder="0.00" required class="service-input">
                        </div>
                        <div class="form-field">
                            <label>Duration (min)</label>
                            <input type="number" name="service_duration" placeholder="30" required class="service-input">
                        </div>
                        <div class="form-field">
                            <button type="submit" class="btn-add">Add</button>
                        </div>
                    </div>
                </form>
                </div>

                <h3 class="service-list">Your Services List</h3>
                <div id="services-list" style="display: grid; gap: 15px;">
                    <?php if (empty($myServices)): ?>
                        <div id="no-services-msg" style="text-align: center; padding: 40px; background: rgba(235, 230, 210, 0.55); border-radius: 10px; color: #000000;">
                            You haven't added any services yet.
                        </div>
                    <?php else: ?>
                        <?php foreach ($myServices as $service): ?>
                            <div class="service-item">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div>
                                        <i class="fas fa-cut" style="color: #555;"></i>
                                    </div>
                                    <div>
                                        <h4 style="margin: 0; font-size: 16px; color: #333;"><?php echo htmlspecialchars($service['name']); ?></h4>
                                        <span style="font-size: 13px; color: #333;"><i class="far fa-clock"></i> <?php echo htmlspecialchars($service['duration']); ?> min</span>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <span style="font-weight: bold; font-size: 18px; color: #000;"><?php echo htmlspecialchars($service['price']); ?> €</span>
                                    <a href="#" onclick="deleteService(<?php echo $service['id']; ?>, this); return false;" style="color: #ff4d4d; background: #fff0f0; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 6px;"><i class="fas fa-trash-alt"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
    <script src="/public/js/script-dashboard.js"></script>
</body>
</html>