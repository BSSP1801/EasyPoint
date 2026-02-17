

    <div id="confirmationModal" class="modal-overlay">
        <div class="modal-box booking-modal">
            <button class="close-btn" id="closeConfirmBtn" onclick="closeConfirmationModal()">×</button>

            <div class="modal-content">
                <h2 class="modal-title">Booking Details</h2>

                <div class="confirmation-details">
                    <div class="detail-block">
                        <h4>Service</h4>
                        <p id="confirmServiceName"><?php echo htmlspecialchars($service['name']); ?></p>
                    </div>

                    <div class="detail-block">
                        <h4>Business</h4>
                        <p id="confirmBusinessName"><?php echo htmlspecialchars($store['business_name']); ?></p>
                    </div>

                    <div class="detail-block">
                        <h4>Date & Time</h4>
                        <p id="confirmDateTime">-</p>
                    </div>

                    <div class="detail-block">
                        <h4>Duration</h4>
                        <p id="confirmDuration"><?php echo htmlspecialchars($service['duration']); ?> minutes</p>
                    </div>

                    <div class="detail-block total">
                        <h4>Total Price</h4>
                        <p id="confirmPrice" class="price"><?php echo number_format($service['price'], 2); ?> €</p>
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="btn-confirm" id="confirmBookingBtn" onclick="confirmBooking()">
                        Confirm
                    </button>
                    <button class="btn-cancel" onclick="cancelBooking()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    
    <div id="cancelConfirmModal" class="modal-overlay">
        <div class="modal-box booking-modal">
            <h2 class="modal-title">Cancel Booking?</h2>
            <p class="cancel-message">Are you sure you want to cancel this booking?</p>
            <div class="modal-actions">
                <button class="btn-confirm" onclick="confirmCancel()">
                    Yes, cancel
                </button>
                <button class="btn-cancel" onclick="continueProceedBooking()">
                    Continue booking
                </button>
            </div>
        </div>
    </div>


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
                        <div style="text-align: right; margin-top: 5px;">
                            <small id="go-to-forgot" style="cursor: pointer; color: #888;">Forgot password?</small>
                        </div>
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
    <div id="forgot-modal" class="modal-overlay" style="display: none;">
        <div class="modal-box">
            <span class="close-forgot-modal"
                style="position: absolute; top: 15px; right: 20px; font-size: 28px; cursor: pointer;">&times;</span>

            <h2 class="modal-title">Reset Password</h2>
            <p class="modal-subtitle">Enter your email to receive a reset link</p>

            <div id="forgot-message" style="margin-bottom: 10px; display: none;"></div>

            <form id="forgot-form">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required class="modal-input">
                </div>
                <button type="submit" class="modal-btn">Send Link</button>
            </form>

            <div class="switch-form">
                Remembered? <span id="back-to-login"
                    style="cursor: pointer; color: var(--accent); text-decoration: underline;">Log In</span>
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


