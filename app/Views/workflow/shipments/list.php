<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i data-feather="package" class="icon-16"></i>
            <?php echo app_lang('shipments'); ?>
        </h5>
        <div class="d-flex gap-2">
            <?php if (get_array_value($permissions, "can_manage_workflow")) { ?>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i data-feather="zap" class="icon-16"></i>
                    <?php echo app_lang('quick_actions'); ?>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="bulkAssignDepartment()">
                        <i data-feather="users" class="icon-16"></i> <?php echo app_lang('assign_to_department'); ?>
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="updateStatusBulk()">
                        <i data-feather="edit-3" class="icon-16"></i> <?php echo app_lang('update_status'); ?>
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="createTaskBulk()">
                        <i data-feather="plus-circle" class="icon-16"></i> <?php echo app_lang('create_tasks'); ?>
                    </a></li>
                </ul>
            </div>
            <?php } ?>
            <?php if (get_array_value($permissions, "can_create_shipments")) { ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shipmentModal">
                <i data-feather="plus" class="icon-16"></i>
                <?php echo app_lang('add_shipment'); ?>
            </button>
            <?php } ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="shipments-table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th class="w50">
                            <input type="checkbox" id="select-all-shipments" class="form-check-input">
                        </th>
                        <th><?php echo app_lang('shipment_number'); ?></th>
                        <th><?php echo app_lang('client'); ?></th>
                        <th><?php echo app_lang('cargo_type'); ?></th>
                        <th><?php echo app_lang('weight'); ?></th>
                        <th><?php echo app_lang('status'); ?></th>
                        <th><?php echo app_lang('priority'); ?></th>
                        <th><?php echo app_lang('current_phase'); ?></th>
                        <th><?php echo app_lang('origin'); ?></th>
                        <th><?php echo app_lang('destination'); ?></th>
                        <th><?php echo app_lang('created_date'); ?></th>
                        <th class="w100"></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Shipment Modal -->
<div class="modal fade" id="shipmentModal" tabindex="-1" aria-labelledby="shipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shipmentModalLabel"><?php echo app_lang('add_shipment'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shipment-form" action="<?php echo get_uri("workflow/save_shipment"); ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_id" class="form-label"><?php echo app_lang('client'); ?> *</label>
                                <select name="client_id" id="client_id" class="form-select select2" required>
                                    <option value=""><?php echo app_lang('select_client'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cargo_type" class="form-label"><?php echo app_lang('cargo_type'); ?> *</label>
                                <input type="text" class="form-control" name="cargo_type" id="cargo_type" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cargo_weight" class="form-label"><?php echo app_lang('cargo_weight'); ?> (<?php echo app_lang('tons'); ?>)</label>
                                <input type="number" step="0.01" class="form-control" name="cargo_weight" id="cargo_weight">
                            </div>
                        </div>
                        <div class="col-md-6">
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="origin_port" class="form-label"><?php echo app_lang('origin_port'); ?> *</label>
                                <input type="text" class="form-control" name="origin_port" id="origin_port" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="destination_port" class="form-label"><?php echo app_lang('destination_port'); ?> *</label>
                                <input type="text" class="form-control" name="destination_port" id="destination_port" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label"><?php echo app_lang('description'); ?></label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
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

<!-- Quick Action Modals -->

<!-- Assign to Department Modal -->
<div class="modal fade" id="assignDepartmentModal" tabindex="-1" aria-labelledby="assignDepartmentLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignDepartmentLabel"><?php echo app_lang('assign_to_department'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assign-department-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="department_select" class="form-label"><?php echo app_lang('select_department'); ?></label>
                        <select name="department_id" id="department_select" class="form-select" required>
                            <option value=""><?php echo app_lang('select_department'); ?></option>
                            <!-- Departments will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i data-feather="info" class="icon-16"></i>
                        <?php echo app_lang('selected_shipments_will_be_assigned'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo app_lang('assign'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTaskLabel"><?php echo app_lang('create_task'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="create-task-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_title" class="form-label"><?php echo app_lang('task_title'); ?> *</label>
                                <input type="text" class="form-control" name="title" id="task_title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_assigned_to" class="form-label"><?php echo app_lang('assign_to'); ?> *</label>
                                <select name="assigned_to" id="task_assigned_to" class="form-select" required>
                                    <option value=""><?php echo app_lang('select_user'); ?></option>
                                    <!-- Users will be loaded via AJAX -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_priority" class="form-label"><?php echo app_lang('priority'); ?></label>
                                <select name="priority_id" id="task_priority" class="form-select">
                                    <option value="1"><?php echo app_lang('low'); ?></option>
                                    <option value="2" selected><?php echo app_lang('medium'); ?></option>
                                    <option value="3"><?php echo app_lang('high'); ?></option>
                                    <option value="4"><?php echo app_lang('urgent'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="task_deadline" class="form-label"><?php echo app_lang('deadline'); ?></label>
                                <input type="datetime-local" class="form-control" name="deadline" id="task_deadline">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="task_description" class="form-label"><?php echo app_lang('description'); ?></label>
                        <textarea class="form-control" name="description" id="task_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo app_lang('create_task'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize DataTable
    $("#shipments-table").appTable({
        source: '<?php echo_uri("workflow/list_shipments") ?>',
        order: [[10, "desc"]], // Created date column
        columns: [
            {title: '', "class": "text-center option w50"},
            {title: '<?php echo app_lang("shipment_number") ?>'},
            {title: '<?php echo app_lang("client") ?>'},
            {title: '<?php echo app_lang("cargo_type") ?>'},
            {title: '<?php echo app_lang("weight") ?>'},
            {title: '<?php echo app_lang("status") ?>'},
            {title: '<?php echo app_lang("priority") ?>'},
            {title: '<?php echo app_lang("current_phase") ?>'},
            {title: '<?php echo app_lang("origin") ?>'},
            {title: '<?php echo app_lang("destination") ?>'},
            {title: '<?php echo app_lang("created_date") ?>', "iDataSort": 10},
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
        ],
        printColumns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        xlsColumns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        onRelaodCallback: function() {
            // Replace feather icons safely
            if (typeof feather !== 'undefined' && feather.replace) {
                feather.replace();
            } else if (typeof safeFeatherReplace === 'function') {
                safeFeatherReplace();
            }
            console.log('DataTable reloaded');
        },
        onInitComplete: function() {
            console.log('DataTable initialized successfully');
        }
    });

    // Initialize select2 for client dropdown
    $("#client_id").select2({
        data: <?php echo json_encode([]) ?>, // Load clients via AJAX
        placeholder: "<?php echo app_lang('select_client'); ?>",
        allowClear: true
    });

    // Handle form submission
    $("#shipment-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#shipmentModal").modal('hide');
                $("#shipments-table").appTable({newData: result.data, dataId: result.id});
                appAlert.success(result.message);
            } else {
                appAlert.error(result.message);
            }
        }
    });

    // Load departments for assignment
    loadDepartments();
    loadTeamMembers();

    // Quick Actions Functions
    function loadDepartments() {
        $.ajax({
            url: '<?php echo_uri("departments/get_departments_dropdown") ?>',
            type: 'POST',
            success: function(response) {
                var departments = JSON.parse(response);
                var options = '<option value=""><?php echo app_lang("select_department"); ?></option>';
                departments.forEach(function(dept) {
                    options += '<option value="' + dept.id + '">' + dept.title + '</option>';
                });
                $('#department_select').html(options);
            }
        });
    }

    function loadTeamMembers() {
        $.ajax({
            url: '<?php echo_uri("team_members/get_team_members_dropdown") ?>',
            type: 'POST',
            success: function(response) {
                var members = JSON.parse(response);
                var options = '<option value=""><?php echo app_lang("select_user"); ?></option>';
                members.forEach(function(member) {
                    options += '<option value="' + member.id + '">' + member.first_name + ' ' + member.last_name + '</option>';
                });
                $('#task_assigned_to').html(options);
            }
        });
    }

    // Quick action handlers
    $("#assign-department-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#assignDepartmentModal").modal('hide');
                $("#shipments-table").appTable({reload: true});
                appAlert.success(result.message);
            }
        }
    });

    $("#create-task-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#createTaskModal").modal('hide');
                $("#shipments-table").appTable({reload: true});
                appAlert.success(result.message);
            }
        }
    });
});

// Global quick action functions
function bulkAssignDepartment() {
    var selectedRows = getSelectedShipments();
    if (selectedRows.length === 0) {
        appAlert.warning('<?php echo app_lang("please_select_shipments"); ?>');
        return;
    }
    $("#assignDepartmentModal").modal('show');
}

function updateStatusBulk() {
    var selectedRows = getSelectedShipments();
    if (selectedRows.length === 0) {
        appAlert.warning('<?php echo app_lang("please_select_shipments"); ?>');
        return;
    }
    // Implement status update modal
    appAlert.info('Status update feature coming soon...');
}

function createTaskBulk() {
    var selectedRows = getSelectedShipments();
    if (selectedRows.length === 0) {
        appAlert.warning('<?php echo app_lang("please_select_shipments"); ?>');
        return;
    }
    $("#createTaskModal").modal('show');
}

function getSelectedShipments() {
    var selectedRows = [];
    $('#shipments-table .shipment-checkbox:checked').each(function() {
        var id = $(this).data('shipment-id');
        if (id) {
            selectedRows.push(id);
        }
    });
    return selectedRows;
}

// Select all functionality
$(document).ready(function() {
    $('#select-all-shipments').on('change', function() {
        $('.shipment-checkbox').prop('checked', this.checked);
    });
    
    $(document).on('change', '.shipment-checkbox', function() {
        var totalCheckboxes = $('.shipment-checkbox').length;
        var checkedCheckboxes = $('.shipment-checkbox:checked').length;
        $('#select-all-shipments').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
});

// Quick assign task to shipment
function quickAssignTask(shipmentId, element) {
    var taskTitle = prompt('<?php echo app_lang("enter_task_title"); ?>:');
    if (!taskTitle) return;
    
    var userId = prompt('<?php echo app_lang("enter_user_id_or_name"); ?>:');
    if (!userId) return;

    $.ajax({
        url: '<?php echo_uri("workflow/quick_assign_task") ?>',
        type: 'POST',
        data: {
            shipment_id: shipmentId,
            user_id: userId,
            task_title: taskTitle
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.success) {
                appAlert.success(result.message);
                $(element).closest('tr').find('.task-indicator').show();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

// Edit shipment function
function editShipment(shipmentId) {
    $.ajax({
        url: '<?php echo_uri("workflow/get_shipment_info") ?>',
        type: 'POST',
        data: {id: shipmentId},
        success: function(response) {
            var result = JSON.parse(response);
            if (result.success) {
                // Populate the modal form with existing data
                var data = result.data;
                $('#shipment-id').val(data.id);
                $('#client-id').val(data.client_id);
                $('#cargo-type').val(data.cargo_type);
                $('#cargo-weight').val(data.cargo_weight);
                $('#cargo-value').val(data.cargo_value);
                $('#origin-port').val(data.origin_port);
                $('#destination-port').val(data.destination_port);
                $('#final-destination').val(data.final_destination);
                $('#estimated-arrival').val(data.estimated_arrival);
                
                $('#shipmentModal').modal('show');
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

// Delete shipment function
function deleteShipment(shipmentId) {
    if (confirm('<?php echo app_lang("delete_confirmation_message"); ?>')) {
        $.ajax({
            url: '<?php echo_uri("workflow/delete_shipment") ?>',
            type: 'POST',
            data: {id: shipmentId},
            success: function(response) {
                var result = JSON.parse(response);
                if (result.success) {
                    appAlert.success(result.message);
                    $('#shipments-table').DataTable().ajax.reload();
                } else {
                    appAlert.error(result.message);
                }
            }
        });
    }
}

// View shipment details function
function viewShipmentDetails(shipmentId) {
    window.location.href = '<?php echo_uri("workflow/shipment_details/") ?>' + shipmentId;
}
</script>