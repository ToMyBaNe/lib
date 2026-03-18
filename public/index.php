<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BASC Library Satisfaction Survey</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="../assets/lib-logo-no-bg.png" alt="library-logo" class="w-28 mx-auto">
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Library Survey</h1>
                <p class="text-gray-600">Help us improve your BASC library online experience</p>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="bg-white rounded-lg shadow-xl p-8 mb-6 text-center">
                <div class="spinner mb-4"></div>
                <p class="text-gray-600">Loading survey form...</p>
            </div>

            <!-- Survey Form -->
            <div id="surveyContainer" class="hidden bg-white rounded-lg shadow-xl p-8 mb-6">
                <form id="surveyForm" class="space-y-8">
                    <!-- Dynamic Questions Section -->
                    <div id="dynamicQuestionsContainer">
                        <!-- All questions will be inserted here from admin -->
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4 pt-4 border-t">
                        
                        <button 
                            type="reset" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-redo"></i> Clear Form
                        </button>
                        <button 
                            type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-check"></i> Submit Survey
                        </button>
                    </div>
                </form>
            </div>

            <!-- Success Message (hidden by default) -->
            <div id="successMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <div>
                    <p class="font-semibold">Thank you!</p>
                    <p class="text-sm">Your survey response has been successfully submitted.</p>
                </div>
            </div>

            <!-- Error Message (hidden by default) -->
            <div id="errorMessage" class="hidden bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-2xl"></i>
                <div class="flex-1">
                    <p class="font-semibold">Error</p>
                    <p class="text-sm" id="errorText">Something went wrong. Please try again.</p>
                    <p class="text-sm mt-2">
                        <a href="../troubleshoot_questions.php" class="underline font-semibold hover:no-underline">
                            📋 Troubleshoot this issue
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/survey-form.js"></script>
</body>
</html>
