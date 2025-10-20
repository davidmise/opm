<?php

// add your custom header here.

// Global accessibility fix for Bootstrap modals - prevent aria-hidden on focused elements
?>
<script>
(function() {
    'use strict';
    
    // Fix aria-hidden accessibility warnings for all Bootstrap modals
    // This prevents the "aria-hidden on a focused element" console warning
    document.addEventListener('DOMContentLoaded', function() {
        // Get all modal elements
        var modals = document.querySelectorAll('.modal');
        
        modals.forEach(function(modal) {
            // Remove initial aria-hidden if present
            if (modal.hasAttribute('aria-hidden')) {
                modal.removeAttribute('aria-hidden');
            }
            
            // Handle modal show event
            modal.addEventListener('show.bs.modal', function() {
                modal.removeAttribute('aria-hidden');
            });
            
            // Handle modal shown event (fully visible)
            modal.addEventListener('shown.bs.modal', function() {
                modal.removeAttribute('aria-hidden');
            });
            
            // Handle modal hide event
            modal.addEventListener('hide.bs.modal', function() {
                // Remove focus from any focused element inside the modal before hiding
                // This prevents the aria-hidden warning about focused descendant
                var focusedElement = modal.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }
            });
            
            // Handle modal hidden event (fully hidden)
            modal.addEventListener('hidden.bs.modal', function() {
                // Only set aria-hidden after modal is completely hidden
                modal.setAttribute('aria-hidden', 'true');
            });
        });
    });
})();
</script>
<?php