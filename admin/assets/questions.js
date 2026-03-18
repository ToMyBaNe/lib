/**
 * Questions Management JavaScript
 */

let questions = [];
let currentEditId = null;
let deleteId = null;

// Load all questions
async function loadQuestions() {
    try {
        const res = await apiRequest('questions.php?action=list');

        // Support both shapes:
        // - adminPanel.apiRequest => { success: true, data: <serverJson> }
        // - direct fetch => <serverJson>
        const server = res?.data ?? res;

        if (!res?.success && server?.success === false) {
            throw new Error(server?.message || res?.message || 'Failed to load questions');
        }

        const list = server?.questions ?? server?.data?.questions ?? [];
        questions = Array.isArray(list) ? list : [];
        renderQuestions();
    } catch (error) {
        handleError('Failed to load questions: ' + error.message);
        showFallbackError(error.message);
    }
}

// Render questions to page
function renderQuestions() {
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const listContainer = document.getElementById('questionsList');

    loadingState.classList.add('hidden');

    if (questions.length === 0) {
        emptyState.classList.remove('hidden');
        listContainer.classList.add('hidden');
        return;
    }

    emptyState.classList.add('hidden');
    listContainer.classList.remove('hidden');

    listContainer.innerHTML = questions.map((q, index) => `
        <div class="question-card">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <i class="drag-handle fas fa-grip-vertical text-gray-400"></i>
                        <h3 class="text-lg font-semibold text-gray-900">
                            ${index + 1}. ${escapeHtml(q.question)}
                        </h3>
                        ${q.is_locked ? `<span class="inline-flex items-center gap-1 rounded bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700"><i class="fas fa-lock"></i> Locked</span>` : ''}
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="editQuestion(${q.id})" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="openDeleteModal(${q.id})" class="btn btn-danger btn-sm" ${q.is_locked ? 'disabled title="Locked questions cannot be deleted"' : ''}>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Type:</p>
                    <p class="font-medium text-gray-900">
                        <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-xs text-gray-800">${escapeHtml(capitalizeCase(q.question_type || ''))}</span>
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Category:</p>
                    <p class="font-medium text-gray-900">
                        <span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-xs text-gray-800">${escapeHtml(q.category_name || '—')}</span>
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Required:</p>
                    <p class="font-medium text-gray-900">
                        ${q.is_required ? '<span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs font-semibold">Required</span>' : '<span class="inline-block px-2 py-0.5 rounded bg-gray-100 text-gray-800 text-xs">Optional</span>'}
                    </p>
                </div>
            </div>
        </div>
    `).join('');
}

// Save question (create or update)
async function saveQuestion(e) {
    e.preventDefault();
    
    const id = document.getElementById('questionId').value;
    const questionText = document.getElementById('questionText').value.trim();
    const questionType = document.getElementById('questionType').value;
    const category = document.getElementById('category').value.trim();
    const required = document.getElementById('required').checked;
    const isLocked = document.getElementById('isLocked')?.checked;

    if (!questionText) {
        showError('Question text is required');
        return;
    }

    const formData = new FormData();
    if (id) formData.append('id', id);
    formData.append('action', id ? 'update' : 'create');
    formData.append('question', questionText);
    formData.append('question_type', questionType);
    formData.append('category_id', category);
    if (required) formData.append('is_required', '1');
    if (isLocked) formData.append('is_locked', '1');

    // Add options if applicable
    if (['rating', 'select', 'checkbox'].includes(questionType)) {
        const optionInputs = document.querySelectorAll('.option-input');
        const options = Array.from(optionInputs).map(input => input.value).filter(v => v);
        if (options.length > 0) {
            formData.append('options', JSON.stringify(options));
        }
    }

    try {
        const button = event.submitter;
        setLoading(button, true);
        
        const action = id ? 'update' : 'create';
        const url = `/admin/api/questions.php?action=${action}` + (id ? `&id=${id}` : '');
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            setLoading(button, false);
            throw new Error('Invalid server response: ' + text.substring(0, 100));
        }

        if (!data.success) {
            setLoading(button, false);
            throw new Error(data.message);
        }

        showSuccess(id ? 'Question updated successfully' : 'Question created successfully');
        closeModal();
        setLoading(button, false);
        await loadQuestions();
    } catch (error) {
        showError(error.message || 'Failed to save question');
        const button = event.submitter;
        if (button) setLoading(button, false);
    }
}

// Edit question
async function editQuestion(id) {
    try {
        const response = await fetch(`/admin/api/questions.php?action=get&id=${id}`);
        const text = await response.text();
        
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid server response');
        }

        if (!data.success) throw new Error(data.message);

        const q = data.question;
        currentEditId = id;

        document.getElementById('questionId').value = id;
        document.getElementById('questionText').value = q.question;
        document.getElementById('questionType').value = q.question_type;
        document.getElementById('category').value = q.category_name || '';
        document.getElementById('required').checked = q.is_required;
        const lockEl = document.getElementById('isLocked');
        if (lockEl) lockEl.checked = !!q.is_locked;

        document.getElementById('modalTitle').textContent = 'Edit Question';

        updateOptionsField();
        if (q.options && Array.isArray(q.options)) {
            const container = document.getElementById('optionsInput');
            container.innerHTML = '';
            q.options.forEach(opt => {
                addOptionWithValue(opt);
            });
        }

        openModal();
    } catch (error) {
        showError('Failed to load question: ' + error.message);
    }
}

// Delete question
async function deleteQuestion(id) {
    try {
        const response = await fetch(`/admin/api/questions.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid server response');
        }

        if (!data.success) throw new Error(data.message);

        showSuccess('Question deleted successfully');
        await loadQuestions();
    } catch (error) {
        showError(error.message || 'Failed to delete question');
    }
}

// Update options field visibility
function updateOptionsField() {
    const type = document.getElementById('questionType').value;
    const container = document.getElementById('optionsContainer');

    if (['rating', 'select', 'checkbox'].includes(type)) {
        container.classList.remove('hidden');
        if (document.getElementById('optionsInput').innerHTML.trim() === '') {
            addOption();
        }
    } else {
        container.classList.add('hidden');
    }
}

// Add option
function addOption() {
    addOptionWithValue('');
}

// Add option with value
function addOptionWithValue(value = '') {
    const container = document.getElementById('optionsInput');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" class="option-input flex-1" value="${escapeHtml(value)}" placeholder="Option">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Modal functions
function openModal() {
    document.getElementById('questionModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('questionModal').classList.add('hidden');
    document.getElementById('questionForm').reset();
    document.getElementById('questionId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Question';
    document.getElementById('optionsInput').innerHTML = '';
    document.getElementById('optionsContainer').classList.add('hidden');
    document.getElementById('questionType').value = 'text';
    const lockEl = document.getElementById('isLocked');
    if (lockEl) lockEl.checked = false;
    currentEditId = null;
}

function openAddModal() {
    document.getElementById('questionForm').reset();
    document.getElementById('questionId').value = '';
    document.getElementById('modalTitle').textContent = 'Add Question';
    document.getElementById('optionsInput').innerHTML = '';
    updateOptionsField();
    openModal();
}

function openDeleteModal(id) {
    const q = (questions || []).find(x => String(x.id) === String(id));
    if (q?.is_locked) {
        showWarning('This question is locked and cannot be deleted.');
        return;
    }
    deleteId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteId = null;
}

function confirmDelete() {
    if (deleteId) {
        deleteQuestion(deleteId);
        closeDeleteModal();
    }
}

// Error handling
function handleError(message) {
    console.error('Error:', message);
}

function showFallbackError(message) {
    const loadingState = document.getElementById('loadingState');
    const emptyState = document.getElementById('emptyState');
    const listContainer = document.getElementById('questionsList');
    
    loadingState.classList.add('hidden');
    emptyState.classList.remove('hidden');
    listContainer.classList.add('hidden');
    
    emptyState.innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
            <p class="text-red-600 text-lg font-semibold">Error Loading Questions</p>
            <p class="text-gray-600 mt-2">${escapeHtml(message)}</p>
            <div class="mt-6 space-y-2">
                <p class="text-sm text-gray-600">Troubleshooting:</p>
                <ol class="text-sm text-gray-600 space-y-1">
                    <li>1. Run <a href="../setup_questions.php" class="text-indigo-600 hover:underline">setup_questions.php</a></li>
                    <li>2. Verify you're logged in</li>
                    <li>3. Check browser console (F12)</li>
                </ol>
            </div>
        </div>
    `;
}

// Batch Import Functions
function openBatchModal() {
    document.getElementById('batchModal').classList.remove('hidden');
    const container = document.getElementById('batchQuestionsContainer');
    container.innerHTML = '';
    
    // Add first question form
    addBatchQuestion();
}

function closeBatchModal() {
    document.getElementById('batchModal').classList.add('hidden');
    document.getElementById('batchForm').reset();
    document.getElementById('batchQuestionsContainer').innerHTML = '';
}

function addBatchQuestion() {
    const container = document.getElementById('batchQuestionsContainer');
    const count = container.children.length + 1;
    const uniqueId = Date.now() + Math.random();
    
    const questionBlock = document.createElement('div');
    questionBlock.className = 'batch-question-block bg-gray-50 p-4 rounded-lg border';
    questionBlock.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold text-gray-700">Question ${count}</h4>
            ${count > 1 ? `<button type="button" onclick="this.closest('.batch-question-block').remove()" class="text-red-600 hover:text-red-700 text-sm">Remove</button>` : ''}
        </div>
        
        <div class="space-y-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Question Text</label>
                <textarea class="form-input w-full batch-question-text" placeholder="Enter question..." rows="2" required></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                    <select class="form-select w-full batch-question-type" onchange="updateBatchOptionsField(this)" data-id="${uniqueId}" required>
                        <option value="text">Text Input</option>
                        <option value="rating">Rating Scale</option>
                        <option value="select">Dropdown</option>
                        <option value="checkbox">Multiple Choice</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                    <input type="text" class="form-input w-full batch-question-category" placeholder="Optional">
                </div>
            </div>
            
            <!-- Options Container (hidden by default) -->
            <div class="batch-options-container hidden" data-id="${uniqueId}">
                <label class="block text-xs font-medium text-gray-600 mb-2">Options</label>
                <div class="batch-options-input space-y-2" data-id="${uniqueId}"></div>
                <button type="button" onclick="addBatchOption(this)" class="mt-2 text-xs text-indigo-600 hover:text-indigo-700">
                    <i class="fas fa-plus mr-1"></i> Add Option
                </button>
            </div>
            
            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="w-4 h-4 batch-question-required" checked>
                    <span class="text-xs font-medium text-gray-700">Required</span>
                </label>
            </div>
        </div>
    `;
    
    container.appendChild(questionBlock);
}

function updateBatchOptionsField(selectElement) {
    const type = selectElement.value;
    const dataId = selectElement.getAttribute('data-id');
    const optionsContainer = document.querySelector(`.batch-options-container[data-id="${dataId}"]`);
    const optionsInput = document.querySelector(`.batch-options-input[data-id="${dataId}"]`);
    
    if (['rating', 'select', 'checkbox'].includes(type)) {
        optionsContainer.classList.remove('hidden');
        
        // Auto-populate rating options
        if (type === 'rating' && optionsInput.innerHTML.trim() === '') {
            ['1', '2', '3', '4', '5'].forEach(val => {
                addBatchOptionWithValue(selectElement, val);
            });
        } else if (optionsInput.innerHTML.trim() === '') {
            addBatchOptionWithValue(selectElement, '');
        }
    } else {
        optionsContainer.classList.add('hidden');
    }
}

function addBatchOption(button) {
    const questionBlock = button.closest('.batch-question-block');
    const select = questionBlock.querySelector('.batch-question-type');
    addBatchOptionWithValue(select, '');
}

function addBatchOptionWithValue(selectElement, value = '') {
    const dataId = selectElement.getAttribute('data-id');
    const optionsInput = document.querySelector(`.batch-options-input[data-id="${dataId}"]`);
    
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" class="batch-option-input flex-1 form-input text-xs" value="${escapeHtml(value)}" placeholder="Option">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-700 text-xs">
            <i class="fas fa-trash"></i>
        </button>
    `;
    optionsInput.appendChild(div);
}


async function processBatchImport(e) {
    e.preventDefault();
    
    const container = document.getElementById('batchQuestionsContainer');
    const questionBlocks = container.querySelectorAll('.batch-question-block');
    
    if (questionBlocks.length === 0) {
        showError('Add at least one question');
        return;
    }
    
    const questions = [];
    
    // Collect all questions from form
    questionBlocks.forEach((block, index) => {
        const text = block.querySelector('.batch-question-text').value.trim();
        const type = block.querySelector('.batch-question-type').value;
        const category = block.querySelector('.batch-question-category').value.trim();
        const required = block.querySelector('.batch-question-required').checked ? 1 : 0;
        
        // Collect options if applicable
        let options = [];
        if (['rating', 'select', 'checkbox'].includes(type)) {
            const optionInputs = block.querySelectorAll('.batch-option-input');
            options = Array.from(optionInputs).map(input => input.value.trim()).filter(v => v);
        }
        
        if (text) {
            questions.push({ text, type, category, required, options, index: index + 1 });
        }
    });
    
    if (questions.length === 0) {
        showError('Please fill in at least one question');
        return;
    }
    
    // Disable submit button
    const button = e.target.querySelector('button[type="submit"]');
    setLoading(button, true);
    
    let successCount = 0;
    let failureCount = 0;
    
    // Save each question
    for (let i = 0; i < questions.length; i++) {
        const q = questions[i];
        
        try {
            const formData = new FormData();
            formData.append('action', 'create');
            formData.append('question', q.text);
            formData.append('question_type', q.type);
            formData.append('category', q.category);
            if (q.required) formData.append('required', '1');
            
            // Add options if applicable
            if (q.options && q.options.length > 0) {
                formData.append('options', JSON.stringify(q.options));
            }
            
            const response = await fetch('/admin/api/questions.php?action=create', {
                method: 'POST',
                body: formData
            });
            
            const text = await response.text();
            let data;
            
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid server response');
            }
            
            if (data.success) {
                successCount++;
            } else {
                failureCount++;
                console.warn(`Question ${q.index} failed:`, data.message);
            }
        } catch (error) {
            failureCount++;
            console.error(`Question ${q.index} error:`, error);
        }
    }
    
    setLoading(button, false);
    
    // Show result and reload
    if (successCount > 0) {
        showSuccess(`${successCount} question${successCount !== 1 ? 's' : ''} added successfully`);
        setTimeout(() => {
            closeBatchModal();
            loadQuestions();
        }, 1500);
    }
    
    if (failureCount > 0) {
        showError(`${failureCount} question${failureCount !== 1 ? 's' : ''} failed to save`);
    }
}

// Utility functions
function capitalizeCase(text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('questionModal');
    const deleteModal = document.getElementById('deleteModal');
    const batchModal = document.getElementById('batchModal');
    
    if (e.target === modal) closeModal();
    if (e.target === deleteModal) closeDeleteModal();
    if (e.target === batchModal) closeBatchModal();
});
