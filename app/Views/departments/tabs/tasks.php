<?php 
/**
 * Department Tasks Tab
 */
?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="card-title"><?php echo app_lang('tasks'); ?></h5>
            </div>
            <div class="col-md-6 text-end">
                <?php if ($login_user->user_type == "staff" && ($login_user->is_admin || get_array_value($login_user->permissions, "can_delete_tasks"))) { ?>
                    <div class="btn-group me-2" role="group">
                        <button class="btn btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" id="quick-actions-dropdown">
                            <i data-feather="zap" class="icon-16"></i>
                            <?php echo app_lang('quick_add'); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="deleteSelectedTasks()">
                                <i data-feather="trash-2" class="icon-16"></i> <?php echo app_lang('delete_task'); ?>
                            </a></li>
                        </ul>
                    </div>
                <?php } ?>
                <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "can_create_tasks")) { ?>
                    <?php echo modal_anchor(get_uri("tasks/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-primary", "title" => app_lang('add_task'), "data-post-department_id" => $department_info->id)); ?>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="department-tasks-table" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th></th> <!-- 0: status_color (hidden) -->
                    <th><?php echo app_lang('id'); ?></th> <!-- 1: id -->
                    <th><?php echo app_lang('title'); ?></th> <!-- 2: title (formatted) -->
                    <th></th> <!-- 3: title (raw, hidden) -->
                    <th></th> <!-- 4: labels (hidden) -->
                    <th></th> <!-- 5: priority (hidden) -->
                    <th></th> <!-- 6: points (hidden) -->
                    <th></th> <!-- 7: start_date (raw, hidden) -->
                    <th><?php echo app_lang('start_date'); ?></th> <!-- 8: start_date (formatted) -->
                    <th></th> <!-- 9: deadline (raw, hidden) -->
                    <th><?php echo app_lang('deadline'); ?></th> <!-- 10: deadline (formatted) -->
                    <th></th> <!-- 11: milestone (hidden) -->
                    <th><?php echo app_lang('project'); ?></th> <!-- 12: context_title -->
                    <th><?php echo app_lang('assigned_to'); ?></th> <!-- 13: assigned_to -->
                    <th></th> <!-- 14: collaborators (hidden) -->
                    <th><?php echo app_lang('status'); ?></th> <!-- 15: status -->
                    <th><?php echo app_lang('actions'); ?></th> <!-- 16: actions -->
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var batchDeleteUrl = "<?php echo_uri('tasks/delete_selected_tasks'); ?>";
    
    var selectionHandler = {batchDeleteUrl: batchDeleteUrl, hideButton: true};
    if("<?php echo $login_user->user_type == "client"; ?>"){
        selectionHandler = false;
    }

    $("#department-tasks-table").appTable({
        source: '<?php echo_uri("tasks/list_data/department/" . $department_info->id) ?>',
        selectionHandler: selectionHandler,
        columns: [
            {visible: false, searchable: false}, // 0: status_color
            {title: '<?php echo app_lang("id") ?>', "class": "text-center w50"}, // 1: id
            {title: '<?php echo app_lang("title") ?>'}, // 2: title (formatted)
            {title: '<?php echo app_lang("title") ?>', visible: false, searchable: false}, // 3: title (raw)
            {title: '<?php echo app_lang("label") ?>', visible: false, searchable: false}, // 4: labels
            {title: '<?php echo app_lang("priority") ?>', visible: false, searchable: false}, // 5: priority
            {title: '<?php echo app_lang("points") ?>', visible: false, searchable: false}, // 6: points
            {visible: false, searchable: false}, // 7: start_date (raw)
            {title: '<?php echo app_lang("start_date") ?>', "class": "text-center", "iDataSort": 7}, // 8: start_date (formatted)
            {visible: false, searchable: false}, // 9: deadline (raw)
            {title: '<?php echo app_lang("deadline") ?>', "class": "text-center", "iDataSort": 9}, // 10: deadline (formatted)
            {title: '<?php echo app_lang("milestone") ?>', visible: false}, // 11: milestone
            {title: '<?php echo app_lang("project") ?>'}, // 12: context_title (project/client/etc)
            {title: '<?php echo app_lang("assigned_to") ?>', "class": "text-center w150"}, // 13: assigned_to
            {title: '<?php echo app_lang("collaborators") ?>', visible: false}, // 14: collaborators
            {title: '<?php echo app_lang("status") ?>', "class": "text-center"}, // 15: status
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w150"} // 16: actions
        ],
        printColumns: [1, 2, 8, 10, 12, 13, 15],
        xlsColumns: [1, 2, 8, 10, 12, 13, 15]
    });
    
    // Quick Actions Functions
    window.deleteSelectedTasks = function() {
        var selectedTasks = $("#department-tasks-table").DataTable().rows('.selected').data();
        
        if (selectedTasks.length === 0) {
            appAlert.warning("<?php echo app_lang('please_select_tasks_to_delete'); ?>", {duration: 10000});
            return false;
        }
        
        var taskIds = [];
        for (var i = 0; i < selectedTasks.length; i++) {
            taskIds.push(selectedTasks[i][1]); // Task ID is in column 1
        }
        
        appAlert.confirm("<?php echo app_lang('delete_confirmation_message'); ?>", function() {
            $.ajax({
                url: "<?php echo_uri('tasks/delete_selected_tasks'); ?>",
                type: 'POST',
                data: {ids: taskIds.join('-')},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        appAlert.success(response.message, {duration: 10000});
                        $("#department-tasks-table").DataTable().ajax.reload();
                    } else {
                        appAlert.error(response.message);
                    }
                },
                error: function() {
                    appAlert.error("<?php echo app_lang('something_went_wrong'); ?>");
                }
            });
        });
    };
});
</script>