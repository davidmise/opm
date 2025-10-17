<?php
/**
 * Workflow Tracking View
 */
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <div class="title-button-group">
            <h1 class="page-title"><?php echo app_lang('tracking'); ?></h1>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="updateTracking()">
                    <i data-feather="refresh-cw" class="icon-16"></i> <?php echo app_lang('update_tracking'); ?>
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="exportTracking()">
                    <i data-feather="download" class="icon-16"></i> <?php echo app_lang('export'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('shipment_tracking'); ?></h4>
                    <div class="card-header-actions">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="<?php echo app_lang('search_by_tracking_number'); ?>" id="tracking-search">
                            <button class="btn btn-outline-secondary" type="button">
                                <i data-feather="search" class="icon-16"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tracking-table">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('shipment_number'); ?></th>
                                    <th><?php echo app_lang('tracking_number'); ?></th>
                                    <th><?php echo app_lang('current_location'); ?></th>
                                    <th><?php echo app_lang('status'); ?></th>
                                    <th><?php echo app_lang('estimated_delivery'); ?></th>
                                    <th><?php echo app_lang('last_updated'); ?></th>
                                    <th><?php echo app_lang('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample tracking data -->
                                <tr>
                                    <td><a href="#" class="text-primary">SHP-2025-001</a></td>
                                    <td>
                                        <code>TRK-ABC123456789</code>
                                        <br><small class="text-muted">UPS</small>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-primary"></i>
                                        Los Angeles, CA
                                        <br><small class="text-muted">Distribution Center</small>
                                    </td>
                                    <td><span class="badge bg-primary">In Transit</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('+2 days')), false); ?></td>
                                    <td>
                                        <?php echo format_to_datetime(date('Y-m-d H:i:s', strtotime('-2 hours')), false); ?>
                                        <br><small class="text-muted">Auto-updated</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewTrackingDetails(1)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="refreshTracking(1)">
                                                <i data-feather="refresh-cw" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-primary">SHP-2025-002</a></td>
                                    <td>
                                        <code>TRK-FDX987654321</code>
                                        <br><small class="text-muted">FedEx</small>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-warning"></i>
                                        Phoenix, AZ
                                        <br><small class="text-muted">In Transit</small>
                                    </td>
                                    <td><span class="badge bg-warning">Delayed</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('+4 days')), false); ?></td>
                                    <td>
                                        <?php echo format_to_datetime(date('Y-m-d H:i:s', strtotime('-1 hour')), false); ?>
                                        <br><small class="text-muted">Manual update</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewTrackingDetails(2)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="refreshTracking(2)">
                                                <i data-feather="refresh-cw" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="reportIssue(2)">
                                                <i data-feather="alert-triangle" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-primary">SHP-2025-003</a></td>
                                    <td>
                                        <code>TRK-DHL111222333</code>
                                        <br><small class="text-muted">DHL</small>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-success"></i>
                                        New York, NY
                                        <br><small class="text-muted">Delivered</small>
                                    </td>
                                    <td><span class="badge bg-success">Delivered</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('-1 day')), false); ?></td>
                                    <td>
                                        <?php echo format_to_datetime(date('Y-m-d H:i:s', strtotime('-1 day')), false); ?>
                                        <br><small class="text-muted">Delivery confirmed</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewTrackingDetails(3)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadPOD(3)">
                                                <i data-feather="download" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-primary">SHP-2025-004</a></td>
                                    <td>
                                        <code>TRK-USP444555666</code>
                                        <br><small class="text-muted">USPS</small>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-info"></i>
                                        Chicago, IL
                                        <br><small class="text-muted">Processing Facility</small>
                                    </td>
                                    <td><span class="badge bg-info">Processing</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('+3 days')), false); ?></td>
                                    <td>
                                        <?php echo format_to_datetime(date('Y-m-d H:i:s', strtotime('-30 minutes')), false); ?>
                                        <br><small class="text-muted">Auto-updated</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewTrackingDetails(4)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="refreshTracking(4)">
                                                <i data-feather="refresh-cw" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTracking() {
    alert('Update Tracking feature coming soon!');
}

function exportTracking() {
    alert('Export Tracking feature coming soon!');
}

function viewTrackingDetails(id) {
    alert('View Tracking Details feature coming soon!');
}

function refreshTracking(id) {
    alert('Refresh Tracking feature coming soon!');
}

function reportIssue(id) {
    alert('Report Issue feature coming soon!');
}

function downloadPOD(id) {
    alert('Download Proof of Delivery feature coming soon!');
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tracking-search');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tracking-table tbody tr');
        
        rows.forEach(row => {
            const shipmentNumber = row.querySelector('td:first-child').textContent.toLowerCase();
            const trackingNumber = row.querySelector('td:nth-child(2) code').textContent.toLowerCase();
            const location = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            if (shipmentNumber.includes(searchTerm) || trackingNumber.includes(searchTerm) || location.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

<style>
.card-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

#tracking-table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 11px;
    padding: 6px 12px;
}

.btn-group .btn {
    margin-right: 2px;
}

.input-group {
    width: 300px;
}

code {
    font-size: 12px;
    color: #333;
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
}
</style>