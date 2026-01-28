<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($businessData['business_name']); ?> - EasyPoint</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <header class="business-header" style="background-image: url('public/<?php echo $businessData['banner_url'] ?? 'assets/images/banner.jpeg'; ?>');">
        <div class="header-content">
            <h1><?php echo htmlspecialchars($businessData['business_name']); ?></h1>
            <p><?php echo htmlspecialchars($businessData['address']); ?>, <?php echo htmlspecialchars($businessData['city']); ?></p>
        </div>
    </header>

    <main class="business-main">
        <section class="about">
            <h2>About us</h2>
            <p><?php echo nl2br(htmlspecialchars($businessData['description'] ?? 'No description available.')); ?></p>
        </section>

        <section class="schedule">
            <h2>Opening Hours</h2>
            <ul>
                <?php 
                $hours = json_decode($businessData['opening_hours'], true);
                if ($hours):
                    foreach ($hours as $day => $info): ?>
                        <li>
                            <strong><?php echo ucfirst($day); ?>:</strong> 
                            <?php echo $info['active'] ? $info['open'] . " - " . $info['close'] : "Closed"; ?>
                        </li>
                    <?php endforeach;
                else: ?>
                    <li>Schedule not available.</li>
                <?php endif; ?>
            </ul>
        </section>
    </main>
</body>
</html>