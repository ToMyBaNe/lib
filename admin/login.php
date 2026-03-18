<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BASC LSMS | Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="assets/imgs/lib-logo-no-bg.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            green: '#16a34a',
                            gold: '#fbbf24',
                            dark: '#064e3b'
                        }
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/admin.css">
    <style>
        .login-page { min-height: 100vh; background: #ecfdf3; display: flex; align-items: center; justify-content: center; padding: 1.5rem; font-family: 'Inter', sans-serif;  }
        .login-card { width: 100%; max-width: 420px; background: #ffffff; border-radius: 18px; border: 1px solid rgba(22,163,74,0.18); box-shadow: 0 18px 45px rgba(15,118,110,0.08); overflow: hidden; }
        .login-card__head { padding: 2rem; text-align: center;  }
        .login-card__title { font-size: 1.25rem; font-weight: 600; color: #052e16; margin-bottom: 0.25rem; }
        .login-card__sub { font-size: 0.875rem; color: #166534; }
        .login-card__body { padding: 1.5rem 2rem 2rem; }
        .login-card__footer { padding: 1rem 2rem; background: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center; font-size: 0.8125rem; color: #64748b; }
        .login-card__footer a { color: #16a34a; text-decoration: none; font-weight: 500; }
        .login-card__footer a:hover { text-decoration: underline; }
    </style>
</head>
<body class="login-page flex-col gap-4">
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl shadow-lg shadow-emerald-900/10">
            <img src="assets/imgs/lib-logo-no-bg.png" alt="">
        </div>
        <h1 class="text-2xl font-semibold text-brand-dark">BASC Library</h1>
        <h2 class="text-sm text-emerald-700">Library Survey Management System</h2>
    </div>
    <div class="login-card">
        <div class="login-card__head">
            <h1 class="login-card__title">Login</h1>
            <p class="login-card__sub">Sign in to continue</p>
        </div>
        
        <form id="loginForm" class="login-card__body space-y-4">
            <div id="errorMessage" style="display: none;" class="mb-2 flex items-center gap-2 rounded-md border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-700">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
                <span id="errorText"></span>
            </div>
            <div>
                <label for="username" class="mb-1 block text-sm font-medium text-emerald-900">Username</label>
                <input type="text" id="username" name="username" class="block w-full rounded-lg border border-emerald-100 bg-white px-3 py-2 text-sm shadow-sm outline-none ring-0 focus:border-brand-green focus:ring-2 focus:ring-brand-green/40" placeholder="Enter username">
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-emerald-900">Password</label>
                <input type="password" id="password" name="password" class="block w-full rounded-lg border border-emerald-100 bg-white px-3 py-2 text-sm shadow-sm outline-none ring-0 focus:border-brand-green focus:ring-2 focus:ring-brand-green/40" placeholder="Enter password" autocomplete="current-password">
            </div>
            
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-green px-4 py-2.5 text-sm font-medium text-white shadow-md shadow-emerald-900/10 transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-brand-gold/60 focus:ring-offset-1">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Logging in…</span>';
            
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
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Login</span>';
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
