<!-- Dashboard Content -->

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Responses Card -->
    <div class="stat-card bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Responses</p>
                <h3 id="totalResponses" class="text-3xl font-bold text-gray-900 mt-2">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
                <p id="totalResponsesChange" class="text-xs text-green-600 mt-1"></p>
            </div>
            <i class="fas fa-poll text-4xl text-indigo-600 opacity-20"></i>
        </div>
    </div>

    <!-- Total Questions Card -->
    <div class="stat-card bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Questions</p>
                <h3 id="totalQuestions" class="text-3xl font-bold text-gray-900 mt-2">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
            </div>
            <i class="fas fa-question-circle text-4xl text-blue-600 opacity-20"></i>
        </div>
    </div>

    <!-- Today's Responses Card -->
    <div class="stat-card bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Today's Responses</p>
                <h3 id="todayResponses" class="text-3xl font-bold text-gray-900 mt-2">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
            </div>
            <i class="fas fa-calendar text-4xl text-green-600 opacity-20"></i>
        </div>
    </div>

    <!-- Average Satisfaction Card -->
    <div class="stat-card bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Avg. Satisfaction</p>
                <h3 id="avgSatisfaction" class="text-3xl font-bold text-gray-900 mt-2">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
                <p class="text-xs text-gray-500 mt-1">out of 5</p>
            </div>
            <i class="fas fa-star text-4xl text-yellow-600 opacity-20"></i>
        </div>
    </div>
</div>

<!-- Analytics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Recommendation Rate -->
    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-thumbs-up mr-2 text-green-600"></i> Would Recommend
        </h2>
        <div class="flex items-center gap-6">
            <div class="flex-1">
                <div class="text-3xl font-bold text-green-600" id="recommendationPercentage">-</div>
                <p class="text-sm text-gray-600 mt-1">of respondents would recommend</p>
            </div>
            <div class="w-32 h-32">
                <canvas id="recommendationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Ratings Breakdown -->
    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-chart-bar mr-2 text-blue-600"></i> Ratings Breakdown
        </h2>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Book Availability</span>
                    <span id="rating-book" class="text-sm font-bold text-gray-900">-</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="rating-book-bar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Staff Helpfulness</span>
                    <span id="rating-staff" class="text-sm font-bold text-gray-900">-</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="rating-staff-bar" class="bg-purple-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Facilities Rating</span>
                    <span id="rating-facilities" class="text-sm font-bold text-gray-900">-</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="rating-facilities-bar" class="bg-green-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visit Patterns -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Visit Frequency -->
    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-repeat mr-2 text-indigo-600"></i> Visit Frequency
        </h2>
        <div id="visitFrequencyList" class="space-y-2">
            <div class="text-center py-8 text-gray-500">Loading...</div>
        </div>
    </div>

    <!-- Top Visit Purposes -->
    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-map-marker-alt mr-2 text-orange-600"></i> Top Visit Purposes
        </h2>
        <div id="visitPurposeList" class="space-y-2">
            <div class="text-center py-8 text-gray-500">Loading...</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-rocket mr-2"></i> Quick Actions
        </h2>
        <div class="space-y-3">
            <a href="manage_questions.php" class="block btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Add New Question
            </a>
            <a href="manage_questions.php" class="block btn btn-secondary">
                <i class="fas fa-list mr-2"></i> View All Questions
            </a>
            <a href="responses.php" class="block btn btn-secondary">
                <i class="fas fa-chart-bar mr-2"></i> View Responses
            </a>
        </div>
    </div>

    <div class="admin-card bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i> System Information
        </h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-600">PHP Version:</dt>
                <dd class="text-gray-900 font-medium"><?php echo phpversion(); ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Server:</dt>
                <dd class="text-gray-900 font-medium"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Database:</dt>
                <dd class="text-gray-900 font-medium">MySQL</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Current User:</dt>
                <dd class="text-gray-900 font-medium"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Unknown'); ?></dd>
            </div>
        </dl>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let recommendationChart = null;

    // Load dashboard statistics
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardStats();
    });

    async function loadDashboardStats() {
        try {
            const response = await fetch('./api/analytics.php');
            const text = await response.text();
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON:', text);
                return;
            }

            if (!data.success) {
                console.error('API error:', data.message);
                return;
            }

            const analytics = data.data;

            // Update stat cards
            document.getElementById('totalResponses').textContent = analytics.total_responses;
            document.getElementById('totalQuestions').textContent = analytics.total_questions;
            document.getElementById('todayResponses').textContent = analytics.today_responses;
            document.getElementById('avgSatisfaction').textContent = 
                analytics.satisfaction_stats.count > 0 
                    ? analytics.satisfaction_stats.average.toFixed(1) 
                    : '-';

            // Update recommendation rate
            const recRate = analytics.recommendation_rate;
            document.getElementById('recommendationPercentage').textContent = recRate.percentage + '%';
            updateRecommendationChart(recRate.yes, recRate.no);

            // Update ratings breakdown
            const ratings = analytics.ratings_breakdown;
            updateRatingsDisplay('book', ratings.book_availability);
            updateRatingsDisplay('staff', ratings.staff_helpfulness);
            updateRatingsDisplay('facilities', ratings.facilities_rating);

            // Update visit frequency
            updateVisitFrequencyList(analytics.visit_frequency);

            // Update visit purpose
            updateVisitPurposeList(analytics.visit_purpose);

        } catch (error) {
            console.error('Stats loading failed:', error);
        }
    }

    function updateRecommendationChart(yes, no) {
        const ctx = document.getElementById('recommendationChart').getContext('2d');
        
        if (recommendationChart) {
            recommendationChart.destroy();
        }

        recommendationChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Would Recommend', 'Would Not'],
                datasets: [{
                    data: [yes, no],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderColor: ['#059669', '#dc2626'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 10
                        }
                    }
                }
            }
        });
    }

    function updateRatingsDisplay(name, value) {
        const percentage = (value / 5) * 100;
        document.getElementById('rating-' + name).textContent = value.toFixed(1);
        document.getElementById('rating-' + name + '-bar').style.width = percentage + '%';
    }

    function updateVisitFrequencyList(frequencies) {
        const list = document.getElementById('visitFrequencyList');
        
        if (!frequencies || frequencies.length === 0) {
            list.innerHTML = '<div class="text-center py-8 text-gray-500">No data yet</div>';
            return;
        }

        const maxValue = Math.max(...frequencies.map(f => f.value));
        list.innerHTML = frequencies.map(item => `
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700">${escapeHtml(item.label)}</span>
                <div class="flex items-center gap-2">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: ${(item.value / maxValue) * 100}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 w-10 text-right">${item.value}</span>
                </div>
            </div>
        `).join('');
    }

    function updateVisitPurposeList(purposes) {
        const list = document.getElementById('visitPurposeList');
        
        if (!purposes || purposes.length === 0) {
            list.innerHTML = '<div class="text-center py-8 text-gray-500">No data yet</div>';
            return;
        }

        const maxValue = Math.max(...purposes.map(p => p.value));
        list.innerHTML = purposes.slice(0, 6).map(item => `
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-700 truncate">${escapeHtml(item.label)}</span>
                <div class="flex items-center gap-2">
                    <div class="w-32 bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full" style="width: ${(item.value / maxValue) * 100}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-900 w-10 text-right">${item.value}</span>
                </div>
            </div>
        `).join('');
    }
</script>
