<?php
if (!isset($shipment_info)) {
    show_404();
}
?>
<div class="page-content page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <!-- Left sidebar navigation -->
            <?php
            $tab_view['active_tab'] = "workflow";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="page-title clearfix">
                <h4>
                    <i data-feather="package" class="icon-16"></i>
                    <?php echo app_lang('shipment_details'); ?> - <?php echo $shipment_info->shipment_number; ?>
                </h4>
                <div class="title-button-group">
                    <?php if (get_array_value($permissions, "can_edit_shipments")) { ?>
                        <button type="button" class="btn btn-primary" onclick="editShipment(<?php echo $shipment_info->id; ?>)">
                            <i data-feather="edit" class="icon-16"></i> <?php echo app_lang('edit'); ?>
                        </button>
                    <?php } ?>
                    <?php if (get_array_value($permissions, "can_delete_shipments")) { ?>
                        <button type="button" class="btn btn-danger" onclick="deleteShipment(<?php echo $shipment_info->id; ?>)">
                            <i data-feather="trash-2" class="icon-16"></i> <?php echo app_lang('delete'); ?>
                        </button>
                    <?php } ?>
                </div>
            </div>

            <!-- Shipment Information Cards -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><?php echo app_lang('shipment_information'); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('shipment_number'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->shipment_number; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('client'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->company_name ?: 'Unknown Client'; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('cargo_type'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->cargo_type; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('weight'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->cargo_weight ? $shipment_info->cargo_weight . ' tons' : '-'; ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('origin'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->origin_port; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('destination'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->destination_port; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('final_destination'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->final_destination ?: '-'; ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo app_lang('estimated_arrival'); ?>:</label>
                                        <p class="form-control-plaintext"><?php echo $shipment_info->estimated_arrival ? format_to_date($shipment_info->estimated_arrival) : '-'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><?php echo app_lang('status_and_actions'); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label"><?php echo app_lang('status'); ?>:</label>
                                <?php
                                $status_colors = [
                                    'active' => 'warning',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $status_class = $status_colors[$shipment_info->status] ?? 'secondary';
                                ?>
                                <p><span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($shipment_info->status); ?></span></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo app_lang('current_phase'); ?>:</label>
                                <p><span class="badge bg-info"><?php echo ucwords(str_replace('_', ' ', $shipment_info->current_phase)); ?></span></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo app_lang('created_date'); ?>:</label>
                                <p class="form-control-plaintext"><?php echo format_to_date($shipment_info->created_at, false); ?></p>
                            </div>
                            
                            <?php if (get_array_value($permissions, "can_manage_workflow")) { ?>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="updateShipmentStatus(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="edit-3" class="icon-16"></i> <?php echo app_lang('update_status'); ?>
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="assignTask(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="plus-circle" class="icon-16"></i> <?php echo app_lang('assign_task'); ?>
                                </button>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabbed Content -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="shipmentDetailsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="true">
                                        <i data-feather="file-text" class="icon-16"></i>
                                        <?php echo app_lang('documents'); ?>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab" aria-controls="tasks" aria-selected="false">
                                        <i data-feather="check-square" class="icon-16"></i>
                                        <?php echo app_lang('tasks'); ?>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab" aria-controls="timeline" aria-selected="false">
                                        <i data-feather="clock" class="icon-16"></i>
                                        <?php echo app_lang('timeline'); ?>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tracking-tab" data-bs-toggle="tab" data-bs-target="#tracking" type="button" role="tab" aria-controls="tracking" aria-selected="false">
                                        <i data-feather="map-pin" class="icon-16"></i>
                                        <?php echo app_lang('tracking'); ?>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="shipmentDetailsTabContent">
                                <!-- Documents Tab -->
                                <div class="tab-pane fade show active" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6><?php echo app_lang('shipment_documents'); ?></h6>
                                        <?php if (get_array_value($permissions, "can_manage_documents")) { ?>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="uploadDocument(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="upload" class="icon-16"></i> <?php echo app_lang('upload_document'); ?>
                                        </button>
                                        <?php } ?>
                                    </div>
                                    <div id="documents-list">
                                        <p class="text-muted"><?php echo app_lang('no_documents_found'); ?></p>
                                    </div>
                                </div>

                                <!-- Tasks Tab -->
                                <div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6><?php echo app_lang('assigned_tasks'); ?></h6>
                                        <?php if (get_array_value($permissions, "can_manage_workflow")) { ?>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="createTask(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('create_task'); ?>
                                        </button>
                                        <?php } ?>
                                    </div>
                                    <div id="tasks-list">
                                        <p class="text-muted"><?php echo app_lang('no_tasks_found'); ?></p>
                                    </div>
                                </div>

                                <!-- Timeline Tab -->
                                <div class="tab-pane fade" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
                                    <h6><?php echo app_lang('activity_timeline'); ?></h6>
                                    <div id="timeline-list">
                                        <div class="timeline">
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-primary"></div>
                                                <div class="timeline-content">
                                                    <h6 class="timeline-title"><?php echo app_lang('shipment_created'); ?></h6>
                                                    <p class="timeline-text"><?php echo app_lang('shipment_number'); ?>: <?php echo $shipment_info->shipment_number; ?></p>
                                                    <small class="text-muted"><?php echo format_to_datetime($shipment_info->created_at); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tracking Tab -->
                                <div class="tab-pane fade" id="tracking" role="tabpanel" aria-labelledby="tracking-tab">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6><?php echo app_lang('tracking_information'); ?></h6>
                                        <?php if (get_array_value($permissions, "can_manage_workflow")) { ?>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="addTrackingUpdate(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="map-pin" class="icon-16"></i> <?php echo app_lang('add_tracking_update'); ?>
                                        </button>
                                        <?php } ?>
                                    </div>
                                    <div id="tracking-list">
                                        <p class="text-muted"><?php echo app_lang('no_tracking_updates'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel"><?php echo app_lang('update_shipment_status'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <input type="hidden" id="status-shipment-id" value="<?php echo $shipment_info->id; ?>">
                    <div class="mb-3">
                        <label for="new-status" class="form-label"><?php echo app_lang('new_status'); ?></label>
                        <select class="form-select" id="new-status" name="status" required>
                            <option value="active" <?php echo $shipment_info->status == 'active' ? 'selected' : ''; ?>><?php echo app_lang('active'); ?></option>
                            <option value="completed" <?php echo $shipment_info->status == 'completed' ? 'selected' : ''; ?>><?php echo app_lang('completed'); ?></option>
                            <option value="cancelled" <?php echo $shipment_info->status == 'cancelled' ? 'selected' : ''; ?>><?php echo app_lang('cancelled'); ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new-phase" class="form-label"><?php echo app_lang('current_phase'); ?></label>
                        <select class="form-select" id="new-phase" name="phase">
                            <option value="clearing_intake" <?php echo $shipment_info->current_phase == 'clearing_intake' ? 'selected' : ''; ?>><?php echo app_lang('clearing_intake'); ?></option>
                            <option value="regulatory_processing" <?php echo $shipment_info->current_phase == 'regulatory_processing' ? 'selected' : ''; ?>><?php echo app_lang('regulatory_processing'); ?></option>
                            <option value="internal_review" <?php echo $shipment_info->current_phase == 'internal_review' ? 'selected' : ''; ?>><?php echo app_lang('internal_review'); ?></option>
                            <option value="transport_loading" <?php echo $shipment_info->current_phase == 'transport_loading' ? 'selected' : ''; ?>><?php echo app_lang('transport_loading'); ?></option>
                            <option value="tracking" <?php echo $shipment_info->current_phase == 'tracking' ? 'selected' : ''; ?>><?php echo app_lang('tracking'); ?></option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app_lang('cancel'); ?></button>
                <button type="button" class="btn btn-primary" onclick="saveStatusUpdate()"><?php echo app_lang('update_status'); ?></button>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 16px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    color: #6c757d;
}
</style>

<script>
// Status update functionality
function updateShipmentStatus(shipmentId) {
    $('#statusUpdateModal').modal('show');
}

function saveStatusUpdate() {
    var shipmentId = $('#status-shipment-id').val();
    var status = $('#new-status').val();
    var phase = $('#new-phase').val();
    
    $.ajax({
        url: '<?php echo_uri("workflow/update_shipment_status") ?>',
        type: 'POST',
        data: {
            shipment_id: shipmentId,
            status: status,
            phase: phase
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.success) {
                appAlert.success(result.message);
                $('#statusUpdateModal').modal('hide');
                location.reload(); // Reload to show updated status
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

// Task assignment functionality
function assignTask(shipmentId) {
    // Open task creation modal (implement based on existing task system)
    appAlert.info('Task assignment feature will be implemented');
}

// Document upload functionality
function uploadDocument(shipmentId) {
    // Open document upload modal
    appAlert.info('Document upload feature will be implemented');
}

// Create task functionality
function createTask(shipmentId) {
    // Open task creation modal
    appAlert.info('Task creation feature will be implemented');
}

// Add tracking update functionality
function addTrackingUpdate(shipmentId) {
    // Open tracking update modal
    appAlert.info('Tracking update feature will be implemented');
}

// Edit and delete functions (reuse from list page)
function editShipment(shipmentId) {
    window.location.href = '<?php echo_uri("workflow/shipments") ?>';
}

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
                    window.location.href = '<?php echo_uri("workflow/shipments") ?>';
                } else {
                    appAlert.error(result.message);
                }
            }
        });
    }
}
</script>