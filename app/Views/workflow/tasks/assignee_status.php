<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('individual_assignee_status'); ?> - <?php echo $task_name; ?></h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo app_lang('assignee'); ?></th>
                        <th><?php echo app_lang('department'); ?></th>
                        <th><?php echo app_lang('status'); ?></th>
                        <th><?php echo app_lang('progress'); ?></th>
                        <th><?php echo app_lang('started_at'); ?></th>
                        <th><?php echo app_lang('completed_at'); ?></th>
                        <th><?php echo app_lang('notes'); ?></th>
                        <th><?php echo app_lang('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignees as $assignee): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs">
                                    <img src="<?php echo get_avatar($assignee->user_email); ?>" alt="avatar">
                                </div>
                                <div class="ms-2">
                                    <strong><?php echo $assignee->user_name; ?></strong>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $assignee->department_name; ?></td>
                        <td>
                            <?php
                            $status_class = array(
                                'pending' => 'secondary',
                                'in_progress' => 'info',
                                'completed' => 'success'
                            );
                            ?>
                            <span class="badge bg-<?php echo $status_class[$assignee->assignee_status]; ?>">
                                <?php echo app_lang($assignee->assignee_status); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $progress = 0;
                            if ($assignee->assignee_status === 'completed') $progress = 100;
                            elseif ($assignee->assignee_status === 'in_progress') $progress = 50;
                            ?>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-<?php echo $status_class[$assignee->assignee_status]; ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo $progress; ?>%"
                                     aria-valuenow="<?php echo $progress; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?php echo $progress; ?>%
                                </div>
                            </div>
                        </td>
                        <td><?php echo $assignee->started_at ? format_to_datetime($assignee->started_at) : '-'; ?></td>
                        <td><?php echo $assignee->completed_at ? format_to_datetime($assignee->completed_at) : '-'; ?></td>
                        <td><?php echo $assignee->assignee_notes ? character_limiter($assignee->assignee_notes, 30) : '-'; ?></td>
                        <td>
                            <?php if ($assignee->user_id == $current_user_id && $assignee->assignee_status !== 'completed'): ?>
                                <?php if ($assignee->assignee_status === 'pending'): ?>
                                    <button class="btn btn-sm btn-info start-work" 
                                            data-task-id="<?php echo $task_id; ?>"
                                            data-user-id="<?php echo $assignee->user_id; ?>">
                                        <i data-feather="play" class="icon-16"></i> <?php echo app_lang('start'); ?>
                                    </button>
                                <?php elseif ($assignee->assignee_status === 'in_progress'): ?>
                                    <button class="btn btn-sm btn-success complete-work" 
                                            data-task-id="<?php echo $task_id; ?>"
                                            data-user-id="<?php echo $assignee->user_id; ?>">
                                        <i data-feather="check" class="icon-16"></i> <?php echo app_lang('complete'); ?>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Overall Task Status -->
        <div class="card mt-3">
            <div class="card-body">
                <h5><?php echo app_lang('overall_task_status'); ?></h5>
                <div class="progress" style="height: 30px;">
                    <?php
                    $overall_progress = ($completed_count / $total_count) * 100;
                    ?>
                    <div class="progress-bar bg-<?php echo $overall_progress == 100 ? 'success' : 'info'; ?>" 
                         role="progressbar" 
                         style="width: <?php echo $overall_progress; ?>%"
                         aria-valuenow="<?php echo $overall_progress; ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?php echo round($overall_progress); ?>% (<?php echo $completed_count; ?>/<?php echo $total_count; ?>)
                    </div>
                </div>

                <?php if ($overall_progress == 100): ?>
                    <div class="alert alert-success mt-3">
                        <i data-feather="check-circle" class="icon-16"></i>
                        <strong><?php echo app_lang('task_auto_completed'); ?></strong> - <?php echo app_lang('all_assignees_finished'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Start work
        $('.start-work').click(function() {
            var taskId = $(this).data('task-id');
            var userId = $(this).data('user-id');
            
            $.ajax({
                url: '<?php echo get_uri('workflow/update_assignee_status'); ?>',
                type: 'POST',
                data: {task_id: taskId, user_id: userId, status: 'in_progress'},
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message, {duration: 10000});
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });
        });

        // Complete work
        $('.complete-work').click(function() {
            var taskId = $(this).data('task-id');
            var userId = $(this).data('user-id');
            
            var notes = prompt('<?php echo app_lang('enter_completion_notes_optional'); ?>');
            if (notes !== null) {
                $.ajax({
                    url: '<?php echo get_uri('workflow/update_assignee_status'); ?>',
                    type: 'POST',
                    data: {task_id: taskId, user_id: userId, status: 'completed', notes: notes},
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
