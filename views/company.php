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
    <title>EasyPoint - Company</title>
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
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" placeholder="Search services"
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>
                <div style="width: 1px; height: 25px; background-color: rgba(165, 134, 104, 0.3);"></div>
                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;"><i
                            class="fa-solid fa-location-dot"></i></span>
                    <input type="text" name="loc" placeholder="Where?"
                        value="<?php echo htmlspecialchars($locationTerm); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>
                <button type="submit" class="sticky-search-btn"
                    style="background-color: #a58668; color: #2b201e; border: none; padding: 8px 24px; border-radius: 30px; cursor: pointer; font-weight: bold; margin-left: 5px; font-size: 14px;">Search</button>
            </form>

            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item"><i class="fa-solid fa-gauge"></i>
                                Dashboard</a>
                            <a href="index.php?action=logout" class="dropdown-item"><i
                                    class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        </div>
                    </div>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item"><i class="fa-solid fa-gauge"></i>
                                Dashboard</a>
                            <a href="index.php?action=logout" class="dropdown-item"><i
                                    class="fa-solid fa-right-from-bracket"></i> Logout</a>
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
                    Welcome to <strong>EasyPoint</strong>, the premier destination for connecting clients with the best
                    health, beauty, and wellness professionals in their area. Founded in 2026, our mission is simple: to
                    make self-care accessible, convenient, and stress-free.
                </p>
                <p style="margin-bottom: 20px;">
                    We believe that everyone deserves to look and feel their best. That's why we've built a platform
                    that empowers users to discover local talent, compare real reviews, and book appointments
                    instantly—all in one place.
                </p>
                <p>
                    For businesses, EasyPoint provides powerful tools to manage schedules, grow client bases, and
                    showcase their best work. We are more than just a booking system; we are a community dedicated to
                    excellence in service and style.
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
                        Have questions about using EasyPoint? Interested in partnering with us? We'd love to hear from
                        you. Fill out the form or use the contact details below.
                    </p>

                    <div class="contact-info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>support@easypoint.com</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>+34 632 11 25 71</span>
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
                    <form class="contact-form"
                        onsubmit="event.preventDefault(); showToast('Message Sent', 'Message sent! We will contact you shortly.', 'fa-paper-plane');">
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
    
    <!-- FOOTER -->
    <?php include "views/footer.php" ?>

      <?php include "views/modals.php"; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/script.js"></script>
</body>

</html>