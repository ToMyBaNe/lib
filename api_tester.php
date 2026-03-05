<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questions API Tester</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-flask text-orange-600 mr-2"></i> Questions API Tester
            </h1>
            <p class="text-gray-600">Test the questions API endpoint directly</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Test Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-network-wired text-blue-600 mr-2"></i> API Endpoint Test
                </h2>
                
                <p class="text-gray-700 mb-4">Endpoint: <code class="bg-gray-100 px-2 py-1 rounded">GET /api/questions.php</code></p>
                
                <button onclick="testAPI()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-play mr-2"></i> Test API
                </button>

                <div id="loading" style="display:none;" class="mt-4">
                    <div class="flex items-center gap-2">
                        <div class="spinner"></div>
                        <span>Testing API...</span>
                    </div>
                </div>
            </div>

            <!-- Response Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-code text-green-600 mr-2"></i> Raw Response
                </h2>
                
                <div id="responseContainer" style="display:none;">
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-gray-700">Status Code:</p>
                        <p id="statusCode" class="text-lg font-mono"></p>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm font-semibold text-gray-700">Headers:</p>
                        <pre id="headers" class="bg-gray-100 p-3 rounded text-xs overflow-auto max-h-32"></pre>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700">Response Body (JSON):</p>
                        <pre id="response" class="bg-gray-100 p-3 rounded text-xs overflow-auto max-h-64"></pre>
                    </div>
                </div>

                <div id="errorContainer" style="display:none;" class="bg-red-50 border-l-4 border-red-600 p-4">
                    <p class="text-red-800 font-semibold">❌ Error</p>
                    <p id="errorMessage" class="text-red-700 text-sm mt-2"></p>
                </div>
            </div>

            <!-- Parsed Data Section -->
            <div id="dataContainer" style="display:none;" class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-list text-purple-600 mr-2"></i> Parsed Questions
                </h2>
                
                <div id="questionsList" class="space-y-4"></div>
            </div>

            <!-- Diagnostics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-wrench text-gray-600 mr-2"></i> Diagnostics
                </h2>
                
                <div id="diagnostics" class="space-y-2 text-sm">
                    <p class="text-gray-500">Run a test to see diagnostics...</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="troubleshoot_questions.php" class="inline-block bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Troubleshooting
            </a>
        </div>
    </div>

    <style>
        .spinner {
            border: 2px solid #f3f4f6;
            border-top: 2px solid #4f46e5;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script>
        async function testAPI() {
            const loading = document.getElementById('loading');
            const responseContainer = document.getElementById('responseContainer');
            const errorContainer = document.getElementById('errorContainer');
            const dataContainer = document.getElementById('dataContainer');
            const diagnostics = document.getElementById('diagnostics');

            loading.style.display = 'block';
            responseContainer.style.display = 'none';
            errorContainer.style.display = 'none';
            dataContainer.style.display = 'none';

            try {
                const startTime = performance.now();
                const response = await fetch('api/questions.php');
                const endTime = performance.now();

                const statusCode = response.status;
                const contentType = response.headers.get('content-type');
                const responseText = await response.text();

                loading.style.display = 'none';

                // Display response info
                document.getElementById('statusCode').textContent = `${statusCode} ${response.statusText}`;
                
                const headerInfo = `Content-Type: ${contentType}\nResponse Time: ${(endTime - startTime).toFixed(2)}ms\nResponse Size: ${responseText.length} bytes`;
                document.getElementById('headers').textContent = headerInfo;

                // Try to parse JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                    document.getElementById('response').textContent = JSON.stringify(data, null, 2);
                    responseContainer.style.display = 'block';

                    // Update diagnostics
                    let diagText = '';
                    
                    if (statusCode === 200 && data.success) {
                        diagText = `✓ <span class="text-green-600 font-semibold">API working correctly</span>\n`;
                        diagText += `• Questions found: ${data.count}\n`;
                        diagText += `• Categories: ${Object.keys(data.categorized || {}).length}\n`;
                        diagText += `• Has warning: ${data.warning ? 'Yes - ' + data.warning : 'No'}`;

                        if (data.count > 0) {
                            displayQuestions(data);
                        }
                    } else if (!data.success) {
                        diagText = `✗ <span class="text-red-600 font-semibold">API returned error</span>\n`;
                        diagText += `• Message: ${data.message}\n`;
                        diagText += `• Error: ${data.error || 'None'}`;
                    }

                    diagnostics.innerHTML = diagText.split('\n').map(l => `<p>${l}</p>`).join('');

                } catch (parseError) {
                    // Not JSON - show raw response
                    document.getElementById('response').textContent = responseText;
                    responseContainer.style.display = 'block';

                    diagnostics.innerHTML = `<p class="text-red-600">✗ Response is not valid JSON</p><p class="text-sm">First 200 chars: ${responseText.substring(0, 200)}</p>`;
                }

            } catch (error) {
                loading.style.display = 'none';
                errorContainer.style.display = 'block';
                document.getElementById('errorMessage').textContent = error.message;

                diagnostics.innerHTML = `<p class="text-red-600">✗ Fetch error: ${error.message}</p><p class="text-sm">Check browser console (F12)</p>`;
            }
        }

        function displayQuestions(data) {
            const list = document.getElementById('questionsList');
            list.innerHTML = '';

            Object.entries(data.categorized || {}).forEach(([category, questions]) => {
                const categoryDiv = document.createElement('div');
                categoryDiv.className = 'bg-gray-50 p-4 rounded border-l-4 border-indigo-600';
                
                let html = `<h3 class="font-semibold text-gray-800 mb-3">${escapeHtml(category)}</h3>`;
                html += '<ul class="space-y-2">';

                questions.forEach(q => {
                    html += `<li class="text-sm">
                        <p class="font-medium text-gray-700">${escapeHtml(q.question)}</p>
                        <p class="text-xs text-gray-500">Type: <code>${escapeHtml(q.type)}</code> | Required: ${q.required ? 'Yes' : 'No'} | Options: ${q.options.length}</p>
                    </li>`;
                });

                html += '</ul>';
                categoryDiv.innerHTML = html;
                list.appendChild(categoryDiv);
            });

            document.getElementById('dataContainer').style.display = 'block';
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Test on page load
        window.addEventListener('load', testAPI);
    </script>
</body>
</html>
