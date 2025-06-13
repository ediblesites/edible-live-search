/**
 * Keyboard navigation for Edible Live Search
 */
document.addEventListener('DOMContentLoaded', function() {
    let currentIndex = -1;
    let searchResults = [];
    
    // Auto-trigger search if input has value on page load
    function autoTriggerSearch() {
        const searchInputs = document.querySelectorAll('.edible-search-input');
        searchInputs.forEach(input => {
            if (input.value.trim().length >= 3) {
                // Trigger HTMX request
                htmx.trigger(input, 'keyup');
            }
        });
    }
    
    // Run auto-trigger after a short delay to ensure HTMX is ready
    setTimeout(autoTriggerSearch, 100);
    
    // Listen for search input events
    document.addEventListener('keydown', function(e) {
        const searchInput = e.target.closest('.edible-search-input');
        if (!searchInput) return;
        
        const resultsContainer = searchInput.closest('.edible-search-form').querySelector('#edible-search-results');
        if (!resultsContainer) return;
        
        const resultItems = resultsContainer.querySelectorAll('.edible-search-result-item');
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, resultItems.length - 1);
                updateSelection(resultItems);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, -1);
                updateSelection(resultItems);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (currentIndex >= 0 && resultItems[currentIndex]) {
                    const link = resultItems[currentIndex].querySelector('a');
                    if (link) {
                        window.location.href = link.href;
                    }
                }
                break;
        }
    });
    
    // Update visual selection
    function updateSelection(resultItems) {
        resultItems.forEach((item, index) => {
            if (index === currentIndex) {
                item.style.backgroundColor = '#f0f0f0';
                item.style.outline = '2px solid #0073aa';
            } else {
                item.style.backgroundColor = '';
                item.style.outline = '';
            }
        });
    }
    
    // Reset selection when input changes
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('edible-search-input')) {
            currentIndex = -1;
        }
    });
    
    // Reset selection when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.edible-search-form')) {
            currentIndex = -1;
            const allResults = document.querySelectorAll('.edible-search-result-item');
            updateSelection(allResults);
        }
    });
}); 