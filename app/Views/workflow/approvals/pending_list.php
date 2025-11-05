<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('my_pending_approvals'); ?></h4>
        <span class="badge bg-warning float-end"><?php echo count($approvals); ?> <?php echo app_lang('pending'); ?></span>
    </div>
    <div class="card-body">
        <?php if (count($approvals) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo app_lang('type'); ?></th>
                            <th><?php echo app_lang('reference'); ?></th>
                            <th><?php echo app_lang('requested_by'); ?></th>
                            <th><?php echo app_lang('request_notes'); ?></th>
                            <th><?php echo app_lang('current_step'); ?></th>
                            <th><?php echo app_lang('requested'); ?></th>
                            <th><?php echo app_lang('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvals as $approval): ?>
                        <tr>
                            <td><span class="badge bg-info"><?php echo app_lang($approval->approval_type); ?></span></td>
                            <td>
                                <?php if ($approval->shipment_id): ?>
                                    <a href="<?php echo get_uri('workflow/shipment_details/' . $approval->shipment_id); ?>">
                                        <?php echo $approval->shipment_number; ?>
                                    </a>
                                <?php elseif ($approval->task_id): ?>
                                    <a href="<?php echo get_uri('tasks/view/' . $approval->task_id); ?>">
                                        Task #<?php echo $approval->task_id; ?>
                                    </a>
                                <?php elseif ($approval->document_id): ?>
                                    Document #<?php echo $approval->document_id; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $approval->requested_by_name; ?></td>
                            <td><?php echo character_limiter($approval->request_notes, 50); ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    Step <?php echo $approval->current_step; ?> of <?php echo $approval->total_steps; ?>
                                </span>
                            </td>
                            <td><?php echo format_to_relative_time($approval->created_at); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-success approve-request" 
                                            data-id="<?php echo $approval->id; ?>">
                                        <i data-feather="check" class="icon-16"></i> <?php echo app_lang('approve'); ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger reject-request" 
                                            data-id="<?php echo $approval->id; ?>">
                                        <i data-feather="x" class="icon-16"></i> <?php echo app_lang('reject'); ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info view-details" 
                                            data-id="<?php echo $approval->id; ?>">
                                        <i data-feather="eye" class="icon-16"></i> <?php echo app_lang('details'); ?>
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
                <i data-feather="check-circle" class="icon-32"></i>
                <p><?php echo app_lang('no_pending_approvals'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Approve request
        $('.approve-request').click(function() {
            var approvalId = $(this).data('id');
            
            var notes = prompt('<?php echo app_lang('enter_approval_notes_optional'); ?>');
            if (notes !== null) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/approve_request'); ?>',
                    type: 'POST',
                    data: {approval_id: approvalId, approval_notes: notes},
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

        // Reject request
        $('.reject-request').click(function() {
            var approvalId = $(this).data('id');
            
            var reason = prompt('<?php echo app_lang('enter_rejection_reason'); ?>');
            if (reason) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/reject_request'); ?>',
                    type: 'POST',
                    data: {approval_id: approvalId, rejection_reason: reason},
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

        // View details
        $('.view-details').click(function() {
            var approvalId = $(this).data('id');
            // Open approval details modal (to be implemented)
            appAlert.warning('<?php echo app_lang('approval_details_modal_coming_soon'); ?>');
        });

        feather.replace();
    });
</script>
