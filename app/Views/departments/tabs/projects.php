<?php 
/**
 * Department Projects Tab
 */
?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h5 class="card-title"><?php echo app_lang('projects'); ?></h5>
            </div>
            <div class="col-md-6 text-end">
                <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "can_create_projects")) { ?>
                    <?php echo modal_anchor(get_uri("projects/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_project'), array("class" => "btn btn-primary", "title" => app_lang('add_project'), "data-post-department_id" => $department_info->id)); ?>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table id="department-projects-table" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><?php echo app_lang('id'); ?></th>
                    <th><?php echo app_lang('title'); ?></th>
                    <th><?php echo app_lang('client'); ?></th>
                    <th class="hide"><?php echo app_lang('department'); ?></th>
                    <th class="hide"><?php echo app_lang('price'); ?></th>
                    <th class="hide"><?php echo app_lang('start_date'); ?></th>
                    <th><?php echo app_lang('start_date'); ?></th>
                    <th class="hide"><?php echo app_lang('deadline'); ?></th>
                    <th><?php echo app_lang('deadline'); ?></th>
                    <th><?php echo app_lang('progress'); ?></th>
                    <th><?php echo app_lang('status'); ?></th>
                    <th><i data-feather="menu" class="icon-16"></i></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    $("#department-projects-table").appTable({
        source: '<?php echo_uri("projects/list_data") ?>',
        filterParams: {department_id: "<?php echo $department_info->id; ?>"},
        columns: [
            {title: '<?php echo app_lang("id") ?>', "class": "text-center w50"},
            {title: '<?php echo app_lang("title") ?>'},
            {title: '<?php echo app_lang("client") ?>'},
            {title: '<?php echo app_lang("department") ?>', visible: false, searchable: false},
            {title: '<?php echo app_lang("price") ?>', visible: false, searchable: false},
            {title: '<?php echo app_lang("start_date") ?>', visible: false, searchable: false},
            {title: '<?php echo app_lang("start_date") ?>', "class": "text-center w100", "iDataSort": 5},
            {title: '<?php echo app_lang("deadline") ?>', visible: false, searchable: false},
            {title: '<?php echo app_lang("deadline") ?>', "class": "text-center w100", "iDataSort": 7},
            {title: '<?php echo app_lang("progress") ?>', "class": "text-center w100"},
            {title: '<?php echo app_lang("status") ?>', "class": "text-center w100"},
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
        ],
        columnDefs: [
            {targets: [3, 4], visible: false}
        ],
        printColumns: [0, 1, 2, 6, 8, 9, 10],
        xlsColumns: [0, 1, 2, 6, 8, 9, 10]
    });
    
    // Initialize feather icons
    setTimeout(function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }, 100);
});
</script>