<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('document_template_generation'); ?></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Loading Order (Task 18) -->
            <div class="col-md-6 mb-3">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5><i data-feather="file-text" class="icon-16"></i> <?php echo app_lang('loading_order'); ?></h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo app_lang('loading_order_description'); ?></p>
                        <ul>
                            <li><?php echo app_lang('shipment_details'); ?></li>
                            <li><?php echo app_lang('cargo_information'); ?></li>
                            <li><?php echo app_lang('truck_allocation'); ?></li>
                            <li><?php echo app_lang('loading_instructions'); ?></li>
                        </ul>
                        <button class="btn btn-primary w-100 generate-loading-order" data-shipment-id="<?php echo $shipment_id; ?>">
                            <i data-feather="download" class="icon-16"></i> <?php echo app_lang('generate_loading_order'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tracking Report (Task 20) -->
            <div class="col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5><i data-feather="map-pin" class="icon-16"></i> <?php echo app_lang('tracking_report'); ?></h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo app_lang('tracking_report_description'); ?></p>
                        <ul>
                            <li><?php echo app_lang('current_location'); ?></li>
                            <li><?php echo app_lang('gps_coordinates'); ?></li>
                            <li><?php echo app_lang('movement_history'); ?></li>
                            <li><?php echo app_lang('eta_calculation'); ?></li>
                        </ul>
                        <button class="btn btn-info w-100 generate-tracking-report" data-shipment-id="<?php echo $shipment_id; ?>">
                            <i data-feather="download" class="icon-16"></i> <?php echo app_lang('generate_tracking_report'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generated Documents List -->
        <?php if (count($generated_documents) > 0): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h5><?php echo app_lang('generated_documents'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?php echo app_lang('document_name'); ?></th>
                                <th><?php echo app_lang('type'); ?></th>
                                <th><?php echo app_lang('generated_by'); ?></th>
                                <th><?php echo app_lang('generated_date'); ?></th>
                                <th><?php echo app_lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($generated_documents as $doc): ?>
                            <tr>
                                <td><?php echo $doc->document_name; ?></td>
                                <td><span class="badge bg-secondary"><?php echo app_lang($doc->document_type); ?></span></td>
                                <td><?php echo $doc->generated_by_name; ?></td>
                                <td><?php echo format_to_datetime($doc->created_at); ?></td>
                                <td>
                                    <a href="<?php echo get_uri('workflow/download_document/' . $doc->id); ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i data-feather="download" class="icon-16"></i> <?php echo app_lang('download'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Generate Loading Order
        $('.generate-loading-order').click(function() {
            var shipmentId = $(this).data('shipment-id');
            var $btn = $(this);
            
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo app_lang('generating'); ?>');
            
            $.ajax({
                url: '<?php echo get_uri('workflow/generate_loading_order'); ?>',
                type: 'POST',
                data: {shipment_id: shipmentId},
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message, {duration: 10000});
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        appAlert.error(result.message);
                        $btn.prop('disabled', false).html('<i data-feather="download" class="icon-16"></i> <?php echo app_lang('generate_loading_order'); ?>');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i data-feather="download" class="icon-16"></i> <?php echo app_lang('generate_loading_order'); ?>');
                }
            });
        });

        // Generate Tracking Report
        $('.generate-tracking-report').click(function() {
            var shipmentId = $(this).data('shipment-id');
            var $btn = $(this);
            
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo app_lang('generating'); ?>');
            
            $.ajax({
                url: '<?php echo get_uri('workflow/generate_tracking_report'); ?>',
                type: 'POST',
                data: {shipment_id: shipmentId},
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message, {duration: 10000});
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        appAlert.error(result.message);
                        $btn.prop('disabled', false).html('<i data-feather="download" class="icon-16"></i> <?php echo app_lang('generate_tracking_report'); ?>');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<i data-feather="download" class="icon-16"></i> <?php echo app_lang('generate_tracking_report'); ?>');
                }
            });
        });

        feather.replace();
    });
</script>
