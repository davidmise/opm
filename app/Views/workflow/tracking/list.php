<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i data-feather="map-pin" class="icon-16"></i>
            <?php echo app_lang('shipment_tracking'); ?>
        </h5>
        <?php if (get_array_value($permissions, "can_manage_workflow")) { ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#trackingModal">
            <i data-feather="plus" class="icon-16"></i>
            <?php echo app_lang('add_tracking_update'); ?>
        </button>
        <?php } ?>
    </div>
    <div class="card-body">
        <!-- Tracking Overview -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-primary">
                            <i data-feather="map" class="icon-lg"></i>
                        </div>
                        <div class="widget-details">
                            <h1 id="total-shipments-tracking">0</h1>
                            <span class="bg-transparent-primary"><?php echo app_lang('total_shipments'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-warning">
                            <i data-feather="truck" class="icon-lg"></i>
                        </div>
                        <div class="widget-details">
                            <h1 id="in-transit-tracking">0</h1>
                            <span class="bg-transparent-warning"><?php echo app_lang('in_transit'); ?></span>
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
                            <h1 id="delivered-tracking">0</h1>
                            <span class="bg-transparent-success"><?php echo app_lang('delivered'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-danger">
                            <i data-feather="alert-circle" class="icon-lg"></i>
                        </div>
                        <div class="widget-details">
                            <h1 id="delayed-tracking">0</h1>
                            <span class="bg-transparent-danger"><?php echo app_lang('delayed'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="shipment-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_shipments'); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="status-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_statuses'); ?></option>
                    <option value="picked_up"><?php echo app_lang('picked_up'); ?></option>
                    <option value="in_transit"><?php echo app_lang('in_transit'); ?></option>
                    <option value="at_customs"><?php echo app_lang('at_customs'); ?></option>
                    <option value="out_for_delivery"><?php echo app_lang('out_for_delivery'); ?></option>
                    <option value="delivered"><?php echo app_lang('delivered'); ?></option>
                    <option value="delayed"><?php echo app_lang('delayed'); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="truck-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_trucks'); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" id="date-filter" class="form-control" placeholder="<?php echo app_lang('filter_by_date'); ?>">
            </div>
        </div>

        <!-- Real-time Map View Toggle -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="viewMode" id="listView" checked>
                    <label class="btn btn-outline-primary" for="listView">
                        <i data-feather="list" class="icon-16"></i>
                        <?php echo app_lang('list_view'); ?>
                    </label>
                    
                    <input type="radio" class="btn-check" name="viewMode" id="mapView">
                    <label class="btn btn-outline-primary" for="mapView">
                        <i data-feather="map" class="icon-16"></i>
                        <?php echo app_lang('map_view'); ?>
                    </label>
                </div>
            </div>
        </div>

        <!-- List View -->
        <div id="tracking-list-view">
            <div class="table-responsive">
                <table id="tracking-table" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><?php echo app_lang('shipment_number'); ?></th>
                            <th><?php echo app_lang('truck_number'); ?></th>
                            <th><?php echo app_lang('current_location'); ?></th>
                            <th><?php echo app_lang('status'); ?></th>
                            <th><?php echo app_lang('last_updated'); ?></th>
                            <th><?php echo app_lang('estimated_arrival'); ?></th>
                            <th><?php echo app_lang('progress'); ?></th>
                            <th><?php echo app_lang('updated_by'); ?></th>
                            <th class="w100"><?php echo app_lang('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Map View -->
        <div id="tracking-map-view" style="display: none;">
            <div class="row">
                <div class="col-md-8">
                    <div id="tracking-map" style="height: 500px; width: 100%; border: 1px solid #ddd; border-radius: 8px;">
                        <!-- Map will be loaded here -->
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <i data-feather="map" class="icon-48 text-muted"></i>
                                <p class="text-muted mt-2"><?php echo app_lang('map_loading'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><?php echo app_lang('active_shipments'); ?></h6>
                        </div>
                        <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                            <div id="active-shipments-list">
                                <!-- Active shipments will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Tracking Update Modal -->
<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trackingModalLabel"><?php echo app_lang('add_tracking_update'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tracking-form" action="<?php echo get_uri("workflow/save_tracking_update"); ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tracking_shipment_id" class="form-label"><?php echo app_lang('shipment'); ?> *</label>
                                <select name="shipment_id" id="tracking_shipment_id" class="form-select select2" required>
                                    <option value=""><?php echo app_lang('select_shipment'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tracking_status" class="form-label"><?php echo app_lang('status'); ?> *</label>
                                <select name="status" id="tracking_status" class="form-select" required>
                                    <option value=""><?php echo app_lang('select_status'); ?></option>
                                    <option value="picked_up"><?php echo app_lang('picked_up'); ?></option>
                                    <option value="in_transit"><?php echo app_lang('in_transit'); ?></option>
                                    <option value="at_customs"><?php echo app_lang('at_customs'); ?></option>
                                    <option value="out_for_delivery"><?php echo app_lang('out_for_delivery'); ?></option>
                                    <option value="delivered"><?php echo app_lang('delivered'); ?></option>
                                    <option value="delayed"><?php echo app_lang('delayed'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="current_location" class="form-label"><?php echo app_lang('current_location'); ?> *</label>
                                <input type="text" class="form-control" name="current_location" id="current_location" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estimated_arrival" class="form-label"><?php echo app_lang('estimated_arrival'); ?></label>
                                <input type="datetime-local" class="form-control" name="estimated_arrival" id="estimated_arrival">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label"><?php echo app_lang('latitude'); ?></label>
                                <input type="number" step="any" class="form-control" name="latitude" id="latitude">
                                <div class="form-text"><?php echo app_lang('optional_gps_coordinates'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label"><?php echo app_lang('longitude'); ?></label>
                                <input type="number" step="any" class="form-control" name="longitude" id="longitude">
                                <div class="form-text"><?php echo app_lang('optional_gps_coordinates'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="tracking_notes" class="form-label"><?php echo app_lang('notes'); ?></label>
                                <textarea class="form-control" name="notes" id="tracking_notes" rows="3" 
                                          placeholder="<?php echo app_lang('tracking_notes_placeholder'); ?>"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Automatic location detection -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-outline-info" id="detect-location">
                                <i data-feather="map-pin" class="icon-16"></i>
                                <?php echo app_lang('detect_current_location'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="icon-16"></i>
                        <?php echo app_lang('save_update'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    // Load tracking statistics
    loadTrackingStats();
    
    // Initialize DataTable
    var trackingTable = $("#tracking-table").appTable({
        source: '<?php echo_uri("workflow/list_tracking") ?>',
        order: [[4, "desc"]],
        columns: [
            {title: '<?php echo app_lang("shipment_number") ?>'},
            {title: '<?php echo app_lang("truck_number") ?>'},
            {title: '<?php echo app_lang("current_location") ?>'},
            {title: '<?php echo app_lang("status") ?>'},
            {title: '<?php echo app_lang("last_updated") ?>', "iDataSort": 4},
            {title: '<?php echo app_lang("estimated_arrival") ?>', "iDataSort": 5},
            {title: '<?php echo app_lang("progress") ?>'},
            {title: '<?php echo app_lang("updated_by") ?>'},
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
        ],
        printColumns: [0, 1, 2, 3, 4, 5, 6, 7],
        xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7]
    });

    // View mode toggle
    $('input[name="viewMode"]').on('change', function() {
        if ($("#listView").is(':checked')) {
            $("#tracking-list-view").show();
            $("#tracking-map-view").hide();
        } else {
            $("#tracking-list-view").hide();
            $("#tracking-map-view").show();
            loadTrackingMap();
        }
    });

    // Filter handlers
    $("#shipment-filter, #status-filter, #truck-filter, #date-filter").on('change', function() {
        var shipmentFilter = $("#shipment-filter").val();
        var statusFilter = $("#status-filter").val();
        var truckFilter = $("#truck-filter").val();
        var dateFilter = $("#date-filter").val();
        
        var source = '<?php echo_uri("workflow/list_tracking") ?>';
        var params = [];
        
        if (shipmentFilter) params.push('shipment_id=' + shipmentFilter);
        if (statusFilter) params.push('status=' + statusFilter);
        if (truckFilter) params.push('truck_id=' + truckFilter);
        if (dateFilter) params.push('date=' + dateFilter);
        
        if (params.length > 0) {
            source += '?' + params.join('&');
        }
        
        $("#tracking-table").DataTable().ajax.url(source).load();
    });

    // Initialize select2 dropdowns
    $("#tracking_shipment_id, #shipment-filter").select2({
        placeholder: "<?php echo app_lang('select_shipment'); ?>",
        allowClear: true
    });

    $("#truck-filter").select2({
        placeholder: "<?php echo app_lang('select_truck'); ?>",
        allowClear: true
    });

    // Handle form submission
    $("#tracking-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#trackingModal").modal('hide');
                $("#tracking-table").appTable({newData: result.data, dataId: result.id});
                appAlert.success(result.message);
                loadTrackingStats(); // Refresh stats
                // If map is active, refresh it
                if ($("#mapView").is(':checked')) {
                    loadTrackingMap();
                }
            } else {
                appAlert.error(result.message);
            }
        }
    });

    // Geolocation detection
    $("#detect-location").on('click', function() {
        if (navigator.geolocation) {
            $(this).prop('disabled', true).html('<i data-feather="loader" class="icon-16"></i> <?php echo app_lang("detecting_location"); ?>');
            
            navigator.geolocation.getCurrentPosition(function(position) {
                $("#latitude").val(position.coords.latitude);
                $("#longitude").val(position.coords.longitude);
                
                // Reverse geocoding to get location name (optional - requires API)
                // reverseGeocode(position.coords.latitude, position.coords.longitude);
                
                $("#detect-location").prop('disabled', false).html('<i data-feather="map-pin" class="icon-16"></i> <?php echo app_lang("detect_current_location"); ?>');
                appAlert.success("<?php echo app_lang('location_detected'); ?>");
            }, function(error) {
                $("#detect-location").prop('disabled', false).html('<i data-feather="map-pin" class="icon-16"></i> <?php echo app_lang("detect_current_location"); ?>');
                appAlert.error("<?php echo app_lang('location_detection_failed'); ?>");
            });
        } else {
            appAlert.error("<?php echo app_lang('geolocation_not_supported'); ?>");
        }
    });

    // Load tracking statistics
    function loadTrackingStats() {
        $.ajax({
            url: '<?php echo_uri("workflow/get_tracking_stats") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var stats = response.data;
                    $("#total-shipments-tracking").text(stats.total || 0);
                    $("#in-transit-tracking").text(stats.in_transit || 0);
                    $("#delivered-tracking").text(stats.delivered || 0);
                    $("#delayed-tracking").text(stats.delayed || 0);
                }
            }
        });
    }

    // Load tracking map (placeholder - would integrate with Google Maps or similar)
    function loadTrackingMap() {
        $.ajax({
            url: '<?php echo_uri("workflow/get_active_shipments_for_map") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var shipments = response.data;
                    var listHtml = '';
                    
                    if (shipments.length > 0) {
                        shipments.forEach(function(shipment) {
                            var statusClass = getStatusClass(shipment.status);
                            listHtml += '<div class="border-bottom pb-2 mb-2">';
                            listHtml += '<h6 class="mb-1">' + shipment.shipment_number + '</h6>';
                            listHtml += '<p class="mb-1 text-muted small">' + shipment.current_location + '</p>';
                            listHtml += '<span class="badge ' + statusClass + '">' + shipment.status + '</span>';
                            listHtml += '</div>';
                        });
                    } else {
                        listHtml = '<p class="text-muted text-center"><?php echo app_lang("no_active_shipments"); ?></p>';
                    }
                    
                    $("#active-shipments-list").html(listHtml);
                }
            }
        });
    }

    function getStatusClass(status) {
        switch(status) {
            case 'delivered': return 'bg-success';
            case 'in_transit': return 'bg-primary';
            case 'delayed': return 'bg-danger';
            case 'at_customs': return 'bg-warning';
            default: return 'bg-secondary';
        }
    }

    // Auto-refresh tracking data every 5 minutes
    setInterval(function() {
        if ($("#listView").is(':checked')) {
            $("#tracking-table").DataTable().ajax.reload(null, false);
        } else {
            loadTrackingMap();
        }
        loadTrackingStats();
    }, 300000); // 5 minutes
});
</script>