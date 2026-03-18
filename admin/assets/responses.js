/**
 * Responses Page JavaScript
 * 
 * Main functionality is in pages/responses-content.php
 */

// This file can be used for additional page-specific utilities

/**
 * Export responses to CSV file
 */
function exportResponses() {
    const filterDate = document.getElementById('filterDate')?.value || '';
    
    let url = '/admin/api/responses.php?action=export';
    
    if (filterDate) {
        url += '&date=' + encodeURIComponent(filterDate);
    }
    
    // Trigger download by navigating to the endpoint
    window.location.href = url;
}

