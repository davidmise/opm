<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i data-feather="check-circle" class="icon-16"></i>
            <?php echo app_lang('workflow_tasks'); ?>
        </h5>
        <?php if (get_array_value($permissions, "can_manage_workflow")) { ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">
            <i data-feather="plus" class="icon-16"></i>
            <?php echo app_lang('add_task'); ?>
        </button>
        <?php } ?>
    </div>
    <div class="card-body">
        <!-- Task Filter Tabs -->
        <ul class="nav nav-pills mb-3" id="task-filter-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tasks-tab" data-bs-toggle="pill" data-bs-target="#all-tasks" type="button" role="tab">
                    <?php echo app_lang('all_tasks'); ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="my-tasks-tab" data-bs-toggle="pill" data-bs-target="#my-tasks" type="button" role="tab">
                    <?php echo app_lang('my_tasks'); ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tasks-tab" data-bs-toggle="pill" data-bs-target="#pending-tasks" type="button" role="tab">
                    <?php echo app_lang('pending_tasks'); ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="overdue-tasks-tab" data-bs-toggle="pill" data-bs-target="#overdue-tasks" type="button" role="tab">
                    <?php echo app_lang('overdue_tasks'); ?>
                </button>
            </li>
        </ul>

        <div class="table-responsive">
            <table id="tasks-table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo app_lang('task_title'); ?></th>
                        <th><?php echo app_lang('shipment'); ?></th>
                        <th><?php echo app_lang('phase'); ?></th>
                        <th><?php echo app_lang('assigned_to'); ?></th>
                        <th><?php echo app_lang('assigned_by'); ?></th>
                        <th><?php echo app_lang('priority'); ?></th>
                        <th><?php echo app_lang('status'); ?></th>
                        <th><?php echo app_lang('due_date'); ?></th>
                        <th><?php echo app_lang('created_date'); ?></th>
                        <th class="w100"><?php echo app_lang('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel"><?php echo app_lang('add_task'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="task-form" action="<?php echo get_uri("workflow/save_task"); ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="title" class="form-label"><?php echo app_lang('task_title'); ?> *</label>
                                <input type="text" class="form-control" name="title" id="title" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="shipment_id" class="form-label"><?php echo app_lang('shipment'); ?> *</label>
                                <select name="shipment_id" id="shipment_id" class="form-select select2" required>
                                    <option value=""><?php echo app_lang('select_shipment'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label"><?php echo app_lang('assigned_to'); ?> *</label>
                                <select name="assigned_to" id="assigned_to" class="form-select select2" required>
                                    <option value=""><?php echo app_lang('select_team_member'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="priority" class="form-label"><?php echo app_lang('priority'); ?></label>
                                <select name="priority" id="priority" class="form-select">
                                    <option value="low"><?php echo app_lang('low'); ?></option>
                                    <option value="medium" selected><?php echo app_lang('medium'); ?></option>
                                    <option value="high"><?php echo app_lang('high'); ?></option>
                                    <option value="urgent"><?php echo app_lang('urgent'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="due_date" class="form-label"><?php echo app_lang('due_date'); ?></label>
                                <input type="date" class="form-control" name="due_date" id="due_date">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estimated_hours" class="form-label"><?php echo app_lang('estimated_hours'); ?></label>
                                <input type="number" step="0.5" class="form-control" name="estimated_hours" id="estimated_hours">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label"><?php echo app_lang('description'); ?></label>
                        <textarea class="form-control" name="description" id="description" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var currentFilter = 'all';
    
    // Initialize DataTable
    function initTasksTable(filter) {
        var source = '<?php echo_uri("workflow/list_tasks") ?>';
        if (filter) {
            source += '?filter=' + filter;
        }
        
        $("#tasks-table").appTable({
            source: source,
            order: [[7, "desc"]],
            columns: [
                {title: '<?php echo app_lang("task_title") ?>'},
                {title: '<?php echo app_lang("shipment") ?>'},
                {title: '<?php echo app_lang("phase") ?>'},
                {title: '<?php echo app_lang("assigned_to") ?>'},
                {title: '<?php echo app_lang("assigned_by") ?>'},
                {title: '<?php echo app_lang("priority") ?>'},
                {title: '<?php echo app_lang("status") ?>'},
                {title: '<?php echo app_lang("due_date") ?>', "iDataSort": 7},
                {title: '<?php echo app_lang("created_date") ?>', "iDataSort": 8},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
            xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
        });
    }

    // Initialize with all tasks
    initTasksTable('all');

    // Handle tab changes
    $('#task-filter-tabs button').on('click', function () {
        var filter = $(this).attr('id').replace('-tab', '').replace('-', '_');
        currentFilter = filter;
        $("#tasks-table").DataTable().destroy();
        initTasksTable(filter);
    });

    // Initialize select2 dropdowns
    $("#shipment_id").select2({
        data: <?php echo json_encode([]) ?>, // Load shipments via AJAX
        placeholder: "<?php echo app_lang('select_shipment'); ?>",
        allowClear: true
    });

    $("#assigned_to").select2({
        data: <?php echo json_encode([]) ?>, // Load team members via AJAX
        placeholder: "<?php echo app_lang('select_team_member'); ?>",
        allowClear: true
    });

    // Handle form submission
    $("#task-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#taskModal").modal('hide');
                $("#tasks-table").appTable({newData: result.data, dataId: result.id});
                appAlert.success(result.message);
            } else {
                appAlert.error(result.message);
            }
        }
    });

    // Auto-select user for "My Tasks"
    $("#my-tasks-tab").on('click', function() {
        // Set filter for current user tasks
        var userId = <?php echo get_array_value($GLOBALS, 'user_id', 0); ?>;
    });
});
</script>