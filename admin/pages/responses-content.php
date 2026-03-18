<?php
if (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'responses-content.php') {
    header('Location: ../responses.php');
    exit;
}
?>
<!-- Survey Responses Page Content -->

<div class="mb-6 flex justify-between items-center">
    <h2 class="text-lg font-semibold text-gray-900">Responses</h2>
    <!-- <button type="button" onclick="exportResponses()" class="btn btn-primary">
        <i class="fas fa-download"></i> Export CSV
    </button> -->
</div>
<div class="admin-card mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="form-label">Filter by Date</label>
            <input type="date" id="filterDate" class="form-input">
        </div>
        <div>
            <label class="form-label">Search by Email</label>
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
<div id="responsesList" class="hidden admin-card overflow-hidden">
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
            document.getElementById('loadingState').classList.remove('hidden');

            let url = `./api/responses.php?action=list&page=${this.currentPage + 1}&limit=${this.pageSize}`;

            if (this.filters.date) {
                url += `&date_from=${encodeURIComponent(this.filters.date)}`;
                url += `&date_to=${encodeURIComponent(this.filters.date)}`;
            }

            if (this.filters.search) {
                url += `&search=${encodeURIComponent(this.filters.search)}`;
            }

            const response = await fetch(url);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to load responses');
            }

            // ✅ Correct structure from your PHP
            allResponses = Array.isArray(data.responses) ? data.responses : [];
            this.totalRows = data.pagination?.total || 0;

            this.updateUI();

        } catch (error) {
            console.error(error);
            showFallbackError(error.message);
        }
    }

    updateUI() {
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const listContainer = document.getElementById('responsesList');
        const table = document.getElementById('responsesTable');

        loadingState.classList.add('hidden');

        if (allResponses.length === 0) {
            emptyState.classList.remove('hidden');
            listContainer.classList.add('hidden');
            return;
        }

        emptyState.classList.add('hidden');
        listContainer.classList.remove('hidden');

        table.innerHTML = allResponses.map((r, idx) => `
            <tr>
                <td class="px-6 py-4 text-sm">
                    ${this.currentPage * this.pageSize + idx + 1}
                </td>
                <td class="px-6 py-4 text-sm">
                    ${escapeHtml(r.visitor_email)}
                </td>
                <td class="px-6 py-4 text-sm">
                    ${new Date(r.created_at).toLocaleString()}
                </td>
                <td class="px-6 py-4 text-sm">
                    ${escapeHtml(r.visit_frequency || '-')}
                </td>
                <td class="px-6 py-4 flex gap-2">
                    <button onclick="viewResponse(${r.id})" class="text-indigo-600">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="deleteResponse(${r.id})" class="text-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        const start = this.currentPage * this.pageSize + 1;
        const end = Math.min((this.currentPage + 1) * this.pageSize, this.totalRows);

        document.getElementById('paginationInfo').textContent =
            `Showing ${start}-${end} of ${this.totalRows} responses`;

        document.getElementById('prevBtn').disabled = this.currentPage === 0;
        document.getElementById('nextBtn').disabled = end >= this.totalRows;
    }

    async deleteResponse(id) {
        try {
            const response = await fetch(`./api/responses.php?id=${id}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message);
            }

            alert('Response deleted successfully');
            await this.loadResponses();

        } catch (error) {
            alert(error.message || 'Delete failed');
        }
    }

    previousPage() {
        if (this.currentPage > 0) {
            this.currentPage--;
            this.loadResponses();
        }
    }

    nextPage() {
        const end = Math.min((this.currentPage + 1) * this.pageSize, this.totalRows);
        if (end < this.totalRows) {
            this.currentPage++;
            this.loadResponses();
        }
    }

    applyFilters() {
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

document.addEventListener('DOMContentLoaded', () => {
    manager.loadResponses();
});

function filterResponses() {
    manager.applyFilters();
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

async function deleteResponse(id) {
    if (!confirm('Are you sure you want to delete this response?')) return;
    await manager.deleteResponse(id);
}

async function viewResponse(id) {
    try {
        const response = await fetch(`./api/responses.php?action=get&id=${id}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message);
        }

        const responseData = data.data;

        let html = `
            <div class="space-y-4">
                <div>
                    <strong>ID:</strong> ${responseData.id}
                </div>
                <div>
                    <strong>Submitted:</strong> ${new Date(responseData.submitted_at).toLocaleString()}
                </div>
                <hr>
        `;

        Object.entries(responseData.responses || {}).forEach(([key, value]) => {
            html += `
                <div class="flex justify-between">
                    <span class="font-medium">${escapeHtml(key)}</span>
                    <span>${escapeHtml(String(value))}</span>
                </div>
            `;
        });

        html += `</div>`;

        document.getElementById('detailContent').innerHTML = html;
        document.getElementById('detailModal').classList.remove('hidden');
        currentDetailId = id;

    } catch (error) {
        alert(error.message);
    }
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
    currentDetailId = null;
}

function showFallbackError(message) {
    document.getElementById('loadingState').classList.add('hidden');
    const emptyState = document.getElementById('emptyState');
    emptyState.classList.remove('hidden');
    emptyState.innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
            <p class="text-red-600 font-semibold">Error Loading Responses</p>
            <p class="text-gray-600 mt-2">${escapeHtml(message)}</p>
        </div>
    `;
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>"']/g, function (m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}
</script>