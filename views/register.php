<?php if (!empty($error_message)): ?>
    <div style="background: #fee; color: #b00; padding: 10px; border: 1px solid #b00; margin-bottom: 10px;">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<?php if (!empty($success_message)): ?>
    <div style="background: #efe; color: #080; padding: 10px; border: 1px solid #080; margin-bottom: 10px;">
        <?php echo $success_message; ?>
    </div>
<?php endif; ?>
<div class="register-container">
    <h2>Create your Account</h2>
    <form action="index.php?action=register" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="index.php?action=login">Login here</a></p>
</div>