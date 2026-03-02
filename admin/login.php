<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Library Survey</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .login-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-lock text-4xl mb-2"></i>
                <h1 class="text-3xl font-bold">Admin Login</h1>
                <p class="text-indigo-100 mt-2">Library Survey Dashboard</p>
            </div>
            
            <form id="loginForm" class="p-8 space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required
                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Enter your username"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Enter your password"
                    >
                </div>
                
                <div id="errorMessage" style="display: none;" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <p id="errorText"></p>
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
            </form>
            
            <div class="bg-gray-50 px-8 py-4 text-center text-sm text-gray-600">
                <p>Demo Credentials:<br><strong>username:</strong> admin <br><strong>password:</strong> password123</p>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');
            
            try {
                const response = await fetch('./api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Store token and redirect
                    localStorage.setItem('auth_token', data.token);
                          window.location.href = './dashboard.php';
                } else {
                    document.getElementById('errorText').textContent = data.message || 'Login failed';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('errorText').textContent = 'Network error. Please try again.';
                errorDiv.style.display = 'block';
            }
        });
    </script>
</body>
</html>
