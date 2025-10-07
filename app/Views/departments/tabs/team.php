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
        xlsColumns: [0, 1, 2, 3]
    });
});
</script>