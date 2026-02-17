<?php
// views/legal.php

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
    <title>EasyPoint - Legal Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            <h1 style="font-size: 36px; color: var(--text-main); font-weight: 600;">Legal Information</h1>
            <p style="color: var(--soft-text); margin-top: 5px;">Transparency is key to our service</p>
        </div>
    </header>

    <div class="company-container">
        
        <section id="privacy" class="content-section">
            <div class="section-header">
                <h2>Privacy Policy</h2>
            </div>
            <div class="about-text">
                <p style="margin-bottom: 20px;">
                    Last updated: January 2026
                </p>
                <p style="margin-bottom: 20px;">
                    At <strong>EasyPoint</strong>, we take your privacy seriously. This Privacy Policy describes how we collect, use, and share your personal information when you use our website and services.
                </p>
                
                <h3 style="color: var(--accent); font-size: 18px; margin-bottom: 10px; margin-top: 25px;">1. Information We Collect</h3>
                <p style="margin-bottom: 15px;">
                    We collect information you provide directly to us, such as when you create an account, book an appointment, or contact customer support. This may include your name, email address, phone number, and payment information.
                </p>

                <h3 style="color: var(--accent); font-size: 18px; margin-bottom: 10px; margin-top: 25px;">2. How We Use Your Information</h3>
                <p style="margin-bottom: 15px;">
                    We use your information to facilitate appointments, improve our platform, and communicate with you about your bookings. We do not sell your personal data to third parties.
                </p>

                <h3 style="color: var(--accent); font-size: 18px; margin-bottom: 10px; margin-top: 25px;">3. Data Security</h3>
                <p>
                    We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction.
                </p>
            </div>
        </section>

        <section id="terms" class="content-section">
            <div class="section-header">
                <h2>Terms of Service</h2>
            </div>
            <div class="about-text">
                <p style="margin-bottom: 20px;">
                    Please read these Terms of Service carefully before using the EasyPoint platform.
                </p>

                <h3 style="color: var(--accent); font-size: 18px; margin-bottom: 10px; margin-top: 25px;">1. Acceptance of Terms</h3>
                <p style="margin-bottom: 15px;">
                    By accessing or using our services, you agree to be bound by these Terms. If you disagree with any part of the terms, you may not access the service.
                </p>

                <h3 style="color: var(--accent); font-size: 18px; margin-bottom: 10px; margin-top: 25px;">2. Booking & Cancellations</h3>
                <p style="margin-bottom: 15px;">
                    When you book an appointment, you agree to honor the scheduled time. Cancellations must be made in accordance with the individual business's cancellation policy. EasyPoint is not responsible for fees incurred due to late cancellations or no-shows.
                </p>

                <h3 style="color: var(--accent); font-size: 18px; margin-bottom: 10px; margin-top: 25px;">3. User Conduct</h3>
                <p style="margin-bottom: 15px;">
                    You agree not to use the platform for any unlawful purpose or to solicit others to perform or participate in any unlawful acts. We reserve the right to terminate accounts that violate our community guidelines.
                </p>

                <h3 style="color: var(--accent); font-size: 18px; margin-bottom: 10px; margin-top: 25px;">4. Limitation of Liability</h3>
                <p>
                    EasyPoint acts as an intermediary between users and businesses. We are not liable for the quality of services provided by the businesses listed on our platform.
                </p>
            </div>
        </section>

    </div>

    <!-- FOOTER -->
    <?php include "views/footer.php" ?>


    <!-- MODALS -->
        <?php include "views/modals.php"; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/script.js"></script>

</body>
</html>