<?php echo form_open(get_uri("departments/dashboard/" . $department_info->id), array("id" => "department-dashboard-form", "class" => "general-form", "role" => "form")); ?>

<div class="page-content clearfix">
    <div class="bg-primary-gradient clearfix">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-title clearfix">
                        <h1 style="color: #fff;">
                            <i class="<?php echo $department_info->icon ?: 'ti ti-building'; ?>" style="color: <?php echo $department_info->color; ?>;"></i>
                            <?php echo $department_info->title; ?> Dashboard
                        </h1>
                        <p style="color: #fff; opacity: 0.8;"><?php echo $department_info->description; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-primary">
                            <i class="ti ti-users"></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo $dashboard_data['statistics']->member_count; ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang("team_members"); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-success">
                            <i class="ti ti-folder"></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo $dashboard_data['statistics']->active_projects; ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang("active_projects"); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-warning">
                            <i class="ti ti-check-square"></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo $dashboard_data['statistics']->active_tasks; ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang("active_tasks"); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card dashboard-icon-widget">
                    <div class="card-body">
                        <div class="widget-icon bg-info">
                            <i class="ti ti-clock"></i>
                        </div>
                        <div class="widget-details">
                            <h1><?php echo $dashboard_data['monthly_hours']; ?></h1>
                            <span class="bg-transparent-white"><?php echo app_lang("hours_this_month"); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Projects -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="ti ti-folder"></i>&nbsp; <?php echo app_lang("recent_projects"); ?>
                        <div class="float-end">
                            <a href="<?php echo get_uri('projects'); ?>" class="btn btn-outline-primary btn-sm">
                                <?php echo app_lang("view_all"); ?>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($dashboard_data['recent_projects'])): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo app_lang("project"); ?></th>
                                            <th><?php echo app_lang("status"); ?></th>
                                            <th><?php echo app_lang("deadline"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dashboard_data['recent_projects'] as $project): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo get_uri('projects/view/' . $project->id); ?>">
                                                        <?php echo $project->title; ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $status_class = $project->status == 'open' ? 'bg-success' : 'bg-primary';
                                                    ?>
                                                    <span class="badge <?php echo $status_class; ?>">
                                                        <?php echo app_lang($project->status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo $project->deadline ? format_to_date($project->deadline, false) : '-'; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted"><?php echo app_lang("no_projects_found"); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Department Alerts -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="ti ti-alert-triangle"></i>&nbsp; <?php echo app_lang("department_alerts"); ?>
                    </div>
                    <div class="card-body">
                        <?php if ($dashboard_data['overdue_tasks'] > 0): ?>
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle"></i>
                                <strong><?php echo $dashboard_data['overdue_tasks']; ?></strong> 
                                <?php echo app_lang("overdue_tasks"); ?>
                                <a href="<?php echo get_uri('tasks?department_id=' . $department_info->id . '&status=overdue'); ?>" class="float-end">
                                    <?php echo app_lang("view_details"); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <i class="ti ti-chart-bar"></i>
                            <strong><?php echo $dashboard_data['statistics']->total_projects; ?></strong> 
                            <?php echo app_lang("total_projects"); ?>
                            <span class="float-end">
                                <small><?php echo $dashboard_data['statistics']->completed_projects; ?> completed</small>
                            </span>
                        </div>

                        <div class="alert alert-success">
                            <i class="ti ti-check"></i>
                            <strong><?php echo $dashboard_data['statistics']->completed_tasks; ?></strong> 
                            <?php echo app_lang("completed_tasks"); ?>
                            <span class="float-end">
                                <small>vs <?php echo $dashboard_data['statistics']->active_tasks; ?> active</small>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Team -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="ti ti-users"></i>&nbsp; <?php echo app_lang("department_team"); ?>
                        <div class="float-end">
                            <a href="<?php echo get_uri('departments/manage_users/' . $department_info->id); ?>" class="btn btn-outline-primary btn-sm">
                                <?php echo app_lang("manage_users"); ?>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="department-team-table"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    // Wait for jQuery to be available before executing
    function initDashboardScript() {
        if (typeof $ === 'undefined') {
            // jQuery not loaded yet, wait 100ms and try again
            setTimeout(initDashboardScript, 100);
            return;
        }
        
        $(document).ready(function () {
            // Load department team table
            $("#department-team-table").appTable({
                source: '<?php echo_uri("departments/department_team_list_data/" . $department_info->id) ?>',
                order: [[1, "asc"]],
                columns: [
                    {title: "<?php echo app_lang("member") ?>", "class": "w-20p"},
                    {title: "<?php echo app_lang("role") ?>", "class": "w-15p"},
                    {title: "<?php echo app_lang("primary_department") ?>", "class": "text-center w-10p"},
                    {title: "<?php echo app_lang("email") ?>", "class": "w-20p"},
                    {title: "<i class='ti ti-menu-2'></i>", "class": "text-center option w-100px"}
                ],
                printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3]
        });

        // Auto refresh dashboard data every 5 minutes
        setInterval(function() {
            location.reload();
        }, 300000); // 5 minutes
    });
}

// Start the initialization
initDashboardScript();
</script>