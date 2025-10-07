<?php
if (!function_exists("make_department_tabs_data")) {

    function make_department_tabs_data($default_department_tabs = array())
    {
        $department_tab_order = get_setting("department_tab_order");
        $custom_department_tabs = array();

        if ($department_tab_order) {
            $custom_department_tabs = explode(',', $department_tab_order);
        }

        $final_department_tabs = array();
        if ($custom_department_tabs) {
            foreach ($custom_department_tabs as $custom_department_tab) {
                if (array_key_exists($custom_department_tab, $default_department_tabs)) {
                    $final_department_tabs[$custom_department_tab] = get_array_value($default_department_tabs, $custom_department_tab);
                }
            }
        }

        $final_department_tabs = $final_department_tabs ? $final_department_tabs : $default_department_tabs;

        foreach ($final_department_tabs as $key => $value) {
            echo "<li class='nav-item' role='presentation'><a class='nav-link' data-bs-toggle='tab' href='" . get_uri($value) . "' data-bs-target='#department-$key-section'>" . app_lang($key) . "</a></li>";
        }
    }
}
?>

<div class="page-content department-details-view clearfix">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="department-title-section">
                    <div class="page-title no-bg clearfix mb5 no-border">
                        <div>
                            <h1 class="pl0">
                                <span style="background-color: <?php echo $department_info->color ? $department_info->color : '#6c757d'; ?>; color: white; padding: 8px 12px; border-radius: 4px; margin-right: 10px;">
                                    <i data-feather="grid" class="icon-16"></i>
                                </span>
                                <?php echo $department_info->title; ?>
                                
                                <?php if ($department_info->status !== 'active'): ?>
                                    <span class="badge bg-secondary ms-2">
                                        <i data-feather="pause-circle" class="icon-12"></i> <?php echo app_lang('inactive'); ?>
                                    </span>
                                <?php endif; ?>
                            </h1>
                            
                            <?php if ($department_info->description): ?>
                                <p class="text-muted mt-2"><?php echo nl2br($department_info->description); ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="department-title-button-group-section">
                            <div class="title-button-group mr0" id="department-action-box">
                                <?php if ($can_edit_departments): ?>
                                    <?php echo modal_anchor(get_uri("departments/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_department'), array("class" => "btn btn-default", "title" => app_lang('edit_department'), "data-post-id" => $department_info->id)); ?>
                                <?php endif; ?>
                                
                                <?php if ($can_manage_department_members): ?>
                                    <?php echo modal_anchor(get_uri("departments/manage_members/" . $department_info->id), "<i data-feather='users' class='icon-16'></i> " . app_lang('manage_department_members'), array("class" => "btn btn-default", "title" => app_lang('manage_department_members'))); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <ul id="department-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs rounded classic mb20 scrollable-tabs" role="tablist">
                        <?php
                        // Default tab order for departments
                        $department_tabs = array(
                            "overview" => "departments/overview/" . $department_info->id,
                            "members" => "departments/members/" . $department_info->id,
                            "projects" => "departments/projects/" . $department_info->id,
                            "tasks" => "departments/tasks/" . $department_info->id,
                        );

                        if ($show_performance_info) {
                            $department_tabs["performance"] = "departments/performance/" . $department_info->id;
                        }

                        if ($show_analytics_info) {
                            $department_tabs["analytics"] = "departments/analytics/" . $department_info->id;
                        }

                        if ($show_notes_info) {
                            $department_tabs["notes"] = "departments/notes/" . $department_info->id;
                        }

                        if ($show_files_info) {
                            $department_tabs["files"] = "departments/files/" . $department_info->id;
                        }

                        $department_tabs_of_hook = array();
                        $department_tabs_of_hook = app_hooks()->apply_filters('app_filter_department_details_tab', $department_tabs_of_hook, $department_info->id);
                        $department_tabs_of_hook = is_array($department_tabs_of_hook) ? $department_tabs_of_hook : array();
                        $department_tabs = array_merge($department_tabs, $department_tabs_of_hook);

                        make_department_tabs_data($department_tabs);
                        ?>
                    </ul>
                </div>
                
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active" id="department-overview-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-members-section"></div>
                    <div role="tabpanel" class="tab-pane fade grid-button" id="department-projects-section"></div>
                    <div role="tabpanel" class="tab-pane fade grid-button" id="department-tasks-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-performance-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-analytics-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-notes-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-files-section"></div>

                    <?php
                    foreach ($department_tabs_of_hook as $key => $value) {
                    ?>
                        <div role="tabpanel" class="tab-pane fade" id="department-<?php echo $key; ?>-section"></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    RELOAD_DEPARTMENT_VIEW_AFTER_UPDATE = true;

    $(document).ready(function() {
        setTimeout(function() {
            var tab = "<?php echo $tab; ?>";
            if (tab === "members") {
                $("[data-bs-target='#department-members-section']").trigger("click");
            } else if (tab === "projects") {
                $("[data-bs-target='#department-projects-section']").trigger("click");
            } else if (tab === "tasks") {
                $("[data-bs-target='#department-tasks-section']").trigger("click");
            } else if (tab === "performance") {
                $("[data-bs-target='#department-performance-section']").trigger("click");
            } else if (tab === "analytics") {
                $("[data-bs-target='#department-analytics-section']").trigger("click");
            } else if (tab === "notes") {
                $("[data-bs-target='#department-notes-section']").trigger("click");
            } else if (tab === "files") {
                $("[data-bs-target='#department-files-section']").trigger("click");
            }
        }, 210);
    });
</script>