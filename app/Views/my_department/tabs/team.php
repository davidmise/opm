<script type="text/javascript">
    $(document).ready(function () {
        // Tab persistence functionality
        var currentTabKey = 'departments_active_tab';
        var currentTab = localStorage.getItem(currentTabKey);
        
        // Store tab when clicked and update URL hash
        $('#department-tabs a[data-bs-toggle="tab"]').on('click', function() {
            var tabTarget = $(this).attr('data-bs-target');
            var tabName = $(this).attr('data-tab-name');
            localStorage.setItem(currentTabKey, tabTarget);
            
            // Update URL hash without triggering scroll
            if (tabName && tabName !== 'overview') {
                history.replaceState(null, null, '#' + tabName);
            } else {
                history.replaceState(null, null, window.location.pathname);
            }
        });
        
        // Check URL hash first, then localStorage, then default
        var hash = window.location.hash.substring(1); // Remove #
        var targetTab = null;
        
        if (hash) {
            // Map hash names to tab targets
            var hashToTarget = {
                'overview': '#overview',
                'departments': '#departments_list', 
                'announcements': '#announcements',
                'settings': '#settings'
            };
            targetTab = hashToTarget[hash];
        }
        
        // Fallback to localStorage if no valid hash
        if (!targetTab && currentTab && $('#department-tabs a[data-bs-target="' + currentTab + '"]').length) {
            targetTab = currentTab;
        }
        
        // Default to overview if nothing else
        if (!targetTab) {
            targetTab = '#overview';
        }
        
        // Activate the determined tab
        setTimeout(function(){
            $('#department-tabs a[data-bs-target="' + targetTab + '"]').trigger('click');
        }, 210);
        
        // Handle browser back/forward navigation
        $(window).on('popstate', function(e) {
            var hash = window.location.hash.substring(1);
            if (hash) {
                var hashToTarget = {
                    'overview': '#overview',
                    'departments': '#departments_list',
                    'announcements': '#announcements', 
                    'settings': '#settings'
                };
                var targetTab = hashToTarget[hash];
                if (targetTab) {
                    $('#department-tabs a[data-bs-target="' + targetTab + '"]').trigger('click');
                }
            }
        });
    });
</script>
