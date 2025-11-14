<?php
/**
 * Shipment Details View - Professional Implementation
 */

if (!function_exists("make_shipment_tabs_data")) {
    function make_shipment_tabs_data($default_shipment_tabs = array())
    {
        $final_shipment_tabs = $default_shipment_tabs;

        foreach ($final_shipment_tabs as $key => $value) {
            echo "<li class='nav-item' role='presentation'><a class='nav-link' data-bs-toggle='tab' href='#' data-bs-target='#shipment-$key-section'>" . app_lang($key) . "</a></li>";
        }
    }
}
?>

<div class="page-content shipment-details-view clearfix">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Breadcrumb Navigation -->
                <nav aria-label="breadcrumb" class="workflow-breadcrumb pb15">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo get_uri('workflow'); ?>">
                                <i data-feather='layers' class='icon-14'></i> <?php echo app_lang('workflow'); ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo get_uri('workflow'); ?>" onclick="$('#shipments-tab').click(); return false;">
                                <?php echo app_lang('shipments'); ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo !empty($shipment_info->shipment_number) ? $shipment_info->shipment_number : app_lang('shipment_details'); ?>
                        </li>
                        
                        <li class="ms-auto">
                            <a href="<?php echo get_uri('workflow'); ?>" class="btn btn-default round" title="<?php echo app_lang('back_to_shipments'); ?>">
                                <i data-feather='arrow-left' class='icon-16'></i> <?php echo app_lang('back'); ?>
                            </a>
                        </li>
                    </ol>
                </nav>

                <?php if (!empty($shipment_info)) { ?>
                <div class="shipment-title-section">
                    <div class="page-title no-bg clearfix mb5 no-border">
                        <div>
                            <h1 class="pl0">
                                <span class="status-indicator me-2" title="<?php echo ucfirst($shipment_info->status ?? 'Unknown'); ?>">
                                    <i data-feather="package" class='icon'></i>
                                </span>
                                <?php echo app_lang('shipment_details'); ?>
                                <?php if (!empty($shipment_info->shipment_number)) { ?>
                                    <span class="text-muted"> - <?php echo $shipment_info->shipment_number; ?></span>
                                <?php } ?>
                            </h1>
                        </div>

                        <div class="shipment-title-button-group-section">
                            <div class="title-button-group mr0" id="shipment-action-box">
                                <button type="button" class="btn btn-primary" onclick="editShipment(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="edit" class="icon-16"></i> <?php echo app_lang('edit'); ?>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="assignShipment(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="user-plus" class="icon-16"></i> <?php echo app_lang('assign'); ?>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="addTask(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="plus-circle" class="icon-16"></i> <?php echo app_lang('add_task'); ?>
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="uploadDocument(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="upload" class="icon-16"></i> <?php echo app_lang('upload_document'); ?>
                                </button>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i data-feather="more-horizontal" class="icon-16"></i> <?php echo app_lang('more_actions'); ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="edit-3" class="icon-16 me-2"></i><?php echo app_lang('update_status'); ?>
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="addTrackingUpdate(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="map-pin" class="icon-16 me-2"></i><?php echo app_lang('add_tracking_update'); ?>
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="allocateTruck(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="truck" class="icon-16 me-2"></i><?php echo app_lang('allocate_truck'); ?>
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="generateReport(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="file-text" class="icon-16 me-2"></i><?php echo app_lang('generate_report'); ?>
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="printDetails(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="printer" class="icon-16 me-2"></i><?php echo app_lang('print'); ?>
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteShipment(<?php echo $shipment_info->id; ?>)">
                                            <i data-feather="trash-2" class="icon-16 me-2"></i><?php echo app_lang('delete'); ?>
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Banner -->
                    <div class="shipment-status-banner mb20">
                        <div class="status-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="status-info">
                                    <h6 class="mb-1">
                                        <span class="badge bg-<?php echo $shipment_info->status == 'active' ? 'primary' : ($shipment_info->status == 'completed' ? 'success' : 'secondary'); ?> me-2">
                                            <?php echo ucfirst($shipment_info->status ?? 'Unknown'); ?>
                                        </span>
                                        <?php echo app_lang('current_phase'); ?>: 
                                        <span class="badge bg-info ms-2">
                                            <?php echo ucfirst(str_replace('_', ' ', $shipment_info->current_phase ?? 'Not Set')); ?>
                                        </span>
                                    </h6>
                                    <div class="route-summary">
                                        <small class="text-muted">
                                            <i data-feather="map-pin" class="icon-12 me-1"></i>
                                            <?php echo $shipment_info->origin_port; ?> → <?php echo $shipment_info->destination_port; ?>
                                            <?php if ($shipment_info->final_destination != $shipment_info->destination_port) { ?>
                                                → <?php echo $shipment_info->final_destination; ?>
                                            <?php } ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="status-meta text-end">
                                    <small class="text-muted">
                                        <i data-feather="calendar" class="icon-12 me-1"></i>
                                        <?php echo app_lang('created'); ?>: <?php echo format_to_date($shipment_info->created_at, false); ?>
                                    </small>
                                    <?php if (!empty($shipment_info->estimated_arrival)) { ?>
                                        <br>
                                        <small class="text-muted">
                                            <i data-feather="clock" class="icon-12 me-1"></i>
                                            <?php echo app_lang('estimated_arrival'); ?>: <?php echo format_to_date($shipment_info->estimated_arrival, false); ?>
                                        </small>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phase Progress Indicator -->
                    <div class="phase-progress-container mb20">
                        <div class="phase-progress">
                            <?php 
                            $phases = [
                                'clearing_intake' => 'Clearing Intake',
                                'regulatory_processing' => 'Regulatory Processing', 
                                'internal_review' => 'Internal Review',
                                'transport_loading' => 'Transport Loading',
                                'tracking' => 'Tracking'
                            ];
                            $current_phase = $shipment_info->current_phase ?? 'clearing_intake';
                            $phase_keys = array_keys($phases);
                            $current_phase_index = array_search($current_phase, $phase_keys);
                            ?>
                            
                            <div class="d-flex align-items-center justify-content-between">
                                <?php foreach ($phases as $phase_key => $phase_name): ?>
                                    <?php 
                                    $phase_index = array_search($phase_key, $phase_keys);
                                    $is_current = ($phase_key == $current_phase);
                                    $is_completed = ($phase_index < $current_phase_index);
                                    $is_upcoming = ($phase_index > $current_phase_index);
                                    
                                    $phase_class = 'phase-step';
                                    if ($is_current) $phase_class .= ' current';
                                    if ($is_completed) $phase_class .= ' completed';
                                    if ($is_upcoming) $phase_class .= ' upcoming';
                                    ?>
                                    
                                    <div class="<?php echo $phase_class; ?>" onclick="updatePhase(<?php echo $shipment_info->id; ?>, '<?php echo $phase_key; ?>')" style="cursor: pointer;" title="<?php echo app_lang('click_to_set_phase'); ?>">
                                        <div class="phase-circle">
                                            <i data-feather="<?php echo $is_completed ? 'check' : ($is_current ? 'play' : 'circle'); ?>" class="icon-16"></i>
                                        </div>
                                        <div class="phase-label">
                                            <small><?php echo $phase_name; ?></small>
                                        </div>
                                    </div>
                                    
                                    <?php if ($phase_index < count($phases) - 1): ?>
                                        <div class="phase-connector <?php echo $is_completed ? 'completed' : ''; ?>"></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <ul id="shipment-tabs" data-bs-toggle="tab" class="nav nav-tabs rounded classic mb20 scrollable-tabs" role="tablist">
                        <?php
                        // Shipment tabs
                        $shipment_tabs = array(
                            "overview" => "#shipment-overview-section",
                            "documents" => "#shipment-documents-section", 
                            "tasks" => "#shipment-tasks-section",
                            "tracking" => "#shipment-tracking-section",
                            "truck_allocation" => "#shipment-truck_allocation-section"
                        );

                        foreach ($shipment_tabs as $key => $value) {
                            $tab_count = '';
                            if ($key == 'documents' && !empty($shipment_documents)) {
                                $tab_count = ' <span class="badge bg-primary ms-1">' . count($shipment_documents) . '</span>';
                            } elseif ($key == 'tasks' && !empty($workflow_tasks)) {
                                $tab_count = ' <span class="badge bg-primary ms-1">' . count($workflow_tasks) . '</span>';
                            } elseif ($key == 'tracking' && !empty($tracking_reports)) {
                                $tab_count = ' <span class="badge bg-primary ms-1">' . count($tracking_reports) . '</span>';
                            } elseif ($key == 'truck_allocation' && !empty($truck_allocations)) {
                                $tab_count = ' <span class="badge bg-primary ms-1">' . count($truck_allocations) . '</span>';
                            }
                            
                            $active_class = ($key == 'overview') ? ' active' : '';
                            echo "<li class='nav-item' role='presentation'><a class='nav-link$active_class' data-bs-toggle='tab' href='$value' data-bs-target='$value'>" . app_lang($key) . $tab_count . "</a></li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="tab-content">
                    <!-- Overview Tab -->
                    <div class="tab-pane fade show active" id="shipment-overview-section" role="tabpanel">
                        <!-- Main Information Cards -->
                        <div class="row">
                            <!-- Shipment Information Card -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i data-feather="info" class="icon-16 me-2"></i>
                                            <?php echo app_lang('shipment_information'); ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <h6 class="text-muted small"><?php echo app_lang('shipment_number'); ?></h6>
                                                <p class="mb-3"><?php echo $shipment_info->shipment_number ?? 'N/A'; ?></p>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted small"><?php echo app_lang('client'); ?></h6>
                                                <p class="mb-3"><?php echo $shipment_info->company_name ?? 'N/A'; ?></p>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted small"><?php echo app_lang('cargo_type'); ?></h6>
                                                <p class="mb-3"><?php echo $shipment_info->cargo_type ?? 'N/A'; ?></p>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted small"><?php echo app_lang('cargo_weight'); ?></h6>
                                                <p class="mb-3"><?php echo $shipment_info->cargo_weight ? $shipment_info->cargo_weight . ' kg' : 'N/A'; ?></p>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="text-muted small mb-2"><?php echo app_lang('assigned_to'); ?></h6>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="assignShipment(<?php echo $shipment_info->id; ?>)" title="<?php echo app_lang('reassign'); ?>">
                                                        <i data-feather="user-plus" class="icon-12"></i>
                                                    </button>
                                                </div>
                                                <p class="mb-0">
                                                    <?php if (!empty($shipment_info->first_name) || !empty($shipment_info->last_name)) { ?>
                                                        <span class="badge bg-light text-dark">
                                                            <i data-feather="user" class="icon-12 me-1"></i>
                                                            <?php echo trim(($shipment_info->first_name ?? '') . ' ' . ($shipment_info->last_name ?? '')); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="text-muted">Not assigned</span>
                                                    <?php } ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Information Card -->
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i data-feather="map-pin" class="icon-16 me-2"></i>
                                            <?php echo app_lang('shipping_information'); ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="shipping-route mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="route-point">
                                                    <div class="route-dot bg-primary"></div>
                                                    <h6 class="mb-1"><?php echo $shipment_info->origin_port ?? 'N/A'; ?></h6>
                                                    <small class="text-muted"><?php echo app_lang('origin_port'); ?></small>
                                                </div>
                                                <div class="route-line flex-fill"></div>
                                                <div class="route-point">
                                                    <div class="route-dot bg-warning"></div>
                                                    <h6 class="mb-1"><?php echo $shipment_info->destination_port ?? 'N/A'; ?></h6>
                                                    <small class="text-muted"><?php echo app_lang('destination_port'); ?></small>
                                                </div>
                                                <?php if ($shipment_info->final_destination != $shipment_info->destination_port) { ?>
                                                    <div class="route-line flex-fill"></div>
                                                    <div class="route-point">
                                                        <div class="route-dot bg-success"></div>
                                                        <h6 class="mb-1"><?php echo $shipment_info->final_destination ?? 'N/A'; ?></h6>
                                                        <small class="text-muted"><?php echo app_lang('final_destination'); ?></small>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($shipment_info->estimated_arrival)) { ?>
                                            <div class="mt-3">
                                                <h6 class="text-muted small"><?php echo app_lang('estimated_arrival'); ?></h6>
                                                <p class="mb-0">
                                                    <i data-feather="calendar" class="icon-14 me-1"></i>
                                                    <?php echo format_to_date($shipment_info->estimated_arrival, false); ?>
                                                </p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div class="tab-pane fade" id="shipment-documents-section" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><?php echo app_lang('shipment_documents'); ?></h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="uploadDocument(<?php echo $shipment_info->id; ?>)">
                                <i data-feather="upload" class="icon-14 me-1"></i>
                                <?php echo app_lang('upload_document'); ?>
                            </button>
                        </div>
                        <?php if (!empty($shipment_documents) && count($shipment_documents) > 0) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang('document_name'); ?></th>
                                            <th><?php echo app_lang('type'); ?></th>
                                            <th><?php echo app_lang('uploaded_by'); ?></th>
                                            <th><?php echo app_lang('uploaded_at'); ?></th>
                                            <th><?php echo app_lang('status'); ?></th>
                                            <th><?php echo app_lang('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($shipment_documents as $document) { ?>
                                            <tr>
                                                <td>
                                                    <i data-feather="file" class="icon-16 me-2"></i>
                                                    <?php echo $document->document_name ?? 'N/A'; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        <?php echo strtoupper($document->document_type ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo trim(($document->first_name ?? '') . ' ' . ($document->last_name ?? '')); ?></td>
                                                <td><?php echo format_to_date($document->upload_date ?? '', false); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $document->status == 'approved' ? 'success' : ($document->status == 'rejected' ? 'danger' : 'warning'); ?>">
                                                        <?php echo ucfirst($document->status ?? 'Pending'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary" onclick="downloadDocument(<?php echo $document->id; ?>)" title="<?php echo app_lang('download'); ?>">
                                                            <i data-feather="download" class="icon-14"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" onclick="deleteDocument(<?php echo $document->id; ?>)" title="<?php echo app_lang('delete'); ?>">
                                                            <i data-feather="trash-2" class="icon-14"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="empty-state text-center py-5">
                                <i data-feather="file-x" class="icon-48 text-muted mb-3"></i>
                                <h6 class="text-muted"><?php echo app_lang('no_documents_found'); ?></h6>
                                <p class="text-muted small"><?php echo app_lang('upload_documents_to_get_started'); ?></p>
                                <button type="button" class="btn btn-primary" onclick="uploadDocument(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="upload" class="icon-16 me-2"></i>
                                    <?php echo app_lang('upload_first_document'); ?>
                                </button>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Tasks Tab -->
                    <div class="tab-pane fade" id="shipment-tasks-section" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><?php echo app_lang('workflow_tasks'); ?></h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addTask(<?php echo $shipment_info->id; ?>)">
                                <i data-feather="plus" class="icon-14 me-1"></i>
                                <?php echo app_lang('add_task'); ?>
                            </button>
                        </div>
                        <?php if (!empty($workflow_tasks) && count($workflow_tasks) > 0) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang('task_name'); ?></th>
                                            <th><?php echo app_lang('assigned_to'); ?></th>
                                            <th><?php echo app_lang('phase'); ?></th>
                                            <th><?php echo app_lang('status'); ?></th>
                                            <th><?php echo app_lang('priority'); ?></th>
                                            <th><?php echo app_lang('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($workflow_tasks as $task) { ?>
                                            <tr>
                                                <td>
                                                    <i data-feather="<?php echo $task->status == 'completed' ? 'check-circle' : 'circle'; ?>" class="icon-16 me-2 text-<?php echo $task->status == 'completed' ? 'success' : 'warning'; ?>"></i>
                                                    <?php echo $task->task_name ?? 'N/A'; ?>
                                                </td>
                                                <td><?php echo trim(($task->assigned_first_name ?? '') . ' ' . ($task->assigned_last_name ?? '')); ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo ucfirst(str_replace('_', ' ', $task->phase_name ?? 'N/A')); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'warning'); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $task->status ?? 'N/A')); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'secondary'); ?>">
                                                        <?php echo ucfirst($task->priority ?? 'Normal'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary" onclick="editTask(<?php echo $task->id; ?>)" title="<?php echo app_lang('edit'); ?>">
                                                            <i data-feather="edit" class="icon-14"></i>
                                                        </button>
                                                        <?php if ($task->status != 'completed') { ?>
                                                            <button type="button" class="btn btn-outline-success" onclick="markComplete(<?php echo $task->id; ?>)" title="<?php echo app_lang('mark_complete'); ?>">
                                                                <i data-feather="check" class="icon-14"></i>
                                                            </button>
                                                        <?php } ?>
                                                        <button type="button" class="btn btn-outline-danger" onclick="deleteTask(<?php echo $task->id; ?>)" title="<?php echo app_lang('delete'); ?>">
                                                            <i data-feather="trash-2" class="icon-14"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="empty-state text-center py-5">
                                <i data-feather="check-square" class="icon-48 text-muted mb-3"></i>
                                <h6 class="text-muted"><?php echo app_lang('no_tasks_found'); ?></h6>
                                <p class="text-muted small"><?php echo app_lang('create_tasks_to_track_progress'); ?></p>
                                <button type="button" class="btn btn-primary" onclick="addTask(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="plus" class="icon-16 me-2"></i>
                                    <?php echo app_lang('create_first_task'); ?>
                                </button>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Tracking Tab -->
                    <div class="tab-pane fade" id="shipment-tracking-section" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><?php echo app_lang('shipment_tracking'); ?></h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addTrackingUpdate(<?php echo $shipment_info->id; ?>)">
                                <i data-feather="map-pin" class="icon-14 me-1"></i>
                                <?php echo app_lang('add_tracking_update'); ?>
                            </button>
                        </div>
                        <?php if (!empty($tracking_reports) && count($tracking_reports) > 0) { ?>
                            <div class="timeline">
                                <?php foreach ($tracking_reports as $index => $tracking) { ?>
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <div class="timeline-dot bg-<?php echo $index == 0 ? 'primary' : 'light'; ?>"></div>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo $tracking->location ?? 'N/A'; ?></h6>
                                                    <p class="mb-1 text-muted"><?php echo $tracking->status_update ?? 'N/A'; ?></p>
                                                    <small class="text-muted">
                                                        <i data-feather="user" class="icon-12 me-1"></i>
                                                        <?php echo trim(($tracking->first_name ?? '') . ' ' . ($tracking->last_name ?? '')); ?>
                                                        <span class="mx-2">•</span>
                                                        <i data-feather="clock" class="icon-12 me-1"></i>
                                                        <?php echo format_to_date($tracking->timestamp ?? '', true); ?>
                                                    </small>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" onclick="editTracking(<?php echo $tracking->id; ?>)" title="<?php echo app_lang('edit'); ?>">
                                                        <i data-feather="edit" class="icon-12"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" onclick="deleteTracking(<?php echo $tracking->id; ?>)" title="<?php echo app_lang('delete'); ?>">
                                                        <i data-feather="trash-2" class="icon-12"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="empty-state text-center py-5">
                                <i data-feather="navigation" class="icon-48 text-muted mb-3"></i>
                                <h6 class="text-muted"><?php echo app_lang('no_tracking_updates'); ?></h6>
                                <p class="text-muted small"><?php echo app_lang('add_tracking_updates_to_monitor_progress'); ?></p>
                                <button type="button" class="btn btn-primary" onclick="addTrackingUpdate(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="map-pin" class="icon-16 me-2"></i>
                                    <?php echo app_lang('add_first_update'); ?>
                                </button>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Truck Allocation Tab -->
                    <div class="tab-pane fade" id="shipment-truck_allocation-section" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><?php echo app_lang('truck_allocation'); ?></h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="allocateTruck(<?php echo $shipment_info->id; ?>)">
                                <i data-feather="truck" class="icon-14 me-1"></i>
                                <?php echo app_lang('allocate_truck'); ?>
                            </button>
                        </div>
                        <?php if (!empty($truck_allocations) && count($truck_allocations) > 0) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang('truck_number'); ?></th>
                                            <th><?php echo app_lang('driver'); ?></th>
                                            <th><?php echo app_lang('phone'); ?></th>
                                            <th><?php echo app_lang('allocated_at'); ?></th>
                                            <th><?php echo app_lang('status'); ?></th>
                                            <th><?php echo app_lang('actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($truck_allocations as $allocation) { ?>
                                            <tr>
                                                <td>
                                                    <i data-feather="truck" class="icon-16 me-2"></i>
                                                    <?php echo $allocation->truck_number ?? 'N/A'; ?>
                                                </td>
                                                <td><?php echo $allocation->driver_name ?? 'N/A'; ?></td>
                                                <td><?php echo $allocation->driver_phone ?? 'N/A'; ?></td>
                                                <td><?php echo format_to_date($allocation->allocated_at ?? '', false); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $allocation->status == 'delivered' ? 'success' : ($allocation->status == 'in_transit' ? 'primary' : 'warning'); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $allocation->status ?? 'N/A')); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary" onclick="editAllocation(<?php echo $allocation->id; ?>)" title="<?php echo app_lang('edit'); ?>">
                                                            <i data-feather="edit" class="icon-14"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info" onclick="trackTruck(<?php echo $allocation->truck_id ?? $allocation->id; ?>)" title="<?php echo app_lang('track'); ?>">
                                                            <i data-feather="map" class="icon-14"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" onclick="deallocateTruck(<?php echo $allocation->id; ?>)" title="<?php echo app_lang('deallocate'); ?>">
                                                            <i data-feather="x-circle" class="icon-14"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="empty-state text-center py-5">
                                <i data-feather="truck" class="icon-48 text-muted mb-3"></i>
                                <h6 class="text-muted"><?php echo app_lang('no_trucks_allocated'); ?></h6>
                                <p class="text-muted small"><?php echo app_lang('allocate_trucks_for_transportation'); ?></p>
                                <button type="button" class="btn btn-primary" onclick="allocateTruck(<?php echo $shipment_info->id; ?>)">
                                    <i data-feather="truck" class="icon-16 me-2"></i>
                                    <?php echo app_lang('allocate_first_truck'); ?>
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } else { ?>
                    <div class="alert alert-danger">
                        <h5><i data-feather="alert-triangle" class="icon-16 me-2"></i><?php echo app_lang('error'); ?></h5>
                        <p class="mb-0"><?php echo app_lang('shipment_not_found'); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced CSS for Professional Styling -->
<style>
.shipment-details-view .page-title h1 {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.workflow-breadcrumb {
    background: transparent;
    padding: 0;
}

.workflow-breadcrumb .breadcrumb {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px 20px;
    margin-bottom: 0;
}

.workflow-breadcrumb .breadcrumb-item a {
    color: #007bff;
    text-decoration: none;
}

.workflow-breadcrumb .breadcrumb-item.active {
    color: #6c757d;
}

.shipment-title-section .page-title {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 0;
}

.shipment-title-button-group-section {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.shipment-status-banner .status-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 20px;
}

.route-summary {
    margin-top: 8px;
}

.nav-tabs.rounded.classic {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
    border: 1px solid #dee2e6;
}

.nav-tabs.rounded.classic .nav-link {
    border: none;
    border-radius: 6px;
    color: #6c757d;
    padding: 12px 16px;
    font-weight: 500;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.nav-tabs.rounded.classic .nav-link:hover {
    background-color: #e9ecef;
    color: #495057;
}

.nav-tabs.rounded.classic .nav-link.active {
    background-color: #fff;
    color: #007bff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #007bff20;
}

.scrollable-tabs {
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
}

.scrollable-tabs::-webkit-scrollbar {
    display: none;
}

.shipping-route {
    position: relative;
}

.route-point {
    text-align: center;
    position: relative;
    z-index: 2;
}

.route-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 auto 8px;
    box-shadow: 0 0 0 3px rgba(255,255,255,1), 0 0 0 4px rgba(0,123,255,0.3);
}

.route-line {
    height: 2px;
    background: linear-gradient(to right, #007bff, #17a2b8);
    margin: 6px 15px;
    position: relative;
}

.timeline {
    position: relative;
}

.timeline-item {
    display: flex;
    margin-bottom: 1.5rem;
    position: relative;
}

.timeline-marker {
    width: 30px;
    flex-shrink: 0;
    position: relative;
}

.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 6px auto;
    box-shadow: 0 0 0 3px rgba(255,255,255,1);
}

.timeline-item:not(:last-child) .timeline-marker::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 18px;
    bottom: -24px;
    width: 2px;
    background-color: #dee2e6;
    transform: translateX(-50%);
}

.timeline-content {
    flex: 1;
    margin-left: 15px;
    padding: 12px 16px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.empty-state {
    background-color: #f8f9fa;
    border-radius: 10px;
    border: 2px dashed #dee2e6;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.badge {
    font-weight: 500;
}

/* Phase Progress Indicator */
.phase-progress-container {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.phase-progress {
    position: relative;
}

.phase-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    position: relative;
    transition: all 0.3s ease;
}

.phase-step:hover {
    transform: translateY(-2px);
}

.phase-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    transition: all 0.3s ease;
    border: 2px solid #e5e5e5;
    background: #fff;
}

.phase-step.completed .phase-circle {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.phase-step.current .phase-circle {
    background: #007bff;
    border-color: #007bff;
    color: white;
    box-shadow: 0 0 0 4px rgba(0,123,255,0.25);
}

.phase-step.upcoming .phase-circle {
    background: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

.phase-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #495057;
}

.phase-step.current .phase-label {
    color: #007bff;
    font-weight: 600;
}

.phase-step.completed .phase-label {
    color: #28a745;
}

.phase-connector {
    height: 2px;
    flex: 1;
    background: #e5e5e5;
    margin: 0 10px;
    margin-top: -20px;
    position: relative;
    z-index: 1;
}

.phase-connector.completed {
    background: #28a745;
}

@media (max-width: 768px) {
    .shipment-title-section .page-title {
        flex-direction: column;
        align-items: stretch;
    }
    
    .shipment-title-button-group-section {
        margin-top: 15px;
        justify-content: center;
    }
    
    .shipment-title-button-group-section .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .route-point h6 {
        font-size: 0.9rem;
    }
    
    .route-line {
        margin: 6px 5px;
    }
}
</style>

<!-- Enhanced JavaScript -->
<script>
$(document).ready(function() {
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize the first tab
    setTimeout(function () {
        var hash = window.location.hash;
        if (hash && hash.indexOf("shipment-") !== -1) {
            $('a[data-bs-target="' + hash + '"]').tab('show');
        } else {
            $('a[data-bs-target="#shipment-overview-section"]').tab('show');
        }
    }, 50);
});

// Action button functions
function editShipment(shipmentId) {
    $('#shipment-edit-modal').appModal({
        source: '<?php echo get_uri("workflow/shipment_modal_form"); ?>',
        data: {id: shipmentId},
        modalType: 'lg',
        title: '<?php echo app_lang('edit_shipment'); ?>',
        onSuccess: function (result, isUpdate) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload(); // Reload to show updated data
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function addTask(shipmentId) {
    $('#task-modal').appModal({
        source: '<?php echo get_uri("workflow/task_modal_form"); ?>',
        data: {shipment_id: shipmentId},
        title: '<?php echo app_lang('add_task'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                // Reload tasks tab content
                location.hash = '#shipment-tasks-section';
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function uploadDocument(shipmentId) {
    $('#document-modal').appModal({
        source: '<?php echo get_uri("workflow/document_modal_form"); ?>',
        data: {shipment_id: shipmentId},
        title: '<?php echo app_lang('upload_document'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                // Reload documents tab content
                location.hash = '#shipment-documents-section';
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function updateStatus(shipmentId) {
    $('#status-update-modal').appModal({
        source: '<?php echo get_uri("workflow/status_modal_form"); ?>',
        data: {shipment_id: shipmentId, current_status: '<?php echo $shipment_info->status ?? 'active'; ?>', current_phase: '<?php echo $shipment_info->current_phase ?? 'clearing_intake'; ?>'},
        title: '<?php echo app_lang('update_status_and_phase'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

// Assignment functionality
function assignShipment(shipmentId) {
    $('#assignment-modal').appModal({
        source: '<?php echo get_uri("workflow/assignment_modal_form"); ?>',
        data: {shipment_id: shipmentId, current_assigned_to: '<?php echo $shipment_info->assigned_to ?? ''; ?>'},
        title: '<?php echo app_lang('assign_shipment'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

// Phase progression functionality
function updatePhase(shipmentId, newPhase) {
    $.ajax({
        url: '<?php echo get_uri("workflow/update_shipment_phase"); ?>',
        type: 'POST',
        data: {
            shipment_id: shipmentId,
            phase: newPhase
        },
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        },
        error: function() {
            appAlert.error('<?php echo app_lang('error_occurred'); ?>');
        }
    });
}

function generateReport(shipmentId) {
    appAlert.success('<?php echo app_lang('feature_coming_soon'); ?>');
    // TODO: Implement report generation functionality
}

function printDetails(shipmentId) {
    window.print();
}

function deleteShipment(shipmentId) {
    appConfirmationModal({
        title: '<?php echo app_lang('delete_shipment'); ?>',
        message: '<?php echo app_lang('delete_confirmation'); ?>',
        confirmButtonText: '<?php echo app_lang('yes'); ?>',
        onConfirm: function() {
            $.ajax({
                url: '<?php echo get_uri("workflow/delete_shipment"); ?>',
                type: 'POST',
                data: {id: shipmentId},
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message);
                        window.location.href = '<?php echo get_uri("workflow"); ?>';
                    } else {
                        appAlert.error(result.message);
                    }
                },
                error: function() {
                    appAlert.error('<?php echo app_lang('error_occurred'); ?>');
                }
            });
        }
    });
}

// Document functions
function downloadDocument(documentId) {
    window.open('<?php echo get_uri("workflow/download_document/"); ?>' + documentId, '_blank');
}

function deleteDocument(documentId) {
    appConfirmationModal({
        title: '<?php echo app_lang('delete_document'); ?>',
        message: '<?php echo app_lang('delete_confirmation'); ?>',
        onConfirm: function() {
            $.ajax({
                url: '<?php echo get_uri("workflow/delete_document"); ?>',
                type: 'POST',
                data: {id: documentId},
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message);
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                },
                error: function() {
                    appAlert.error('<?php echo app_lang('error_occurred'); ?>');
                }
            });
        }
    });
}

// Task functions
function editTask(taskId) {
    $('#task-edit-modal').appModal({
        source: '<?php echo get_uri("workflow/task_modal_form"); ?>',
        data: {id: taskId},
        title: '<?php echo app_lang('edit_task'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function markComplete(taskId) {
    $.ajax({
        url: '<?php echo get_uri("workflow/update_task_status"); ?>',
        type: 'POST',
        data: {
            task_id: taskId,
            status: 'completed'
        },
        dataType: 'json',
        success: function(result) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        },
        error: function() {
            appAlert.error('<?php echo app_lang('error_occurred'); ?>');
        }
    });
}

function deleteTask(taskId) {
    appConfirmationModal({
        title: '<?php echo app_lang('delete_task'); ?>',
        message: '<?php echo app_lang('delete_confirmation'); ?>',
        onConfirm: function() {
            $.ajax({
                url: '<?php echo get_uri("workflow/delete_task"); ?>',
                type: 'POST',
                data: {id: taskId},
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message);
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                },
                error: function() {
                    appAlert.error('<?php echo app_lang('error_occurred'); ?>');
                }
            });
        }
    });
}

// Tracking functions
function addTrackingUpdate(shipmentId) {
    $('#tracking-modal').appModal({
        source: '<?php echo get_uri("workflow/tracking_modal_form"); ?>',
        data: {shipment_id: shipmentId},
        title: '<?php echo app_lang('add_tracking_update'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                location.hash = '#shipment-tracking-section';
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function editTracking(trackingId) {
    $('#tracking-edit-modal').appModal({
        source: '<?php echo get_uri("workflow/tracking_modal_form"); ?>',
        data: {id: trackingId},
        title: '<?php echo app_lang('edit_tracking'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function deleteTracking(trackingId) {
    appConfirmationModal({
        title: '<?php echo app_lang('delete_tracking'); ?>',
        message: '<?php echo app_lang('delete_confirmation'); ?>',
        onConfirm: function() {
            $.ajax({
                url: '<?php echo get_uri("workflow/delete_tracking"); ?>',
                type: 'POST',
                data: {id: trackingId},
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message);
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                },
                error: function() {
                    appAlert.error('<?php echo app_lang('error_occurred'); ?>');
                }
            });
        }
    });
}

// Truck allocation functions
function allocateTruck(shipmentId) {
    $('#truck-allocation-modal').appModal({
        source: '<?php echo get_uri("workflow/truck_allocation_modal_form"); ?>',
        data: {shipment_id: shipmentId},
        title: '<?php echo app_lang('allocate_truck'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                location.hash = '#shipment-truck_allocation-section';
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function editAllocation(allocationId) {
    $('#truck-allocation-edit-modal').appModal({
        source: '<?php echo get_uri("workflow/truck_allocation_modal_form"); ?>',
        data: {id: allocationId},
        title: '<?php echo app_lang('edit_allocation'); ?>',
        onSuccess: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                location.reload();
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

function trackTruck(truckId) {
    $('#truck-tracking-modal').appModal({
        source: '<?php echo get_uri("workflow/truck_tracking_modal_form"); ?>',
        data: {truck_id: truckId},
        title: '<?php echo app_lang('track_truck'); ?>'
    });
}

function deallocateTruck(allocationId) {
    appConfirmationModal({
        title: '<?php echo app_lang('deallocate_truck'); ?>',
        message: '<?php echo app_lang('deallocate_truck_confirmation'); ?>',
        onConfirm: function() {
            $.ajax({
                url: '<?php echo get_uri("workflow/deallocate_truck"); ?>',
                type: 'POST',
                data: {id: allocationId},
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message);
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                },
                error: function() {
                    appAlert.error('<?php echo app_lang('error_occurred'); ?>');
                }
            });
        }
    });
}
</script>
