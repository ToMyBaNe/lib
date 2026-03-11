<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Library Survey</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/admin.css">
    <style>
        .login-page { min-height: 100vh; background: #f8fafc; display: flex; align-items: center; justify-content: center; padding: 1.5rem; font-family: 'Inter', sans-serif; }
        .login-card { width: 100%; max-width: 400px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden; }
        .login-card__head { padding: 2rem; text-align: center; border-bottom: 1px solid #f1f5f9; }
        .login-card__title { font-size: 1.25rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem; }
        .login-card__sub { font-size: 0.875rem; color: #64748b; }
        .login-card__body { padding: 1.5rem 2rem 2rem; }
        .login-card__footer { padding: 1rem 2rem; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; font-size: 0.8125rem; color: #64748b; }
        .login-card__footer a { color: #3b82f6; text-decoration: none; font-weight: 500; }
        .login-card__footer a:hover { text-decoration: underline; }
    </style>
</head>
<body class="login-page">
    <div class="login-card">
        <div class="login-card__head">
            <h1 class="login-card__title">Login</h1>
            <p class="login-card__sub">Sign in to continue</p>
        </div>
        
        <form id="loginForm" class="login-card__body space-y-4">
            <div>
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" required class="form-input" placeholder="Enter username" autocomplete="username">
            </div>
            <div>
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" required class="form-input" placeholder="Enter password" autocomplete="current-password">
            </div>
            <div id="errorMessage" style="display: none;" class="alert alert-danger">
                <p id="errorText"></p>
            </div>
            <button type="submit" class="btn btn-primary w-full py-2.5">
                <i class="fas fa-sign-in-alt"></i>
                <span>Sign in</span>
            </button>
        </form>
    </div>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Signing in…</span>';
            
            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);
                
                const response = await fetch('./api/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = './dashboard.php';
                } else {
                    document.getElementById('errorText').textContent = data.message || 'Login failed';
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Sign in</span>';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('errorText').textContent = 'Network error: ' + error.message + '. Make sure database is running.';
                errorDiv.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Sign in</span>';
            }
        });
    </script>
</body>
</html>
