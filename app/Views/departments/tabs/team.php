<?php 
/**
 * Department Team Members Tab
 */
?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="card-title"><?php echo app_lang('team_members'); ?></h5>
            </div>
            <div class="col-md-6 text-end">
                <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "can_manage_departments")) { ?>
                    <?php echo modal_anchor(get_uri("departments/add_user_modal/" . $department_info->id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_member'), array("class" => "btn btn-primary", "title" => app_lang('add_member_to_department'))); ?>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="department-team-table" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><?php echo app_lang('member'); ?></th>
                    <th><?php echo app_lang('role'); ?></th>
                    <th><?php echo app_lang('status'); ?></th>
                    <th><?php echo app_lang('email'); ?></th>
                    <th><?php echo app_lang('actions'); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<style>
/* Basic team table styling */
#department-team-table .d-flex {
    align-items: center;
    justify-content: center;
}
</style>

<script type="text/javascript">
// Wait for jQuery to be available before executing
function initDepartmentTeamScript() {
    if (typeof $ === 'undefined') {
        // jQuery not loaded yet, wait 100ms and try again
        setTimeout(initDepartmentTeamScript, 100);
        return;
    }
    
    $(document).ready(function () {
        // Destroy any existing DataTable instance first
        if ($.fn.DataTable.isDataTable('#department-team-table')) {
            $('#department-team-table').DataTable().destroy();
        }
    
    $("#department-team-table").appTable({
        source: '<?php echo_uri("departments/department_team_list_data/" . $department_info->id) ?>',
        columns: [
            {title: '<?php echo app_lang("member") ?>'},
            {title: '<?php echo app_lang("role") ?>'},
            {title: '<?php echo app_lang("status") ?>'},
            {title: '<?php echo app_lang("email") ?>'},
            {title: '<?php echo app_lang("actions") ?>', "class": "text-center option w100"}
        ],
        printColumns: [0, 1, 2, 3],
        xlsColumns: [0, 1, 2, 3],
        onInitComplete: function() {
            // Handle set as primary
            $('body').on('click', '.set-as-primary', function(e) {
                e.preventDefault();
                var userId = $(this).data('user-id');
                var departmentId = $(this).data('department-id');
                var userName = $(this).data('user-name');
                
                console.log('Set as primary clicked:', {userId: userId, departmentId: departmentId, userName: userName});
                
                if (confirm('<?php echo app_lang("set_as_primary_confirmation"); ?>'.replace('{member}', userName))) {
                    $.ajax({
                        url: '<?php echo_uri("departments/set_primary_department") ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            user_id: userId,
                            department_id: departmentId
                        },
                        success: function(result) {
                            console.log('Set primary success:', result);
                            if (result.success) {
                                appAlert.success(result.message, {duration: 10000});
                                // Reload department team table
                                $("#department-team-table").appTable({reload: true});
                                // Reload main team members table if it exists (on team members page)
                                if ($("#team_member-table").length) {
                                    $("#team_member-table").appTable({reload: true});
                                }
                            } else {
                                appAlert.error(result.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Set primary error:', {xhr: xhr, status: status, error: error});
                            appAlert.error('Error: ' + error);
                        }
                    });
                }
            });
            
            // Handle remove user from department
            $('body').on('click', '.remove-user-from-department', function(e) {
                e.preventDefault();
                var userId = $(this).data('user-id');
                var departmentId = $(this).data('department-id');
                var userName = $(this).data('user-name');
                
                if (confirm('<?php echo app_lang("remove_member_from_department_confirmation"); ?>'.replace('{member}', userName))) {
                    $.ajax({
                        url: '<?php echo_uri("departments/remove_user_from_department") ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            user_id: userId,
                            department_id: departmentId
                        },
                        success: function(result) {
                            if (result.success) {
                                appAlert.success(result.message, {duration: 10000});
                                // Reload department team table
                                $("#department-team-table").appTable({reload: true});
                                // Reload main team members table if it exists (on team members page)
                                if ($("#team_member-table").length) {
                                    $("#team_member-table").appTable({reload: true});
                                }
                            } else {
                                appAlert.error(result.message);
                            }
                        }
                    });
                }
            });
            
            // Initialize feather icons after table loads
            if (typeof feather !== 'undefined') {
                setTimeout(function() {
                    feather.replace();
                }, 100);
            }
        }
    });
}

// Start the initialization
initDepartmentTeamScript();
</script>