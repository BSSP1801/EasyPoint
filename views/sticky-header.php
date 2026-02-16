<div class="sticky-header">
        <div class="sticky-container">
            <div class="sticky-logo"><a href="/index.php">EasyPoint</a></div>

            <form action="index.php" method="GET" class="sticky-search-bar">
                <input type="hidden" name="action" value="search">
                <div class="search-field">
                    <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" placeholder="Search services">
                </div>
                <div class="search-field border-left">
                    <span class="search-icon"><i class="fa-solid fa-location-dot"></i></span>
                    <input type="text" name="loc" placeholder="Where?">
                </div>
                <button type="submit" class="sticky-search-btn">Search</button>
            </form>

            <div class="sticky-menu">
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
        </div>
    </div>