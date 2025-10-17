<?php
/**
 * Workflow Shipments View
 */
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <div class="title-button-group">
            <h1 class="page-title"><?php echo app_lang('shipments'); ?></h1>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="addShipment()">
                    <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('add_shipment'); ?>
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="exportShipments()">
                    <i data-feather="download" class="icon-16"></i> <?php echo app_lang('export'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('all_shipments'); ?></h4>
                    <div class="card-header-actions">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="all">
                                <?php echo app_lang('all'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="active">
                                <?php echo app_lang('active'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="pending">
                                <?php echo app_lang('pending'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="completed">
                                <?php echo app_lang('completed'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="shipments-table">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('shipment_number'); ?></th>
                                    <th><?php echo app_lang('client'); ?></th>
                                    <th><?php echo app_lang('cargo_type'); ?></th>
                                    <th><?php echo app_lang('status'); ?></th>
                                    <th><?php echo app_lang('current_phase'); ?></th>
                                    <th><?php echo app_lang('created_date'); ?></th>
                                    <th><?php echo app_lang('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data - will be replaced with dynamic data -->
                                <tr>
                                    <td><a href="#" class="text-primary">SHP-2025-001</a></td>
                                    <td>ABC Trading Ltd</td>
                                    <td>Electronics</td>
                                    <td><span class="badge bg-primary">Active</span></td>
                                    <td>Clearing Intake</td>
                                    <td><?php echo format_to_date(date('Y-m-d'), false); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewShipment(1)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editShipment(1)">
                                                <i data-feather="edit" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteShipment(1)">
                                                <i data-feather="trash-2" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-primary">SHP-2025-002</a></td>
                                    <td>XYZ Imports</td>
                                    <td>Textiles</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>Regulatory Processing</td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('-1 day')), false); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewShipment(2)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editShipment(2)">
                                                <i data-feather="edit" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteShipment(2)">
                                                <i data-feather="trash-2" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="#" class="text-primary">SHP-2025-003</a></td>
                                    <td>Global Logistics Corp</td>
                                    <td>Machinery</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>Delivery Complete</td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('-3 days')), false); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewShipment(3)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="downloadDocuments(3)">
                                                <i data-feather="download" class="icon-12"></i>
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
function addShipment() {
    // Open modal form for adding shipment
    $.ajax({
        url: '<?php echo get_uri("workflow/shipment_modal_form"); ?>',
        type: 'POST',
        dataType: 'html',
        success: function(result) {
            var $modal = $('<div class="modal fade" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"></div></div></div>');
            $modal.find('.modal-content').html(result);
            $modal.modal('show');
            
            // When modal is hidden, remove it from DOM
            $modal.on('hidden.bs.modal', function () {
                $modal.remove();
            });
            
            // The form submission is handled by appForm plugin in modal_form.php
            // It will show the toast notification and refresh the tab automatically
        }
    });
}

function viewShipment(id) {
    window.location.href = '<?php echo get_uri("workflow/shipment_details/"); ?>' + id;
}

function editShipment(id) {
    // Open modal form for editing shipment
    $.ajax({
        url: '<?php echo get_uri("workflow/shipment_modal_form"); ?>',
        type: 'POST',
        data: {id: id},
        dataType: 'html',
        success: function(result) {
            var $modal = $('<div class="modal fade" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"></div></div></div>');
            $modal.find('.modal-content').html(result);
            $modal.modal('show');
            
            // When modal is hidden, remove it from DOM
            $modal.on('hidden.bs.modal', function () {
                $modal.remove();
            });
            
            // The form submission is handled by appForm plugin in modal_form.php
            // It will show the toast notification and refresh the tab automatically
        }
    });
}

function deleteShipment(id) {
    if (confirm('Are you sure you want to delete this shipment?')) {
        $.ajax({
            url: '<?php echo get_uri("workflow/delete_shipment"); ?>',
            type: 'POST',
            data: {id: id},
            dataType: 'json',
            success: function(result) {
                if (result && result.success) {
                    if (typeof appAlert !== 'undefined') {
                        appAlert.success(result.message);
                    } else {
                        alert(result.message);
                    }
                    // Refresh the tab content without page reload
                    setTimeout(function() {
                        $('[data-bs-target="#workflow-shipments-tab"]').trigger('click');
                    }, 500);
                } else {
                    if (typeof appAlert !== 'undefined') {
                        appAlert.error(result.message || 'An error occurred');
                    } else {
                        alert(result.message || 'An error occurred');
                    }
                }
            },
            error: function() {
                if (typeof appAlert !== 'undefined') {
                    appAlert.error('An error occurred while deleting');
                } else {
                    alert('An error occurred while deleting');
                }
            }
        });
    }
}

function exportShipments() {
    alert('Export feature coming soon!');
}

function downloadDocuments(id) {
    alert('Download Documents feature coming soon!');
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
            filterShipments(filter);
        });
    });
});

function filterShipments(status) {
    const rows = document.querySelectorAll('#shipments-table tbody tr');
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            const statusCell = row.querySelector('td:nth-child(4) .badge');
            const shipmentStatus = statusCell.textContent.toLowerCase();
            if (shipmentStatus.includes(status)) {
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

#shipments-table th {
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