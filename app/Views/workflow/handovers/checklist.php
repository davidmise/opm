<div class="card">
    <div class="card-header">
        <h4><?php echo app_lang('handover_checklist'); ?></h4>
        <span class="badge bg-info float-end"><?php echo $completed_items . ' / ' . $total_items . ' ' . app_lang('completed'); ?></span>
    </div>
    <div class="card-body">
        <div class="checklist-container" data-handover-id="<?php echo $handover_id; ?>">
            <?php foreach ($checklist as $item): ?>
                <div class="form-check mb-3 checklist-item" data-item-id="<?php echo $item->id; ?>">
                    <input class="form-check-input checklist-checkbox" type="checkbox" 
                           id="check_<?php echo $item->id; ?>" 
                           <?php echo $item->is_completed ? 'checked' : ''; ?>
                           data-item-id="<?php echo $item->id; ?>">
                    <label class="form-check-label <?php echo $item->is_completed ? 'text-decoration-line-through' : ''; ?>" 
                           for="check_<?php echo $item->id; ?>">
                        <strong><?php echo $item->item_title; ?></strong>
                        <?php if ($item->item_description): ?>
                            <br><small class="text-muted"><?php echo $item->item_description; ?></small>
                        <?php endif; ?>
                    </label>
                    <?php if ($item->completed_by): ?>
                        <small class="text-muted ms-3">
                            <i data-feather="user" class="icon-14"></i> 
                            <?php echo $item->completed_by_name; ?> • 
                            <?php echo format_to_relative_time($item->completed_at); ?>
                        </small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($is_all_complete): ?>
            <div class="alert alert-success mt-3">
                <i data-feather="check-circle" class="icon-16"></i>
                <?php echo app_lang('all_checklist_items_completed'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.checklist-checkbox').change(function() {
            var $checkbox = $(this);
            var itemId = $checkbox.data('item-id');
            var handoverId = $('.checklist-container').data('handover-id');
            var isCompleted = $checkbox.is(':checked');

            $.ajax({
                url: '<?php echo get_uri('workflow/update_handover_checklist'); ?>',
                type: 'POST',
                data: {
                    handover_id: handoverId,
                    checklist_item_id: itemId,
                    is_completed: isCompleted
                },
                success: function(result) {
                    if (result.success) {
                        var $label = $checkbox.next('label');
                        if (isCompleted) {
                            $label.addClass('text-decoration-line-through');
                        } else {
                            $label.removeClass('text-decoration-line-through');
                        }
                        appAlert.success(result.message, {duration: 3000});
                    } else {
                        appAlert.error(result.message);
                        $checkbox.prop('checked', !isCompleted);
                    }
                },
                error: function() {
                    $checkbox.prop('checked', !isCompleted);
                }
            });
        });

        feather.replace();
    });
</script>
