<!-- Survey Responses Page Content -->

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-900">Survey Responses</h2>
    <button onclick="exportResponses()" class="btn btn-primary">
        <i class="fas fa-download"></i> Export CSV
    </button>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Date</label>
            <input type="date" id="filterDate" class="form-input">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search by Email</label>
            <input type="text" id="searchEmail" placeholder="email@example.com" class="form-input">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
            <button onclick="filterResponses()" class="btn btn-primary w-full">
                <i class="fas fa-search"></i> Filter
            </button>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
            <button onclick="resetFilters()" class="btn btn-secondary w-full">
                <i class="fas fa-refresh"></i> Reset
            </button>
        </div>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="text-center py-12">
    <div class="spinner inline-block"></div>
    <p class="text-gray-600 mt-4">Loading responses...</p>
</div>

<!-- Responses Table -->
<div id="responsesList" class="hidden bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Answers</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="responsesTable" class="divide-y">
                <!-- Responses will be inserted here -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t">
        <span id="paginationInfo" class="text-sm text-gray-600"></span>
        <div class="flex gap-2">
            <button onclick="previousPage()" class="btn btn-secondary" id="prevBtn">Previous</button>
            <button onclick="nextPage()" class="btn btn-secondary" id="nextBtn">Next</button>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="hidden text-center py-12 bg-white rounded-lg shadow">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600 text-lg">No responses found</p>
</div>

<!-- Response Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="sticky top-0 bg-white p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">Response Details</h2>
            <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <div id="detailContent" class="p-6">
            <!-- Details will be inserted here -->
        </div>
        
        <div class="sticky bottom-0 bg-gray-50 p-6 border-t flex gap-3 justify-end">
            <button onclick="closeDetailModal()" class="btn btn-secondary">Close</button>
            <button onclick="deleteCurrentResponse()" class="btn btn-danger">Delete</button>
        </div>
    </div>
</div>

<script>
    let allResponses = [];
    let currentPage = 0;
    let totalRows = 0;
    let pageSize = 50;
    let currentDetailId = null;

    class ResponsesManager {
        constructor() {
            this.currentPage = 0;
            this.pageSize = 50;
            this.totalRows = 0;
            this.filters = {
                date: '',
                search: ''
            };
        }

        async loadResponses() {
            try {
                const offset = this.currentPage * this.pageSize;
                let url = `./api/responses.php?action=list&limit=${this.pageSize}&offset=${offset}`;
                
                if (this.filters.date) {
                    url += `&date=${encodeURIComponent(this.filters.date)}`;
                }
                if (this.filters.search) {
                    url += `&search=${encodeURIComponent(this.filters.search)}`;
                }

                const data = await apiRequest(url);
                
                if (!data.success) {
                    // Check if this is a setup error (missing table)
                    if (data.setup_url) {
                        showSetupError(data.message, data.setup_url);
                        return;
                    }
                    throw new Error(data.message || 'Failed to load responses');
                }

                allResponses = data.data || [];
                totalRows = data.total;
                this.updateUI();
            } catch (error) {
                handleError('Failed to load responses: ' + error.message);
                showFallbackError(error.message);
            }
        }

        updateUI() {
            const loadingState = document.getElementById('loadingState');
            const emptyState = document.getElementById('emptyState');
            const listContainer = document.getElementById('responsesList');

            loadingState.classList.add('hidden');

            if (allResponses.length === 0) {
                emptyState.classList.remove('hidden');
                listContainer.classList.add('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            listContainer.classList.remove('hidden');

            const table = document.getElementById('responsesTable');
            table.innerHTML = allResponses.map((r, idx) => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${this.currentPage * this.pageSize + idx + 1}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${escapeHtml(r.email)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${new Date(r.submitted_at).toLocaleString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${r.answer_count}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm gap-2 flex">
                        <button onclick="viewResponse(${r.id})" class="text-indigo-600 hover:text-indigo-700">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="deleteResponse(${r.id})" class="text-red-600 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Update pagination
            const paginationInfo = document.getElementById('paginationInfo');
            const start = this.currentPage * this.pageSize + 1;
            const end = Math.min((this.currentPage + 1) * this.pageSize, totalRows);
            paginationInfo.textContent = `Showing ${start}-${end} of ${totalRows} responses`;

            document.getElementById('prevBtn').disabled = this.currentPage === 0;
            document.getElementById('nextBtn').disabled = end >= totalRows;
        }

        async deleteResponse(id) {
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                const response = await fetch('./api/responses.php', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();
                const data = JSON.parse(text);

                if (!data.success) throw new Error(data.message);

                showSuccess('Response deleted successfully');
                await this.loadResponses();
            } catch (error) {
                showError(error.message || 'Failed to delete response');
            }
        }

        previousPage() {
            if (this.currentPage > 0) {
                this.currentPage--;
                this.loadResponses();
            }
        }

        nextPage() {
            const end = Math.min((this.currentPage + 1) * this.pageSize, totalRows);
            if (end < totalRows) {
                this.currentPage++;
                this.loadResponses();
            }
        }

        filterAndReset() {
            this.currentPage = 0;
            this.filters.date = document.getElementById('filterDate').value;
            this.filters.search = document.getElementById('searchEmail').value;
            this.loadResponses();
        }

        resetFilters() {
            this.currentPage = 0;
            document.getElementById('filterDate').value = '';
            document.getElementById('searchEmail').value = '';
            this.filters = { date: '', search: '' };
            this.loadResponses();
        }
    }

    const manager = new ResponsesManager();

    // Field labels mapping
    const fieldLabels = {
        'id': 'ID',
        'visitor_name': 'Visitor Name',
        'visitor_email': 'Visitor Email',
        'visit_frequency': 'Visit Frequency',
        'purpose': 'Purpose of Visit',
        'satisfaction': 'Overall Satisfaction',
        'book_availability': 'Book Availability Rating',
        'staff_helpfulness': 'Staff Helpfulness Rating',
        'facilities_rating': 'Facilities Rating',
        'would_recommend': 'Would Recommend',
        'improvements_feedback': 'Improvements Feedback',
        'created_at': 'Submitted At'
    };

    // Format field value for display
    function formatFieldValue(value) {
        if (value === null || value === undefined || value === '') {
            return '<span class="text-gray-400 italic">Not provided</span>';
        }
        
        if (typeof value === 'boolean') {
            return value ? '<span class="text-green-600 font-semibold">Yes</span>' : '<span class="text-red-600 font-semibold">No</span>';
        }

        if (typeof value === 'string' && value.match(/^\d{4}-\d{2}-\d{2}/)) {
            return escapeHtml(new Date(value).toLocaleString());
        }

        return escapeHtml(String(value));
    }

    document.addEventListener('DOMContentLoaded', function() {
        manager.loadResponses();
    });

    function filterResponses() {
        manager.filterAndReset();
    }

    function resetFilters() {
        manager.resetFilters();
    }

    function previousPage() {
        manager.previousPage();
    }

    function nextPage() {
        manager.nextPage();
    }

    async function viewResponse(id) {
        try {
            const data = await apiRequest(`./api/responses.php?action=get&id=${id}`);
            if (!data.success) throw new Error(data.message);

            const response = data.data;
            currentDetailId = id;

            let detailHTML = `
                <div class="space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-600 mb-2">Response ID & Timestamp</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">ID</p>
                                <p class="text-gray-900 font-mono">${response.id}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Submitted</p>
                                <p class="text-gray-900">${new Date(response.submitted_at).toLocaleString()}</p>
                            </div>
                        </div>
                    </div>
            `;

            // Organize responses into sections
            const responseData = response.responses || {};
            
            // Visitor Information Section
            const visitorFields = ['visitor_name', 'visitor_email', 'visit_frequency', 'purpose'];
            const visitorData = {};
            visitorFields.forEach(field => {
                if (field in responseData) {
                    visitorData[field] = responseData[field];
                    delete responseData[field];
                }
            });

            if (Object.keys(visitorData).length > 0) {
                detailHTML += `<div class="bg-blue-50 p-4 rounded-lg"><h3 class="text-sm font-semibold text-blue-900 mb-3">Visitor Information</h3><div class="space-y-2">`;
                Object.entries(visitorData).forEach(([key, value]) => {
                    detailHTML += `
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">${fieldLabels[key] || key}:</span>
                            <span class="text-gray-900">${formatFieldValue(value)}</span>
                        </div>
                    `;
                });
                detailHTML += `</div></div>`;
            }

            // Ratings Section
            const ratingFields = ['satisfaction', 'book_availability', 'staff_helpfulness', 'facilities_rating'];
            const ratingData = {};
            ratingFields.forEach(field => {
                if (field in responseData) {
                    ratingData[field] = responseData[field];
                    delete responseData[field];
                }
            });

            if (Object.keys(ratingData).length > 0) {
                detailHTML += `<div class="bg-green-50 p-4 rounded-lg"><h3 class="text-sm font-semibold text-green-900 mb-3">Ratings & Feedback</h3><div class="space-y-2">`;
                Object.entries(ratingData).forEach(([key, value]) => {
                    detailHTML += `
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">${fieldLabels[key] || key}:</span>
                            <span class="text-gray-900">${formatFieldValue(value)}</span>
                        </div>
                    `;
                });
                detailHTML += `</div></div>`;
            }

            // Recommendation Section
            if ('would_recommend' in responseData) {
                const recommend = responseData['would_recommend'];
                const recommendClass = recommend ? 'bg-emerald-50' : 'bg-yellow-50';
                const recommendColor = recommend ? 'text-emerald-900' : 'text-yellow-900';
                detailHTML += `
                    <div class="${recommendClass} p-4 rounded-lg">
                        <h3 class="text-sm font-semibold ${recommendColor} mb-2">Recommendation</h3>
                        <p class="text-gray-900">${formatFieldValue(recommend)}</p>
                    </div>
                `;
                delete responseData['would_recommend'];
            }

            // Improvements/Feedback Section
            if ('improvements_feedback' in responseData) {
                detailHTML += `
                    <div class="bg-amber-50 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-amber-900 mb-2">Improvements & Feedback</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">${formatFieldValue(responseData['improvements_feedback'])}</p>
                    </div>
                `;
                delete responseData['improvements_feedback'];
            }

            // Any remaining fields
            if (Object.keys(responseData).length > 0) {
                detailHTML += `<div class="bg-gray-50 p-4 rounded-lg"><h3 class="text-sm font-semibold text-gray-700 mb-3">Additional Information</h3><div class="space-y-2">`;
                Object.entries(responseData).forEach(([key, value]) => {
                    detailHTML += `
                        <div class="flex justify-between">
                            <span class="text-gray-700 font-medium">${fieldLabels[key] || key}:</span>
                            <span class="text-gray-900">${formatFieldValue(value)}</span>
                        </div>
                    `;
                });
                detailHTML += `</div></div>`;
            }

            detailHTML += `</div>`;

            document.getElementById('detailContent').innerHTML = detailHTML;
            document.getElementById('detailModal').classList.remove('hidden');
        } catch (error) {
            showError(error.message);
        }
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
        currentDetailId = null;
    }

    async function deleteResponse(id) {
        if (!confirm('Are you sure you want to delete this response?')) return;
        
        await manager.deleteResponse(id);
    }

    async function deleteCurrentResponse() {
        if (!currentDetailId) return;
        if (!confirm('Are you sure you want to delete this response?')) return;
        
        await manager.deleteResponse(currentDetailId);
        closeDetailModal();
    }

    async function exportResponses() {
        const date = document.getElementById('filterDate').value;
        let url = './api/responses.php?action=export';
        if (date) {
            url += `&date=${encodeURIComponent(date)}`;
        }
        window.location.href = url;
    }

    function handleError(message) {
        console.error('Error:', message);
    }

    function showFallbackError(message) {
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const listContainer = document.getElementById('responsesList');
        
        loadingState.classList.add('hidden');
        emptyState.classList.remove('hidden');
        listContainer.classList.add('hidden');
        
        emptyState.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-red-600 text-lg font-semibold">Error Loading Responses</p>
                <p class="text-gray-600 mt-2">${escapeHtml(message)}</p>
            </div>
        `;
    }

    function showSetupError(message, setupUrl) {
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const listContainer = document.getElementById('responsesList');
        
        loadingState.classList.add('hidden');
        emptyState.classList.remove('hidden');
        listContainer.classList.add('hidden');
        
        emptyState.innerHTML = `
            <div class="text-center py-12 bg-blue-50 rounded-lg">
                <i class="fas fa-tools text-4xl text-blue-500 mb-4"></i>
                <p class="text-blue-600 text-lg font-semibold">Setup Required</p>
                <p class="text-gray-600 mt-2">${escapeHtml(message)}</p>
                <div class="mt-6">
                    <p class="text-sm text-gray-700 mb-4">The responses table needs to be initialized. Click the button below to set it up:</p>
                    <a href="${escapeHtml(setupUrl)}" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Run Setup
                    </a>
                </div>
            </div>
        `;
    }

</script>
