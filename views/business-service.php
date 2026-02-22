<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/service.php';
require_once __DIR__ . '/../models/user.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($businessData)) {
    $userData = $businessData;
} else {
    header("Location: ../index.php");
    exit();
}

$storeId = $_GET['id'];
$userModel = new User();

$store = $userModel->getFullProfile($storeId);

if (!$store) {
    header("Location: ../index.php");
    exit();
}

$reviews = $userModel->getReviews($store['business_profile_id']);

$averageRating = 5.0; // Por defecto es la máxima si no hay reseñas
$totalReviews = count($reviews);

if ($totalReviews > 0) {
    $sumRatings = 0;
    foreach ($reviews as $review) {
        $sumRatings += $review['rating'];
    }
    // Calculamos la media y redondeamos a 1 decimal (ej. 4.5)
    $averageRating = number_format($sumRatings / $totalReviews, 1);
}
// --- FIN CÁLCULO DE MEDIA ---

$serviceModel = new Service();

$serviceModel = new Service();

$serviceModel = new Service();
$storeServices = $serviceModel->getAllByUserId($storeId);
$targetUserId = $userData['user_id'] ?? $userData['id'];

$logoUrl = !empty($store['logo_url'])
    ? '../public/' . htmlspecialchars($store['logo_url'])
    : '../public/assets/images/tienda-1.png';

$bannerUrl = !empty($store['banner_url'])
    ? '../public/' . htmlspecialchars($store['banner_url'])
    : '../public/assets/images/img-resource-1.jpeg';

$businessName = htmlspecialchars($store['business_name'] ?? 'Negocio sin nombre');

$searchTerm = $_GET['q'] ?? '';
$locationTerm = $_GET['loc'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyPoint</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/styles-business-service.css">
    <link rel="icon" type="image/svg+xml" href="public/assets/images/favicon.svg">
</head>

<body>

 <?php include "views/sticky-header.php"; ?>
    <header>
        <?php include "views/navigation-bar.php"; ?>
       
       
    </header>

    <div class="main-container">

        <div class="left-panel">
            <div class="gallery-carousel-wrapper">
                <div class="carousel-main">

                    <button class="carousel-control prev" id="btnPrev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="carousel-control next" id="btnNext">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <div class="carousel-track" id="carouselTrack">
                        <?php if (!empty($galleryImages)): ?>
                            <?php foreach ($galleryImages as $image): ?>
                                <div class="carousel-slide">
                                    <img src="/public/<?php echo htmlspecialchars($image['image_url']); ?>" alt="Gallery Image">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="carousel-slide">
                                <img src="../public/assets/images/img-resource-1.jpeg" alt="Default Image">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="carousel-dots" id="carouselDots">
                    </div>
                </div>
            </div>
            <div class="business-title">
                <h1><?php echo htmlspecialchars($store['business_name']); ?></h1>

                <?php if (!empty($store['business_type']) && $store['business_type'] !== 'General'): ?>
                    <span
                        style="background-color: #a58668; color: #2b201e; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 8px;">
                        <?php echo htmlspecialchars($store['business_type']); ?>
                    </span>
                <?php endif; ?>

                <p>
                    <?php
                    $parts = [];
                    if (!empty($store['address']))
                        $parts[] = $store['address'];
                    if (!empty($store['postal_code']))
                        $parts[] = $store['postal_code'];
                    if (!empty($store['city']))
                        $parts[] = $store['city'];
                    echo htmlspecialchars(implode(', ', $parts));
                    ?>
                </p>
                <div class="stars" style="color: #a58668; font-weight: bold;">
    <i class="fas fa-star"></i> <?php echo $averageRating; ?> 
    <span style="color:#b0a8a6; font-weight: normal; font-size: 0.9em;">
        <?php if ($totalReviews > 0): ?>
            (<?php echo $totalReviews; ?> opinions)
        <?php else: ?>
            (New)
        <?php endif; ?>
    </span>
</div>
            </div>

            <div>
                <h2 class="section-title">Services</h2>

                <?php if (empty($storeServices)): ?>
                    <p style="color: #888; font-style: italic;">No services listed yet.</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                        <?php foreach ($storeServices as $service): ?>
                            <div class="service-row"
                                style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px;">

                                <h4 style="margin: 0; font-size: 16px; color: #ffffff; flex: 1;">
                                    <?php echo htmlspecialchars($service['name']); ?>
                                </h4>

                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div class="service-details" style="text-align: right;">
                                        <span class="price-tag" style="display: block; font-weight: bold; font-size: 15px;">
                                            <?php echo htmlspecialchars($service['price']); ?> €
                                        </span>
                                        <span class="duration-tag" style="font-size: 12px; color: #888;">
                                            <?php echo htmlspecialchars($service['duration']); ?> min
                                        </span>
                                    </div>

                                    <a href="../index.php?action=book&service_id=<?php echo htmlspecialchars($service['id']); ?>&store_id=<?php echo htmlspecialchars($storeId); ?>" 
                                        class="book-btn">
                                        Book
                                    </a>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="reviews-section" style="margin-top: 40px; padding-top: 20px;">
    <h2 class="section-title">Reviews</h2>

    <?php if (isset($_SESSION['user_id'])): ?>
        <form id="review-form" style="background: #2b201e; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
            <h3 style="color: #cbbba6; margin-top: 0;">Give a review</h3>
<input type="hidden" name="business_id" value="<?php echo htmlspecialchars($store['business_profile_id']); ?>">            
            <div class="star-rating" style="font-size: 24px; color: #a58668; margin-bottom: 15px; cursor: pointer;">
                <i class="far fa-star" data-rating="1"></i>
                <i class="far fa-star" data-rating="2"></i>
                <i class="far fa-star" data-rating="3"></i>
                <i class="far fa-star" data-rating="4"></i>
                <i class="far fa-star" data-rating="5"></i>
                <input type="hidden" name="rating" id="rating-value" value="0">
            </div>

            <textarea name="comment" maxlength="500" placeholder="Write your comment here (max. 500 characters)..." 
    style="width: 100%; padding: 10px; border-radius: 8px; background: #3d2d2a; color: white; border: 1px solid #a58668; margin-bottom: 10px; resize: vertical; min-height: 80px;"></textarea>
            
            <button type="submit" class="book-btn" style="width: auto; padding: 10px 25px;">Send Review</button>
        </form>
    <?php else: ?>
        <p style="color: #888; font-style: italic;">Log in to leave a review.</p>
    <?php endif; ?>

    <div id="reviews-list" style="margin-top: 20px;">
    <?php if (empty($reviews)): ?>
        <p style="color: #888; font-style: italic;">There are no reviews yet. Be the first to review it!</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="review-item" style="border-bottom: 1px solid rgba(165, 134, 104, 0.2); padding: 15px 0;">
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="color: #cbbba6; font-size: 16px;">
                        <?php echo htmlspecialchars($review['username']); ?>
                    </strong>
                    <div style="color: #a58668; font-size: 14px;">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="review-text-scroll" style="
                    color: #ebe6d2; 
                    font-size: 14px; 
                    line-height: 1.5; 
                    word-wrap: break-word;       
                    overflow-wrap: break-word;   
                    word-break: break-word;      
                    max-height: 100px;           
                    overflow-y: auto;            
                    padding-right: 10px; /* Espacio extra para que no choque con la barra */
                ">
                    <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                </div>

                <small style="color: #888; font-size: 12px; display: block; margin-top: 8px;">
                    <?php echo date('d/m/Y', strtotime($review['created_at'])); ?>
                </small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</div>
        </div>

        <div class="right-panel">
            <div class="map-box"
                style="padding: 0; overflow: hidden; height: 300px; border-radius: 12px; border: 1px solid #e0e0e0;">
                <?php
                $mapParts = [];
                // Limpiamos la dirección de espacios extra
                if (!empty($store['address']))
                    $mapParts[] = trim($store['address']);
                if (!empty($store['postal_code']))
                    $mapParts[] = trim($store['postal_code']);
                if (!empty($store['city']))
                    $mapParts[] = trim($store['city']);

                // AÑADIDO: Agregamos el país al final para evitar confusiones
                if (!empty($mapParts)) {
                    $addressString = implode(', ', $mapParts) . ", España";
                } else {
                    $addressString = "Valencia, España";
                }

                $encodedAddress = urlencode($addressString);
                ?>

                <iframe width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen
                    src="https://maps.google.com/maps?q=<?php echo $encodedAddress; ?>&t=&z=15&ie=UTF8&iwloc=&output=embed">
                </iframe>
            </div>

            <div>
                <h3 class="info-header">About Us</h3>
                <p style="font-size: 14px; line-height: 1.5;">
                    <?php echo nl2br(htmlspecialchars($store['description'] ?? 'No description available.')); ?>
                </p>
            </div>

            <div>
                <h3 class="info-header">Schedule</h3>
                <div>
                    <?php
                    $schedule = json_decode($store['opening_hours'] ?? '', true);

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
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">

                    <?php
                    $socials = [
                        'website' => ['icon' => 'fas fa-globe', 'val' => $store['website'] ?? '', 'type' => 'url'],
                        'instagram' => ['icon' => 'fab fa-instagram', 'val' => $store['instagram_link'] ?? '', 'type' => 'instagram'],
                        'facebook' => ['icon' => 'fab fa-facebook', 'val' => $store['facebook_link'] ?? '', 'type' => 'url'],
                        'tiktok' => ['icon' => 'fab fa-tiktok', 'val' => $store['tiktok_link'] ?? '', 'type' => 'tiktok'],
                        'twitter' => ['icon' => 'fab fa-twitter', 'val' => $store['twitter_link'] ?? '', 'type' => 'twitter']
                    ];

                    $hasSocial = false;

                    foreach ($socials as $key => $data):
                        $input = trim($data['val']);

                        if (!empty($input)):
                            $hasSocial = true;
                            $finalLink = $input;

                            if (!preg_match("~^(?:f|ht)tps?://~i", $input)) {

                                $cleanUser = ltrim($input, '@');

                                switch ($data['type']) {
                                    case 'tiktok':
                                        $finalLink = "https://www.tiktok.com/@" . $cleanUser;
                                        break;

                                    case 'instagram':
                                        $finalLink = "https://www.instagram.com/" . $cleanUser;
                                        break;

                                    case 'twitter':
                                        $finalLink = "https://twitter.com/" . $cleanUser;
                                        break;

                                    default:
                                        $finalLink = "https://" . $input;
                                        break;
                                }
                            }
                            ?>
                            <a href="<?php echo htmlspecialchars($finalLink); ?>" target="_blank" rel="noopener noreferrer"
                                style="color: #cbbba6; text-decoration: none; font-size: 22px; transition: color 0.3s;"
                                onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#cbbba6'">
                                <i class="<?php echo $data['icon']; ?>"></i>
                            </a>
                        <?php
                        endif;
                    endforeach;

                    if (!$hasSocial): ?>
                        <p style="font-size: 13px; color: #666; font-style: italic;">No social media linked.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
<?php include "views/footer.php" ?>

    <?php include "views/modals.php"; ?>

    <div id="toast"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
       <script src="public/js/script.js"></script>
    <script src="../public/js/script-business-service.js"></script>
</body>

</html>