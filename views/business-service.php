<?php
// views/business-service.php
if (!isset($businessData)) {
    header("Location: index.php");
    exit();
}

// Obtenemos las imágenes de la galería
$galleryImages = $userModel->getBusinessGallery($businessData['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/styles-business-service.css">
</head>
<body>

    <div class="sticky-header">
        <div class="sticky-container">
           <a href="index.php"><div class="sticky-logo">EasyPoint</div></a> 

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
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <span class="user-link">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span> 
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="../index.php?action=login" class="login-link">Log In/Sign Up</a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <header>
        <nav class="navigation-bar">
         <a href="index.php" >  <div class="logo">EasyPoint</div></a>  

            <div class="search-bar">
                   <input type="text" class="search-input" placeholder="Search services or businesses">
               </div>

            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
                    <a href="../index.php?action=dashboard">
                        <span class="user-link">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span></a>
                    <a href="#" class="business-button" onclick="openStoreModal(event)">List your business</a>
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <span class="user-link">
                        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </span>
                    <a href="../index.php?action=dashboard" class="dashboard-link">Dashboard</a>
                    <a href="../index.php?action=logout" class="logout-link">Logout</a>
                <?php else: ?>
                    <a href="../index.php?action=login" class="login-link">Log In/Sign Up</a>
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
           <section class="gallery-carousel-wrapper">
    <div class="carousel-main">
        <?php if (!empty($galleryImages)): ?>
            <button class="carousel-control prev" onclick="moveCarousel(-1)">&#10094;</button>
            
            <div class="carousel-track" id="carouselTrack">
                <?php foreach ($galleryImages as $img): ?>
                    <div class="carousel-slide">
                        <img src="public/<?php echo htmlspecialchars($img['image_url']); ?>" alt="Business Image">
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-control next" onclick="moveCarousel(1)">&#10095;</button>
            
            <div class="carousel-dots">
                <?php foreach ($galleryImages as $index => $img): ?>
                    <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $index; ?>)"></span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-images-banner">
                <p>Este establecimiento aún no ha añadido fotos a su galería.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
            <div class="business-title">
                <h1><?php echo htmlspecialchars($businessData['business_name']); ?></h1>
                <p>
                    <?php 
                    $parts = [];
                    if (!empty($businessData['address'])) $parts[] = $businessData['address'];
                    if (!empty($businessData['postal_code'])) $parts[] = $businessData['postal_code'];
                    if (!empty($businessData['city'])) $parts[] = $businessData['city'];
                    echo htmlspecialchars(implode(', ', $parts));
                    ?>
                </p>
                <div class="stars">
                    <i class="fas fa-star"></i> 5.0 <span style="color:#b0a8a6">(New)</span>
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
                <p style="font-size: 14px; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($businessData['description'] ?? 'No description available.')); ?>
                </p>
            </div>

            <div>
                <h3 class="info-header">Schedule</h3>
                <div>
                    <?php 
                    $schedule = json_decode($businessData['opening_hours'] ?? '', true);
                    
                    $orderedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    
                    if ($schedule && is_array($schedule)): 
                        foreach ($orderedDays as $day):
                            if (isset($schedule[$day])):
                                $dayData = $schedule[$day];
                                
                                $hoursText = "Closed";
                                
                                $isActive = !empty($dayData['active']) && $dayData['active'] == true;
                                
                                if ($isActive) {
                                    $open = $dayData['open'] ?? '';
                                    $close = $dayData['close'] ?? '';            
                                    if ($open && $close) {
                                        $hoursText = $open . ' - ' . $close;
                                    }
                                }
                            ?>
                            <div class="schedule-row">
                                <span><?php echo htmlspecialchars(ucfirst($day)); ?></span> 
                                <span><?php echo htmlspecialchars($hoursText); ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                    else: ?>
                        <p style="font-size: 13px; color: #666;">Schedule not available.</p>
                    <?php endif; ?>
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
    <script src="public/js/styles-business-service.js"></script>
    <script>
function moveCarousel(direction) {
    const track = document.getElementById('carouselTrack');
    if (!track) return;
    
    const width = track.offsetWidth;
    track.scrollBy({
        left: direction * width,
        behavior: 'smooth'
    });
    // Pequeño retardo para actualizar los puntos después del scroll
    setTimeout(updateDots, 500);
}

function currentSlide(index) {
    const track = document.getElementById('carouselTrack');
    if (!track) return;
    
    const width = track.offsetWidth;
    track.scrollTo({
        left: index * width,
        behavior: 'smooth'
    });
    updateDots();
}

function updateDots() {
    const track = document.getElementById('carouselTrack');
    const dots = document.querySelectorAll('.dot');
    if (!track || dots.length === 0) return;
    
    const index = Math.round(track.scrollLeft / track.offsetWidth);
    
    dots.forEach((dot, i) => {
        if (i === index) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

// Escuchar el scroll manual para que los puntos se muevan solos
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('carouselTrack');
    if (track) {
        track.addEventListener('scroll', () => {
            // Usamos un debounce para no saturar el navegador
            clearTimeout(window.scrollTimer);
            window.scrollTimer = setTimeout(updateDots, 100);
        });
    }
});
</script>
</body>
</html>