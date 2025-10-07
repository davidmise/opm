<div class="card">
    <div class="card-header">
        <i class="ti ti-filter"></i>&nbsp;<?php echo app_lang('department_filter'); ?>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="department-filter"><?php echo app_lang('department'); ?></label>
            <div id="department-filter-area">
                <?php 
                if (get_setting("module_departments")) {
                    $departments_dropdown = array();
                    $departments_dropdown[""] = "- " . app_lang("department") . " -";
                    
                    if ($this->login_user->is_admin) {
                        $departments_dropdown["all"] = app_lang("all_departments");
                        $departments = $this->Departments_model->get_all_departments()->getResult();
                    } else {
                        // Get user's accessible departments
                        $accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
                        if (!empty($accessible_departments)) {
                            $departments = $this->Departments_model->get_details(array(
                                "where_in" => array("id" => $accessible_departments)
                            ))->getResult();
                        } else {
                            $departments = array();
                        }
                    }
                    
                    foreach ($departments as $department) {
                        $departments_dropdown[$department->id] = $department->title;
                    }
                    
                    echo form_dropdown("department_filter", $departments_dropdown, "", "class='select2 mini' id='department_filter'");
                }
                ?>
            </div>
        </div>

        <?php if (isset($show_department_stats) && $show_department_stats): ?>
        <div class="mt-3">
            <div id="department-stats-area">
                <!-- Department statistics will be loaded here -->
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    
    // Initialize department filter dropdown
    $("#department_filter").select2({
        placeholder: "<?php echo app_lang('select_department'); ?>",
        allowClear: true
    });

    // Handle department filter change
    $("#department_filter").on("change", function() {
        var departmentId = $(this).val();
        
        // Trigger custom event for other components to listen
        $(document).trigger('department-filter-changed', [departmentId]);
        
        // If there's a table to filter, reload it
        if (typeof window.departmentFilterTable !== 'undefined') {
            window.departmentFilterTable.fnReloadAjax();
        }
        
        // Update URL parameters if needed
        if (departmentId) {
            updateUrlParameter('department_id', departmentId);
        } else {
            removeUrlParameter('department_id');
        }

        <?php if (isset($show_department_stats) && $show_department_stats): ?>
        // Load department statistics
        if (departmentId && departmentId !== 'all') {
            loadDepartmentStats(departmentId);
        } else {
            $("#department-stats-area").html('');
        }
        <?php endif; ?>
    });

    <?php if (isset($show_department_stats) && $show_department_stats): ?>
    // Function to load department statistics
    function loadDepartmentStats(departmentId) {
        $.ajax({
            url: '<?php echo_uri("departments/get_dashboard_data") ?>',
            type: 'POST',
            data: {department_id: departmentId},
            success: function(response) {
                if (response.success) {
                    var stats = response.data.statistics;
                    var html = '<div class="row text-center">';
                    html += '<div class="col-4"><h6 class="text-primary">' + stats.member_count + '</h6><small><?php echo app_lang("members"); ?></small></div>';
                    html += '<div class="col-4"><h6 class="text-success">' + stats.active_projects + '</h6><small><?php echo app_lang("projects"); ?></small></div>';
                    html += '<div class="col-4"><h6 class="text-warning">' + stats.active_tasks + '</h6><small><?php echo app_lang("tasks"); ?></small></div>';
                    html += '</div>';
                    
                    $("#department-stats-area").html(html);
                }
            }
        });
    }
    <?php endif; ?>

    // Helper functions for URL parameter management
    function updateUrlParameter(param, value) {
        var url = new URL(window.location);
        url.searchParams.set(param, value);
        window.history.replaceState({}, '', url);
    }

    function removeUrlParameter(param) {
        var url = new URL(window.location);
        url.searchParams.delete(param);
        window.history.replaceState({}, '', url);
    }

    // Initialize filter from URL parameters
    var urlParams = new URLSearchParams(window.location.search);
    var departmentParam = urlParams.get('department_id');
    if (departmentParam) {
        $("#department_filter").val(departmentParam).trigger('change');
    }
});
</script>