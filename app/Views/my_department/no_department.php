<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-md-12">
            <div class="page-title clearfix mb20">
                <h1>
                    <i data-feather="grid" class="icon-32"></i>
                    <?php echo app_lang('my_department'); ?>
                </h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-body text-center p-5">
                    <div class="empty-state">
                        <i data-feather="user-x" class="icon-80 text-muted mb-4"></i>
                        <h4 class="text-muted"><?php echo app_lang('no_department_assigned'); ?></h4>
                        <p class="text-muted mb-4">
                            <?php echo app_lang('no_department_assigned_message'); ?>
                        </p>
                        
                        <div class="mt-4">
                            <?php if ($this->login_user->is_admin): ?>
                                <p class="text-info mb-3">
                                    <i data-feather="info" class="icon-16"></i>
                                    <?php echo app_lang('admin_can_assign_departments'); ?>
                                </p>
                                <?php echo anchor(get_uri("departments"), "<i data-feather='grid' class='icon-16'></i> " . app_lang('manage_departments'), array("class" => "btn btn-primary")); ?>
                            <?php else: ?>
                                <p class="text-warning mb-3">
                                    <i data-feather="alert-triangle" class="icon-16"></i>
                                    <?php echo app_lang('contact_admin_for_department_assignment'); ?>
                                </p>
                                <?php echo anchor(get_uri("dashboard"), "<i data-feather='home' class='icon-16'></i> " . app_lang('go_to_dashboard'), array("class" => "btn btn-outline-primary")); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Show available departments if admin -->
    <?php if ($this->login_user->is_admin): ?>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i data-feather="grid" class="icon-16"></i> <?php echo app_lang('available_departments'); ?></h4>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted">
                        <p><?php echo app_lang('manage_departments_from_admin_panel'); ?></p>
                        <?php echo anchor(get_uri("departments"), app_lang('view_all_departments'), array("class" => "btn btn-outline-primary")); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.empty-state {
    padding: 40px 20px;
}

.icon-80 {
    width: 80px;
    height: 80px;
    stroke-width: 1;
}
</style>