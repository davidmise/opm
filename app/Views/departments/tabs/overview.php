<?php 
/**
 * Department Overview Tab
 */

// Get department statistics
$total_members = $statistics->member_count ?? 0;
$total_projects = $statistics->total_projects ?? 0;
$total_tasks = ($statistics->active_tasks ?? 0) + ($statistics->completed_tasks ?? 0);
$completed_tasks = $statistics->completed_tasks ?? 0;
$ongoing_projects = $statistics->active_projects ?? 0;
$completed_projects = $statistics->completed_projects ?? 0;

// Calculate percentages
$task_completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 1) : 0;
$project_completion_rate = $total_projects > 0 ? round(($completed_projects / $total_projects) * 100, 1) : 0;
?>

<div class="card bg-white">
    <div class="card-body">
        <div class="row">
            <!-- Department Info Card -->
            <div class="col-md-6">
                <div class="card department-info-card" style="border-top: 4px solid <?php echo $department_info->color; ?>;">
                    <div class="card-header" style="background: linear-gradient(135deg, <?php echo $department_info->color; ?>15, <?php echo $department_info->color; ?>05);">
                        <h5 class="card-title mb-0" style="color: <?php echo $department_info->color; ?>;">
                            <i data-feather="info" class="icon-16 me-2"></i>
                            <?php echo app_lang('department_information'); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small text-uppercase fw-bold"><?php echo app_lang('title'); ?>:</label>
                            <div class="d-flex align-items-center">
                                <span class="badge me-2" style="background-color: <?php echo $department_info->color; ?>; color: white; font-size: 14px; padding: 8px 12px;">
                                    <i data-feather="grid" class="icon-14"></i> <?php echo $department_info->title; ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($department_info->description) { ?>
                        <div class="mb-3">
                            <label class="form-label text-muted small text-uppercase fw-bold"><?php echo app_lang('description'); ?>:</label>
                            <div class="description-content" style="background: <?php echo $department_info->color; ?>08; border-left: 3px solid <?php echo $department_info->color; ?>; padding: 12px; border-radius: 4px;">
                                <?php echo nl2br(htmlspecialchars($department_info->description)); ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="mb-3">
                            <label class="form-label text-muted small text-uppercase fw-bold"><?php echo app_lang('created_date'); ?>:</label>
                            <div class="text-dark">
                                <i data-feather="calendar" class="icon-14 me-1" style="color: <?php echo $department_info->color; ?>;"></i>
                                <?php echo format_to_date($department_info->created_at, false); ?>
                                <?php if ($department_info->created_by_user) { ?>
                                    <span class="text-muted ms-2"><?php echo app_lang('by'); ?> <?php echo $department_info->created_by_user; ?></span>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if (property_exists($department_info, 'head_user_name') && $department_info->head_user_name) { ?>
                        <div class="mb-3">
                            <label class="form-label text-muted small text-uppercase fw-bold"><?php echo app_lang('department_head'); ?>:</label>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs me-2" style="background: <?php echo $department_info->color; ?>20; border: 2px solid <?php echo $department_info->color; ?>; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i data-feather="user" class="icon-14" style="color: <?php echo $department_info->color; ?>;"></i>
                                </div>
                                <span class="fw-medium department-head-link" style="cursor: pointer; color: <?php echo $department_info->color; ?>; text-decoration: none;" 
                                      onclick="window.location.href='<?php echo get_uri('team_members/view/' . ($department_info->head_user_id ?? '')); ?>'"
                                      onmouseover="this.style.textDecoration='underline'" 
                                      onmouseout="this.style.textDecoration='none'">
                                    <?php echo $department_info->head_user_name; ?>
                                </span>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="department-theme-indicator mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted">
                                    <i data-feather="palette" class="icon-12 me-1"></i>
                                    <?php echo app_lang('department_theme_color'); ?>: 
                                    <span class="fw-medium" style="color: <?php echo $department_info->color; ?>;"><?php echo strtoupper($department_info->color); ?></span>
                                </small>
                                <button class="btn btn-sm btn-outline-secondary color-picker-trigger" 
                                        type="button" 
                                        data-department-id="<?php echo $department_info->id; ?>"
                                        title="<?php echo app_lang('change_department_color'); ?>">
                                    <div class="color-swatch" 
                                         style="width: 20px; height: 20px; background-color: <?php echo $department_info->color; ?>; border-radius: 4px; border: 1px solid #dee2e6; display: inline-block; vertical-align: middle;">
                                    </div>
                                    <i data-feather="edit-2" class="icon-12 ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="col-md-6">
                <h5 class="mb-3" style="color: <?php echo $department_info->color; ?>;">
                    <i data-feather="bar-chart-2" class="icon-16 me-2"></i>
                    <?php echo app_lang('department_statistics'); ?>
                </h5>
                
                <div class="row">
                    <!-- Team Members -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-left: 4px solid <?php echo $department_info->color; ?> !important;">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2" style="background: <?php echo $department_info->color; ?>15; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i data-feather="users" class="icon-24" style="color: <?php echo $department_info->color; ?>;"></i>
                                </div>
                                <h3 class="mb-1" style="color: <?php echo $department_info->color; ?>;"><?php echo $total_members; ?></h3>
                                <p class="text-muted mb-0 small"><?php echo app_lang('team_members'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Projects -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-left: 4px solid <?php echo $department_info->color; ?> !important;">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2" style="background: <?php echo $department_info->color; ?>15; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i data-feather="command" class="icon-24" style="color: <?php echo $department_info->color; ?>;"></i>
                                </div>
                                <h3 class="mb-1" style="color: <?php echo $department_info->color; ?>;"><?php echo $total_projects; ?></h3>
                                <p class="text-muted mb-0 small"><?php echo app_lang('projects'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-left: 4px solid <?php echo $department_info->color; ?> !important;">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2" style="background: <?php echo $department_info->color; ?>15; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i data-feather="check-square" class="icon-24" style="color: <?php echo $department_info->color; ?>;"></i>
                                </div>
                                <h3 class="mb-1" style="color: <?php echo $department_info->color; ?>;"><?php echo $total_tasks; ?></h3>
                                <p class="text-muted mb-0 small"><?php echo app_lang('tasks'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Task Completion Rate -->
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm" style="border-left: 4px solid <?php echo $department_info->color; ?> !important;">
                            <div class="card-body text-center">
                                <div class="stat-icon mb-2" style="background: <?php echo $department_info->color; ?>15; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i data-feather="trending-up" class="icon-24" style="color: <?php echo $department_info->color; ?>;"></i>
                                </div>
                                <h3 class="mb-1" style="color: <?php echo $department_info->color; ?>;"><?php echo $task_completion_rate; ?>%</h3>
                                <p class="text-muted mb-0 small"><?php echo app_lang('task_completion_rate'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bars -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title" style="color: <?php echo $department_info->color; ?>;">
                            <i data-feather="command" class="icon-14 me-2"></i>
                            <?php echo app_lang('project_progress'); ?>
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted"><?php echo app_lang('completed'); ?>: <?php echo $completed_projects; ?></span>
                            <span class="small text-muted"><?php echo app_lang('ongoing'); ?>: <?php echo $ongoing_projects; ?></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $project_completion_rate; ?>%; background-color: <?php echo $department_info->color; ?>;">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <span class="fw-bold" style="color: <?php echo $department_info->color; ?>;"><?php echo $project_completion_rate; ?>%</span>
                            <small class="text-muted ms-1"><?php echo app_lang('completion_rate'); ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title" style="color: <?php echo $department_info->color; ?>;">
                            <i data-feather="check-square" class="icon-14 me-2"></i>
                            <?php echo app_lang('task_progress'); ?>
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted"><?php echo app_lang('completed'); ?>: <?php echo $completed_tasks; ?></span>
                            <span class="small text-muted"><?php echo app_lang('remaining'); ?>: <?php echo ($total_tasks - $completed_tasks); ?></span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $task_completion_rate; ?>%; background-color: <?php echo $department_info->color; ?>;">
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <span class="fw-bold" style="color: <?php echo $department_info->color; ?>;"><?php echo $task_completion_rate; ?>%</span>
                            <small class="text-muted ms-1"><?php echo app_lang('completion_rate'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <?php if (isset($recent_activities) && count($recent_activities)) { ?>
        <div class="row mt-4">
            <div class="col-12">
                <h5><?php echo app_lang('recent_activities'); ?></h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?php echo app_lang('activity'); ?></th>
                                <th><?php echo app_lang('user'); ?></th>
                                <th><?php echo app_lang('date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_activities as $activity) { ?>
                            <tr>
                                <td><?php echo $activity->action; ?></td>
                                <td><?php echo $activity->user_name; ?></td>
                                <td><?php echo format_to_datetime($activity->created_at); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<style>
.department-info-card {
    transition: all 0.3s ease;
}

.department-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.description-content {
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.stat-icon {
    transition: all 0.3s ease;
}

.card:hover .stat-icon {
    transform: scale(1.05);
}

.form-label {
    font-size: 12px;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.progress {
    border-radius: 10px;
    background-color: #f8f9fa;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    padding: 16px 20px 12px;
}

.card-body {
    padding: 20px;
}

@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 20px;
    }
    
    .stat-icon {
        width: 50px !important;
        height: 50px !important;
    }
    
    .card-body h3 {
        font-size: 1.5rem;
    }
}

.department-head-link {
    transition: all 0.2s ease;
}

.department-head-link:hover {
    transform: translateX(2px);
}

.color-picker-trigger {
    border: none !important;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.color-picker-trigger:hover {
    background-color: #f8f9fa;
    transform: scale(1.05);
}

.color-swatch {
    transition: all 0.2s ease;
    cursor: pointer;
}

.color-picker-trigger:hover .color-swatch {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
</style>

<script>
$(document).ready(function() {
    // Color picker functionality
    $('.color-picker-trigger').on('click', function(e) {
        e.preventDefault();
        var departmentId = $(this).data('department-id');
        var currentColor = $('.color-swatch').css('background-color');
        
        // Convert RGB to hex for display
        function rgbToHex(rgb) {
            if (rgb.startsWith('#')) return rgb;
            const values = rgb.match(/\d+/g);
            if (!values || values.length < 3) return '#000000';
            const hex = values.slice(0, 3).map(x => {
                const hexValue = parseInt(x).toString(16);
                return hexValue.length === 1 ? '0' + hexValue : hexValue;
            }).join('');
            return '#' + hex;
        }
        
        var hexColor = rgbToHex(currentColor);
        
        // Create color picker modal
        var colorPickerModal = `
            <div class="modal fade" id="colorPickerModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i data-feather="palette" class="icon-16 me-2"></i>
                                <?php echo app_lang('change_department_color'); ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label"><?php echo app_lang('select_color'); ?>:</label>
                                <div class="color-options d-flex flex-wrap gap-2 mb-3">
                                    <div class="color-option" data-color="#3498db" style="background: #3498db;"></div>
                                    <div class="color-option" data-color="#e74c3c" style="background: #e74c3c;"></div>
                                    <div class="color-option" data-color="#2ecc71" style="background: #2ecc71;"></div>
                                    <div class="color-option" data-color="#f39c12" style="background: #f39c12;"></div>
                                    <div class="color-option" data-color="#9b59b6" style="background: #9b59b6;"></div>
                                    <div class="color-option" data-color="#1abc9c" style="background: #1abc9c;"></div>
                                    <div class="color-option" data-color="#34495e" style="background: #34495e;"></div>
                                    <div class="color-option" data-color="#e67e22" style="background: #e67e22;"></div>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">#</span>
                                    <input type="text" id="colorInput" class="form-control" placeholder="Enter hex color" value="${hexColor.substring(1)}" maxlength="6">
                                </div>
                                <div class="color-preview mt-3 p-3 rounded" style="background: ${hexColor}; border: 1px solid #dee2e6;">
                                    <span class="text-white fw-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><?php echo app_lang('preview'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app_lang('cancel'); ?></button>
                            <button type="button" class="btn btn-primary" id="saveColorBtn"><?php echo app_lang('save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal and add new one
        $('#colorPickerModal').remove();
        $('body').append(colorPickerModal);
        
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('colorPickerModal'));
        modal.show();
        
        // Initialize color picker events
        setTimeout(function() {
            safeFeatherReplace();
            
            // Color option clicks
            $('.color-option').on('click', function() {
                var color = $(this).data('color');
                $('#colorInput').val(color.substring(1));
                $('.color-preview').css('background', color);
                $('.color-option').removeClass('selected');
                $(this).addClass('selected');
            });
            
            // Color input changes
            $('#colorInput').on('input', function() {
                var value = $(this).val().replace('#', '');
                if (/^[0-9A-F]{6}$/i.test(value)) {
                    var color = '#' + value;
                    $('.color-preview').css('background', color);
                }
            });
            
            // Save button
            $('#saveColorBtn').on('click', function() {
                var newColor = '#' + $('#colorInput').val().replace('#', '');
                if (!/^#[0-9A-F]{6}$/i.test(newColor)) {
                    appAlert.error('<?php echo app_lang('invalid_color_format'); ?>');
                    return;
                }
                
                // Update department color via AJAX
                $.ajax({
                    url: '<?php echo get_uri('departments/update_color'); ?>',
                    type: 'POST',
                    data: {
                        department_id: departmentId,
                        color: newColor,
                        '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI with new color
                            location.reload(); // Simple reload for now
                        } else {
                            appAlert.error(response.message || '<?php echo app_lang('something_went_wrong'); ?>');
                        }
                    },
                    error: function() {
                        appAlert.error('<?php echo app_lang('something_went_wrong'); ?>');
                    }
                });
                
                modal.hide();
            });
        }, 100);
    });
});
</script>

<style>
.color-option {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.2s ease;
}

.color-option:hover, .color-option.selected {
    border-color: #333;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.color-preview {
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease;
}
</style>