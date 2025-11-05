<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('parallel_task_assignment'); ?> - <?php echo $task_name; ?></h4>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i data-feather="info" class="icon-16"></i>
            <?php echo app_lang('parallel_assignment_explanation'); ?>
        </div>

        <form id="parallel-assignment-form">
            <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />

            <div class="form-group">
                <label><?php echo app_lang('select_assignees'); ?></label>
                <select name="user_ids[]" id="user_ids" class="select2 form-control" multiple data-placeholder="<?php echo app_lang('select_users'); ?>">
                    <?php foreach ($team_members as $member): ?>
                        <option value="<?php echo $member->id; ?>">
                            <?php echo $member->first_name . ' ' . $member->last_name; ?> 
                            (<?php echo $member->department_name; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="alert alert-warning">
                <i data-feather="alert-triangle" class="icon-16"></i>
                <strong><?php echo app_lang('example_task_4'); ?>:</strong> <?php echo app_lang('task_4_description'); ?>
                <br><em><?php echo app_lang('assignees_pendo_and_edson'); ?></em>
            </div>

            <button type="submit" class="btn btn-primary">
                <i data-feather="user-plus" class="icon-16"></i> <?php echo app_lang('assign_users'); ?>
            </button>
        </form>

        <!-- Current Assignees -->
        <?php if (count($current_assignees) > 0): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5><?php echo app_lang('current_assignees'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?php echo app_lang('name'); ?></th>
                                <th><?php echo app_lang('department'); ?></th>
                                <th><?php echo app_lang('status'); ?></th>
                                <th><?php echo app_lang('started_at'); ?></th>
                                <th><?php echo app_lang('completed_at'); ?></th>
                                <th><?php echo app_lang('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($current_assignees as $assignee): ?>
                            <tr>
                                <td><?php echo $assignee->user_name; ?></td>
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
                                <td><?php echo $assignee->started_at ? format_to_datetime($assignee->started_at) : '-'; ?></td>
                                <td><?php echo $assignee->completed_at ? format_to_datetime($assignee->completed_at) : '-'; ?></td>
                                <td>
                                    <?php if ($assignee->assignee_status !== 'completed'): ?>
                                        <button class="btn btn-sm btn-danger remove-assignee" 
                                                data-task-id="<?php echo $task_id; ?>"
                                                data-user-id="<?php echo $assignee->user_id; ?>">
                                            <i data-feather="user-minus" class="icon-16"></i> <?php echo app_lang('remove'); ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Auto-completion Status -->
                <div class="alert <?php echo $all_completed ? 'alert-success' : 'alert-warning'; ?> mt-3">
                    <i data-feather="<?php echo $all_completed ? 'check-circle' : 'clock'; ?>" class="icon-16"></i>
                    <?php if ($all_completed): ?>
                        <strong><?php echo app_lang('all_assignees_completed'); ?></strong> - <?php echo app_lang('task_auto_completed'); ?>
                    <?php else: ?>
                        <strong><?php echo app_lang('waiting_for_completion'); ?></strong> - 
                        <?php echo $completed_count . ' / ' . $total_count . ' ' . app_lang('assignees_completed'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2();

        $('#parallel-assignment-form').submit(function(e) {
            e.preventDefault();
            
            var userIds = $('#user_ids').val();
            if (!userIds || userIds.length === 0) {
                appAlert.error('<?php echo app_lang('please_select_at_least_one_user'); ?>');
                return;
            }

            $.ajax({
                url: '<?php echo get_uri('workflow/assign_parallel_users'); ?>',
                type: 'POST',
                data: {
                    task_id: $('input[name="task_id"]').val(),
                    user_ids: userIds
                },
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

        // Remove assignee
        $('.remove-assignee').click(function() {
            if (confirm('<?php echo app_lang('confirm_remove_assignee'); ?>')) {
                var taskId = $(this).data('task-id');
                var userId = $(this).data('user-id');
                
                // This would need a remove_assignee endpoint
                appAlert.warning('<?php echo app_lang('remove_assignee_feature_coming_soon'); ?>');
            }
        });

        feather.replace();
    });
</script>
