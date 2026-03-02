<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Questions Diagnostic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold mb-6">🔍 Questions System Diagnostic</h1>
        
        <div id="results" class="space-y-4"></div>
    </div>

    <script>
        async function runDiagnostics() {
            const results = document.getElementById('results');
            const tests = [];

            // Test 1: Session/Authentication
            console.log('Checking session...');
            tests.push({
                name: 'Admin Session',
                pass: document.cookie.includes('PHPSESSID'),
                details: 'Check if logged in to admin panel'
            });

            // Test 2: Questions API
            console.log('Testing Questions API...');
            try {
                const response = await fetch('./api/questions.php?action=list');
                const text = await response.text();
                
                let isJson = false;
                try {
                    const data = JSON.parse(text);
                    isJson = true;
                    tests.push({
                        name: 'Questions API Response',
                        pass: data.success !== false,
                        details: `Status: ${response.status}, Message: ${data.message || 'Success'}`
                    });
                } catch {
                    tests.push({
                        name: 'Questions API Response',
                        pass: false,
                        details: `Status: ${response.status}, Response type: ${isJson ? 'JSON' : 'HTML/Text'}, First 200 chars: ${text.substring(0, 200)}`
                    });
                }
            } catch (e) {
                tests.push({
                    name: 'Questions API Response',
                    pass: false,
                    details: `Error: ${e.message}`
                });
            }

            // Test 3: Database Table
            console.log('Testing database...');
            try {
                const response = await fetch('../api/test.php');
                const data = await response.json();
                const hasQuestionsTable = data.database?.tables?.survey_questions === true;
                tests.push({
                    name: 'Survey Questions Table',
                    pass: hasQuestionsTable,
                    details: hasQuestionsTable ? 'Table exists' : 'Table NOT found - Run setup_questions.php'
                });
            } catch (e) {
                tests.push({
                    name: 'Survey Questions Table',
                    pass: false,
                    details: `Error: ${e.message}`
                });
            }

            // Render results
            results.innerHTML = tests.map(test => `
                <div class="p-4 rounded-lg border-l-4 ${test.pass ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500'}">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">${test.pass ? '✓' : '✗'}</span>
                        <div>
                            <h3 class="font-semibold ${test.pass ? 'text-green-900' : 'text-red-900'}">${test.name}</h3>
                            <p class="text-sm ${test.pass ? 'text-green-700' : 'text-red-700'}">${test.details}</p>
                        </div>
                    </div>
                </div>
            `).join('');

            // Summary
            const passCount = tests.filter(t => t.pass).length;
            const summary = document.createElement('div');
            summary.className = 'mt-8 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500';
            summary.innerHTML = `
                <h2 class="font-semibold text-blue-900 mb-3">Results: ${passCount}/${tests.length} checks passed</h2>
                ${passCount < tests.length ? `
                    <div class="text-sm text-blue-800 space-y-2">
                        <p><strong>Troubleshooting steps:</strong></p>
                        <ol class="list-decimal ml-4 space-y-1">
                            <li>Make sure you're logged into admin panel (are you redirected to login?)</li>
                            <li>Run setup_questions.php first to create the database table</li>
                            <li>Check browser console (F12) for JavaScript errors</li>
                            <li>Check XAMPP error logs for PHP errors</li>
                        </ol>
                    </div>
                ` : `<p class="text-green-700"><i class="fas fa-check-circle"></i> All systems operational!</p>`}
            `;
            results.insertBefore(summary, results.firstChild);
        }

        window.addEventListener('load', runDiagnostics);
    </script>
</body>
</html>
