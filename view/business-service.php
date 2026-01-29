<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styles-business-service.css">
    <style>
        
    </style>
</head>
<body>

    <div class="sticky-header">
        <div class="sticky-container">
            <div class="sticky-logo">EasyPoint</div>

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
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <span class="user-link">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span> 
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <header>
        <nav class="navigation-bar">
            <div class="logo">EasyPoint</div>

            <div class="search-bar">
                   <input type="text" class="search-input" placeholder="Search services or businesses">
               </div>

            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <a href="index.php?action=dashboard">
                        <span class="user-link">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span></a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <span class="user-link">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                    <a href="index.php?action=dashboard" class="dashboard-link">Dashboard</a>
                    <a href="index.php?action=logout" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="index.php?action=login" class="login-link">Log In/Sign Up</a>
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
            <div class="main-img">
                <i class="far fa-heart" style="position: absolute; top: 15px; right: 15px; font-size: 20px;"></i>
                <div style="text-align: center;">
                    <i class="fas fa-cut" style="font-size: 50px;"></i>
                    <h2 style="margin-top: 10px; text-transform: uppercase;">Estilo Urbano</h2>
                </div>
            </div>

            <div class="gallery-thumbs">
                <div class="thumb"><i class="fas fa-cut"></i></div>
                <div class="thumb"><i class="fas fa-cut"></i></div>
                <div class="thumb"><i class="fas fa-cut"></i></div>
            </div>

            <div class="business-title">
                <h1>Fuensanta Barbershop</h1>
                <p>c/Riel Saud 3, 46014, Valencia</p>
                <div class="stars">
                    <i class="fas fa-star"></i> 4,73 <span style="color:#b0a8a6">(173 reviews)</span>
                </div>
            </div>

            <div>
                <h2 class="section-title">Services</h2>
                <h3 style="font-size: 16px; margin-bottom: 10px;">Popular Services</h3>
                
                <div class="service-row">
                    <h4>Haircut</h4>
                    <div style="display: flex; align-items: center;">
                        <div class="service-details">
                            <span class="price-tag">14,00 €</span>
                            <span class="duration-tag">30min</span>
                        </div>
                        <button class="book-btn">Book</button>
                    </div>
                </div>

                <div class="service-row">
                    <h4>Haircut & Beard</h4>
                    <div style="display: flex; align-items: center;">
                        <div class="service-details">
                            <span class="price-tag">22,00 €</span>
                            <span class="duration-tag">45min</span>
                        </div>
                        <button class="book-btn">Book</button>
                    </div>
                </div>

                <div class="service-row">
                    <h4>Beard</h4>
                    <div style="display: flex; align-items: center;">
                        <div class="service-details">
                            <span class="price-tag">9,00 €</span>
                            <span class="duration-tag">15min</span>
                        </div>
                        <button class="book-btn">Book</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="map-box">
                <i class="fas fa-map-marker-alt" style="font-size: 30px; margin-right: 10px;"></i>
                <span>Valencia Map</span>
            </div>

            <div>
                <h3 class="info-header">About Us</h3>
                <p style="font-size: 14px; line-height: 1.5;">Hair ... <br> -- <br> --</p>
            </div>

            <div>
                <h3 class="info-header">Schedule</h3>
                <div>
                    <div class="schedule-row"><span>Sunday</span> <span>10:00 AM - 4:40 PM</span></div>
                    <div class="schedule-row"><span>Monday</span> <span>09:00 AM - 06:00 PM</span></div>
                    <div class="schedule-row"><span>Tuesday</span> <span>09:00 AM - 09:00 PM</span></div>
                    <div class="schedule-row"><span>Wednesday</span> <span>09:00 AM - 09:00 PM</span></div>
                    <div class="schedule-row"><span>Thursday</span> <span>09:00 AM - 09:00 PM</span></div>
                    <div class="schedule-row"><span>Friday</span> <span>09:00 AM - 09:00 PM</span></div>
                    <div class="schedule-row"><span>Saturday</span> <span>09:00 AM - 09:00 PM</span></div>
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

    <script src="../public/js/styles-business-service.js"></script>
</body>
</html>