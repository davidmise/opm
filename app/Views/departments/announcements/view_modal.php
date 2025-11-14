<div class="modal-body clearfix">
    <div class="container-fluid">
        
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('title'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-plaintext"><?php echo $model_info->title; ?></p>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('content'); ?></label>
                <div class="col-md-9">
                    <div class="form-control-plaintext" style="min-height: 120px; white-space: pre-wrap; background-color: #f8f9fa; padding: 12px; border-radius: 4px;">
                        <?php echo nl2br(htmlspecialchars($model_info->description)); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('category'); ?></label>
                <div class="col-md-9">
                    <?php 
                    $category_badges = array(
                        'general' => 'bg-info',
                        'urgent' => 'bg-danger', 
                        'policy' => 'bg-warning',
                        'event' => 'bg-secondary',
                        'maintenance' => 'bg-dark'
                    );
                    $badge_class = isset($category_badges[$model_info->category]) ? $category_badges[$model_info->category] : 'bg-info';
                    ?>
                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($model_info->category); ?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('priority'); ?></label>
                <div class="col-md-9">
                    <?php 
                    $priority_badges = array(
                        'low' => 'bg-secondary',
                        'normal' => 'bg-info',
                        'high' => 'bg-warning', 
                        'urgent' => 'bg-danger'
                    );
                    $priority_badge_class = isset($priority_badges[$model_info->priority]) ? $priority_badges[$model_info->priority] : 'bg-info';
                    ?>
                    <span class="badge <?php echo $priority_badge_class; ?>"><?php echo ucfirst($model_info->priority); ?></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('target_departments'); ?></label>
                <div class="col-md-9">
                    <?php if (empty($model_info->share_with) || $model_info->share_with == 'all_members'): ?>
                        <span class="badge bg-light text-dark"><?php echo app_lang('all_departments'); ?></span>
                    <?php else: ?>
                        <?php 
                        $share_with_parts = explode(',', $model_info->share_with);
                        foreach ($share_with_parts as $part) {
                            if (strpos($part, 'dept:') === 0) {
                                $dept_id = str_replace('dept:', '', $part);
                                $department = null;
                                foreach ($departments as $dept) {
                                    if ($dept->id == $dept_id) {
                                        $department = $dept;
                                        break;
                                    }
                                }
                                if ($department) {
                                    echo '<span class="badge bg-primary me-1">' . $department->title . '</span>';
                                }
                            }
                        }
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('start_date'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-plaintext">
                        <i data-feather="calendar" class="icon-16"></i>
                        <?php echo $model_info->start_date ? format_to_datetime($model_info->start_date) : '-'; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('end_date'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-plaintext">
                        <i data-feather="calendar" class="icon-16"></i>
                        <?php echo $model_info->end_date ? format_to_datetime($model_info->end_date) : app_lang('never_expires'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('status'); ?></label>
                <div class="col-md-9">
                    <?php 
                    $is_active = empty($model_info->end_date) || $model_info->end_date >= date('Y-m-d');
                    ?>
                    <?php if ($is_active): ?>
                        <span class="badge bg-success"><i data-feather="check-circle" class="icon-14"></i> <?php echo app_lang('active'); ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><i data-feather="clock" class="icon-14"></i> <?php echo app_lang('expired'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label class="col-md-3"><?php echo app_lang('created_by'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-plaintext">
                        <i data-feather="user" class="icon-16"></i>
                        <?php 
                        // Get creator info from model_info if available, otherwise show generic text
                        if (isset($model_info->creator_name)) {
                            echo $model_info->creator_name;
                        } else {
                            echo 'Admin User';
                        }
                        ?>
                        <small class="text-muted">
                            (<?php echo format_to_datetime($model_info->created_at ? $model_info->created_at : date('Y-m-d H:i:s')); ?>)
                        </small>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
    </button>
    
    <button type="button" class="btn btn-primary" onclick="editAnnouncement(<?php echo $model_info->id; ?>)">
        <span data-feather="edit" class="icon-16"></span> <?php echo app_lang('edit'); ?>
    </button>
</div>

<script type="text/javascript">
// Wait for jQuery to be available before executing
function initViewModalScript() {
    if (typeof $ === 'undefined') {
        // jQuery not loaded yet, wait 100ms and try again
        setTimeout(initViewModalScript, 100);
        return;
    }
    
    $(document).ready(function () {
        // Initialize feather icons
        setTimeout(function() {
            if (typeof feather !== 'undefined' && feather.replace) {
                try {
                    feather.replace();
                } catch (e) {
                    console.warn('Feather icon replacement failed:', e);
                }
        }
    }, 100);
});

function editAnnouncement(announcementId) {
    // Close current modal
    var currentModal = bootstrap.Modal.getInstance(document.getElementById('ajaxModal'));
    if (currentModal) {
        currentModal.hide();
    }
    
    // Open edit modal
    setTimeout(function() {
        var editUrl = '<?php echo get_uri("departments/announcement_modal_form"); ?>/' + announcementId + '?mode=edit';
        $.ajaxModal({
            url: editUrl,
            title: '<?php echo app_lang("edit_announcement"); ?>'
        });
    }, 300);
}

    }); // End of $(document).ready
}

// Start the initialization
initViewModalScript();
</script>