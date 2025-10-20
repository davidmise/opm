<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <ul id="department-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#overview" data-tab-name="overview"><?php echo app_lang('overview'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo base_url("index.php/departments/departments_list/"); ?>" data-bs-target="#departments_list" data-tab-name="departments"><?php echo app_lang('departments'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo base_url("index.php/departments/announcements/"); ?>" data-bs-target="#announcements" data-tab-name="announcements"><?php echo app_lang('announcements'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo base_url("index.php/departments/settings/"); ?>" data-bs-target="#settings" data-tab-name="settings"><?php echo app_lang('settings'); ?></a></li>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php echo anchor(base_url('index.php/departments/export'), "<i data-feather='download' class='icon-16'></i> " . app_lang('download'), array("class" => "btn btn-default")); ?>
                    <?php echo modal_anchor(base_url("index.php/departments/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_department'), array("class" => "btn btn-default", "title" => app_lang('add_department'))); ?>
                </div>
            </div>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="overview">
                <?php echo view("departments/overview/index"); ?>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="departments_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="announcements"></div>
            <div role="tabpanel" class="tab-pane fade" id="settings"></div>
        </div>
    </div>
</div>

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
