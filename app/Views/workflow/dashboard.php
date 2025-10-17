<?php
/**
 * Workflow Dashboard View
 */
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <div class="title-button-group">
            <h1 class="page-title"><?php echo app_lang('workflow_dashboard'); ?></h1>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="widget-icon bg-primary">
                        <i data-feather="package" class="icon-16"></i>
                    </div>
                    <div class="widget-details">
                        <h3><?php echo $total_shipments; ?></h3>
                        <span class="text-muted"><?php echo app_lang('total_shipments'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="widget-icon bg-success">
                        <i data-feather="play" class="icon-16"></i>
                    </div>
                    <div class="widget-details">
                        <h3><?php echo $active_shipments; ?></h3>
                        <span class="text-muted"><?php echo app_lang('active_shipments'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="widget-icon bg-warning">
                        <i data-feather="clock" class="icon-16"></i>
                    </div>
                    <div class="widget-details">
                        <h3><?php echo $pending_shipments; ?></h3>
                        <span class="text-muted"><?php echo app_lang('pending_shipments'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="widget-icon bg-info">
                        <i data-feather="check-circle" class="icon-16"></i>
                    </div>
                    <div class="widget-details">
                        <h3><?php echo $completed_shipments; ?></h3>
                        <span class="text-muted"><?php echo app_lang('completed_shipments'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Shipments and Tasks -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('recent_shipments'); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_shipments)) { ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo app_lang('shipment_number'); ?></th>
                                        <th><?php echo app_lang('client'); ?></th>
                                        <th><?php echo app_lang('status'); ?></th>
                                        <th><?php echo app_lang('created_date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_shipments as $shipment) { ?>
                                        <tr>
                                            <td>
                                                <a href="#" class="text-primary">
                                                    <?php echo isset($shipment->shipment_number) ? $shipment->shipment_number : '#' . $shipment->id; ?>
                                                </a>
                                            </td>
                                            <td><?php echo isset($shipment->client_name) ? $shipment->client_name : '-'; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($shipment->status == 'completed') ? 'success' : (($shipment->status == 'active' || $shipment->status == 'in_progress') ? 'primary' : 'warning'); ?>">
                                                    <?php echo ucfirst($shipment->status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo isset($shipment->created_at) ? format_to_date($shipment->created_at, false) : '-'; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="text-center text-muted">
                            <i data-feather="package" class="icon-48 mb-3"></i>
                            <p><?php echo app_lang('no_recent_shipments'); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('urgent_tasks'); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($urgent_tasks)) { ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($urgent_tasks as $task) { ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1"><?php echo $task->title; ?></h6>
                                            <p class="mb-1 text-muted small"><?php echo $task->description; ?></p>
                                        </div>
                                        <small class="text-danger"><?php echo app_lang('urgent'); ?></small>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="text-center text-muted">
                            <i data-feather="check-circle" class="icon-48 mb-3"></i>
                            <p><?php echo app_lang('no_urgent_tasks'); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase Statistics -->
    <?php if (isset($phase_statistics) && !empty($phase_statistics)) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('phase_statistics'); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($phase_statistics as $phase => $count) { ?>
                            <div class="col-md-3 text-center">
                                <h3 class="text-primary"><?php echo $count; ?></h3>
                                <p class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $phase)); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<style>
.dashboard-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.dashboard-card .card-body {
    padding: 20px;
    display: flex;
    align-items: center;
}

.widget-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
}

.widget-icon i {
    color: white;
    font-size: 24px;
}

.widget-details h3 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
}

.widget-details span {
    font-size: 14px;
}
</style>