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
                        <table class="table table-hover display" id="shipments-table">
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
                                <!-- Data will be loaded via DataTables AJAX -->
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

// Initialize DataTable when page loads
$(document).ready(function() {
    $('#shipments-table').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?php echo get_uri('workflow/list_shipments'); ?>",
            "type": "POST"
        },
        "columns": [
            {
                "data": "shipment_number",
                "render": function(data, type, row) {
                    return '<a href="<?php echo get_uri("workflow/shipment_details/"); ?>' + row.id + '" class="text-primary">' + data + '</a>';
                }
            },
            {"data": "client_name"},
            {"data": "cargo_type"},
            {
                "data": "status",
                "render": function(data, type, row) {
                    var badgeClass = 'secondary';
                    switch(data) {
                        case 'active':
                            badgeClass = 'primary';
                            break;
                        case 'completed':
                            badgeClass = 'success';
                            break;
                        case 'cancelled':
                            badgeClass = 'warning';
                            break;
                    }
                    return '<span class="badge bg-' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                }
            },
            {
                "data": "current_phase",
                "render": function(data, type, row) {
                    return data ? data.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'N/A';
                }
            },
            {"data": "created_at"},
            {
                "data": "id",
                "orderable": false,
                "render": function(data, type, row) {
                    return '<div class="btn-group">' +
                        '<button type="button" class="btn btn-sm btn-outline-primary" onclick="viewShipment(' + data + ')" title="View Details">' +
                        '<i data-feather="eye" class="icon-12"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="editShipment(' + data + ')" title="Edit">' +
                        '<i data-feather="edit" class="icon-12"></i>' +
                        '</button>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteShipment(' + data + ')" title="Delete">' +
                        '<i data-feather="trash-2" class="icon-12"></i>' +
                        '</button>' +
                        '</div>';
                }
            }
        ],
        "order": [[5, "desc"]], // Order by created date descending
        "pageLength": 25,
        "responsive": true,
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        "language": {
            "processing": "Loading shipments...",
            "emptyTable": "No shipments found",
            "zeroRecords": "No matching shipments found"
        },
        "drawCallback": function(settings) {
            // Re-initialize Feather icons after table redraw
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    });
});

function filterShipments(status) {
    var table = $('#shipments-table').DataTable();
    if (status === 'all') {
        table.column(3).search('').draw(); // Clear search on status column
    } else {
        table.column(3).search(status).draw(); // Search in status column
    }
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