<?php
// Reuse the stats prepared by controller for a compact overview like clients/overview
$total_departments = (int) get_array_value($stats, 'total_departments');
$active_departments = (int) get_array_value($stats, 'active_departments');
$total_members = (int) get_array_value($stats, 'total_members');
$total_tasks = (int) get_array_value($stats, 'total_tasks');
?>

<div class="mt20">
    <!-- Standard Stats Widgets -->
    <div class="row">
        <div class="col-md-3">
            <div class="card dashboard-icon-widget">
                <div class="card-body">
                    <div class="widget-icon bg-primary">
                        <i data-feather="layers" class="icon-24"></i>
                    </div>
                    <div class="widget-details">
                        <h1><?php echo $total_departments; ?></h1>
                        <span class="bg-transparent-white"><?php echo app_lang('departments'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-icon-widget">
                <div class="card-body">
                    <div class="widget-icon bg-success">
                        <i data-feather="check-circle" class="icon-24"></i>
                    </div>
                    <div class="widget-details">
                        <h1><?php echo $active_departments; ?></h1>
                        <span class="bg-transparent-white"><?php echo app_lang('active'); ?> <?php echo app_lang('departments'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-icon-widget">
                <div class="card-body">
                    <div class="widget-icon bg-info">
                        <i data-feather="users" class="icon-24"></i>
                    </div>
                    <div class="widget-details">
                        <h1><?php echo $total_members; ?></h1>
                        <span class="bg-transparent-white"><?php echo app_lang('team_members'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card dashboard-icon-widget">
                <div class="card-body">
                    <div class="widget-icon bg-warning">
                        <i data-feather="clipboard" class="icon-24"></i>
                    </div>
                    <div class="widget-details">
                        <h1><?php echo $total_tasks; ?></h1>
                        <span class="bg-transparent-white"><?php echo app_lang('tasks'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i data-feather="users" class="icon-16"></i> Top by <?php echo app_lang('members'); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($top_by_members)) { ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <?php foreach (array_slice($top_by_members, 0, 5) as $index => $d) { ?>
                                        <tr>
                                            <td class="text-center w10p"><?php echo $index + 1; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="color-tag me-2" style="background-color: <?php echo $d->color ?: '#6c757d'; ?>"></div>
                                                    <a href="<?php echo get_uri('departments/view/' . $d->id); ?>"><?php echo $d->title; ?></a>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-secondary"><?php echo (int)$d->total_members; ?></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="text-center text-muted">
                            <i data-feather="users" class="icon-24"></i>
                            <p><?php echo app_lang('no_record_found'); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i data-feather="clipboard" class="icon-16"></i> Top by <?php echo app_lang('tasks'); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($top_by_tasks)) { ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <?php foreach (array_slice($top_by_tasks, 0, 5) as $index => $d) { ?>
                                        <tr>
                                            <td class="text-center w10p"><?php echo $index + 1; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="color-tag me-2" style="background-color: <?php echo $d->color ?: '#6c757d'; ?>"></div>
                                                    <a href="<?php echo get_uri('departments/view/' . $d->id); ?>"><?php echo $d->title; ?></a>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-secondary"><?php echo (int)$d->total_tasks; ?></span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="text-center text-muted">
                            <i data-feather="clipboard" class="icon-24"></i>
                            <p><?php echo app_lang('no_record_found'); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

  
</div>
