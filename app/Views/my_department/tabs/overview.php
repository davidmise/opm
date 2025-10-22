<?php helper('text'); ?>

<!-- Department Statistics -->
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card dashboard-icon-widget h-100">
            <div class="card-body d-flex flex-column">
                <div class="widget-icon" style="background-color: <?php echo $department_info->color; ?>20;">
                    <i data-feather="command" class="icon-32" style="color: <?php echo $department_info->color; ?>;"></i>
                </div>
                <div class="widget-details flex-fill">
                    <h1><?php echo $stats['projects']['active']; ?></h1>
                    <span class="bg-transparent-white"><?php echo app_lang("active_projects"); ?></span>
                    <div class="progress mt10" style="height: 6px;">
                        <div class="progress-bar" style="width: <?php echo $stats['projects']['success_rate']; ?>%; background-color: <?php echo $department_info->color; ?>;"></div>
                    </div>
                    <small class="text-muted"><?php echo $stats['projects']['success_rate']; ?>% <?php echo app_lang('success_rate'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card dashboard-icon-widget h-100">
            <div class="card-body d-flex flex-column">
                <div class="widget-icon bg-warning">
                    <i data-feather="clock" class="icon-32 text-white"></i>
                </div>
                <div class="widget-details flex-fill">
                    <h1><?php echo $stats['tasks']['pending']; ?></h1>
                    <span class="bg-transparent-white"><?php echo app_lang("pending_tasks"); ?></span>
                    <div class="progress mt10" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $stats['tasks']['completion_rate']; ?>%;"></div>
                    </div>
                    <small class="text-muted"><?php echo $stats['tasks']['completion_rate']; ?>% <?php echo app_lang('completed'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card dashboard-icon-widget h-100">
            <div class="card-body d-flex flex-column">
                <div class="widget-icon bg-success">
                    <i data-feather="check-circle" class="icon-32 text-white"></i>
                </div>
                <div class="widget-details flex-fill">
                    <h1><?php echo $stats['tasks']['completed']; ?></h1>
                    <span class="bg-transparent-white"><?php echo app_lang("completed_tasks"); ?></span>
                    <div class="progress mt10" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 100%;"></div>
                    </div>
                    <small class="text-success"><?php echo app_lang('this_month'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card dashboard-icon-widget h-100">
            <div class="card-body d-flex flex-column">
                <div class="widget-icon bg-info">
                    <i data-feather="users" class="icon-32 text-white"></i>
                </div>
                <div class="widget-details flex-fill">
                    <h1><?php echo $stats['team']['total']; ?></h1>
                    <span class="bg-transparent-white"><?php echo app_lang("team_members"); ?></span>
                    <div class="progress mt10" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 100%;"></div>
                    </div>
                    <small class="text-info"><?php echo app_lang('active_members'); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4><i data-feather="activity" class="icon-16"></i> <?php echo app_lang('recent_activities'); ?></h4>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_activities)): ?>
                    <div class="timeline-container">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i data-feather="<?php echo $activity['icon']; ?>" class="icon-14 text-<?php echo $activity['color']; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <p class="mb5"><?php echo $activity['title']; ?></p>
                                    <small class="text-muted">
                                        <?php echo $activity['user']; ?> • <?php echo $activity['time']; ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i data-feather="activity" class="icon-48 text-muted"></i>
                        <p class="text-muted mt-2"><?php echo app_lang('no_recent_activities'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Deadlines -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4><i data-feather="calendar" class="icon-16"></i> <?php echo app_lang('upcoming_deadlines'); ?></h4>
            </div>
            <div class="card-body">
                <?php if (!empty($upcoming_deadlines)): ?>
                    <?php foreach ($upcoming_deadlines as $deadline): ?>
                        <div class="deadline-item d-flex align-items-center mb-3 p-2 rounded" style="background-color: var(--bs-light);">
                            <div class="deadline-badge me-3">
                                <span class="badge bg-<?php echo $deadline['urgency']; ?> rounded-pill">
                                    <?php echo $deadline['days_remaining']; ?>d
                                </span>
                            </div>
                            <div class="deadline-content flex-grow-1">
                                <h6 class="mb-1">
                                    <?php echo modal_anchor(get_uri("tasks/view"), $deadline['title'], array(
                                        "title" => app_lang('task_info') . " #" . $deadline['id'], 
                                        "data-post-id" => $deadline['id'],
                                        "class" => "text-dark"
                                    )); ?>
                                </h6>
                                <small class="text-muted">
                                    <?php echo $deadline['project']; ?> • 
                                    <?php echo format_to_date($deadline['deadline'], false); ?>
                                    <?php if ($deadline['assigned_to']): ?>
                                        • <?php echo $deadline['assigned_to']; ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i data-feather="calendar" class="icon-48 text-muted"></i>
                        <p class="text-muted mt-2"><?php echo app_lang('no_upcoming_deadlines'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Announcements & Team Performance -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4><i data-feather="bell" class="icon-16"></i> <?php echo app_lang('announcements'); ?></h4>
            </div>
            <div class="card-body">
                <?php if (!empty($department_announcements)): ?>
                    <?php foreach ($department_announcements as $announcement): ?>
                        <div class="announcement-item border-start ps-3 mb-3" style="border-color: <?php echo $department_info->color; ?> !important; border-width: 3px !important;">
                            <h6 class="mb-1"><?php echo $announcement->title; ?></h6>
                            <p class="text-muted mb-2"><?php echo character_limiter($announcement->description, 120); ?></p>
                            <small class="text-muted">
                                <?php echo isset($announcement->created_by_user) ? $announcement->created_by_user : 'Admin'; ?> • 
                                <?php echo isset($announcement->start_date) ? format_to_date($announcement->start_date, false) : date('Y-m-d'); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i data-feather="bell" class="icon-48 text-muted"></i>
                        <p class="text-muted mt-2"><?php echo app_lang('no_announcements'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4><i data-feather="trending-up" class="icon-16"></i> <?php echo app_lang('team_performance'); ?></h4>
            </div>
            <div class="card-body">
                <?php if (!empty($team_metrics)): ?>
                    <?php foreach ($team_metrics as $member): ?>
                        <div class="team-member-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><?php echo $member['name']; ?></h6>
                                <small class="text-muted"><?php echo $member['completion_rate']; ?>%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: <?php echo $member['completion_rate']; ?>%; background-color: <?php echo $department_info->color; ?>;"></div>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <?php echo $member['completed_tasks']; ?>/<?php echo $member['total_tasks']; ?> tasks completed
                            </small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i data-feather="trending-up" class="icon-48 text-muted"></i>
                        <p class="text-muted mt-2"><?php echo app_lang('no_team_data'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-container {
    max-height: 400px;
    overflow-y: auto;
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
    display: flex;
    align-items: flex-start;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: -13px;
    top: 25px;
    height: calc(100% - 15px);
    width: 2px;
    background: #e9ecef;
}

.timeline-marker {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.timeline-content {
    flex: 1;
    margin-left: 15px;
}

.deadline-item:hover {
    background-color: var(--bs-gray-100) !important;
}

.announcement-item:hover {
    background-color: var(--bs-gray-100) !important;
}

.announcement-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
}

.team-member-item:last-child {
    margin-bottom: 0 !important;
}
</style>

<script type="text/javascript">
$(document).ready(function() {
    // Initialize feather icons for this tab
    feather.replace();
});
</script>
