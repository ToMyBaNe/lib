<?php
if (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'questions-content.php') {
    header('Location: ../manage_questions.php');
    exit;
}
?>
<!-- Questions Management Page Content -->

<!-- Add Buttons -->
<div class="mb-6 flex justify-between gap-3">
    <h1 class="text-lg font-semibold">Manage your survey questionairre here</h1>
    <div class="flex gap-4 items-center">
        <button onclick="openAddModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Question
        </button>
        <button onclick="openBatchModal()" class="btn btn-secondary">
            <i class="fas fa-upload"></i> Batch Import
        </button>
    </div>
    
</div>

<!-- Loading State -->
<div id="loadingState" class="text-center py-12">
    <div class="spinner inline-block"></div>
    <p class="text-gray-600 mt-4">Loading questions...</p>
</div>

<!-- Questions List -->
<div id="questionsList" class="hidden space-y-4">
    <!-- Questions will be inserted here -->
</div>

<!-- Empty State -->
<div id="emptyState" class="hidden text-center py-12">
    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
    <p class="text-gray-600 text-lg mb-4">No questions found</p>
    <button onclick="openAddModal()" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create First Question
    </button>
</div>



<!-- Add/Edit Modal -->
<div id="questionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="sticky top-0 bg-white p-6 border-b flex justify-between items-center">
            <h2 id="modalTitle" class="text-2xl font-bold">Add Question</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="questionForm" class="p-6 space-y-4">
            <input type="hidden" id="questionId" name="id">
            
            <!-- Question Text -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Question Text <span class="text-red-500">*</span>
                </label>
                <textarea id="questionText" name="question_text" required rows="3"
                    class="form-input"
                    placeholder="Enter the survey question"></textarea>
            </div>
            
            <!-- Question Type -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Category
                    </label>
                    <input type="text" id="category" name="category"
                        class="form-input"
                        placeholder="e.g., Feedback, About You">
                </div>
            </div>
            
            <!-- Options Field -->
            <div id="optionsContainer" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Options
                </label>
                <div id="optionsInput" class="space-y-2"></div>
                <button type="button" onclick="addOption()" class="mt-2 text-sm text-indigo-600 hover:text-indigo-700">
                    <i class="fas fa-plus mr-1"></i> Add Option
                </button>
            </div>
            
            <!-- Required Checkbox -->
            <div>
                <label class="flex items-center gap-3">
                    <input type="checkbox" id="required" name="required" checked
                        class="w-4 h-4 text-indigo-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Required field</span>
                </label>
            </div>
            
            <!-- Buttons -->
            <div class="pt-6 border-t flex gap-3 justify-end">
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
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md">
        <i class="fas fa-exclamation-circle text-4xl text-red-600 mb-4"></i>
        <h2 class="text-2xl font-bold mb-2">Delete Question?</h2>
        <p class="text-gray-600 mb-6">This action cannot be undone.</p>
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
<div id="batchModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="sticky top-0 bg-white p-6 border-b flex justify-between items-center">
            <h2 class="text-2xl font-bold">Batch Add Questions</h2>
            <button onclick="closeBatchModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <form id="batchForm" class="p-6 space-y-4">
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
