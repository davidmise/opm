<?php
/**
 * Workflow Trucks View
 */
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <div class="title-button-group">
            <h1 class="page-title"><?php echo app_lang('trucks'); ?></h1>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="addTruck()">
                    <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('add_truck'); ?>
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="truckMaintenance()">
                    <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('maintenance'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('fleet_management'); ?></h4>
                    <div class="card-header-actions">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary active" data-filter="all">
                                <?php echo app_lang('all'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="available">
                                <?php echo app_lang('available'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="in_transit">
                                <?php echo app_lang('in_transit'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="maintenance">
                                <?php echo app_lang('maintenance'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="trucks-table">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('truck_id'); ?></th>
                                    <th><?php echo app_lang('driver'); ?></th>
                                    <th><?php echo app_lang('current_location'); ?></th>
                                    <th><?php echo app_lang('status'); ?></th>
                                    <th><?php echo app_lang('current_shipment'); ?></th>
                                    <th><?php echo app_lang('next_maintenance'); ?></th>
                                    <th><?php echo app_lang('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample truck data -->
                                <tr data-status="available">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="truck" class="icon-16 text-primary me-2"></i>
                                            <div>
                                                <strong>TRK-001</strong>
                                                <br><small class="text-muted">Freightliner Cascadia</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>John Smith</strong>
                                            <br><small class="text-muted">CDL Class A</small>
                                        </div>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-success"></i>
                                        Los Angeles Depot
                                        <br><small class="text-muted">Idle since 2 hours</small>
                                    </td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>
                                        <span class="text-muted">-</span>
                                        <br><small class="text-muted">Ready for assignment</small>
                                    </td>
                                    <td>
                                        <?php echo format_to_date(date('Y-m-d', strtotime('+15 days')), false); ?>
                                        <br><small class="text-success">On schedule</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="assignShipment(1)">
                                                <i data-feather="arrow-right" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewTruckDetails(1)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="in_transit">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="truck" class="icon-16 text-warning me-2"></i>
                                            <div>
                                                <strong>TRK-002</strong>
                                                <br><small class="text-muted">Volvo VNL</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Mike Johnson</strong>
                                            <br><small class="text-muted">CDL Class A</small>
                                        </div>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-warning"></i>
                                        Phoenix, AZ
                                        <br><small class="text-muted">En route to Denver</small>
                                    </td>
                                    <td><span class="badge bg-warning">In Transit</span></td>
                                    <td>
                                        <a href="#" class="text-primary">SHP-2025-001</a>
                                        <br><small class="text-muted">ETA: <?php echo format_to_date(date('Y-m-d', strtotime('+1 day')), false); ?></small>
                                    </td>
                                    <td>
                                        <?php echo format_to_date(date('Y-m-d', strtotime('+8 days')), false); ?>
                                        <br><small class="text-success">On schedule</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="trackTruck(2)">
                                                <i data-feather="navigation" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewTruckDetails(2)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="maintenance">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="truck" class="icon-16 text-danger me-2"></i>
                                            <div>
                                                <strong>TRK-003</strong>
                                                <br><small class="text-muted">Peterbilt 579</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>Sarah Wilson</strong>
                                            <br><small class="text-muted">CDL Class A</small>
                                        </div>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-danger"></i>
                                        Service Center
                                        <br><small class="text-muted">Brake repair</small>
                                    </td>
                                    <td><span class="badge bg-danger">Maintenance</span></td>
                                    <td>
                                        <span class="text-muted">-</span>
                                        <br><small class="text-muted">Out of service</small>
                                    </td>
                                    <td>
                                        <?php echo format_to_date(date('Y-m-d', strtotime('-2 days')), false); ?>
                                        <br><small class="text-danger">Overdue</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="completeMaintenance(3)">
                                                <i data-feather="check" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewTruckDetails(3)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="available">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="truck" class="icon-16 text-primary me-2"></i>
                                            <div>
                                                <strong>TRK-004</strong>
                                                <br><small class="text-muted">Kenworth T680</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>David Brown</strong>
                                            <br><small class="text-muted">CDL Class A</small>
                                        </div>
                                    </td>
                                    <td>
                                        <i data-feather="map-pin" class="icon-12 text-success"></i>
                                        Chicago Depot
                                        <br><small class="text-muted">Idle since 30 min</small>
                                    </td>
                                    <td><span class="badge bg-success">Available</span></td>
                                    <td>
                                        <span class="text-muted">-</span>
                                        <br><small class="text-muted">Ready for assignment</small>
                                    </td>
                                    <td>
                                        <?php echo format_to_date(date('Y-m-d', strtotime('+25 days')), false); ?>
                                        <br><small class="text-success">On schedule</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="assignShipment(4)">
                                                <i data-feather="arrow-right" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewTruckDetails(4)">
                                                <i data-feather="eye" class="icon-12"></i>
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
function addTruck() {
    alert('Add Truck feature coming soon!');
}

function truckMaintenance() {
    alert('Truck Maintenance feature coming soon!');
}

function assignShipment(id) {
    alert('Assign Shipment feature coming soon!');
}

function trackTruck(id) {
    alert('Track Truck feature coming soon!');
}

function viewTruckDetails(id) {
    alert('View Truck Details feature coming soon!');
}

function completeMaintenance(id) {
    if (confirm('Mark maintenance as completed?')) {
        alert('Complete Maintenance feature coming soon!');
    }
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active', 'btn-primary'));
            filterButtons.forEach(btn => btn.classList.add('btn-outline-secondary'));
            
            // Add active class to clicked button
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary', 'active');
            
            const filter = this.getAttribute('data-filter');
            filterTrucks(filter);
        });
    });
});

function filterTrucks(status) {
    const rows = document.querySelectorAll('#trucks-table tbody tr');
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            const rowStatus = row.getAttribute('data-status');
            if (rowStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}
</script>

<style>
.card-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-group .btn.active {
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

#trucks-table th {
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
</style>