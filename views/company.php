<?php
// views/company.php

// Asegurar que la sesión esté iniciada si no lo está (aunque index.php ya lo hace)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variables para mantener la búsqueda funcional en el header si el usuario quiere buscar desde aquí
$searchTerm = $_GET['q'] ?? '';
$locationTerm = $_GET['loc'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint - Company</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/styles-search-services.css">
    <link rel="stylesheet" href="public/css/styles-company.css">
</head>

<body>

    <div class="sticky-header">
        <div class="sticky-container">
            <div class="sticky-logo"><a href="index.php">EasyPoint</a></div>

            <form action="index.php" method="GET" class="sticky-search-bar">
                <input type="hidden" name="action" value="search">
                <div class="search-field">
                    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" placeholder="Search services" value="<?php echo htmlspecialchars($searchTerm); ?>">
                </div>
                <div class="search-field border-left">
                    <span class="search-icon"><i class="fa-solid fa-location-dot"></i></span>
                    <input type="text" name="loc" placeholder="Where?" value="<?php echo htmlspecialchars($locationTerm); ?>">
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
                            <a href="index.php?action=dashboard" class="dropdown-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                            <a href="index.php?action=logout" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        </div>
                    </div>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
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
        </div>
    </div>

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
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
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
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
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
            <h1 style="font-size: 36px; color: var(--text-main); font-weight: 600;">Company Information</h1>
            <p style="color: var(--soft-text); margin-top: 5px;">Learn more about who we are and get in touch</p>
        </div>
    </header>

    <div class="company-container">
        
        <section id="about" class="content-section">
            <div class="section-header">
                <h2>About Us</h2>
            </div>
            <div class="about-text">
                <p style="margin-bottom: 20px;">
                    Welcome to <strong>EasyPoint</strong>, the premier destination for connecting clients with the best health, beauty, and wellness professionals in their area. Founded in 2026, our mission is simple: to make self-care accessible, convenient, and stress-free.
                </p>
                <p style="margin-bottom: 20px;">
                    We believe that everyone deserves to look and feel their best. That's why we've built a platform that empowers users to discover local talent, compare real reviews, and book appointments instantly—all in one place.
                </p>
                <p>
                    For businesses, EasyPoint provides powerful tools to manage schedules, grow client bases, and showcase their best work. We are more than just a booking system; we are a community dedicated to excellence in service and style.
                </p>
            </div>
        </section>

        <section id="contact" class="content-section">
            <div class="section-header">
                <h2>Contact Us</h2>
            </div>
            
            <div class="contact-grid">
                <div>
                    <p class="about-text" style="margin-bottom: 30px;">
                        Have questions about using EasyPoint? Interested in partnering with us? We'd love to hear from you. Fill out the form or use the contact details below.
                    </p>
                    
                    <div class="contact-info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>support@easypoint.com</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>+1 (555) 123-4567</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Valencia, Spain</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                    </div>
                </div>

                <div>
                    <form class="contact-form" onsubmit="event.preventDefault(); alert('Message sent! We will contact you shortly.');">
                        <label>Your Name</label>
                        <input type="text" placeholder="John Doe" required>
                        
                        <label>Email Address</label>
                        <input type="email" placeholder="john@example.com" required>
                        
                        <label>Subject</label>
                        <input type="text" placeholder="How can we help?" required>
                        
                        <label>Message</label>
                        <textarea rows="5" placeholder="Tell us more..." required></textarea>
                        
                        <button type="submit" class="send-btn">Send Message</button>
                    </form>
                </div>
            </div>
        </section>

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
    
    <script src="public/js/script.js"></script>

</body>
</html>