<div class="card dashboard-icon-widget">
    <div class="card-body">
        <div class="widget-icon bg-warning">
            <i data-feather="alert-triangle"></i>
        </div>
        <div class="widget-details">
            <h1 id="my-escalations-count"><?php echo count($escalations); ?></h1>
            <span class="bg-transparent-white"><?php echo app_lang('my_pending_escalations'); ?></span>
        </div>
    </div>
</div>

<div class="card mt15">
    <div class="card-header">
        <h4><?php echo app_lang('my_escalations'); ?></h4>
    </div>
    <div class="card-body">
        <?php if (count($escalations) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo app_lang('type'); ?></th>
                            <th><?php echo app_lang('reference'); ?></th>
                            <th><?php echo app_lang('reason'); ?></th>
                            <th><?php echo app_lang('escalated_by'); ?></th>
                            <th><?php echo app_lang('level'); ?></th>
                            <th><?php echo app_lang('priority'); ?></th>
                            <th><?php echo app_lang('created'); ?></th>
                            <th><?php echo app_lang('actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($escalations as $escalation): ?>
                        <tr>
                            <td><?php echo ucfirst($escalation->escalation_type); ?></td>
                            <td>
                                <?php if ($escalation->escalation_type === 'task'): ?>
                                    <a href="<?php echo get_uri('tasks/view/' . $escalation->task_id); ?>">
                                        Task #<?php echo $escalation->task_id; ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo get_uri('workflow/shipment_details/' . $escalation->shipment_id); ?>">
                                        <?php echo $escalation->shipment_number; ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo character_limiter($escalation->escalation_reason, 50); ?></td>
                            <td><?php echo $escalation->escalated_by_name; ?></td>
                            <td><span class="badge bg-info"><?php echo app_lang($escalation->escalation_level); ?></span></td>
                            <td>
                                <?php
                                $priority_class = array(
                                    'low' => 'secondary',
                                    'medium' => 'info',
                                    'high' => 'warning',
                                    'urgent' => 'danger'
                                );
                                ?>
                                <span class="badge bg-<?php echo $priority_class[$escalation->priority]; ?>">
                                    <?php echo app_lang($escalation->priority); ?>
                                </span>
                            </td>
                            <td><?php echo format_to_relative_time($escalation->created_at); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-success acknowledge-escalation" data-id="<?php echo $escalation->id; ?>">
                                        <i data-feather="check" class="icon-16"></i> <?php echo app_lang('acknowledge'); ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary resolve-escalation" data-id="<?php echo $escalation->id; ?>">
                                        <i data-feather="check-circle" class="icon-16"></i> <?php echo app_lang('resolve'); ?>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning re-escalate" data-id="<?php echo $escalation->id; ?>">
                                        <i data-feather="arrow-up" class="icon-16"></i> <?php echo app_lang('re_escalate'); ?>
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
                <p><?php echo app_lang('no_pending_escalations'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Acknowledge escalation
        $('.acknowledge-escalation').click(function() {
            var escalationId = $(this).data('id');
            
            if (confirm('<?php echo app_lang('acknowledge_escalation_confirmation'); ?>')) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/update_escalation_status'); ?>',
                    type: 'POST',
                    data: {escalation_id: escalationId, status: 'acknowledged'},
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

        // Resolve escalation
        $('.resolve-escalation').click(function() {
            var escalationId = $(this).data('id');
            
            var notes = prompt('<?php echo app_lang('enter_resolution_notes'); ?>');
            if (notes) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/update_escalation_status'); ?>',
                    type: 'POST',
                    data: {escalation_id: escalationId, status: 'resolved', resolution_notes: notes},
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

        // Re-escalate
        $('.re-escalate').click(function() {
            var escalationId = $(this).data('id');
            // Open modal for re-escalation (to be implemented)
            appAlert.warning('<?php echo app_lang('re_escalation_feature_coming_soon'); ?>');
        });

        // Refresh feather icons
        feather.replace();
    });
</script>
