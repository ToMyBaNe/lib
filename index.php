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
                <p class="text-gray-600 mt-2">Welcome to the Library Survey System</p>
            </div>
            
            <div class="flex flex-col gap-4">
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
                            <h3 class="text-xl font-semibold text-gray-900">Access the Admin</h3>
                            <p class="text-gray-600 text-sm">Redirect to login page</p>
                        </div>
                    </div>
                </a>
            </div>
        
    </div>
</body>
</html>
