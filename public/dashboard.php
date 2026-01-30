<?php
// public/dashboard.php

// 1. Iniciar sesión si hace falta
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Seguridad: Si no está logueado, fuera
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// 3. Cargar datos necesarios para el HTML
require_once dirname(__DIR__) . '/models/Service.php';

$serviceModel = new Service();
$myServices = $serviceModel->getAllByUserId($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="public/css/styles-dashboard.css">
</head>

<body>

    <aside class="sidebar">
        <div class="logo">
            <i class="fas fa-calendar-check"></i>
            <span>EasyPoint</span>
        </div>
        <div><a href="index.php">Back to Main Page</a></div>
        <nav>
            <!-- DEBUG: Show current role -->
            <div class="debug-info">
                User: <?php echo $_SESSION['username'] ?? 'N/A'; ?> | Role:
                <strong><?php echo $_SESSION['role'] ?? 'NOT SET'; ?></strong>
            </div>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>

                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-calendar')"><i
                        class="far fa-calendar-alt"></i> Calendar</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-appointments')"><i
                        class="far fa-clock"></i> Appointments</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-settings')"><i class="fas fa-cog"></i>
                    Settings</a>
            <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                <a href="#" class="menu-item active" onclick="switchMainView(event, 'view-dashboard')"><i
                        class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-calendar')"><i
                        class="far fa-calendar-alt"></i> Calendar</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-clients')"><i class="far fa-clock"></i>
                    Clients</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-settings')">
                    <i class="fas fa-cog"></i> Settings
                </a>
            <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-dashboard')"><i
                        class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="#" class="menu-item active" onclick="switchMainView(event, 'view-calendar')"><i
                        class="far fa-calendar-alt"></i> Calendar</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-appointments')"><i
                        class="far fa-clock"></i> Appointments</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-clients')"><i class="far fa-clock"></i>
                    Clients</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-settings')">
                    <i class="fas fa-cog"></i> Settings
                </a>
                </a>
            <?php endif; ?>





            <!-- <a href="#" class="menu-item active" onclick="switchMainView(event, 'view-dashboard')">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>


            <a href="#" class="menu-item"><i class="far fa-calendar-alt"></i> Calendar</a>
            <a href="#" class="menu-item"><i class="far fa-clock"></i> Appointments</a>
            <a href="#" class="menu-item"><i class="far fa-user"></i> Clients</a>
            <a href="#" class="menu-item" onclick="switchMainView(event, 'view-settings')">
                <i class="fas fa-cog"></i> Settings
            </a> -->
        </nav>
    </aside>

    <main class="content">

        <div id="view-calendar" class="main-view">
            <header class="header">
                <div class="welcome">Welcome back. Here is a summary of your schedule.</div>
                <div class="header-tools">
                    <input type="text" placeholder="Search..." class="search-input">
                    <i class="fas fa-bell notification-icon"></i>
                </div>
            </header>

            <section class="summary-cards">
                <div class="card">
                    <div class="card-content">
                        <h3>0</h3>
                        <p>Appointments Today</p>
                    </div>
                    <div class="card-icon card-icon-blue"><i class="far fa-calendar"></i></div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>1</h3>
                        <p>Pending</p>
                    </div>
                    <div class="card-icon card-icon-yellow"><i class="far fa-clock"></i></div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>1</h3>
                        <p>Confirmed</p>
                    </div>
                    <div class="card-icon card-icon-green"><i class="far fa-check-circle"></i></div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>2</h3>
                        <p>Total Appointments</p>
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
                            <p><i class="far fa-user"></i> Maria Garcia | <i class="fas fa-phone"></i> +54 11 5555-1234
                                | <i class="far fa-clock"></i> Tue, Jan 20 - 10:00</p>
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
                    <div class="quick-actions">
                        <h2 class="section-title">Quick Actions</h2>
                        <a href="#" class="quick-action-btn"><i class="fas fa-plus"></i> New Manual Appointment</a>
                        <a href="#" class="quick-action-btn"><i class="fas fa-external-link-alt"></i> View Booking
                            Page</a>
                    </div>
                </aside>
            </div>
        </div>
        <div id="view-dashboard" class="main-view hidden">
            <header class="header">
                <div class="header-text">
                    <h1 class="page-title">Settings</h1>
                    <p class="page-subtitle">Manage your business configuration</p>
                </div>
            </header>

            <div class="settings-tabs">
    <button class="tab-btn active" data-tab="business" onclick="openTab(event, 'business')">
        <i class="fas fa-store"></i> Business
    </button>
    
    <button class="tab-btn" data-tab="schedule" onclick="openTab(event, 'schedule')">
        <i class="far fa-clock"></i> Schedule
    </button>
    
    <button class="tab-btn" data-tab="notifications" onclick="openTab(event, 'notifications')">
        <i class="far fa-bell"></i> Services
    </button>
</div>

            <div id="business" class="tab-content active-content">
                <section class="settings-card">
                    <div class="card-header">
                        <h3>Business Information</h3>
                        <p>Update the business data that your clients will see</p>
                    </div>

                    <form class="settings-form" id="business-form" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-group half">
                                <label>Business Name</label>
                                <input type="text" name="business_name"
                                    value="<?php echo htmlspecialchars($userData['business_name'] ?? ''); ?>"
                                    class="form-input">
                            </div>
                            <div class="form-group half">
                                <label>Phone Number</label>
                                <input type="text" name="phone"
                                    value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>"
                                    class="form-input">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label>Contact Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>"
                                    class="form-input" readonly>
                            </div>
                            <div class="form-group half">
                                <label>Locality</label>
                                <input type="text" name="city"
                                    value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>" class="form-input">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label>Address</label>
                                <input type="text" name="address"
                                    value="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>"
                                    class="form-input">
                            </div>
                            <div class="form-group half">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code"
                                    value="<?php echo htmlspecialchars($userData['postal_code'] ?? ''); ?>"
                                    class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="4" maxlength="500"
                                class="form-input"><?php echo htmlspecialchars($userData['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group half">
                                <label>Logo</label>
                                <input type="file" name="logo" accept="image/*" class="form-input">
                                <?php if (!empty($userData['logo_url'])): ?>
                                    <small>Current: <a href="<?php echo $userData['logo_url']; ?>" 
                                            target="_blank">View Logo</a></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group half">
                                <label>Banner Image</label>
                                <input type="file" name="banner" accept="image/*" class="form-input">
                                <?php if (!empty($userData['banner_url'])): ?>
                                    <small>Current: <a href="<?php echo $userData['banner_url']; ?>" 
                                            target="_blank">View Banner</a></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
    <label>Visibility Status</label>
    <div class="day-toggle">
        <label class="switch">
            <input type="checkbox" name="is_public" value="1" 
                <?php echo ($userData['is_public'] ?? 0) == 1 ? 'checked' : ''; ?>>
            <span class="slider round"></span>
        </label>
        <span class="day-name">Public (Visible in Home Carousel)</span>
    </div>
</div>
                        </div>

                        <div class="form-group">
    <label>Gallery Photos (You can select multiple)</label>
    <input type="file" name="gallery[]" accept="image/*" multiple class="form-input">
    <div id="gallery-preview" class="gallery-preview-container">
        </div>
</div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </form> 


                </section>
            </div>

            <div id="schedule" class="tab-content hidden">
                <section class="settings-card">
                    <div class="card-header">
                        <h3>Opening Hours</h3>
                        <p>Set your weekly availability for appointments</p>
                    </div>

                    <?php
                    // 1. Decode the JSON saved in the database
                    $schedule = [];
                    if (!empty($userData['opening_hours'])) {
                        $schedule = json_decode($userData['opening_hours'], true);
                    }

                    // Array of days to generate HTML in loop
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    ?>

                    <div class="schedule-container">
                        <?php foreach ($days as $day): ?>
                            <?php
                            // Retrieve saved values for this day (if any)
                            $dayData = $schedule[$day] ?? [];
                            $isActive = !empty($dayData['active']) && $dayData['active'] == true;
                            $openTime = $dayData['open'] ?? '09:00';
                            $closeTime = $dayData['close'] ?? '20:00';
                            ?>

                            <div class="schedule-row">
                                <div class="day-toggle">
                                    <label class="switch">
                                        <input type="checkbox" id="<?php echo $day; ?>-active" onchange="toggleDay(this)"
                                            <?php echo $isActive ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="day-name"><?php echo ucfirst($day); ?></span>
                                </div>

                                <div class="time-inputs <?php echo $isActive ? 'active-time' : 'hidden'; ?>">
                                    <input type="time" id="<?php echo $day; ?>-open" value="<?php echo $openTime; ?>"
                                        class="form-input time-input">
                                    <span>to</span>
                                    <input type="time" id="<?php echo $day; ?>-close" value="<?php echo $closeTime; ?>"
                                        class="form-input time-input">
                                </div>

                                <div class="closed-label <?php echo $isActive ? 'hidden' : ''; ?>">
                                    Closed
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-actions">
                        <button id="save-schedule-btn" class="btn-save"><i class="fas fa-save"></i> Save
                            Schedule</button>
                    </div>
                </section>
            </div>

            <div id="notifications" class="content-section hidden">
    
    <h2 class="section-heading">Manage Services</h2>

    <div class="card-panel">
        <h3 class="card-title">Add New Service</h3>
        
        <form id="add-service-form" onsubmit="submitService(event)">
            <div class="grid-form">
                
                <div>
                    <label class="form-label">Service Name</label>
                    <input type="text" name="service_name" placeholder="e.g. Haircut & Beard" required 
                           class="form-field">
                </div>
                
                <div>
                    <label class="form-label">Price (€)</label>
                    <input type="number" step="0.01" name="service_price" placeholder="0.00" required 
                           class="form-field">
                </div>

                <div>
                    <label class="form-label">Duration (min)</label>
                    <input type="number" name="service_duration" placeholder="30" required 
                           class="form-field">
                </div>

                <div>
                    <button type="submit" class="btn-add">
    Add
</button>
                </div>
            </div>
        </form>
    </div>

    <h3 class="section-subtitle">Your Services List</h3>
    
    <div id="services-list" class="services-grid">
        <?php if (empty($myServices)): ?>
            <div id="no-services-msg" class="no-services-msg">
                <i class="fas fa-cut no-services-icon"></i>
                You haven't added any services yet.
            </div>
        <?php else: ?>
            <?php foreach ($myServices as $service): ?>
                <div class="service-item">
                    
                    <div class="service-main">
                        <div class="service-icon">
                            <i class="fas fa-cut service-icon-i"></i>
                        </div>
                        <div>
                            <h4 class="service-name"><?php echo htmlspecialchars($service['name']); ?></h4>
                            <span class="service-meta">
                                <i class="far fa-clock"></i> <?php echo htmlspecialchars($service['duration']); ?> min
                            </span>
                        </div>
                    </div>

                    <div class="service-actions">
                        <span class="service-price">
                            <?php echo htmlspecialchars($service['price']); ?> €
                        </span>
                        
                        <a href="#" 
                           onclick="deleteService(<?php echo $service['id']; ?>, this); return false;" 
                           class="service-delete-btn">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
        </div>
    </main>
    <script src="public/js/script-dashboard.js"></script>
</body>
</html>