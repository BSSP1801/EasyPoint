<div class="login-container">
    <h2>Login to EasyPoint</h2>
    
    <?php if (!empty($error_message)): ?>
        <div style="color: red; margin-bottom: 10px;"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="index.php?action=login" method="POST">
        <input type="text" name="identifier" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="index.php?action=register">Register here</a></p>
</div>