<?php
// Ensure session is started if accessed directly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: Log the session variables
error_log("Dashboard: user_id=" . ($_SESSION['user_id'] ?? 'NOT SET'));
error_log("Dashboard: role=" . ($_SESSION['role'] ?? 'NOT SET'));

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
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
            <div
                style="font-size: 11px; color: #999; padding: 10px; border-bottom: 1px solid #eee; word-break: break-all;">
                User: <?php echo $_SESSION['username'] ?? 'N/A'; ?> | Role:
                <strong><?php echo $_SESSION['role'] ?? 'NOT SET'; ?></strong>
            </div>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>

                <a href="#" class="menu-item active" onclick="switchMainView(event, 'view-calendar')"><i
                        class="far fa-calendar-alt"></i> Calendar</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-appointments')"><i
                        class="far fa-clock"></i> Appointments</a>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-settings')"><i class="fas fa-cog"></i>
                    Settings</a>
            <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                <a href="#" class="menu-item" onclick="switchMainView(event, 'view-dashboard')"><i
                        class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="#" class="menu-item active" onclick="switchMainView(event, 'view-calendar')"><i
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
        <div id="view-dashboard" class="main-view" style="display: none;">
            <header class="header">
                <div class="header-text">
                    <h1 class="page-title">Settings</h1>
                    <p class="page-subtitle">Manage your business configuration</p>
                </div>
            </header>

            <div class="settings-tabs">
                <button class="tab-btn active" onclick="openTab(event, 'business')"><i class="fas fa-store"></i>
                    Business</button>
                <button class="tab-btn" onclick="openTab(event, 'schedule')"><i class="far fa-clock"></i>
                    Schedule</button>
                <button class="tab-btn" onclick="openTab(event, 'notifications')"><i class="far fa-bell"></i>
                    Notifications</button>
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
                                    class="form-input" readonly style="background: #eee;">
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
                                    <small>Current: <a href="public/<?php echo $userData['logo_url']; ?>"
                                            target="_blank">View Logo</a></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group half">
                                <label>Banner Image</label>
                                <input type="file" name="banner" accept="image/*" class="form-input">
                                <?php if (!empty($userData['banner_url'])): ?>
                                    <small>Current: <a href="public/<?php echo $userData['banner_url']; ?>"
                                            target="_blank">View Banner</a></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
    <label>Visibility Status</label>
    <div class="day-toggle" style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
        <label class="switch">
            <input type="checkbox" name="is_public" value="1" 
                <?php echo ($userData['is_public'] ?? 0) == 1 ? 'checked' : ''; ?>>
            <span class="slider round"></span>
        </label>
        <span class="day-name">Public (Visible in Home Carousel)</span>
    </div>
</div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </form>
                </section>
            </div>

            <div id="schedule" class="tab-content" style="display: none;">
                <section class="settings-card">
                    <div class="card-header">
                        <h3>Opening Hours</h3>
                        <p>Set your weekly availability for appointments</p>
                    </div>

                    <?php
                    // 1. Decodificar el JSON guardado en la base de datos
                    $schedule = [];
                    if (!empty($userData['opening_hours'])) {
                        $schedule = json_decode($userData['opening_hours'], true);
                    }

                    // Array de días para generar el HTML en bucle
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    ?>

                    <div class="schedule-container">
                        <?php foreach ($days as $day): ?>
                            <?php
                            // Recuperar valores guardados para este día (si existen)
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

                                <div class="time-inputs" style="display: <?php echo $isActive ? 'flex' : 'none'; ?>;">
                                    <input type="time" id="<?php echo $day; ?>-open" value="<?php echo $openTime; ?>"
                                        class="form-input time-input">
                                    <span>to</span>
                                    <input type="time" id="<?php echo $day; ?>-close" value="<?php echo $closeTime; ?>"
                                        class="form-input time-input">
                                </div>

                                <div class="closed-label" style="display: <?php echo $isActive ? 'none' : 'block'; ?>;">
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

            <div id="notifications" class="tab-content" style="display: none;">
                <section class="settings-card">
                    <div style="padding: 20px;">Notification Settings Content</div>
                </section>
            </div>
        </div>
    </main>
    <script src="public/js/script-dashboard.js"></script>

</body>

</html>