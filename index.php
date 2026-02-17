<?php
// --- DEBUG START ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// --- DEBUG END ---
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/BookingController.php';
require_once __DIR__ . '/controllers/ReviewController.php';
require_once __DIR__ . '/models/service.php';

session_start();

$action = $_GET['action'] ?? 'home';
$controller = new UserController();
$userModel = new User();

$stores = [];

if ($action === 'home') {
    $searchTerm = $_GET['q'] ?? null;
    $locationTerm = $_GET['loc'] ?? null;
    $categoryFilter = $_GET['category'] ?? null;

    if ($searchTerm || $locationTerm) {
        $stores = $userModel->searchStores($searchTerm, $locationTerm);
    } else {
        $stores = $userModel->getRecommendedStores($categoryFilter);
    }
}

include "views/action-switch.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint - Appointment System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/styles.css">
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
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
                                <i class="fa-solid fa-right-from-bracket"></i> Logout
                            </a>
                        </div>
                    </div>

                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] === 'store'): ?>
                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </span>
                        <div class="dropdown-menu">
                            <a href="index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
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

        <div class="central-content">
            <h1 class="main-title">Believe in yourself</h1>
            <p class="subtitle">Discover and book an appointment with beauty and wellness professionals near you</p>

            <form action="index.php" method="GET" class="hero-search-bar" style="
    background-color: rgba(235, 230, 210, 0.1); 
    border: 1px solid rgba(165, 134, 104, 0.3);
    border-radius: 50px; 
    padding: 5px; 
    display: flex; 
    align-items: center; 
    box-shadow: 0 4px 20px rgba(0,0,0,0.2); 
    max-width: 750px; 
    margin: 30px auto; 
    backdrop-filter: blur(5px);">

                <input type="hidden" name="action" value="search">

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 10px 20px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 15px; font-size: 18px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" placeholder="Search services"
                        value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 16px; background: transparent; color: #ebe6d2;">
                </div>

                <div style="width: 1px; height: 30px; background-color: rgba(165, 134, 104, 0.3);"></div>

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 10px 20px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 15px; font-size: 18px;">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <input type="text" name="loc" placeholder="Where?"
                        value="<?php echo htmlspecialchars($_GET['loc'] ?? ''); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 16px; background: transparent; color: #ebe6d2;">
                </div>

                <button type="submit" class="sticky-search-btn" style="
        background-color: #a58668; 
        color: #2b201e; 
        border: none; 
        padding: 12px 30px; 
        border-radius: 30px; 
        cursor: pointer; 
        font-weight: bold; 
        margin-left: 5px; 
        font-size: 16px;">
                    Search
                </button>
            </form>
            <ul class="category-list">
                <?php
                // Definimos las categorías
                $categories = [
                    'Hair Salon',
                    'Barbershop',
                    'Nail Salon',
                    'Hair Removal',
                    'Eyebrows & Lashes',
                    'Skincare',
                    'Massage',
                    'Makeup'
                ];
                function buildUrl($newCategory = null)
                {
                    $params = $_GET; // Copia los parámetros actuales (q, loc, action, etc.)
                    $params['action'] = 'search'; // Aseguramos que la acción sea 'search'
                
                    if ($newCategory) {
                        $params['category'] = $newCategory;
                    } else {
                        unset($params['category']); // Si es para limpiar filtro, quitamos la categoría
                    }

                    return 'index.php?' . http_build_query($params);
                }

                ?>

                <?php foreach ($categories as $cat): ?>
                    <?php
                    // Clase activa si es la categoría actual
                    $activeClass = ($categoryFilter === $cat) ? 'font-weight: bold; text-decoration: underline;' : '';
                    ?>
                    <li>
                        <a href="<?php echo buildUrl($cat); ?>" class="cat-link" style="<?php echo $activeClass; ?>">
                            <?php echo $cat; ?>
                        </a>
                    </li>
                <?php endforeach; ?>

                <?php if (!empty($categoryFilter)): ?>
                    <li>
                        <a href="<?php echo buildUrl(null); ?>" class="cat-link" style="color: #d9534f;">
                            Clear Filters
                        </a>
                    </li>
                <?php elseif (!empty($searchTerm) || !empty($locationTerm)): ?>
                    <li>
                        <a href="index.php?action=search" class="cat-link" style="font-weight: bold;">
                            View All Stores
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <section class="recommended-section">
        <h2 class="section-title">Recommended</h2>

        <div class="carousel-container">
            <button class="arrow-button left-arrow">
                <i class="fa-solid fa-arrow-left"></i>
            </button>

            <div class="shops-grid">
                <?php if (empty($stores)): ?>
                    <p style="padding: 20px;">No stores available yet. Be the first to join!</p>
                <?php else: ?>
                    <?php foreach ($stores as $store): ?>
                        <?php
                        $name = !empty($store['business_name']) ? htmlspecialchars($store['business_name']) : 'Unnamed Business';

                        $addressParts = [];
                        if (!empty($store['address']))
                            $addressParts[] = htmlspecialchars($store['address']);
                        if (!empty($store['postal_code']))
                            $addressParts[] = htmlspecialchars($store['postal_code']);
                        if (!empty($store['city']))
                            $addressParts[] = htmlspecialchars($store['city']);
                        $fullAddress = implode(', ', $addressParts);

                        $image = !empty($store['logo_url']) ? 'public/' . htmlspecialchars($store['logo_url']) : 'public/assets/images/tienda-1.png';
                        $ratingVal = isset($store['avg_rating']) ? number_format($store['avg_rating'], 1) : '5.0';
                        $reviewCount = isset($store['review_count']) ? $store['review_count'] : 0;

                        $reviewText = ($reviewCount > 0) ? "($reviewCount)" : "New";
                        ?>

                        <a href="index.php?action=view_business&id=<?php echo $store['id']; ?>"
                            style="text-decoration: none; color: inherit;">
                            <article class="shop-card">
                                <div class="image-container">
                                    <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" class="shop-image">
                                    <div class="rating-label">
                                        <?php echo number_format($store['avg_rating'], 1); ?>
                                        <span class="reviews-text">
                                            <?php echo ($store['review_count'] > 0) ? '(' . $store['review_count'] . ')' : 'New'; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="shop-info">
                                    <h3 class="shop-name"><?php echo $name; ?></h3>
                                    <p class="shop-address"><?php echo $fullAddress; ?></p>
                                    <span class="sponsored-text">Recommended</span>
                                </div>
                            </article>
                        </a>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button class="arrow-button right-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </section>

    <section class="features-section">
        <div class="features-container">
            <div class="features-image-wrapper">
                <img src="public/assets/images/img-resource-1.png" alt="Beauty Experience" class="features-image">
            </div>
            <div class="features-content">
                <h2 class="features-title">Book with top professionals near you</h2>
                <p class="features-text">
                    Navigate through our platform to discover the finest health and beauty businesses available on
                    EasyPoint. We curate the best local professionals to ensure high-quality service.
                </p>
                <p class="features-text">
                    Check out business profiles and read verified reviews from other users to make an informed decision.
                    You can also explore their portfolios to see the real results of their work before you book.
                </p>
                <p class="features-text">
                    Save time and leave the stress behind. With EasyPoint, booking your next beauty appointment is free,
                    easy, and fast, giving you more time to focus on yourself.
                </p>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="features-container">
            <div class="features-content">
                <h2 class="features-title">Discover and book with the best local talent</h2>

                <p class="features-text">
                    Explore our platform to find the highest-rated health and beauty businesses available on EasyPoint.
                    We carefully select top-tier professionals to ensure you receive only quality service.
                </p>

                <p class="features-text">
                    Make the right choice by checking out business profiles and reading verified reviews from real
                    clients. You can also browse their portfolios to see the results of their work before you commit.
                </p>

                <p class="features-text">
                    Save time and skip the stress. With EasyPoint, securing your next appointment is simple, free, and
                    instant, leaving you more time to focus on what matters: you.
                </p>
            </div>

            <div class="features-image-wrapper">
                <img src="public/assets/images/img-resource-2.png" alt="Booking Illustration" class="features-image">
            </div>
        </div>
    </section>

    <section class="faq-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8">

                    <h2 class="features-title text-center">Frequently Asked Questions</h2>
                    <p class="subtitle text-center mb-5">Find answers to common questions about EasyPoint</p>

                    <div class="accordion" id="faqAccordion">

                        <div class="accordion-item easypoint-accordion">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    How do I book an appointment?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    It's very simple. Use the search bar to find a service (like "haircut") or a
                                    specific business. Select the store you like, choose a service, pick a date and
                                    time, and confirm your booking. You will need to log in to finalize the appointment.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item easypoint-accordion">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Is it free to use EasyPoint?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! For customers, browsing and booking appointments on EasyPoint is completely
                                    free. You only pay the business for the service you receive, according to their
                                    prices.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item easypoint-accordion">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Can I cancel or reschedule my appointment?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, you can manage your bookings from your <strong>Dashboard</strong> under the
                                    "Appointments" tab. Please note that cancellations are subject to the specific
                                    policy of each business.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item easypoint-accordion">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Do I need to pay online?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Currently, EasyPoint is a booking platform. Payments are typically handled directly
                                    at the business location after you receive your service, unless the store specifies
                                    otherwise.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item easypoint-accordion">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    I own a business, how can I list it here?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                                data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We would love to have you! Click on the "List your business" button in the menu,
                                    create a business account, and fill in your details. Once registered, you can start
                                    managing your services and receiving bookings immediately.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "views/footer.php" ?>


    <?php include "views/modals.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/script.js"></script>
</body>

</html>