<div class="dashboard-container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>You are logged in as: <strong><?php echo ucfirst($_SESSION['role']); ?></strong></p>

    <hr>

  <?php if ($userData['role'] === 'store'): ?>
    <div class="business-info" style="background: #f4f4f4; padding: 20px; border-radius: 8px;">
        <h2>Store Data</h2>
        <p><strong>Business Name:</strong> <?php echo htmlspecialchars($userData['business_name']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($userData['address']); ?></p>
        <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($userData['postal_code']); ?></p>
        <button>Edit Business Info</button>
    </div>

    <?php else: ?>
       <div class="customer-info">
            <h2>My Appointments</h2>
            <div class="user-actions">
                <a href="index.php?action=book" class="btn">Book New Appointment</a>
                <a href="index.php?action=my-bookings" class="btn">View My History</a>
            </div>
            <p>Check your upcoming visits to your favorite stores.</p>
        </div>
    <?php endif; ?>
</div>