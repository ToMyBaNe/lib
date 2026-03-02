<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Satisfaction Survey</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-book-open text-4xl text-indigo-600"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Library Survey</h1>
                <p class="text-gray-600">Help us improve your library experience</p>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="bg-white rounded-lg shadow-xl p-8 mb-6 text-center">
                <div class="spinner mb-4"></div>
                <p class="text-gray-600">Loading survey form...</p>
            </div>

            <!-- Survey Form -->
            <div id="surveyContainer" class="hidden bg-white rounded-lg shadow-xl p-8 mb-6">
                <form id="surveyForm" class="space-y-8">
                    <!-- Visitor Information Section -->
                    <div class="border-b pb-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-user text-indigo-600 mr-3"></i> About You
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="visitor_name" 
                                    required
                                    class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Enter your full name"
                                >
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-gray-500">(Optional)</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="visitor_email"
                                    class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="your.email@example.com"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Visit Information Section -->
                    <div class="border-b pb-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-calendar text-indigo-600 mr-3"></i> Your Visit
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">
                                    How often do you visit the library? <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    id="frequency" 
                                    name="visit_frequency" 
                                    required
                                    class="form-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                >
                                    <option value="">Select frequency</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="occasionally">Occasionally</option>
                                    <option value="first_time">First time</option>
                                </select>
                            </div>

                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                    What was the primary purpose of your visit? <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="purpose" 
                                    name="purpose" 
                                    required
                                    class="form-input w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="e.g., Borrow books, study, research, events"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Questions Section -->
                    <div id="dynamicQuestionsContainer">
                        <!-- Questions will be inserted here -->
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-4">
                        <button 
                            type="submit" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-check"></i> Submit Survey
                        </button>
                        <button 
                            type="reset" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-redo"></i> Clear Form
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
                <div>
                    <p class="font-semibold">Error</p>
                    <p class="text-sm" id="errorText">Something went wrong. Please try again.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allQuestions = [];

        // Load survey form on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSurveyQuestions();
            setupFormSubmission();
        });

        async function loadSurveyQuestions() {
            try {
                const response = await fetch('../api/questions.php');
                const text = await response.text();
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid server response');
                }

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load questions');
                }

                allQuestions = data.categorized || {};
                renderDynamicQuestions();
                showSurveyForm();

            } catch (error) {
                console.error('Error loading questions:', error);
                showError('Failed to load survey form: ' + error.message);
            }
        }

        function renderDynamicQuestions() {
            const container = document.getElementById('dynamicQuestionsContainer');
            let html = '';

            Object.entries(allQuestions).forEach(([category, questions]) => {
                // Add category header
                html += `
                    <div class="border-b pb-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-list text-indigo-600 mr-3"></i> ${escapeHtml(category)}
                        </h2>
                        <div class="space-y-6">
                `;

                // Render each question
                questions.forEach(question => {
                    html += renderQuestion(question);
                });

                html += `
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            attachQuestionHandlers();
        }

        function renderQuestion(question) {
            const required = question.required ? '<span class="text-red-500">*</span>' : '';
            const qId = `q_${question.id}`;

            switch(question.type) {
                case 'text':
                    return `
                        <div>
                            <label for="${qId}" class="block text-sm font-medium text-gray-700 mb-2">
                                ${escapeHtml(question.question)} ${required}
                            </label>
                            <input 
                                type="text" 
                                id="${qId}" 
                                name="responses[${question.id}]"
                                ${question.required ? 'required' : ''}
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                        </div>
                    `;

                case 'textarea':
                    return `
                        <div>
                            <label for="${qId}" class="block text-sm font-medium text-gray-700 mb-2">
                                ${escapeHtml(question.question)} ${required}
                            </label>
                            <textarea 
                                id="${qId}" 
                                name="responses[${question.id}]"
                                rows="4"
                                ${question.required ? 'required' : ''}
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            ></textarea>
                        </div>
                    `;

                case 'select':
                    const options = question.options.map(opt => 
                        `<option value="${escapeHtml(opt)}">${escapeHtml(opt)}</option>`
                    ).join('');
                    
                    return `
                        <div>
                            <label for="${qId}" class="block text-sm font-medium text-gray-700 mb-2">
                                ${escapeHtml(question.question)} ${required}
                            </label>
                            <select 
                                id="${qId}" 
                                name="responses[${question.id}]"
                                ${question.required ? 'required' : ''}
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            >
                                <option value="">Select an option</option>
                                ${options}
                            </select>
                        </div>
                    `;

                case 'radio':
                    const radioOptions = question.options.map((opt, idx) => `
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="radio" 
                                name="responses[${question.id}]" 
                                value="${escapeHtml(opt)}"
                                ${question.required ? 'required' : ''}
                                class="w-4 h-4"
                            >
                            <span class="text-gray-700">${escapeHtml(opt)}</span>
                        </label>
                    `).join('');

                    return `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                ${escapeHtml(question.question)} ${required}
                            </label>
                            <div class="space-y-2">
                                ${radioOptions}
                            </div>
                        </div>
                    `;

                case 'checkbox':
                    const checkboxOptions = question.options.map((opt, idx) => `
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="responses[${question.id}][]" 
                                value="${escapeHtml(opt)}"
                                class="w-4 h-4"
                            >
                            <span class="text-gray-700">${escapeHtml(opt)}</span>
                        </label>
                    `).join('');

                    return `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                ${escapeHtml(question.question)} ${required}
                            </label>
                            <div class="space-y-2">
                                ${checkboxOptions}
                            </div>
                        </div>
                    `;

                case 'rating':
                    const maxRating = 5;
                    const ratingButtons = Array.from({length: maxRating}, (_, i) => i + 1)
                        .map(val => `
                            <button type="button" class="rating-btn px-4 py-2 border-2 border-gray-300 rounded-lg hover:border-indigo-600 hover:bg-indigo-50 transition text-sm font-medium" data-value="${val}" data-question="${question.id}">
                                ${val}
                            </button>
                        `).join('');

                    return `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                ${escapeHtml(question.question)} ${required}
                            </label>
                            <input type="hidden" name="responses[${question.id}]" value="" ${question.required ? 'required' : ''}>
                            <div class="flex gap-2">
                                ${ratingButtons}
                            </div>
                        </div>
                    `;

                default:
                    return '';
            }
        }

        function attachQuestionHandlers() {
            // Handle rating buttons
            document.querySelectorAll('.rating-btn[data-question]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const questionId = this.dataset.question;
                    const value = this.dataset.value;
                    
                    // Deselect all buttons in this group
                    document.querySelectorAll(`.rating-btn[data-question="${questionId}"]`).forEach(b => {
                        b.classList.remove('border-indigo-600', 'bg-indigo-100');
                        b.classList.add('border-gray-300');
                    });
                    
                    // Select this button
                    this.classList.add('border-indigo-600', 'bg-indigo-100');
                    this.classList.remove('border-gray-300');
                    
                    // Set hidden input value
                    document.querySelector(`input[name="responses[${questionId}]"]`).value = value;
                });
            });
        }

        function setupFormSubmission() {
            document.getElementById('surveyForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                try {
                    // Collect form data
                    const formData = new FormData(this);
                    
                    // Build responses object from inputs
                    const responsesData = {};
                    document.querySelectorAll('input[name^="responses"], select[name^="responses"], textarea[name^="responses"]').forEach(input => {
                        const match = input.name.match(/responses\[(\d+)\]/);
                        if (match) {
                            const qId = match[1];
                            if (input.type === 'checkbox') {
                                if (input.checked) {
                                    if (!responsesData[qId]) responsesData[qId] = [];
                                    responsesData[qId].push(input.value);
                                }
                            } else if (input.value) {
                                responsesData[qId] = input.value;
                            }
                        }
                    });

                    const submitData = {
                        visitor_name: formData.get('visitor_name'),
                        visitor_email: formData.get('visitor_email'),
                        visit_frequency: formData.get('visit_frequency'),
                        purpose: formData.get('purpose'),
                        responses: responsesData
                    };

                    const response = await fetch('../api/submit.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(submitData)
                    });

                    const text = await response.text();
                    const result = JSON.parse(text);

                    if (!result.success) {
                        throw new Error(result.message);
                    }

                    // Hide form and show success
                    document.getElementById('surveyContainer').classList.add('hidden');
                    document.getElementById('successMessage').classList.remove('hidden');

                } catch (error) {
                    console.error('Submission error:', error);
                    showError(error.message || 'Failed to submit survey');
                }
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showSurveyForm() {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('surveyContainer').classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('errorText').textContent = message;
            document.getElementById('errorMessage').classList.remove('hidden');
        }
    </script>
</body>
</html>
