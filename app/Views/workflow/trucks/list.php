<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i data-feather="truck" class="icon-16"></i>
            <?php echo app_lang('trucks_management'); ?>
        </h5>
        <?php if (get_array_value($permissions, "can_manage_workflow")) { ?>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#truckAllocationModal">
                <i data-feather="map-pin" class="icon-16"></i>
                <?php echo app_lang('allocate_truck'); ?>
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#truckModal">
                <i data-feather="plus" class="icon-16"></i>
                <?php echo app_lang('add_truck'); ?>
            </button>
        </div>
        <?php } ?>
    </div>
    <div class="card-body">
        <!-- Trucks Overview Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-primary">
                            <i data-feather="truck" class="icon-lg"></i>
                        </div>
                        <div class="widget-details">
                            <h1 id="total-trucks">0</h1>
                            <span class="bg-transparent-primary"><?php echo app_lang('total_trucks'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-success">
                            <i data-feather="check-circle" class="icon-lg"></i>
                        </div>
                        <div class="widget-details">
                            <h1 id="available-trucks">0</h1>
                            <span class="bg-transparent-success"><?php echo app_lang('available'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-warning">
                            <i data-feather="clock" class="icon-lg"></i>
                        </div>
                        <div class="widget-details">
                            <h1 id="in-transit-trucks">0</h1>
                            <span class="bg-transparent-warning"><?php echo app_lang('in_transit'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-danger">
                            <i data-feather="alert-triangle" class="icon-lg"></i>
                        </div>
                        <div class="widget-details">
                            <h1 id="maintenance-trucks">0</h1>
                            <span class="bg-transparent-danger"><?php echo app_lang('maintenance'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="status-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_statuses'); ?></option>
                    <option value="available"><?php echo app_lang('available'); ?></option>
                    <option value="in_transit"><?php echo app_lang('in_transit'); ?></option>
                    <option value="maintenance"><?php echo app_lang('maintenance'); ?></option>
                    <option value="out_of_service"><?php echo app_lang('out_of_service'); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="truck-type-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_truck_types'); ?></option>
                    <option value="trailer"><?php echo app_lang('trailer'); ?></option>
                    <option value="flatbed"><?php echo app_lang('flatbed'); ?></option>
                    <option value="container"><?php echo app_lang('container'); ?></option>
                    <option value="refrigerated"><?php echo app_lang('refrigerated'); ?></option>
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" id="truck-search" class="form-control" placeholder="<?php echo app_lang('search_trucks'); ?>">
                    <button class="btn btn-outline-secondary" type="button">
                        <i data-feather="search" class="icon-16"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="trucks-table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo app_lang('truck_number'); ?></th>
                        <th><?php echo app_lang('truck_type'); ?></th>
                        <th><?php echo app_lang('capacity'); ?></th>
                        <th><?php echo app_lang('driver_name'); ?></th>
                        <th><?php echo app_lang('driver_phone'); ?></th>
                        <th><?php echo app_lang('status'); ?></th>
                        <th><?php echo app_lang('current_location'); ?></th>
                        <th><?php echo app_lang('last_maintenance'); ?></th>
                        <th class="w100"><?php echo app_lang('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Truck Modal -->
<div class="modal fade" id="truckModal" tabindex="-1" aria-labelledby="truckModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="truckModalLabel"><?php echo app_lang('add_truck'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="truck-form" action="<?php echo get_uri("workflow/save_truck"); ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="truck_number" class="form-label"><?php echo app_lang('truck_number'); ?> *</label>
                                <input type="text" class="form-control" name="truck_number" id="truck_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="truck_type" class="form-label"><?php echo app_lang('truck_type'); ?> *</label>
                                <select name="truck_type" id="truck_type" class="form-select" required>
                                    <option value=""><?php echo app_lang('select_truck_type'); ?></option>
                                    <option value="trailer"><?php echo app_lang('trailer'); ?></option>
                                    <option value="flatbed"><?php echo app_lang('flatbed'); ?></option>
                                    <option value="container"><?php echo app_lang('container'); ?></option>
                                    <option value="refrigerated"><?php echo app_lang('refrigerated'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacity" class="form-label"><?php echo app_lang('capacity'); ?> (<?php echo app_lang('tons'); ?>)</label>
                                <input type="number" step="0.1" class="form-control" name="capacity" id="capacity">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="year_manufactured" class="form-label"><?php echo app_lang('year_manufactured'); ?></label>
                                <input type="number" class="form-control" name="year_manufactured" id="year_manufactured" min="1980" max="<?php echo date('Y'); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="driver_name" class="form-label"><?php echo app_lang('driver_name'); ?> *</label>
                                <input type="text" class="form-control" name="driver_name" id="driver_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="driver_phone" class="form-label"><?php echo app_lang('driver_phone'); ?></label>
                                <input type="tel" class="form-control" name="driver_phone" id="driver_phone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label"><?php echo app_lang('notes'); ?></label>
                                <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                            </div>
                        </div>
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

<!-- Truck Allocation Modal -->
<div class="modal fade" id="truckAllocationModal" tabindex="-1" aria-labelledby="allocationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allocationModalLabel"><?php echo app_lang('allocate_truck'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="allocation-form" action="<?php echo get_uri("workflow/allocate_truck"); ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="allocation_shipment_id" class="form-label"><?php echo app_lang('shipment'); ?> *</label>
                        <select name="shipment_id" id="allocation_shipment_id" class="form-select select2" required>
                            <option value=""><?php echo app_lang('select_shipment'); ?></option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="allocation_truck_id" class="form-label"><?php echo app_lang('truck'); ?> *</label>
                        <select name="truck_id" id="allocation_truck_id" class="form-select select2" required>
                            <option value=""><?php echo app_lang('select_truck'); ?></option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="allocation_date" class="form-label"><?php echo app_lang('allocation_date'); ?> *</label>
                                <input type="date" class="form-control" name="allocation_date" id="allocation_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expected_return_date" class="form-label"><?php echo app_lang('expected_return_date'); ?></label>
                                <input type="date" class="form-control" name="expected_return_date" id="expected_return_date">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="allocation_notes" class="form-label"><?php echo app_lang('notes'); ?></label>
                        <textarea class="form-control" name="notes" id="allocation_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo app_lang('allocate'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    // Load truck statistics
    loadTruckStats();
    
    // Initialize DataTable
    var trucksTable = $("#trucks-table").appTable({
        source: '<?php echo_uri("workflow/list_trucks") ?>',
        order: [[0, "asc"]],
        columns: [
            {title: '<?php echo app_lang("truck_number") ?>'},
            {title: '<?php echo app_lang("truck_type") ?>'},
            {title: '<?php echo app_lang("capacity") ?>'},
            {title: '<?php echo app_lang("driver_name") ?>'},
            {title: '<?php echo app_lang("driver_phone") ?>'},
            {title: '<?php echo app_lang("status") ?>'},
            {title: '<?php echo app_lang("current_location") ?>'},
            {title: '<?php echo app_lang("last_maintenance") ?>', "iDataSort": 7},
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
        ],
        printColumns: [0, 1, 2, 3, 4, 5, 6, 7],
        xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7]
    });

    // Filter handlers
    $("#status-filter, #truck-type-filter").on('change', function() {
        var statusFilter = $("#status-filter").val();
        var typeFilter = $("#truck-type-filter").val();
        
        var source = '<?php echo_uri("workflow/list_trucks") ?>';
        var params = [];
        
        if (statusFilter) params.push('status=' + statusFilter);
        if (typeFilter) params.push('truck_type=' + typeFilter);
        
        if (params.length > 0) {
            source += '?' + params.join('&');
        }
        
        $("#trucks-table").DataTable().ajax.url(source).load();
    });

    // Search handler
    $("#truck-search").on('keyup', function() {
        trucksTable.search(this.value).draw();
    });

    // Initialize select2 dropdowns
    $("#allocation_shipment_id").select2({
        placeholder: "<?php echo app_lang('select_shipment'); ?>",
        allowClear: true
    });

    $("#allocation_truck_id").select2({
        placeholder: "<?php echo app_lang('select_truck'); ?>",
        allowClear: true
    });

    // Handle truck form submission
    $("#truck-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#truckModal").modal('hide');
                $("#trucks-table").appTable({newData: result.data, dataId: result.id});
                appAlert.success(result.message);
                loadTruckStats(); // Refresh stats
            } else {
                appAlert.error(result.message);
            }
        }
    });

    // Handle allocation form submission
    $("#allocation-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#truckAllocationModal").modal('hide');
                $("#trucks-table").DataTable().ajax.reload();
                appAlert.success(result.message);
                loadTruckStats(); // Refresh stats
            } else {
                appAlert.error(result.message);
            }
        }
    });

    // Load truck statistics
    function loadTruckStats() {
        $.ajax({
            url: '<?php echo_uri("workflow/get_truck_stats") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var stats = response.data;
                    $("#total-trucks").text(stats.total || 0);
                    $("#available-trucks").text(stats.available || 0);
                    $("#in-transit-trucks").text(stats.in_transit || 0);
                    $("#maintenance-trucks").text(stats.maintenance || 0);
                }
            }
        });
    }

    // Auto-set expected return date based on allocation date
    $("#allocation_date").on('change', function() {
        var allocationDate = new Date($(this).val());
        if (allocationDate && !$("#expected_return_date").val()) {
            // Add 7 days as default return date
            allocationDate.setDate(allocationDate.getDate() + 7);
            var returnDate = allocationDate.toISOString().split('T')[0];
            $("#expected_return_date").val(returnDate);
        }
    });
});
</script>