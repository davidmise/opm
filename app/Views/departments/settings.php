<!-- COMPREHENSIVE DEPARTMENTS SETTINGS TAB -->
<div class="row">
    <div class="col-12">
        
        <!-- Settings Navigation Tabs -->
        <div class="card mb-4">
            <div class="card-header p-0">
                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#general-settings-tab" role="tab">
                            <i data-feather="settings" class="icon-14"></i> <?php echo app_lang('general_settings'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rbac-settings-tab" role="tab">
                            <i data-feather="shield" class="icon-14"></i> <?php echo app_lang('rbac_permissions'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#templates-tab" role="tab">
                            <i data-feather="layers" class="icon-14"></i> <?php echo app_lang('department_templates'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#audit-tab" role="tab">
                            <i data-feather="activity" class="icon-14"></i> <?php echo app_lang('audit_activity'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- GENERAL SETTINGS TAB -->
                    <div class="tab-pane fade show active" id="general-settings-tab" role="tabpanel">
                        <?php echo form_open(base_url("index.php/departments/save_general_settings"), array("id" => "general-settings-form", "class" => "general-form", "role" => "form")); ?>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="default_department_color"><?php echo app_lang('default_department_color'); ?></label>
                                    <input type="color" name="default_department_color" id="default_department_color" 
                                           value="<?php echo $default_department_color; ?>" 
                                           class="form-control" />
                                    <small class="form-text text-muted"><?php echo app_lang('default_color_for_new_departments'); ?></small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="auto_assign_new_users"><?php echo app_lang('auto_assign_new_users'); ?></label>
                                    <?php
                                    echo form_dropdown("auto_assign_new_users", array(
                                        "0" => app_lang("no"),
                                        "1" => app_lang("yes")
                                    ), $auto_assign_new_users, "class='select2 form-control'");
                                    ?>
                                    <small class="form-text text-muted"><?php echo app_lang('automatically_assign_users_to_default_department'); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="department_approval_required"><?php echo app_lang('department_approval_required'); ?></label>
                                    <?php
                                    echo form_dropdown("department_approval_required", array(
                                        "0" => app_lang("no"),
                                        "1" => app_lang("yes")
                                    ), $department_approval_required, "class='select2 form-control'");
                                    ?>
                                    <small class="form-text text-muted"><?php echo app_lang('require_admin_approval_for_department_changes'); ?></small>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="max_departments_per_user"><?php echo app_lang('max_departments_per_user'); ?></label>
                                    <input type="number" name="max_departments_per_user" id="max_departments_per_user" 
                                           value="<?php echo $max_departments_per_user; ?>" 
                                           class="form-control" min="1" max="50" />
                                    <small class="form-text text-muted"><?php echo app_lang('maximum_departments_user_can_belong_to'); ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Department Statistics -->
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <h6><?php echo app_lang('department_overview'); ?></h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="text-primary"><?php echo $total_departments; ?></h4>
                                            <small><?php echo app_lang('total_departments'); ?></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="text-success"><?php echo $active_departments; ?></h4>
                                            <small><?php echo app_lang('active_departments'); ?></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="text-info"><?php echo count($all_users); ?></h4>
                                            <small><?php echo app_lang('total_users'); ?></small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-light rounded">
                                            <h4 class="text-warning"><?php echo count($department_templates); ?></h4>
                                            <small><?php echo app_lang('available_templates'); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="check-circle" class="icon-16"></i> <?php echo app_lang('save_general_settings'); ?>
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="$('#general-settings-form')[0].reset();">
                                <i data-feather="refresh-cw" class="icon-16"></i> <?php echo app_lang('reset'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>

                    <!-- RBAC PERMISSIONS TAB -->
                    <div class="tab-pane fade" id="rbac-settings-tab" role="tabpanel">
                        <h5><?php echo app_lang('role_based_access_control'); ?></h5>
                        <p class="text-muted"><?php echo app_lang('configure_department_permissions_by_user_role'); ?></p>
                        
                        <?php echo form_open(base_url("index.php/departments/save_rbac_settings"), array("id" => "rbac-settings-form", "class" => "general-form", "role" => "form")); ?>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th><?php echo app_lang('permission'); ?></th>
                                        <?php foreach($rbac_roles as $role): ?>
                                            <th class="text-center"><?php echo ucfirst(app_lang($role)); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($rbac_permissions as $permission_key => $permission_name): ?>
                                    <tr>
                                        <td><strong><?php echo $permission_name; ?></strong></td>
                                        <?php foreach($rbac_roles as $role): ?>
                                            <?php 
                                            $is_checked = ($role == 'admin') ? true : 
                                                (isset($current_rbac_settings[$role][$permission_key]) && $current_rbac_settings[$role][$permission_key] == 1);
                                            $is_disabled = ($role == 'admin');
                                            ?>
                                            <td class="text-center">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="rbac[<?php echo $role; ?>][<?php echo $permission_key; ?>]" 
                                                           id="rbac_<?php echo $role; ?>_<?php echo $permission_key; ?>"
                                                           <?php echo $is_checked ? 'checked' : ''; ?>
                                                           <?php echo $is_disabled ? 'disabled' : ''; ?>
                                                           value="1">
                                                    <label class="form-check-label" for="rbac_<?php echo $role; ?>_<?php echo $permission_key; ?>"></label>
                                                    <?php if ($is_disabled): ?>
                                                        <input type="hidden" name="rbac[<?php echo $role; ?>][<?php echo $permission_key; ?>]" value="1">
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info">
                            <i data-feather="info" class="icon-16"></i>
                            <strong><?php echo app_lang('note'); ?>:</strong> <?php echo app_lang('admin_role_has_all_permissions_by_default'); ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="shield-check" class="icon-16"></i> <?php echo app_lang('save_rbac_settings'); ?>
                            </button>
                            <button type="button" class="btn btn-warning" onclick="resetRbacToDefaults();">
                                <i data-feather="refresh-cw" class="icon-16"></i> <?php echo app_lang('reset_to_defaults'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>

                    <!-- DEPARTMENT TEMPLATES TAB -->
                    <div class="tab-pane fade" id="templates-tab" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><?php echo app_lang('department_templates'); ?></h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="$('#template-modal').modal('show');">
                                <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('add_template'); ?>
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped" id="templates-table">
                                <thead>
                                    <tr>
                                        <th><?php echo app_lang('template_name'); ?></th>
                                        <th><?php echo app_lang('description'); ?></th>
                                        <th><?php echo app_lang('created_date'); ?></th>
                                        <th class="text-center"><?php echo app_lang('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($department_templates as $template): ?>
                                    <tr>
                                        <td><strong><?php echo $template->name; ?></strong></td>
                                        <td><?php echo $template->description; ?></td>
                                        <td><?php echo $template->created_date; ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="<?php echo app_lang('edit'); ?>">
                                                <i data-feather="edit-2" class="icon-14"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" title="<?php echo app_lang('use_template'); ?>">
                                                <i data-feather="copy" class="icon-14"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="<?php echo app_lang('delete'); ?>">
                                                <i data-feather="trash-2" class="icon-14"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- AUDIT & ACTIVITY TAB -->
                    <div class="tab-pane fade" id="audit-tab" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><?php echo app_lang('audit_activity_log'); ?></h5>
                            <div>
                                <button type="button" class="btn btn-outline-secondary btn-sm">
                                    <i data-feather="download" class="icon-14"></i> <?php echo app_lang('export_logs'); ?>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm">
                                    <i data-feather="trash-2" class="icon-14"></i> <?php echo app_lang('clear_logs'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <select class="form-control form-control-sm" id="audit-filter-action">
                                    <option value=""><?php echo app_lang('filter_by_action'); ?></option>
                                    <option value="create"><?php echo app_lang('create'); ?></option>
                                    <option value="update"><?php echo app_lang('update'); ?></option>
                                    <option value="delete"><?php echo app_lang('delete'); ?></option>
                                    <option value="assign"><?php echo app_lang('assign'); ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control form-control-sm" id="audit-filter-date" 
                                       placeholder="<?php echo app_lang('filter_by_date'); ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" id="audit-filter-user" 
                                       placeholder="<?php echo app_lang('filter_by_user'); ?>">
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped" id="audit-table">
                                <thead>
                                    <tr>
                                        <th><?php echo app_lang('timestamp'); ?></th>
                                        <th><?php echo app_lang('action'); ?></th>
                                        <th><?php echo app_lang('details'); ?></th>
                                        <th><?php echo app_lang('user'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_activity as $activity): ?>
                                    <tr>
                                        <td><small><?php echo $activity->date; ?></small></td>
                                        <td><span class="badge bg-primary"><?php echo $activity->action; ?></span></td>
                                        <td><?php echo $activity->details; ?></td>
                                        <td><?php echo $activity->user; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template Modal -->
<div class="modal fade" id="template-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo app_lang('add_department_template'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php echo form_open(base_url("index.php/departments/save_template"), array("id" => "template-form")); ?>
                <div class="form-group">
                    <label for="template_name"><?php echo app_lang('template_name'); ?></label>
                    <input type="text" name="template_name" id="template_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="template_description"><?php echo app_lang('description'); ?></label>
                    <textarea name="template_description" id="template_description" class="form-control" rows="3"></textarea>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app_lang('cancel'); ?></button>
                <button type="submit" form="template-form" class="btn btn-primary"><?php echo app_lang('save_template'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables for templates and audit logs
    $('#templates-table').DataTable({
        "pageLength": 10,
        "searching": true,
        "ordering": true
    });
    
    $('#audit-table').DataTable({
        "pageLength": 15,
        "searching": true,
        "ordering": true,
        "order": [[ 0, "desc" ]]
    });

    // Form submissions
    $('#general-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    appAlert.success(response.message || '<?php echo app_lang('settings_saved_successfully'); ?>');
                } else {
                    appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
                }
            },
            error: function() {
                appAlert.error('<?php echo app_lang('error_occurred'); ?>');
            }
        });
    });

    $('#rbac-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    appAlert.success(response.message || '<?php echo app_lang('rbac_settings_saved_successfully'); ?>');
                } else {
                    appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
                }
            },
            error: function() {
                appAlert.error('<?php echo app_lang('error_occurred'); ?>');
            }
        });
    });

    // Initialize feather icons
    if (window.feather && typeof feather.replace === 'function') {
        feather.replace();
    }
});

function resetRbacToDefaults() {
    if(confirm('<?php echo app_lang('confirm_reset_rbac_to_defaults'); ?>')) {
        // Reset all checkboxes
        $('#rbac-settings-form input[type="checkbox"]').prop('checked', false);
        
        // Check admin permissions
        $('#rbac-settings-form input[name^="rbac[admin]"]').prop('checked', true);
        
        // Check some manager permissions
        $('#rbac-settings-form input[name="rbac[manager][view_all_departments]"]').prop('checked', true);
        $('#rbac-settings-form input[name="rbac[manager][manage_department_users]"]').prop('checked', true);
        $('#rbac-settings-form input[name="rbac[manager][view_department_reports]"]').prop('checked', true);
        
        appAlert.success('<?php echo app_lang('rbac_reset_to_defaults'); ?>');
    }
}
</script>
                <p>Timestamp: <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    console.log('Departments settings page loaded successfully');
    
    // Initialize Select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2();
    }
    
    // Form submission
    $('#general-settings-form').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        $.post($(this).attr('action'), $(this).serialize())
            .done(function(response) {
                console.log('Response:', response);
                if (response.success) {
                    if (typeof appAlert !== 'undefined') {
                        appAlert.success(response.message);
                    } else {
                        alert('Settings saved successfully!');
                    }
                } else {
                    if (typeof appAlert !== 'undefined') {
                        appAlert.error(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            })
            .fail(function() {
                alert('Failed to save settings');
            });
    });
});
</script>

<style>
.card-header h5 {
    margin: 0;
    display: flex;
    align-items: center;
}

.card-header h5 i {
    margin-right: 8px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 5px;
}
</style>

<script>
$(document).ready(function() {
    // Settings nested tab persistence
    var activeSettingsTab = localStorage.getItem('departments_settings_tab') || '#general-settings-tab';
    
    // Show the active settings tab
    if (activeSettingsTab && $('.nav-tabs-horizontal a[href="' + activeSettingsTab + '"]').length) {
        $('.nav-tabs-horizontal a[href="' + activeSettingsTab + '"]').tab('show');
    }
    
    // Save active settings tab when changed (for nested tabs only)
    $('.nav-tabs-horizontal a').on('shown.bs.tab', function(e) {
        var targetTab = $(e.target).attr('href');
        localStorage.setItem('departments_settings_tab', targetTab);
    });
});
</script>