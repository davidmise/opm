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
                <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "can_create_tasks")) { ?>
                    <?php echo modal_anchor(get_uri("tasks/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-primary", "title" => app_lang('add_task'))); ?>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="department-tasks-table" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><?php echo app_lang('id'); ?></th>
                    <th><?php echo app_lang('title'); ?></th>
                    <th><?php echo app_lang('project'); ?></th>
                    <th><?php echo app_lang('assigned_to'); ?></th>
                    <th><?php echo app_lang('start_date'); ?></th>
                    <th><?php echo app_lang('deadline'); ?></th>
                    <th><?php echo app_lang('status'); ?></th>
                    <th><?php echo app_lang('actions'); ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $("#department-tasks-table").appTable({
        source: '<?php echo_uri("tasks/list_data") ?>',
        filterDropdown: [
            {name: "department_id", class: "w200", options: <?php echo json_encode(array($department_info->id => $department_info->title)); ?>}
        ],
        columns: [
            {title: '<?php echo app_lang("id") ?>', "class": "text-center w50"},
            {title: '<?php echo app_lang("title") ?>'},
            {title: '<?php echo app_lang("project") ?>'},
            {title: '<?php echo app_lang("assigned_to") ?>', "class": "text-center w150"},
            {title: '<?php echo app_lang("start_date") ?>', "class": "text-center", "iDataSort": 4},
            {title: '<?php echo app_lang("deadline") ?>', "class": "text-center", "iDataSort": 5},
            {title: '<?php echo app_lang("status") ?>', "class": "text-center"},
            {title: '<?php echo app_lang("actions") ?>', "class": "text-center option w150"}
        ],
        printColumns: [0, 1, 2, 3, 4, 5, 6],
        xlsColumns: [0, 1, 2, 3, 4, 5, 6],
        summation: [{column: 1, dataType: 'number'}]
    });
});
</script>