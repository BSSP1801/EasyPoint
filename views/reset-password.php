<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - EasyPoint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/styles.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .reset-card { background-color: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 15px; width: 100%; max-width: 400px; border: 1px solid rgba(165, 134, 104, 0.3); }
        /* h2 { color: var(--accent); text-align: center; margin-bottom: 30px; }
        .form-control { background: rgba(255, 255, 255, 0.1); border: 1px solid #444; color: #fff; }
        .form-control:focus { background: rgba(255, 255, 255, 0.15); color: #fff; border-color: var(--accent); box-shadow: none; } */
    </style>
</head>
<body>
    <div class="reset-card">
        <h2>Reset Password</h2>
        <div id="reset-message" class="alert d-none"></div>
        
        <form id="reset-password-form">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn w-100" style="background-color: var(--accent); color: var(--bg-dark); font-weight: bold;">Update Password</button>
        </form>
    </div>

    <script>
        document.getElementById('reset-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const msgDiv = document.getElementById('reset-message');
            
            fetch('index.php?action=reset_password_action', {
                method: 'POST', body: formData
            })
            .then(res => res.json())
            .then(data => {
                msgDiv.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                msgDiv.textContent = data.message;
                msgDiv.classList.remove('d-none');
                
                if(data.success) {
                    setTimeout(() => window.location.href = 'index.php', 2000);
                }
            });
        });
    </script>
</body>
</html>