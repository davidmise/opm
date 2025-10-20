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
/* Tooltip styles for dropdown icons */
.dropdown-menu .dropdown-item {
    position: relative;
}

.dropdown-menu .dropdown-item[title]:hover::after {
    content: attr(title);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    margin-left: 10px;
    padding: 5px 10px;
    background-color: #333;
    color: #fff;
    border-radius: 4px;
    white-space: nowrap;
    font-size: 12px;
    z-index: 1000;
    pointer-events: none;
}

.dropdown-menu .dropdown-item[title]:hover::before {
    content: '';
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    margin-left: 5px;
    border: 5px solid transparent;
    border-right-color: #333;
    z-index: 1000;
    pointer-events: none;
}
</style>

<script type="text/javascript">
$(document).ready(function () {
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
            
            // Initialize feather icons for dropdown
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    });
});
</script>