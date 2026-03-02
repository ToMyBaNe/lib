// Initialize rating buttons
document.addEventListener('DOMContentLoaded', function() {
    initializeRatingButtons();
    setupFormSubmission();
});

function initializeRatingButtons() {
    const ratingScales = document.querySelectorAll('.rating-scale');
    
    ratingScales.forEach(scale => {
        const buttons = scale.querySelectorAll('.rating-btn');
        const hiddenInput = scale.querySelector('input[type="hidden"]');
        const fieldName = scale.getAttribute('data-name');
        
        buttons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const value = this.getAttribute('data-value');
                
                // Update hidden input
                hiddenInput.value = value;
                
                // Update button styles
                buttons.forEach(b => {
                    b.classList.remove('border-indigo-600', 'bg-indigo-100', 'border-2');
                    b.classList.add('border-gray-300');
                });
                
                this.classList.remove('border-gray-300');
                this.classList.add('border-indigo-600', 'bg-indigo-100');
            });
        });
    });
}

function setupFormSubmission() {
    const surveyForm = document.getElementById('surveyForm');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    
    surveyForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate that all required rating fields have values
        const ratings = [
            { name: 'satisfaction', label: 'Overall satisfaction' },
            { name: 'book_availability', label: 'Book availability' },
            { name: 'staff_helpfulness', label: 'Staff helpfulness' },
            { name: 'facilities_rating', label: 'Facilities rating' },
            { name: 'would_recommend', label: 'Recommendation' }
        ];
        
        let isValid = true;
        for (let rating of ratings) {
            const value = surveyForm.querySelector(`input[name="${rating.name}"]`).value;
            if (value === '0' || value === '') {
                showError(`Please select a rating for: ${rating.label}`);
                isValid = false;
                break;
            }
        }
        
        if (!isValid) return;
        
        // Collect form data
        const formData = new FormData(surveyForm);
        
        try {
            // Submit to backend
            const response = await fetch('../api/submit_survey.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Hide form and show success message
                surveyForm.classList.add('hidden');
                successMessage.classList.remove('hidden');
                
                // Reset form after 3 seconds (for page reload)
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } else {
                showError(data.message || 'An error occurred. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Network error. Please check your connection and try again.');
        }
    });
}

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    
    errorText.textContent = message;
    errorMessage.classList.remove('hidden');
    
    // Auto-hide error after 5 seconds
    setTimeout(() => {
        errorMessage.classList.add('hidden');
    }, 5000);
}
