<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Survey Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-book-open text-5xl text-indigo-600 mb-4"></i>
                <h1 class="text-4xl font-bold text-gray-900">Library Survey System</h1>
                <p class="text-gray-600 mt-2">Welcome to the Setup Page</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Public Survey -->
                <a href="./public/" class="block p-6 border-2 border-gray-300 rounded-lg hover:border-indigo-600 hover:shadow-lg transition">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-poll text-4xl text-blue-600"></i>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Public Survey</h3>
                            <p class="text-gray-600 text-sm">Take the library survey</p>
                        </div>
                    </div>
                </a>
                
                <!-- Admin Dashboard -->
                <a href="./admin/login.php" class="block p-6 border-2 border-gray-300 rounded-lg hover:border-indigo-600 hover:shadow-lg transition">
                    <div class="flex items-center gap-4">
                        <i class="fas fa-chart-line text-4xl text-purple-600"></i>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Admin Dashboard</h3>
                            <p class="text-gray-600 text-sm">View analytics & responses</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Setup Instructions -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cogs mr-3 text-indigo-600"></i> Setup Instructions
                </h2>
                
                <ol class="space-y-4 ml-6 list-decimal">
                    <li class="text-gray-700">
                        <strong>Create Database:</strong>
                        <p class="text-sm text-gray-600 mt-1">Import the SQL file at <code class="bg-gray-100 px-2 py-1 rounded">database/survey.sql</code> into your MySQL server</p>
                    </li>
                    
                    <li class="text-gray-700 mt-4">
                        <strong>Create Admin Account:</strong>
                        <div class="mt-2 bg-white p-4 rounded border border-gray-300">
                            <p class="text-sm text-gray-600 mb-3">Run this SQL query to create the admin user:</p>
                            <code class="block bg-gray-100 p-3 rounded text-sm font-mono overflow-x-auto mb-3">
INSERT INTO users (username, password, email) VALUES 
('admin', '$2y$10$YIjlrDn2.PneOVz75BCKNe.pHJYABT8KqOgkxShsHkiVIZaGIW0dO', 'admin@library.local');
                            </code>
                            <p class="text-xs text-gray-600">Username: <strong>admin</strong> | Password: <strong>password123</strong></p>
                        </div>
                    </li>
                    
                    <li class="text-gray-700 mt-4">
                        <strong>Configure Database:</strong>
                        <p class="text-sm text-gray-600 mt-1">Edit <code class="bg-gray-100 px-2 py-1 rounded">api/db_config.php</code> with your database credentials if needed</p>
                    </li>
                    
                    <li class="text-gray-700 mt-4">
                        <strong>Access the Application:</strong>
                        <ul class="text-sm text-gray-600 mt-1 ml-4 space-y-2">
                            <li><i class="fas fa-check text-green-600 mr-2"></i>Public Survey: <a href="./public/" class="text-indigo-600 hover:underline">./public/</a></li>
                            <li><i class="fas fa-check text-green-600 mr-2"></i>Admin Dashboard: <a href="./admin/login.php" class="text-indigo-600 hover:underline">./admin/login.php</a></li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <!-- Features -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-pencil-alt text-indigo-600 mr-2"></i> Public Survey Features
                    </h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li><i class="fas fa-check text-green-600 mr-2"></i> User-friendly survey form</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Multiple rating types</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Optional feedback section</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Form validation</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Responsive design</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-chart-pie text-indigo-600 mr-2"></i> Admin Dashboard Features
                    </h3>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Interactive charts & graphs</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Real-time analytics</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Survey response table</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Detailed view modal</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i> Secure admin login</li>
                    </ul>
                </div>
            </div>
            
            <!-- File Structure -->
            <div class="mt-8 bg-gray-50 rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Project Structure</h3>
                <pre class="bg-gray-900 text-gray-100 p-4 rounded overflow-x-auto text-sm"><code>survey/
├── index.php                 # This main page
├── public/
│   └── index.php            # Public survey form
├── admin/
│   ├── login.php            # Admin login page
│   ├── dashboard.php        # Admin dashboard
│   └── api/
│       └── login.php        # Login API endpoint
├── api/
│   ├── db_config.php        # Database configuration
│   ├── submit_survey.php    # Submit survey endpoint
│   └── analytics.php        # Analytics data endpoint
├── assets/
│   ├── styles.css           # Custom styles
│   ├── script.js            # Public form script
│   └── dashboard.js         # Dashboard script
└── database/
    └── survey.sql           # Database schema</code></pre>
            </div>
        </div>
    </div>
</body>
</html>
