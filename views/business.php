<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$searchTerm = $_GET['q'] ?? '';
$locationTerm = $_GET['loc'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint - For Business</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/styles-search-services.css">
    <link rel="stylesheet" href="public/css/styles-company.css">
    <link rel="icon" type="image/svg+xml" href="public/assets/images/favicon.svg">
</head>

<body>

   <?php include "views/sticky-header.php"; ?>

    <header style="min-height: auto; padding-bottom: 20px;">
        <nav class="navigation-bar">
            <div class="logo"><a href="index.php">EasyPoint</a></div>
            
            <form action="index.php" method="GET" class="hero-search-bar" style="
                background-color: rgba(235, 230, 210, 0.1); 
                border: 1px solid rgba(165, 134, 104, 0.3);
                border-radius: 50px; 
                padding: 4px; 
                display: flex; 
                align-items: center; 
                flex: 1;
                max-width: 600px;
                margin: 0 20px;
                backdrop-filter: blur(5px);">
                
                <input type="hidden" name="action" value="search">
                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" placeholder="Search services" value="<?php echo htmlspecialchars($searchTerm); ?>" style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>
                <div style="width: 1px; height: 25px; background-color: rgba(165, 134, 104, 0.3);"></div>
                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;"><i class="fa-solid fa-location-dot"></i></span>
                    <input type="text" name="loc" placeholder="Where?" value="<?php echo htmlspecialchars($locationTerm); ?>" style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>
                <button type="submit" class="sticky-search-btn" style="background-color: #a58668; color: #2b201e; border: none; padding: 8px 24px; border-radius: 30px; cursor: pointer; font-weight: bold; margin-left: 5px; font-size: 14px;">Search</button>
            </form>

            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                            <a href="index.php?action=logout" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        </div>
                    </div>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                            <a href="index.php?action=logout" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </nav>
        
        <div style="text-align: center; margin-top: 10px;">
            <h1 style="font-size: 36px; color: var(--text-main); font-weight: 600;">For Business</h1>
            <p style="color: var(--soft-text); margin-top: 5px;">Grow your business with EasyPoint</p>
        </div>
    </header>

    <div class="company-container">
        
        <section id="partner" class="content-section">
            <div class="section-header">
                <h2>Partner with us</h2>
            </div>
            <div class="about-text">
                <p style="margin-bottom: 20px;">
                    Join thousands of health and beauty professionals who are growing their business with <strong>EasyPoint</strong>. Our platform is designed to help you streamline your operations, reduce no-shows, and attract new clients effortlessly.
                </p>
                
                <h3 style="color: var(--accent); font-size: 20px; margin-bottom: 15px; margin-top: 30px;">Why join EasyPoint?</h3>
                <ul style="list-style: none; padding: 0; margin-bottom: 30px;">
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--accent); margin-right: 10px;"></i> <strong>24/7 Booking:</strong> Let clients book appointments anytime, anywhere.</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--accent); margin-right: 10px;"></i> <strong>Smart Dashboard:</strong> Manage your schedule, staff, and services in one place.</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--accent); margin-right: 10px;"></i> <strong>Growth Tools:</strong> Get visibility and verified reviews to build trust.</li>
                    <li style="margin-bottom: 10px;"><i class="fa-solid fa-check" style="color: var(--accent); margin-right: 10px;"></i> <strong>Zero Hidden Fees:</strong> Transparent pricing for you and your customers.</li>
                </ul>

                <p>
                    Ready to take your business to the next level?
                </p>
                <div style="margin-top: 25px;">
                    <a href="#" onclick="openStoreModal(event)" class="send-btn" style="text-decoration: none; display: inline-block;">List Your Business Now</a>
                </div>
            </div>
        </section>

        <section id="support" class="content-section">
            <div class="section-header">
                <h2>Business Support</h2>
            </div>
            
            <div class="contact-grid">
                <div>
                    <p class="about-text" style="margin-bottom: 30px;">
                        We are here to help our partners succeed. If you are experiencing technical issues, have questions about billing, or need assistance setting up your profile, our dedicated business support team is ready to assist you.
                    </p>
                    
                    <div class="contact-info-item">
                        <i class="fa-solid fa-headset"></i>
                        <span>partners@easypoint.com</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>+34 632 11 25 71 (Priority Line)</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Visit Knowledge Base</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Mon - Sat: 8:00 AM - 8:00 PM</span>
                    </div>
                </div>

                <div>
                    <form class="contact-form" onsubmit="event.preventDefault(); showToast('Support Request', 'Support request sent. Ticket #99283 created.', 'fa-clipboard-check');">
                        <label>Business Name</label>
                        <input type="text" placeholder="Your Salon/Shop Name" required>
                        
                        <label>Contact Email</label>
                        <input type="email" placeholder="owner@business.com" required>
                        
                        <label>Topic</label>
                        <select style="width: 100%; padding: 12px; background-color: rgba(235, 230, 210, 0.05); border: 1px solid rgba(165, 134, 104, 0.3); border-radius: 6px; color: var(--text-main); margin-bottom: 20px; font-family: inherit;">
                            <option value="" style="background-color: #3a2b28;">Select an issue...</option>
                            <option value="tech" style="background-color: #3a2b28;">Technical Issue</option>
                            <option value="billing" style="background-color: #3a2b28;">Billing & Payments</option>
                            <option value="account" style="background-color: #3a2b28;">Account Management</option>
                        </select>
                        
                        <label>Details</label>
                        <textarea rows="4" placeholder="Describe your issue..." required></textarea>
                        
                        <button type="submit" class="send-btn">Submit Ticket</button>
                    </form>
                </div>
            </div>
        </section>

    </div>

    <!-- FOOTER -->
    <?php include "views/footer.php" ?>

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
                <div class="switch-form">Don't have an account? <span id="go-to-register">Sign up</span></div>
            </div>
            <div id="register-view" class="hidden">
                <h2 class="modal-title">Create Account</h2>
                <p class="modal-subtitle">Join EasyPoint today</p>
                <div id="register-error" style="color: red; margin-bottom: 10px; display: none;"></div>
                <div id="register-success" style="color: green; margin-bottom: 10px; display: none;"></div>
                <form id="register-form">
                    <div class="form-group"><label>Username</label><input type="text" name="username" required class="modal-input"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" required class="modal-input"></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required class="modal-input"></div>
                    <button type="submit" class="modal-btn">Sign Up</button>
                </form>
                <div class="switch-form">Already have an account? <span id="go-to-login">Log In</span></div>
            </div>
        </div>
    </div>

    <div id="store-modal" class="modal-overlay">
        <div class="modal-box">
            <span class="close-store-modal" style="position: absolute; top: 15px; right: 20px; font-size: 28px; font-weight: bold; color: #aaa; cursor: pointer;">&times;</span>
            <h2 class="modal-title">Register your Business</h2>
            <p class="modal-subtitle">List your store on EasyPoint</p>
            <div id="store-error" style="color: red; margin-bottom: 10px; display: none;"></div>
            <div id="store-success" style="color: green; margin-bottom: 10px; display: none;"></div>
            <form id="store-register-form">
                <div class="form-group"><label>Username</label><input type="text" name="username" required class="modal-input"></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" required class="modal-input"></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required class="modal-input"></div>
                <div class="form-group"><label>Business Name</label><input type="text" name="business_name" required class="modal-input"></div>
                <div class="form-group"><label>Address</label><input type="text" name="address" required class="modal-input"></div>
                <div class="form-group"><label>Postal Code</label><input type="text" name="postal_code" class="modal-input"></div>
                <button type="submit" class="modal-btn">Create Business Account</button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/script.js"></script>
</body>
</html>