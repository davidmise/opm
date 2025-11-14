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
            <table id="shipments-table" class="table table-striped" width="100%">
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
                        <th><?php echo app_lang('current_phase'); ?></th>
                        <th><?php echo app_lang('origin'); ?></th>
                        <th><?php echo app_lang('destination'); ?></th>
                        <th><?php echo app_lang('created_date'); ?></th>
                        <th class="w100"><?php echo app_lang('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Get shipments data directly in the view for now
                    $db = \Config\Database::connect();
                    $builder = $db->table('opm_workflow_shipments s');
                    $builder->select('s.*, c.company_name');
                    $builder->join('opm_clients c', 's.client_id = c.id', 'left');
                    $builder->orderBy('s.created_at', 'DESC');
                    $builder->limit(50); // Limit for performance
                    
                    $shipments = $builder->get()->getResult();
                    
                    if (!empty($shipments)) {
                        foreach ($shipments as $shipment) {
                            $status_class = '';
                            switch($shipment->status) {
                                case 'active': $status_class = 'warning'; break;
                                case 'completed': $status_class = 'success'; break;
                                case 'cancelled': $status_class = 'danger'; break;
                                default: $status_class = 'secondary';
                            }
                            
                            $phase_display = ucwords(str_replace('_', ' ', $shipment->current_phase ?? 'N/A'));
                            ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input shipment-checkbox" data-shipment-id="<?php echo $shipment->id; ?>">
                                </td>
                                <td>
                                    <a href="<?php echo get_uri('workflow/shipment_details/' . $shipment->id); ?>" class="text-primary">
                                        <?php echo $shipment->shipment_number; ?>
                                    </a>
                                </td>
                                <td><?php echo $shipment->company_name ?: 'Unknown Client'; ?></td>
                                <td><?php echo $shipment->cargo_type; ?></td>
                                <td><?php echo $shipment->cargo_weight ? $shipment->cargo_weight . ' tons' : '-'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo ucfirst($shipment->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo $phase_display; ?>
                                    </span>
                                </td>
                                <td><?php echo $shipment->origin_port; ?></td>
                                <td><?php echo $shipment->destination_port; ?></td>
                                <td><?php echo format_to_date($shipment->created_at, false); ?></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i data-feather="more-horizontal" class="icon-16"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="<?php echo get_uri('workflow/shipment_details/' . $shipment->id); ?>">
                                                    <i data-feather="eye" class="icon-16 me-2"></i><?php echo app_lang('view_details'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="editShipment(<?php echo $shipment->id; ?>)">
                                                    <i data-feather="edit" class="icon-16 me-2"></i><?php echo app_lang('edit'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="quickAssignTask(<?php echo $shipment->id; ?>, this)">
                                                    <i data-feather="plus-circle" class="icon-16 me-2"></i><?php echo app_lang('assign_task'); ?>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteShipment(<?php echo $shipment->id; ?>)">
                                                    <i data-feather="trash-2" class="icon-16 me-2"></i><?php echo app_lang('delete'); ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                <?php echo app_lang('no_data_available'); ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
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
            <form id="shipment-form" action="<?php echo get_uri("workflow/save_shipment"); ?>" method="post" onsubmit="return false;">
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
$(document).ready(function() {
    // Initialize feather icons when tab loads
    setTimeout(function() {
        if (typeof feather !== 'undefined' && feather.replace) {
            feather.replace();
        }
    }, 100);
    
    // Initialize basic table functionality
    initShipmentsTable();
    
    // Initialize select2 for client dropdown
    $("#client_id").select2({
        placeholder: "<?php echo app_lang('select_client'); ?>",
        allowClear: true
    });
    
    // Prevent any form submission that might bypass AJAX
    $(document).on('submit', 'form', function(e) {
        if ($(this).attr('id') === 'shipment-form') {
            e.preventDefault();
            return false;
        }
    });

    // Handle form submission
    $("#shipment-form").on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalBtnText = $submitBtn.text();
        
        // Disable submit button and show loading
        $submitBtn.prop('disabled', true).text('Saving...');
        
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        $("#shipmentModal").modal('hide');
                        showAlert('success', result.message || 'Shipment saved successfully');
                        
                        // Clear form
                        $form[0].reset();
                        $('#client_id').val(null).trigger('change');
                        
                        // Reload page after a short delay
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('error', result.message || 'Error saving shipment');
                    }
                } catch (e) {
                    console.log('Response parsing error:', e);
                    showAlert('error', 'An error occurred while processing the response');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                showAlert('error', 'Network error occurred. Please try again.');
            },
            complete: function() {
                // Re-enable submit button
                $submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
        
        return false; // Extra safety to prevent form submission
    });

    // Additional safety: handle submit button clicks
    $(document).on('click', '#shipment-form button[type="submit"]', function(e) {
        e.preventDefault();
        $('#shipment-form').trigger('submit');
        return false;
    });
    });
});

function initShipmentsTable() {
    // Simple table initialization without complex DataTables for now
    console.log('Basic shipments table initialized');
    
    // Add row highlighting on hover
    $('#shipments-table tbody tr').hover(
        function() { $(this).addClass('table-hover-highlight'); },
        function() { $(this).removeClass('table-hover-highlight'); }
    );
}

// Utility function for alerts
function showAlert(type, message) {
    if (typeof appAlert !== 'undefined') {
        if (type === 'success') appAlert.success(message);
        else if (type === 'error') appAlert.error(message);
        else appAlert.info(message);
    } else {
        alert(message);
    }
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

// Global functions
function bulkAssignDepartment() {
    var selectedRows = getSelectedShipments();
    if (selectedRows.length === 0) {
        showAlert('warning', 'Please select shipments first');
        return;
    }
    $("#assignDepartmentModal").modal('show');
}

function updateStatusBulk() {
    var selectedRows = getSelectedShipments();
    if (selectedRows.length === 0) {
        showAlert('warning', 'Please select shipments first');
        return;
    }
    showAlert('info', 'Status update feature coming soon...');
}

function createTaskBulk() {
    var selectedRows = getSelectedShipments();
    if (selectedRows.length === 0) {
        showAlert('warning', 'Please select shipments first');
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

function quickAssignTask(shipmentId, element) {
    var taskTitle = prompt('Enter task title:');
    if (!taskTitle) return;
    
    var userId = prompt('Enter user ID or name:');
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
            try {
                var result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    showAlert('success', 'Task assigned successfully');
                    $(element).closest('tr').find('.task-indicator').show();
                } else {
                    showAlert('error', result.message || 'Error assigning task');
                }
            } catch (e) {
                console.log('Error:', e);
                showAlert('error', 'An error occurred');
            }
        }
    });
}

function editShipment(shipmentId) {
    // For now, redirect to a simple edit page or show modal
    showAlert('info', 'Edit functionality will be implemented soon');
}

function deleteShipment(shipmentId) {
    if (confirm('Are you sure you want to delete this shipment?')) {
        $.ajax({
            url: '<?php echo_uri("workflow/delete_shipment") ?>',
            type: 'POST',
            data: {id: shipmentId},
            success: function(response) {
                try {
                    var result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        showAlert('success', 'Shipment deleted successfully');
                        location.reload();
                    } else {
                        showAlert('error', result.message || 'Error deleting shipment');
                    }
                } catch (e) {
                    console.log('Error:', e);
                    showAlert('error', 'An error occurred');
                }
            }
        });
    }
}

function viewShipmentDetails(shipmentId) {
    window.location.href = '<?php echo_uri("workflow/shipment_details/") ?>' + shipmentId;
}

// Add event listener for tab activation to fix icon rendering
$(document).on('shown.bs.tab', 'a[data-bs-target="#shipments_list"]', function() {
    setTimeout(function() {
        if (typeof feather !== 'undefined' && feather.replace) {
            feather.replace();
        }
    }, 50);
});
</script>

<style>
.table-hover-highlight {
    background-color: rgba(0, 123, 255, 0.1);
}

.dropdown-toggle::after {
    margin-left: 0.255em;
}

.badge {
    font-size: 0.75em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.icon-16 {
    width: 16px;
    height: 16px;
}

.w50 {
    width: 50px !important;
}

.w100 {
    width: 100px !important;
}

#shipments-table {
    font-size: 0.9em;
}

#shipments-table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 8px;
}

#shipments-table td {
    padding: 8px;
    vertical-align: middle;
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #e9ecef;
}

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: 1px solid #e9ecef;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.text-primary {
    text-decoration: none;
}

.text-primary:hover {
    text-decoration: underline;
}
</style>
</script>