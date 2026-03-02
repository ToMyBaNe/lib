// Global variables to store chart instances
let charts = {};
let currentPage = 1;
const itemsPerPage = 10;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadDashboardData();
    setupTabNavigation();
});

// Check authentication
function checkAuth() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = './login.php';
    }
}

// Setup tab navigation
function setupTabNavigation() {
    const sidebar_links = document.querySelectorAll('.sidebar-link');
    sidebar_links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
}

// Switch between tabs
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from sidebar
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    // Add active class to clicked link
    event.target.closest('.sidebar-link')?.classList.add('active');
    
    // Update page title
    const titles = {
        'dashboard': 'Dashboard',
        'responses': 'Survey Responses',
        'analytics': 'Analytics',
        'settings': 'Settings'
    };
    document.getElementById('pageTitle').textContent = titles[tabName] || 'Dashboard';
    
    // Load tab-specific data
    if (tabName === 'responses') {
        loadSurveyResponses();
    } else if (tabName === 'analytics') {
        loadAnalyticsData();
    }
}

// Load main dashboard data
async function loadDashboardData() {
    try {
        // Load total responses
        let response = await fetch('../api/analytics.php?action=total_responses');
        let data = await response.json();
        if (data.success) {
            document.getElementById('totalResponses').textContent = data.data.total;
        }
        
        // Load average ratings
        response = await fetch('../api/analytics.php?action=average_ratings');
        data = await response.json();
        if (data.success) {
            document.getElementById('avgSatisfaction').textContent = data.data.avg_satisfaction || '0';
            document.getElementById('avgAvailability').textContent = data.data.avg_book_availability || '0';
            document.getElementById('avgStaff').textContent = data.data.avg_staff_helpfulness || '0';
        }
        
        // Load charts
        loadSatisfactionChart();
        loadFrequencyChart();
        loadRecommendationChart();
        loadDailySubmissionsChart();
        loadRecentFeedback();
        
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
}

// Load satisfaction chart
async function loadSatisfactionChart() {
    try {
        const response = await fetch('../api/analytics.php?action=satisfaction_distribution');
        const data = await response.json();
        
        if (data.success) {
            const labels = ['Very Poor', 'Poor', 'Average', 'Good', 'Excellent'];
            const counts = [0, 0, 0, 0, 0];
            
            data.data.forEach(item => {
                counts[item.satisfaction - 1] = item.count;
            });
            
            const ctx = document.getElementById('satisfactionChart').getContext('2d');
            
            if (charts.satisfaction) {
                charts.satisfaction.destroy();
            }
            
            charts.satisfaction = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: [
                            '#ef4444',
                            '#f97316',
                            '#eab308',
                            '#84cc16',
                            '#22c55e'
                        ],
                        borderColor: 'white',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading satisfaction chart:', error);
    }
}

// Load frequency chart
async function loadFrequencyChart() {
    try {
        const response = await fetch('../api/analytics.php?action=visit_frequency');
        const data = await response.json();
        
        if (data.success) {
            const labels = data.data.map(item => item.visit_frequency);
            const counts = data.data.map(item => item.count);
            
            const ctx = document.getElementById('frequencyChart').getContext('2d');
            
            if (charts.frequency) {
                charts.frequency.destroy();
            }
            
            charts.frequency = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Visitors',
                        data: counts,
                        backgroundColor: '#667eea',
                        borderColor: '#5a67d8',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading frequency chart:', error);
    }
}

// Load recommendation chart
async function loadRecommendationChart() {
    try {
        const response = await fetch('../api/analytics.php?action=recommendation_breakdown');
        const data = await response.json();
        
        if (data.success) {
            const labels = data.data.map(item => item.label);
            const counts = data.data.map(item => item.count);
            
            const ctx = document.getElementById('recommendationChart').getContext('2d');
            
            if (charts.recommendation) {
                charts.recommendation.destroy();
            }
            
            charts.recommendation = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Responses',
                        data: counts,
                        backgroundColor: [
                            '#ef4444',
                            '#f97316',
                            '#eab308',
                            '#84cc16',
                            '#22c55e'
                        ],
                        borderColor: ['#dc2626', '#ea580c', '#ca8a04', '#65a30d', '#16a34a'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading recommendation chart:', error);
    }
}

// Load daily submissions chart
async function loadDailySubmissionsChart() {
    try {
        const response = await fetch('../api/analytics.php?action=daily_submissions');
        const data = await response.json();
        
        if (data.success) {
            const labels = data.data.map(item => item.date).reverse();
            const counts = data.data.map(item => item.count).reverse();
            
            const ctx = document.getElementById('dailyChart').getContext('2d');
            
            if (charts.daily) {
                charts.daily.destroy();
            }
            
            charts.daily = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Submissions',
                        data: counts,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#667eea'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading daily chart:', error);
    }
}

// Load recent feedback
async function loadRecentFeedback() {
    try {
        const response = await fetch('../api/analytics.php?action=all_responses&limit=3');
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('recentFeedback');
            container.innerHTML = '';
            
            data.data.forEach(response => {
                if (response.improvements_feedback) {
                    const feedbackDiv = document.createElement('div');
                    feedbackDiv.className = 'p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200';
                    feedbackDiv.innerHTML = `
                        <p class="text-sm text-gray-600 mb-2"><strong>${response.visitor_name}</strong> - ${new Date(response.created_at).toLocaleDateString()}</p>
                        <p class="text-gray-800">"${response.improvements_feedback}"</p>
                    `;
                    container.appendChild(feedbackDiv);
                }
            });
            
            if (container.children.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">No feedback yet</p>';
            }
        }
    } catch (error) {
        console.error('Error loading recent feedback:', error);
    }
}

// Load survey responses table
async function loadSurveyResponses(page = 1) {
    try {
        const offset = (page - 1) * itemsPerPage;
        const response = await fetch(`../api/analytics.php?action=all_responses&limit=${itemsPerPage}&offset=${offset}`);
        const data = await response.json();
        
        if (data.success) {
            const tableBody = document.getElementById('responsesTable');
            tableBody.innerHTML = '';
            
            data.data.forEach(row => {
                const tr = document.createElement('tr');
                const recommendLevel = ['No', 'No', 'Neutral', 'Yes', 'Yes'][row.would_recommend] || '-';
                const date = new Date(row.created_at).toLocaleDateString();
                
                tr.innerHTML = `
                    <td>${row.visitor_name}</td>
                    <td>${row.visitor_email || '-'}</td>
                    <td>${row.visit_frequency}</td>
                    <td>
                        <span class="rating-badge ${row.satisfaction >= 4 ? 'excellent' : row.satisfaction >= 3 ? 'good' : 'average'}">
                            ${row.satisfaction}/5
                        </span>
                    </td>
                    <td>${recommendLevel}</td>
                    <td>${date}</td>
                    <td>
                        <button onclick="viewDetails(${row.id})" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                            View
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
            
            currentPage = page;
        }
    } catch (error) {
        console.error('Error loading survey responses:', error);
    }
}

// Load analytics data
async function loadAnalyticsData() {
    try {
        // Load average ratings
        const avgResponse = await fetch('../api/analytics.php?action=average_ratings');
        const avgData = await avgResponse.json();
        
        if (avgData.success) {
            document.getElementById('ana-satisfaction').textContent = (avgData.data.avg_satisfaction || 0) + '/5';
            document.getElementById('ana-availability').textContent = (avgData.data.avg_book_availability || 0) + '/5';
            document.getElementById('ana-staff').textContent = (avgData.data.avg_staff_helpfulness || 0) + '/5';
            document.getElementById('ana-facilities').textContent = (avgData.data.avg_facilities || 0) + '/5';
        }
        
        // Load frequency distribution
        const freqResponse = await fetch('../api/analytics.php?action=visit_frequency');
        const freqData = await freqResponse.json();
        
        if (freqData.success) {
            const freqList = document.getElementById('frequencyList');
            freqList.innerHTML = '';
            
            freqData.data.forEach(item => {
                const li = document.createElement('li');
                li.className = 'flex justify-between';
                li.innerHTML = `
                    <span class="text-gray-700">${item.visit_frequency}:</span>
                    <span class="font-semibold">${item.count}</span>
                `;
                freqList.appendChild(li);
            });
        }
        
        // Load quick stats
        const totalResponse = await fetch('../api/analytics.php?action=total_responses');
        const totalData = await totalResponse.json();
        
        if (totalData.success) {
            document.getElementById('quick-total').textContent = totalData.data.total;
        }
        
        // Load all responses to calculate additional stats
        const allResponse = await fetch('../api/analytics.php?action=all_responses&limit=1000');
        const allData = await allResponse.json();
        
        if (allData.success) {
            const today = new Date();
            const thisMonth = today.getMonth();
            const thisYear = today.getFullYear();
            
            const monthlyCount = allData.data.filter(r => {
                const rDate = new Date(r.created_at);
                return rDate.getMonth() === thisMonth && rDate.getFullYear() === thisYear;
            }).length;
            
            const recommendCount = allData.data.filter(r => r.would_recommend >= 3).length;
            const recommendRate = totalData.data.total > 0 ? Math.round((recommendCount / totalData.data.total) * 100) : 0;
            
            document.getElementById('quick-month').textContent = monthlyCount;
            document.getElementById('quick-recommend').textContent = recommendRate;
            
            if (allData.data.length > 0) {
                const lastDate = new Date(allData.data[0].created_at).toLocaleDateString();
                document.getElementById('quick-last').textContent = lastDate;
            }
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

// View response details in modal
async function viewDetails(id) {
    try {
        const response = await fetch(`../api/analytics.php?action=response_detail&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const r = data.data;
            const modalBody = document.getElementById('modalBody');
            
            const ratingLabels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            const recommendLabels = ['Definitely Not', 'Probably Not', 'Neutral', 'Probably Yes', 'Definitely Yes'];
            
            modalBody.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Name</p>
                        <p class="font-semibold">${r.visitor_name}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Email</p>
                        <p class="font-semibold">${r.visitor_email || '-'}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Visit Frequency</p>
                        <p class="font-semibold">${r.visit_frequency}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Purpose</p>
                        <p class="font-semibold">${r.purpose}</p>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <h4 class="font-semibold text-gray-900 mb-3">Ratings</h4>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Satisfaction</p>
                        <p class="text-lg font-bold text-indigo-600">${r.satisfaction}/5</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Book Availability</p>
                        <p class="text-lg font-bold text-indigo-600">${r.book_availability}/5</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Staff Helpfulness</p>
                        <p class="text-lg font-bold text-indigo-600">${r.staff_helpfulness}/5</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Facilities</p>
                        <p class="text-lg font-bold text-indigo-600">${r.facilities_rating}/5</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600 mb-2">Would Recommend</p>
                    <p class="font-semibold">${recommendLabels[r.would_recommend]}</p>
                </div>
                
                ${r.improvements_feedback ? `
                    <hr class="my-4">
                    <h4 class="font-semibold text-gray-900 mb-2">Feedback</h4>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded">${r.improvements_feedback}</p>
                ` : ''}
                
                <hr class="my-4">
                <p class="text-xs text-gray-500">Submitted: ${new Date(r.created_at).toLocaleString()}</p>
            `;
            
            document.getElementById('detailModal').classList.add('show');
        }
    } catch (error) {
        console.error('Error viewing details:', error);
    }
}

// Close modal
function closeModal() {
    document.getElementById('detailModal').classList.remove('show');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target === modal) {
        closeModal();
    }
});

// Logout function
function logout() {
    localStorage.removeItem('auth_token');
    window.location.href = './login.php';
}

// Clear all data (with confirmation)
function clearAllData() {
    if (confirm('Are you sure? This will delete all survey responses. This action cannot be undone.')) {
        alert('This feature requires additional backend implementation.');
        // Implement actual clear data API call here
    }
}
