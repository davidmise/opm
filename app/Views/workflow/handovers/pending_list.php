<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('pending_handovers'); ?></h4>
    </div>
    <div class="card-body">
        <?php if (count($handovers) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo app_lang('shipment'); ?></th>
                            <th><?php echo app_lang('from_department'); ?></th>
                            <th><?php echo app_lang('phase_transition'); ?></th>
                            <th><?php echo app_lang('initiated_by'); ?></th>
                            <th><?php echo app_lang('checklist_progress'); ?></th>
                            <th><?php echo app_lang('created'); ?></th>
                            <th><?php echo app_lang('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($handovers as $handover): ?>
                        <tr>
                            <td>
                                <a href="<?php echo get_uri('workflow/shipment_details/' . $handover->shipment_id); ?>">
                                    <?php echo $handover->shipment_number; ?>
                                </a>
                            </td>
                            <td><?php echo $handover->from_department_name; ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo app_lang($handover->from_phase); ?></span>
                                <i data-feather="arrow-right" class="icon-14"></i>
                                <span class="badge bg-primary"><?php echo app_lang($handover->to_phase); ?></span>
                            </td>
                            <td><?php echo $handover->initiated_by_name; ?></td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar <?php echo $handover->checklist_completion == 100 ? 'bg-success' : 'bg-info'; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $handover->checklist_completion; ?>%"
                                         aria-valuenow="<?php echo $handover->checklist_completion; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo $handover->checklist_completion; ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?php echo format_to_relative_time($handover->created_at); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-info view-checklist" 
                                            data-id="<?php echo $handover->id; ?>">
                                        <i data-feather="list" class="icon-16"></i> <?php echo app_lang('checklist'); ?>
                                    </button>
                                    <?php if ($handover->checklist_completion == 100): ?>
                                    <button type="button" class="btn btn-sm btn-success approve-handover" 
                                            data-id="<?php echo $handover->id; ?>">
                                        <i data-feather="check" class="icon-16"></i> <?php echo app_lang('approve'); ?>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-sm btn-danger reject-handover" 
                                            data-id="<?php echo $handover->id; ?>">
                                        <i data-feather="x" class="icon-16"></i> <?php echo app_lang('reject'); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center text-muted p20">
                <i data-feather="inbox" class="icon-32"></i>
                <p><?php echo app_lang('no_pending_handovers'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // View checklist
        $('.view-checklist').click(function() {
            var handoverId = $(this).data('id');
            // Load checklist modal (to be implemented)
            appAlert.warning('<?php echo app_lang('checklist_modal_coming_soon'); ?>');
        });

        // Approve handover
        $('.approve-handover').click(function() {
            var handoverId = $(this).data('id');
            
            var notes = prompt('<?php echo app_lang('enter_approval_notes'); ?>');
            if (notes !== null) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/approve_handover'); ?>',
                    type: 'POST',
                    data: {handover_id: handoverId, approval_notes: notes},
                    success: function(result) {
                        if (result.success) {
                            appAlert.success(result.message, {duration: 10000});
                            location.reload();
                        } else {
                            appAlert.error(result.message);
                        }
                    }
                });
            }
        });

        // Reject handover
        $('.reject-handover').click(function() {
            var handoverId = $(this).data('id');
            
            var reason = prompt('<?php echo app_lang('enter_rejection_reason'); ?>');
            if (reason) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/reject_handover'); ?>',
                    type: 'POST',
                    data: {handover_id: handoverId, rejection_reason: reason},
                    success: function(result) {
                        if (result.success) {
                            appAlert.success(result.message, {duration: 10000});
                            location.reload();
                        } else {
                            appAlert.error(result.message);
                        }
                    }
                });
            }
        });

        feather.replace();
    });
</script>
