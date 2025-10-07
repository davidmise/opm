<?php echo form_open(get_uri("departments/manage_users/" . $department_info->id), array("id" => "department-users-form", "class" => "general-form", "role" => "form")); ?>

<div class="page-content clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "departments";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="card">
                <div class="page-title clearfix">
                    <h4>
                        <i class="<?php echo $department_info->icon ?: 'ti ti-building'; ?>" style="color: <?php echo $department_info->color; ?>;"></i>
                        <?php echo $department_info->title; ?> - <?php echo app_lang("manage_users"); ?>
                    </h4>
                    <div class="title-button-group">
                        <a href="<?php echo get_uri('departments/dashboard/' . $department_info->id); ?>" class="btn btn-outline-primary">
                            <i class="ti ti-dashboard"></i> <?php echo app_lang("dashboard"); ?>
                        </a>
                        <?php echo modal_anchor(get_uri("departments/add_user_modal/" . $department_info->id), "<i class='ti ti-plus'></i> " . app_lang('add_user_to_department'), array("class" => "btn btn-primary", "title" => app_lang('add_user_to_department'))); ?>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Department Info Summary -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg" style="background-color: <?php echo $department_info->color; ?>;">
                                        <i class="<?php echo $department_info->icon ?: 'ti ti-building'; ?>" style="color: white; font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="mt-3"><?php echo $department_info->title; ?></h5>
                                    <p class="text-muted"><?php echo $department_info->description; ?></p>
                                    
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h6 class="text-primary"><?php echo count($department_users->getResult()); ?></h6>
                                            <small class="text-muted"><?php echo app_lang("total_members"); ?></small>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="text-success">
                                                <?php 
                                                $primary_count = 0;
                                                foreach ($department_users->getResult() as $user) {
                                                    if ($user->is_primary) $primary_count++;
                                                }
                                                echo $primary_count;
                                                ?>
                                            </h6>
                                            <small class="text-muted"><?php echo app_lang("primary_members"); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Users List -->
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-hover" id="department-users-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang("member"); ?></th>
                                            <th class="text-center"><?php echo app_lang("primary"); ?></th>
                                            <th><?php echo app_lang("role"); ?></th>
                                            <th class="text-center"><?php echo app_lang("actions"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($department_users->getResult() as $user): ?>
                                            <tr data-user-id="<?php echo $user->user_id; ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-xs me-2">
                                                            <img src="<?php echo get_avatar($user->image); ?>" alt="avatar">
                                                        </div>
                                                        <div>
                                                            <strong><?php echo $user->first_name . ' ' . $user->last_name; ?></strong>
                                                            <?php if ($user->is_admin): ?>
                                                                <span class="badge bg-danger ms-1">Admin</span>
                                                            <?php endif; ?>
                                                            <br>
                                                            <small class="text-muted"><?php echo $user->email; ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($user->is_primary): ?>
                                                        <span class="badge bg-success">
                                                            <i class="ti ti-star"></i> <?php echo app_lang("primary"); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <button class="btn btn-outline-secondary btn-sm set-primary-btn" 
                                                                data-user-id="<?php echo $user->user_id; ?>" 
                                                                data-department-id="<?php echo $department_info->id; ?>">
                                                            <?php echo app_lang("set_primary"); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark"><?php echo $user->role_title ?: app_lang("no_role"); ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo get_uri('team_members/view/' . $user->user_id); ?>" 
                                                           class="btn btn-outline-primary" title="<?php echo app_lang('view_details'); ?>">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <button class="btn btn-outline-danger remove-user-btn" 
                                                                data-user-id="<?php echo $user->user_id; ?>" 
                                                                data-department-id="<?php echo $department_info->id; ?>"
                                                                title="<?php echo app_lang('remove_from_department'); ?>">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <?php if (empty($department_users->getResult())): ?>
                                    <div class="text-center py-4">
                                        <i class="ti ti-users text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="text-muted"><?php echo app_lang("no_users_assigned"); ?></h5>
                                        <p class="text-muted"><?php echo app_lang("click_add_user_to_get_started"); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        
        // Set Primary Department
        $('.set-primary-btn').click(function() {
            var userId = $(this).data('user-id');
            var departmentId = $(this).data('department-id');
            var $button = $(this);
            
            $.ajax({
                url: '<?php echo_uri("departments/set_primary_department") ?>',
                type: 'POST',
                data: {
                    user_id: userId,
                    department_id: departmentId
                },
                beforeSend: function() {
                    $button.prop('disabled', true).html('<i class="ti ti-loader fa-spin"></i>');
                },
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message, {duration: 10000});
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                },
                complete: function() {
                    $button.prop('disabled', false).html('<?php echo app_lang("set_primary"); ?>');
                }
            });
        });

        // Remove User from Department
        $('.remove-user-btn').click(function() {
            var userId = $(this).data('user-id');
            var departmentId = $(this).data('department-id');
            var $row = $(this).closest('tr');
            
            if (confirm('<?php echo app_lang("confirm_remove_user_from_department"); ?>')) {
                $.ajax({
                    url: '<?php echo_uri("departments/remove_user_from_department") ?>',
                    type: 'POST',
                    data: {
                        user_id: userId,
                        department_id: departmentId
                    },
                    success: function(result) {
                        if (result.success) {
                            appAlert.success(result.message, {duration: 10000});
                            $row.fadeOut(function() {
                                $(this).remove();
                            });
                        } else {
                            appAlert.error(result.message);
                        }
                    }
                });
            }
        });
    });
</script>