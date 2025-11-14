<div class="page-content clearfix">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h4><?php echo app_lang('shipment_details'); ?></h4>
                </div>
            </div>

            <?php if (!empty($shipment_details)) { ?>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5><?php echo app_lang('shipment_information'); ?></h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong><?php echo app_lang('shipment_number'); ?>:</strong></td>
                                <td><?php echo $shipment_details->shipment_number ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('client'); ?>:</strong></td>
                                <td><?php echo $shipment_details->company_name ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('cargo_type'); ?>:</strong></td>
                                <td><?php echo $shipment_details->cargo_type ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('cargo_weight'); ?>:</strong></td>
                                <td><?php echo $shipment_details->cargo_weight ?? 'N/A'; ?> kg</td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('status'); ?>:</strong></td>
                                <td>
                                    <span class="badge bg-<?php echo $shipment_details->status == 'active' ? 'primary' : ($shipment_details->status == 'completed' ? 'success' : 'secondary'); ?>">
                                        <?php echo ucfirst($shipment_details->status ?? 'N/A'); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><?php echo app_lang('shipping_information'); ?></h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong><?php echo app_lang('origin_port'); ?>:</strong></td>
                                <td><?php echo $shipment_details->origin_port ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('destination_port'); ?>:</strong></td>
                                <td><?php echo $shipment_details->destination_port ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('final_destination'); ?>:</strong></td>
                                <td><?php echo $shipment_details->final_destination ?? 'N/A'; ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('current_phase'); ?>:</strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo ucfirst(str_replace('_', ' ', $shipment_details->current_phase ?? 'N/A')); ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?php echo app_lang('assigned_to'); ?>:</strong></td>
                                <td><?php echo ($shipment_details->first_name ?? '') . ' ' . ($shipment_details->last_name ?? ''); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5><?php echo app_lang('documents'); ?></h5>
                        <?php if (!empty($workflow_documents)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang('document_name'); ?></th>
                                            <th><?php echo app_lang('document_type'); ?></th>
                                            <th><?php echo app_lang('uploaded_by'); ?></th>
                                            <th><?php echo app_lang('upload_date'); ?></th>
                                            <th><?php echo app_lang('status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($workflow_documents as $document) { ?>
                                            <tr>
                                                <td><?php echo $document->document_name ?? 'N/A'; ?></td>
                                                <td><?php echo ucfirst(str_replace('_', ' ', $document->document_type ?? 'N/A')); ?></td>
                                                <td><?php echo ($document->first_name ?? '') . ' ' . ($document->last_name ?? ''); ?></td>
                                                <td><?php echo $document->upload_date ?? 'N/A'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $document->status == 'approved' ? 'success' : ($document->status == 'rejected' ? 'danger' : 'warning'); ?>">
                                                        <?php echo ucfirst($document->status ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo app_lang('no_documents_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5><?php echo app_lang('tasks'); ?></h5>
                        <?php if (!empty($workflow_tasks)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang('task_name'); ?></th>
                                            <th><?php echo app_lang('assigned_to'); ?></th>
                                            <th><?php echo app_lang('phase'); ?></th>
                                            <th><?php echo app_lang('status'); ?></th>
                                            <th><?php echo app_lang('priority'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($workflow_tasks as $task) { ?>
                                            <tr>
                                                <td><?php echo $task->task_name ?? 'N/A'; ?></td>
                                                <td><?php echo ($task->assigned_first_name ?? '') . ' ' . ($task->assigned_last_name ?? ''); ?></td>
                                                <td><?php echo ucfirst(str_replace('_', ' ', $task->phase_name ?? 'N/A')); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $task->status == 'completed' ? 'success' : ($task->status == 'in_progress' ? 'primary' : 'warning'); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $task->status ?? 'N/A')); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $task->priority == 'urgent' ? 'danger' : ($task->priority == 'high' ? 'warning' : 'secondary'); ?>">
                                                        <?php echo ucfirst($task->priority ?? 'N/A'); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo app_lang('no_tasks_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>

                <!-- Tracking Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5><?php echo app_lang('tracking_reports'); ?></h5>
                        <?php if (!empty($tracking_reports)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang('location'); ?></th>
                                            <th><?php echo app_lang('status_update'); ?></th>
                                            <th><?php echo app_lang('updated_by'); ?></th>
                                            <th><?php echo app_lang('timestamp'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tracking_reports as $tracking) { ?>
                                            <tr>
                                                <td><?php echo $tracking->location ?? 'N/A'; ?></td>
                                                <td><?php echo $tracking->status_update ?? 'N/A'; ?></td>
                                                <td><?php echo ($tracking->first_name ?? '') . ' ' . ($tracking->last_name ?? ''); ?></td>
                                                <td><?php echo $tracking->timestamp ?? 'N/A'; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo app_lang('no_tracking_reports_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>

                <!-- Truck Allocations Section -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5><?php echo app_lang('truck_allocations'); ?></h5>
                        <?php if (!empty($truck_allocations)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang('truck_number'); ?></th>
                                            <th><?php echo app_lang('driver_name'); ?></th>
                                            <th><?php echo app_lang('driver_phone'); ?></th>
                                            <th><?php echo app_lang('allocated_at'); ?></th>
                                            <th><?php echo app_lang('status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($truck_allocations as $allocation) { ?>
                                            <tr>
                                                <td><?php echo $allocation->truck_number ?? 'N/A'; ?></td>
                                                <td><?php echo $allocation->driver_name ?? 'N/A'; ?></td>
                                                <td><?php echo $allocation->driver_phone ?? 'N/A'; ?></td>
                                                <td><?php echo $allocation->allocated_at ?? 'N/A'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $allocation->status == 'delivered' ? 'success' : ($allocation->status == 'in_transit' ? 'primary' : 'warning'); ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $allocation->status ?? 'N/A')); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo app_lang('no_truck_allocations_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>

            <?php } else { ?>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <p class="text-muted text-center"><?php echo app_lang('shipment_not_found'); ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>