   <nav class="navigation-bar">
            <div class="logo"><a href="/index.php">EasyPoint</a></div>

            <form action="index.php" method="GET" class="hero-search-bar" style="
                background-color: rgba(235, 230, 210, 0.1); 
                border: 1px solid rgba(165, 134, 104, 0.3);
                border-radius: 50px; 
                padding: 4px; 
                display: flex; 
                align-items: center; 
                box-shadow: 0 4px 20px rgba(0,0,0,0.2); 
                backdrop-filter: blur(5px);">

                <input type="hidden" name="action" value="search">

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="q" placeholder="Search services"
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>

                <div style="width: 1px; height: 25px; background-color: rgba(165, 134, 104, 0.3);"></div>

                <div class="search-field" style="flex: 1; display: flex; align-items: center; padding: 0 15px;">
                    <span class="search-icon" style="color: #a58668; margin-right: 10px; font-size: 16px;">
                        <i class="fa-solid fa-location-dot"></i>
                    </span>
                    <input type="text" name="loc" placeholder="Where?"
                        value="<?php echo htmlspecialchars($locationTerm); ?>"
                        style="border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: #ebe6d2;">
                </div>

                <button type="submit" class="sticky-search-btn" style="
                    background-color: #a58668; 
                    color: #2b201e; 
                    border: none; 
                    padding: 8px 24px; 
                    border-radius: 30px; 
                    cursor: pointer; 
                    font-weight: bold; 
                    margin-left: 5px; 
                    font-size: 14px;">
                    Search
                </button>
            </form>

            <div class="user-menu">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>

                    <div class="dropdown">
                        <span class="user-link dropdown-toggle">
                            Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                            <i class="fa-solid fa-caret-down" style="margin-left: 5px;"></i>
                        </span>
                        <div class="dropdown-menu">
                            <a href="../index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="../index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
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
                            <a href="../index.php?action=dashboard" class="dropdown-item">
                                <i class="fa-solid fa-gauge"></i> Dashboard
                            </a>
                            <a href="../index.php?action=logout" class="dropdown-item" onclick="sessionStorage.clear()">
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

        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">

        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">

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
