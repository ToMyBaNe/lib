<!-- Dashboard Content -->

<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-4">
    <!-- Total Responses Card -->
    <div class="stat-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-emerald-700">Total Responses</p>
                <h3 id="totalResponses" class="mt-2 text-3xl font-bold text-emerald-900">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
                <p id="totalResponsesChange" class="mt-1 text-xs font-medium text-emerald-600"></p>
            </div>
            <i class="fas fa-poll text-4xl text-brand-gold opacity-70"></i>
        </div>
    </div>

    <!-- Total Questions Card -->
    <div class="stat-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-emerald-700">Total Respondents</p>
                <h3 id="totalQuestions" class="mt-2 text-3xl font-bold text-emerald-900">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
            </div>
            <i class="fas fa-user text-4xl text-brand-gold opacity-70"></i>
        </div>
    </div>

    <!-- Today's Responses Card -->
    <div class="stat-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-emerald-700">Today's Responses</p>
                <h3 id="todayResponses" class="mt-2 text-3xl font-bold text-emerald-900">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
            </div>
            <i class="fas fa-calendar text-4xl text-brand-gold opacity-70"></i>
        </div>
    </div>

    <!-- Average Satisfaction Card -->
    <div class="stat-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-emerald-700">Avg. Satisfaction</p>
                <h3 id="avgSatisfaction" class="mt-2 text-3xl font-bold text-emerald-900">
                    <span class="inline-block animate-pulse">...</span>
                </h3>
                <p class="mt-1 text-xs text-emerald-700/80">out of 5</p>
            </div>
            <i class="fas fa-star text-4xl text-brand-gold opacity-70"></i>
        </div>
    </div>
</div>

<!-- Analytics Grid -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
    <!-- Recommendation Rate -->
    <div class="admin-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <h2 class="mb-4 text-lg font-semibold text-emerald-900">
            <i class="mr-2 fas fa-thumbs-up text-brand-gold"></i> Would Recommend
        </h2>
        <div class="flex items-center gap-6">
            <div class="flex-1">
                <div class="text-3xl font-bold text-brand-green" id="recommendationPercentage">-</div>
                <p class="mt-1 text-sm text-emerald-700">of respondents would recommend</p>
            </div>
            <div class="w-32 h-32">
                <canvas id="recommendationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Ratings Breakdown -->
    <div class="admin-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <h2 class="mb-4 text-lg font-semibold text-emerald-900">
            <i class="mr-2 fas fa-chart-bar text-brand-gold"></i> Ratings Breakdown
        </h2>
        <div id="ratingsBreakdownList" class="space-y-3">
            <div class="text-sm text-center text-emerald-700/80">Loading ratings...</div>
        </div>
    </div>
</div>

<!-- Visit Patterns -->
<div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
    <!-- Visit Frequency -->
    <div class="admin-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <h2 class="mb-4 text-lg font-semibold text-emerald-900">
            <i class="mr-2 fas fa-repeat text-brand-gold"></i> Visit Frequency
        </h2>
        <div id="visitFrequencyList" class="space-y-2">
            <div class="py-8 text-sm text-center text-emerald-700/80">Loading...</div>
        </div>
    </div>

    <!-- Top Visit Purposes -->
    <div class="admin-card rounded-2xl bg-white/95 p-6 shadow-sm ring-1 ring-emerald-50">
        <h2 class="mb-4 text-lg font-semibold text-emerald-900">
            <i class="mr-2 fas fa-map-marker-alt text-brand-gold"></i> Top Visit Purposes
        </h2>
        <div id="visitPurposeList" class="space-y-2">
            <div class="py-8 text-sm text-center text-emerald-700/80">Loading...</div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let recommendationChart = null;

    // Load dashboard statistics
    document.addEventListener('DOMContentLoaded', function() {
        loadDashboardStats();
    });

    function escapeHtml(text) {
        if (text == null) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    async function loadDashboardStats() {
        try {
            console.debug('Loading dashboard stats...');
            // Prefer shared apiRequest helper when available
            let serverResponse = null;

            if (typeof apiRequest === 'function') {
                try {
                    const res = await apiRequest('analytics.php');
                    // adminPanel.apiRequest returns { success: true, data: parsedJson }
                    // parsedJson is the server response with { success, data }
                    if (res && res.success && res.data) {
                        serverResponse = res.data; // parsed server JSON
                    }
                } catch (e) {
                    console.warn('apiRequest failed, falling back to fetch:', e.message);
                }
            }

            // Fallback to direct fetch if needed
            if (!serverResponse) {
                const response = await fetch('./api/analytics.php');
                serverResponse = await response.json();
            }

            if (!serverResponse || serverResponse.success !== true) {
                console.error('Analytics API error or invalid response', serverResponse);
                return;
            }

            // Support both shapes: serverResponse.data (analytics object)
            // or if someone already nested it differently, try to normalize
            const analytics = serverResponse.data ?? serverResponse;

            // Update stat cards
            document.getElementById('totalResponses').textContent = analytics.total_responses ?? 0;
            document.getElementById('totalQuestions').textContent = analytics.total_respondents ?? 0;
            document.getElementById('todayResponses').textContent = analytics.today_responses ?? 0;
            const satStats = analytics.satisfaction_stats || {};
            document.getElementById('avgSatisfaction').textContent = 
                (satStats.count > 0 && satStats.average != null)
                    ? Number(satStats.average).toFixed(1)
                    : '-';

            // Update recommendation rate
            const recRate = analytics.recommendation_rate || {};
            document.getElementById('recommendationPercentage').textContent = (recRate.percentage ?? 0) + '%';
            updateRecommendationChart(recRate.yes ?? 0, recRate.no ?? 0);

            // Update ratings breakdown
            const ratings = analytics.ratings_breakdown || [];
            updateRatingsList(ratings);

            // Update visit frequency
            updateVisitFrequencyList(analytics.visit_frequency || []);

            // Update visit purpose
            updateVisitPurposeList(analytics.visit_purpose || []);

        } catch (error) {
            console.error('Stats loading failed:', error);
            // Friendly UI fallbacks
            const setText = (id, text) => {
                const el = document.getElementById(id);
                if (el) el.textContent = text;
            };
            setText('totalResponses', '-');
            setText('totalQuestions', '-');
            setText('todayResponses', '-');
            setText('avgSatisfaction', '-');
            setText('recommendationPercentage', '-');
            const ratingsContainer = document.getElementById('ratingsBreakdownList');
            if (ratingsContainer) ratingsContainer.innerHTML = `<div class="text-sm text-red-500">Failed to load ratings: ${escapeHtml(error.message || String(error))}</div>`;
            const visitFreq = document.getElementById('visitFrequencyList');
            if (visitFreq) visitFreq.innerHTML = `<div class="text-sm text-red-500">Failed to load visit frequency</div>`;
            const visitPurpose = document.getElementById('visitPurposeList');
            if (visitPurpose) visitPurpose.innerHTML = `<div class="text-sm text-red-500">Failed to load visit purposes</div>`;
        }
    }
    function updateRecommendationChart(yes, no) {
        const ctxEl = document.getElementById('recommendationChart');
        if (!ctxEl) return;
        const ctx = ctxEl.getContext('2d');

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
        const num = Number(value);
        const percentage = isNaN(num) ? 0 : (num / 5) * 100;
        const el = document.getElementById('rating-' + name);
        const bar = document.getElementById('rating-' + name + '-bar');
        if (el) el.textContent = isNaN(num) ? '-' : num.toFixed(1);
        if (bar) bar.style.width = percentage + '%';
    }

        function updateRatingsList(ratings) {
            const container = document.getElementById('ratingsBreakdownList');
            if (!container) return;
            if (!ratings || ratings.length === 0) {
                container.innerHTML = '<div class="text-sm text-gray-500">No rating questions found</div>';
                return;
            }

            // Find max average for relative bars (use 5 as max scale)
            const maxAvg = 5;

            container.innerHTML = ratings.map(r => {
                const avg = Number(r.average) || 0;
                const pct = (avg / maxAvg) * 100;
                return `
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">${escapeHtml(r.question)}</span>
                            <span class="text-sm font-bold text-gray-900">${avg.toFixed(1)} <span class="text-xs text-gray-500">(${r.responses})</span></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: ${pct}%"></div>
                        </div>
                    </div>
                `;
            }).join('');
        }

    function updateVisitFrequencyList(frequencies) {
        const list = document.getElementById('visitFrequencyList');
        
        if (!frequencies || frequencies.length === 0) {
            list.innerHTML = '<div class="text-center py-8 text-gray-500">No data yet</div>';
            return;
        }

        const maxValue = Math.max(1, ...frequencies.map(f => f.value));
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

        const maxValue = Math.max(1, ...purposes.map(p => p.value));
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
