<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <ul id="department-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#overview" data-tab-name="overview"><?php echo app_lang('overview'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo base_url("index.php/departments/departments_list/"); ?>" data-bs-target="#departments_list" data-tab-name="departments"><?php echo app_lang('departments'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo base_url("index.php/departments/announcements/"); ?>" data-bs-target="#announcements" data-tab-name="announcements"><?php echo app_lang('announcements'); ?></a></li>
            <?php if ($login_user->is_admin || (isset($user_permissions) && $user_permissions['can_manage_department_settings'])): ?>
                <li><a role="presentation" data-bs-toggle="tab" href="<?php echo base_url("index.php/departments/settings/"); ?>" data-bs-target="#settings" data-tab-name="settings"><?php echo app_lang('settings'); ?></a></li>
            <?php endif; ?>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php if ($login_user->is_admin || (isset($user_permissions) && $user_permissions['can_export_department_data'])): ?>
                        <?php echo anchor(base_url('index.php/departments/export'), "<i data-feather='download' class='icon-16'></i> " . app_lang('download'), array("class" => "btn btn-default")); ?>
                    <?php endif; ?>
                    
                    <?php if ($login_user->is_admin || (isset($user_permissions) && $user_permissions['can_create_departments'])): ?>
                        <?php echo modal_anchor(base_url("index.php/departments/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_department'), array("class" => "btn btn-default", "title" => app_lang('add_department'))); ?>
                    <?php endif; ?>
                    
                    <?php if (!$login_user->is_admin && !(isset($user_permissions) && $user_permissions['can_view_all_departments'])): ?>
                        <div class="alert alert-info d-inline-block ms-2 mb-0" style="padding: 5px 10px;">
                            <small><i data-feather="info" class="icon-12"></i> <?php echo app_lang('limited_access_departments'); ?></small>
                        </div>
                    <?php endif; ?>
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
    // Wait for jQuery to be available before executing
    function initDepartmentTabs() {
        if (typeof $ === 'undefined') {
            // jQuery not loaded yet, wait 100ms and try again
            setTimeout(initDepartmentTabs, 100);
            return;
        }
        
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
    }
    
    // Start the initialization
    initDepartmentTabs();
</script>
