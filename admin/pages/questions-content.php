<?php
if (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'questions-content.php') {
    header('Location: ../manage_questions.php');
    exit;
}
?>
<!-- Questions Management Page Content -->

<!-- Add Buttons -->
<div class="mb-6 flex items-center justify-between gap-3">
    <div>
        <h1 class="text-lg font-semibold text-emerald-900">Survey Questions</h1>
        <p class="text-sm text-emerald-700/80">Create, edit and batch-import questions for your library survey.</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="openAddModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Question
        </button>
        <button onclick="openBatchModal()" class="btn btn-secondary">
            <i class="fas fa-upload"></i> Batch Import
        </button>
    </div>
    
</div>

<!-- Loading State -->
<div id="loadingState" class="py-12 text-center">
    <div class="spinner inline-block"></div>
    <p class="mt-4 text-sm text-emerald-700/80">Loading questions...</p>
</div>

<!-- Questions List -->
<div id="questionsList" class="hidden space-y-4">
    <!-- Questions will be inserted here -->
</div>

<!-- Empty State -->
<div id="emptyState" class="hidden rounded-2xl bg-white py-12 text-center shadow-sm ring-1 ring-emerald-50">
    <i class="mb-4 fas fa-inbox text-4xl text-emerald-300"></i>
    <p class="mb-2 text-base font-medium text-emerald-900">No questions yet</p>
    <p class="mb-4 text-sm text-emerald-700/80">Start by adding your first question to the survey.</p>
    <button onclick="openAddModal()" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create First Question
    </button>
</div>



<!-- Add/Edit Modal -->
<div id="questionModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="max-h-screen w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-lg ring-1 ring-emerald-50">
        <div class="sticky top-0 flex items-center justify-between border-b border-emerald-50 bg-white px-6 py-4">
            <h2 id="modalTitle" class="text-lg font-semibold text-emerald-900">Add Question</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="questionForm" class="space-y-4 px-6 py-5">
            <input type="hidden" id="questionId" name="id">
            
            <!-- Question Text -->
            <div>
                <label class="mb-2 block text-sm font-medium text-emerald-900">
                    Question Text <span class="text-red-500">*</span>
                </label>
                <textarea id="questionText" name="question_text" required rows="3"
                    class="form-input"
                    placeholder="Enter the survey question"></textarea>
            </div>
            
            <!-- Question Type -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-emerald-900">
                        Question Type <span class="text-red-500">*</span>
                    </label>
                    <select id="questionType" name="question_type" onchange="updateOptionsField()" required
                        class="form-select">
                        <option value="text">Text Input</option>
                        <option value="rating">Rating Scale</option>
                        <option value="select">Dropdown/Select</option>
                        <option value="checkbox">Multiple Choice</option>
                    </select>
                </div>
                
                <div>
                    <label class="mb-2 block text-sm font-medium text-emerald-900">
                        Category
                    </label>
                    <input type="text" id="category" name="category"
                        class="form-input"
                        placeholder="e.g., Feedback, About You">
                </div>
            </div>
            
            <!-- Options Field -->
            <div id="optionsContainer" class="hidden">
                <label class="mb-2 block text-sm font-medium text-emerald-900">
                    Options
                </label>
                <div id="optionsInput" class="space-y-2"></div>
                <button type="button" onclick="addOption()" class="mt-2 text-sm font-medium text-brand-green hover:text-emerald-700">
                    <i class="fas fa-plus mr-1"></i> Add Option
                </button>
            </div>
            
            <!-- Required Checkbox -->
            <div>
                <label class="flex items-center gap-3">
                    <input type="checkbox" id="required" name="required" checked
                        class="h-4 w-4 rounded border-emerald-300 text-brand-green focus:ring-brand-green">
                    <span class="text-sm font-medium text-emerald-900">Required field</span>
                </label>
            </div>

            <!-- Lock Question -->
            <div>
                <label class="flex items-center justify-between gap-3 rounded-lg border border-emerald-100 bg-emerald-50/40 px-3 py-2">
                    <span>
                        <span class="block text-sm font-medium text-emerald-900">
                            <i class="fas fa-lock mr-1 text-brand-gold"></i> Lock question
                        </span>
                        <span class="block text-xs text-emerald-700/80">Locked questions cannot be deleted.</span>
                    </span>
                    <input type="checkbox" id="isLocked" name="is_locked"
                        class="h-4 w-4 rounded border-emerald-300 text-brand-green focus:ring-brand-green">
                </label>
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end gap-3 border-t border-emerald-50 pt-5">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save Question
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-lg ring-1 ring-red-100">
        <i class="mb-3 fas fa-exclamation-circle text-3xl text-red-500"></i>
        <h2 class="mb-1 text-lg font-semibold text-emerald-950">Delete Question?</h2>
        <p class="mb-5 text-sm text-emerald-700/90">This action cannot be undone. The question will be removed from the survey.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 btn btn-secondary">
                Cancel
            </button>
            <button id="confirmDeleteBtn" onclick="confirmDelete()" class="flex-1 btn btn-danger">
                Delete
            </button>
        </div>
    </div>
</div>

<!-- Batch Import Modal -->
<div id="batchModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="max-h-screen w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-lg ring-1 ring-emerald-50">
        <div class="sticky top-0 flex items-center justify-between border-b border-emerald-50 bg-white px-6 py-4">
            <h2 class="text-lg font-semibold text-emerald-900">Batch add questions</h2>
            <button onclick="closeBatchModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="batchForm" class="space-y-4 px-6 py-5">
            <!-- Questions Container -->
            <div id="batchQuestionsContainer" class="space-y-4">
                <!-- Question fields will be added here -->
            </div>

            <!-- Add More Question Button -->
            <button type="button" onclick="addBatchQuestion()" class="btn btn-secondary w-full">
                <i class="fas fa-plus mr-2"></i> Add Another Question
            </button>

            <!-- Buttons -->
            <div class="pt-6 border-t flex gap-3 justify-end">
                <button type="button" onclick="closeBatchModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Save All Questions
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadQuestions();
        document.getElementById('questionForm').addEventListener('submit', saveQuestion);
        document.getElementById('batchForm').addEventListener('submit', processBatchImport);

    });
</script>
