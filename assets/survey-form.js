/**
 * Survey Form Manager
 * Handles survey form loading, rendering, and submission
 */

class SurveyFormManager {
    constructor() {
        this.allQuestions = {};
        this.isLoading = false;
        this.init();
    }

    /**
     * Initialize the survey form manager
     */
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.loadSurveyQuestions();
            this.setupFormSubmission();
        });
    }

    /**
     * Load questions from API
     */
    async loadSurveyQuestions() {
        try {
            this.isLoading = true;
            const response = await fetch('../api/questions.php');
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const text = await response.text();

            if (!text || text.trim().length === 0) {
                throw new Error('API returned empty response. Make sure database tables exist and are populated.');
            }

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse response:', text);
                throw new Error('Invalid JSON response from server. Check browser console for details.');
            }

            if (!data || !data.success) {
                const message = data?.message || 'Unknown error loading questions';
                throw new Error(message);
            }

            if (!data.categorized || Object.keys(data.categorized).length === 0) {
                throw new Error('No questions found. Please run setup_questions.php from the admin panel.');
            }

            this.allQuestions = data.categorized;
            this.renderDynamicQuestions();
            this.showSurveyForm();

        } catch (error) {
            console.error('Error loading questions:', error);
            this.showError(error.message || 'Failed to load survey form');
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Render all dynamic questions in their categories
     */
    renderDynamicQuestions() {
        const container = this.getElement('dynamicQuestionsContainer');
        if (!container) {
            console.error('dynamicQuestionsContainer not found');
            return;
        }

        if (!this.allQuestions || Object.keys(this.allQuestions).length === 0) {
            container.innerHTML = '<div class="text-center text-gray-600">No questions available. Please contact administrator.</div>';
            return;
        }

        try {
            const html = Object.entries(this.allQuestions)
                .map(([category, questions]) => {
                    if (!Array.isArray(questions)) {
                        console.warn('Questions for category', category, 'is not an array:', questions);
                        return '';
                    }
                    return this.renderCategory(category, questions);
                })
                .filter(html => html.length > 0)
                .join('');

            if (!html) {
                container.innerHTML = '<div class="text-center text-gray-600">No valid questions to display.</div>';
                return;
            }

            container.innerHTML = html;
            this.attachQuestionHandlers();
        } catch (error) {
            console.error('Error rendering questions:', error);
            container.innerHTML = `<div class="bg-red-50 border border-red-200 p-4 rounded text-red-800">Error rendering survey: ${error.message}</div>`;
        }
    }

    /**
     * Render a category section with its questions
     */
    renderCategory(category, questions) {
        const questionsHtml = questions
            .map(question => this.renderQuestion(question))
            .join('');

        return `
            <div class="border-b pb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-list text-indigo-600 mr-3"></i> ${this.escapeHtml(category)}
                </h2>
                <div class="space-y-6">
                    ${questionsHtml}
                </div>
            </div>
        `;
    }

    /**
     * Render a single question based on its type
     */
    renderQuestion(question) {
        // Validate question object
        if (!question || typeof question !== 'object') {
            console.warn('Invalid question object:', question);
            return '';
        }

        if (!question.id || !question.question) {
            console.warn('Question missing id or question text:', question);
            return '';
        }

        const required = question.required ? '<span class="text-red-500">*</span>' : '';
        const qId = `q_${question.id}`;
        const questionText = String(question.question || '');
        
        const commonLabel = `
            <label for="${qId}" class="block text-sm font-medium text-gray-700 mb-2">
                ${this.escapeHtml(questionText)} ${required}
            </label>
        `;

        const questionType = String(question.type || 'text').toLowerCase();

        const renderMethods = {
            text: () => this.renderTextInput(qId, question, commonLabel),
            textarea: () => this.renderTextarea(qId, question, commonLabel),
            select: () => this.renderSelect(qId, question, commonLabel),
            radio: () => this.renderRadio(qId, question, commonLabel),
            checkbox: () => this.renderCheckbox(qId, question, commonLabel),
            rating: () => this.renderRating(qId, question, commonLabel),
        };

        const renderer = renderMethods[questionType];
        if (!renderer) {
            console.warn('Unknown question type:', questionType);
            return this.renderTextInput(qId, question, commonLabel); // Fallback to text input
        }

        try {
            return renderer();
        } catch (error) {
            console.error('Error rendering question type', questionType, ':', error, question);
            return `<div class="text-red-600"><strong>Error rendering question:</strong> ${error.message}</div>`;
        }
    }

    /**
     * Render text input field
     */
    renderTextInput(qId, question, label) {
        const requiredAttr = question.required ? 'required' : '';
        return `
            <div>
                ${label}
                <input 
                    type="text" 
                    id="${qId}" 
                    name="responses[${question.id}]"
                    ${requiredAttr}
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
        `;
    }

    /**
     * Render textarea field
     */
    renderTextarea(qId, question, label) {
        const requiredAttr = question.required ? 'required' : '';
        return `
            <div>
                ${label}
                <textarea 
                    id="${qId}" 
                    name="responses[${question.id}]"
                    rows="4"
                    ${requiredAttr}
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                ></textarea>
            </div>
        `;
    }

    /**
     * Render select dropdown
     */
    renderSelect(qId, question, label) {
        const requiredAttr = question.required ? 'required' : '';
        const optionsList = Array.isArray(question.options) ? question.options : [];
        const options = optionsList
            .map(opt => {
                const optStr = String(opt || '');
                return `<option value="${this.escapeHtml(optStr)}">${this.escapeHtml(optStr)}</option>`;
            })
            .join('');

        return `
            <div>
                ${label}
                <select 
                    id="${qId}" 
                    name="responses[${question.id}]"
                    ${requiredAttr}
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    <option value="">Select an option</option>
                    ${options}
                </select>
            </div>
        `;
    }

    /**
     * Render radio button group
     */
    renderRadio(qId, question, label) {
        const requiredAttr = question.required ? 'required' : '';
        const optionsList = Array.isArray(question.options) ? question.options : [];
        const options = optionsList
            .map(opt => {
                const optStr = String(opt || '');
                return `
                <label class="flex items-center gap-2 cursor-pointer">
                    <input 
                        type="radio" 
                        name="responses[${question.id}]" 
                        value="${this.escapeHtml(optStr)}"
                        ${requiredAttr}
                        class="w-4 h-4"
                    >
                    <span class="text-gray-700">${this.escapeHtml(optStr)}</span>
                </label>
            `;
            })
            .join('');

        return `
            <div>
                ${label}
                <div class="space-y-2">
                    ${options}
                </div>
            </div>
        `;
    }

    /**
     * Render checkbox group
     */
    renderCheckbox(qId, question, label) {
        const optionsList = Array.isArray(question.options) ? question.options : [];
        const options = optionsList
            .map(opt => {
                const optStr = String(opt || '');
                return `
                <label class="flex items-center gap-2 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="responses[${question.id}][]" 
                        value="${this.escapeHtml(optStr)}"
                        class="w-4 h-4"
                    >
                    <span class="text-gray-700">${this.escapeHtml(optStr)}</span>
                </label>
            `;
            })
            .join('');

        return `
            <div>
                ${label}
                <div class="space-y-2">
                    ${options}
                </div>
            </div>
        `;
    }

    /**
     * Render rating scale
     */
    renderRating(qId, question, label) {
        const requiredAttr = question.required ? 'required' : '';
        const maxRating = 5;
        const ratingButtons = Array.from({ length: maxRating }, (_, i) => i + 1)
            .map(val => `
                <button 
                    type="button" 
                    class="rating-btn px-4 py-2 border-2 border-gray-300 rounded-lg hover:border-indigo-600 hover:bg-indigo-50 transition text-sm font-medium" 
                    data-value="${val}" 
                    data-question="${question.id}"
                >
                    ${val}
                </button>
            `)
            .join('');

        return `
            <div>
                ${label}
                <input type="hidden" name="responses[${question.id}]" value="" ${requiredAttr}>
                <div class="flex gap-2">
                    ${ratingButtons}
                </div>
            </div>
        `;
    }

    /**
     * Attach event handlers to dynamic question elements
     */
    attachQuestionHandlers() {
        // Handle rating button clicks
        document.querySelectorAll('.rating-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleRatingClick(e, btn));
        });
    }

    /**
     * Handle rating button click
     */
    handleRatingClick(e, btn) {
        e.preventDefault();
        const questionId = btn.dataset.question;
        const value = btn.dataset.value;

        // Remove selection from all buttons in this group
        document.querySelectorAll(`.rating-btn[data-question="${questionId}"]`).forEach(b => {
            b.classList.remove('border-indigo-600', 'bg-indigo-100');
            b.classList.add('border-gray-300');
        });

        // Add selection to clicked button
        btn.classList.add('border-indigo-600', 'bg-indigo-100');
        btn.classList.remove('border-gray-300');

        // Update hidden input value
        const hiddenInput = document.querySelector(`input[name="responses[${questionId}]"]`);
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    }

    /**
     * Setup form submission handler
     */
    setupFormSubmission() {
        const form = this.getElement('surveyForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                const formData = this.collectFormData(form);
                const response = await fetch('../api/submit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'Submission failed');
                }

                this.handleSubmissionSuccess();

            } catch (error) {
                console.error('Submission error:', error);
                this.showError(error.message || 'Failed to submit survey');
            }
        });
    }

    /**
     * Collect form data from all inputs
     * Extracts standard fields from response data if they exist
     */
    collectFormData(form) {
        const responsesData = this.collectResponses();
        
        // Extract standard visitor information from responses
        // These typically have IDs 1-4 based on default setup
        // But we use a flexible approach looking for matching questions
        let visitor_name = '';
        let visitor_email = '';
        let visit_frequency = '';
        let purpose = '';
        
        // Search through responses to find the standard fields
        Object.entries(this.allQuestions).forEach(([category, questions]) => {
            questions.forEach(q => {
                const value = responsesData[q.id];
                if (!value) return;
                
                const questionLower = q.question.toLowerCase();
                
                // Match by question content/category
                if ((category === 'About You' || questionLower.includes('name')) && !visitor_name && questionLower.includes('name')) {
                    visitor_name = value;
                }
                if ((category === 'About You' || questionLower.includes('email')) && !visitor_email && questionLower.includes('email')) {
                    visitor_email = value;
                }
                if ((category === 'Your Visit' || questionLower.includes('visit')) && !visit_frequency && questionLower.includes('how often')) {
                    visit_frequency = value;
                }
                if ((category === 'Your Visit' || questionLower.includes('purpose')) && !purpose && questionLower.includes('purpose')) {
                    purpose = value;
                }
            });
        });

        return {
            visitor_name: visitor_name,
            visitor_email: visitor_email,
            visit_frequency: visit_frequency,
            purpose: purpose,
            responses: responsesData
        };
    }

    /**
     * Collect responses from all response inputs
     */
    collectResponses() {
        const responsesData = {};

        document.querySelectorAll('input[name^="responses"], select[name^="responses"], textarea[name^="responses"]').forEach(input => {
            const match = input.name.match(/responses\[(\d+)\]/);
            if (!match) return;

            const qId = match[1];

            if (input.type === 'checkbox') {
                if (input.checked) {
                    if (!responsesData[qId]) {
                        responsesData[qId] = [];
                    }
                    responsesData[qId].push(input.value);
                }
            } else if (input.value) {
                responsesData[qId] = input.value;
            }
        });

        return responsesData;
    }

    /**
     * Handle successful form submission
     */
    handleSubmissionSuccess() {
        this.getElement('surveyContainer')?.classList.add('hidden');
        this.getElement('successMessage')?.classList.remove('hidden');

        const params = new URLSearchParams(window.location.search);

        const data = params.get("data");
        const decoded = atob(data);

        window.location.href=decoded
    }

    /**
     * Show survey form
     */
    showSurveyForm() {
        this.getElement('loadingState')?.classList.add('hidden');
        this.getElement('surveyContainer')?.classList.remove('hidden');
    }

    /**
     * Show error message
     */
    showError(message) {
        this.getElement('loadingState')?.classList.add('hidden');
        const errorText = this.getElement('errorText');
        if (errorText) {
            errorText.textContent = message;
        }
        this.getElement('errorMessage')?.classList.remove('hidden');
    }

    /**
     * Get element by ID safely
     */
    getElement(id) {
        return document.getElementById(id);
    }

    /**
     * Escape HTML special characters
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize the survey form manager when script loads
new SurveyFormManager();
