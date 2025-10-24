<?php helper('text'); ?>
<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix row">
        <div class="col-md-12">
            <div class="page-title clearfix mb20">
                <div class="d-flex justify-content-between align-items-center mb15">
                    <h1 class="mb-0">
                        <i data-feather="grid" class="icon-32"></i>
                        <?php echo $department_info->title; ?>
                    </h1>
                    
                    <?php if ($can_switch_department): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i data-feather="shuffle" class="icon-16"></i>
                            <?php echo app_lang('switch_department'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php foreach ($accessible_departments_details as $dept_details): ?>
                                <?php 
                                $is_active = ($dept_details->id == $department_info->id);
                                ?>
                                <li>
                                    <a class="dropdown-item <?php echo $is_active ? 'active' : ''; ?>" 
                                       href="#" 
                                       onclick="switchDepartment(<?php echo $dept_details->id; ?>); return false;">
                                        <?php echo $dept_details->title; ?>
                                        <?php if ($is_active): ?>
                                            <i data-feather="check" class="icon-16 float-end"></i>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Department Navigation Buttons -->
                <div class="title-button-group">
                    <button class="btn btn-primary department-nav-btn active" onclick="showSection('overview')" id="overview-btn">
                        <i data-feather="eye" class="icon-16"></i> <?php echo app_lang('overview'); ?>
                    </button>
                    <button class="btn btn-default department-nav-btn" onclick="showSection('projects')" id="projects-btn">
                        <i data-feather="command" class="icon-16"></i> <?php echo app_lang('projects'); ?>
                    </button>
                    <button class="btn btn-default department-nav-btn" onclick="showSection('tasks')" id="tasks-btn">
                        <i data-feather="check-square" class="icon-16"></i> <?php echo app_lang('tasks'); ?>
                    </button>
                    <button class="btn btn-default department-nav-btn" onclick="showSection('team')" id="team-btn">
                        <i data-feather="users" class="icon-16"></i> <?php echo app_lang('team_members'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Section -->
    <div id="overview-section" class="department-section">
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
                                    <p class="text-muted mb-2"><?php echo (strlen($announcement->description) > 120) ? substr($announcement->description, 0, 120) . '...' : $announcement->description; ?></p>
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
    </div>

    <!-- Projects Section -->
    <div id="projects-section" class="department-section" style="display: none;">
        <div class="card">
            <div class="page-title clearfix">
                <h1><i data-feather="command" class="icon-16"></i> <?php echo app_lang('projects'); ?></h1>
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("labels/modal_form"), "<i data-feather='tag' class='icon-16'></i> " . app_lang('manage_labels'), array("class" => "btn btn-outline-light", "title" => app_lang('manage_labels'), "data-post-type" => "project")); ?>
                    <?php echo anchor(get_uri("projects/import_projects_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_projects'), array("class" => "btn btn-outline-light")); ?>
                    <?php if ($login_user->user_type == "staff") { ?>
                        <?php echo modal_anchor(get_uri("projects/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_project'), array("class" => "btn btn-primary", "title" => app_lang('add_project'), "data-post-department_id" => $department_info->id)); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="table-responsive">
                <table id="department-projects-table" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><?php echo app_lang('id'); ?></th>
                            <th><?php echo app_lang('title'); ?></th>
                            <th><?php echo app_lang('client'); ?></th>
                            <th class="hide"><?php echo app_lang('department'); ?></th>
                            <th class="hide"><?php echo app_lang('price'); ?></th>
                            <th class="hide"><?php echo app_lang('start_date'); ?></th>
                            <th><?php echo app_lang('start_date'); ?></th>
                            <th class="hide"><?php echo app_lang('deadline'); ?></th>
                            <th><?php echo app_lang('deadline'); ?></th>
                            <th><?php echo app_lang('progress'); ?></th>
                            <th><?php echo app_lang('status'); ?></th>
                            <th><i data-feather="menu" class="icon-16"></i></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Tasks Section -->
    <div id="tasks-section" class="department-section" style="display: none;">
        <div class="card">
            <div class="page-title clearfix">
                <h1><i data-feather="check-square" class="icon-16"></i> <?php echo app_lang('tasks'); ?></h1>
                <div class="title-button-group">
                    <?php if ($can_create_tasks) { ?>
                        <?php echo modal_anchor(get_uri("tasks/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-primary", "title" => app_lang('add_task'))); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="table-responsive">
                <table id="department-tasks-table" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th></th> <!-- Status color (hidden) -->
                            <th><?php echo app_lang('id'); ?></th>
                            <th><?php echo app_lang('title'); ?></th>
                            <th></th> <!-- Title raw (hidden) -->
                            <th></th> <!-- Labels (hidden) -->
                            <th></th> <!-- Priority (hidden) -->
                            <th></th> <!-- Points (hidden) -->
                            <th></th> <!-- Start date raw (hidden) -->
                            <th><?php echo app_lang('start_date'); ?></th>
                            <th></th> <!-- Deadline raw (hidden) -->
                            <th><?php echo app_lang('deadline'); ?></th>
                            <th></th> <!-- Milestone (hidden) -->
                            <th><?php echo app_lang('project'); ?></th>
                            <th><?php echo app_lang('assigned_to'); ?></th>
                            <th></th> <!-- Collaborators (hidden) -->
                            <th><?php echo app_lang('status'); ?></th>
                            <th><?php echo app_lang('actions'); ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div id="team-section" class="department-section" style="display: none;">
        <div class="card">
            <div class="page-title clearfix">
                <h1><i data-feather="users" class="icon-16"></i> <?php echo app_lang('team_members'); ?></h1>
                <div class="title-button-group">
                    <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "can_add_or_invite_new_team_members")) { ?>
                        <!-- <?php echo modal_anchor(get_uri("team_members/import_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_team_members'), array("class" => "btn btn-default", "title" => app_lang('import_team_members'))); ?>
                        <!-- <?php echo modal_anchor(get_uri("team_members/invitation_modal"), "<i data-feather='mail' class='icon-16'></i> " . app_lang('send_invitation'), array("class" => "btn btn-default", "title" => app_lang('send_invitation'))); ?> --> 
                          <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "can_manage_departments")) { ?>
                    <?php echo modal_anchor(get_uri("departments/add_user_modal/" . $department_info->id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_member'), array("class" => "btn btn-primary", "title" => app_lang('add_member_to_department'))); ?>
                <?php } ?>
                
                    <?php } ?>
                </div>
            </div>
            <div class="table-responsive">
                <table id="department-team-table" class="display" cellspacing="0" width="100%">            
                </table>
            </div>
        </div>
    </div>
</div>



<style>
/* Department Header Styling */
.page-title h1 {
    font-size: 28px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-title .d-flex {
    margin-bottom: 20px;
}

.page-title .dropdown .btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-size: 14px;
    white-space: nowrap;
}

.page-title .dropdown-menu {
    min-width: 200px;
}

.page-title .dropdown-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
}

.page-title .dropdown-item.active {
    background-color: #f8f9fa;
    color: #333;
    font-weight: 500;
}

.department-nav-btn {
    margin-right: 10px;
    margin-bottom: 10px;
}

.department-nav-btn.active {
    background-color: <?php echo $department_info->color; ?> !important;
    border-color: <?php echo $department_info->color; ?> !important;
    color: white !important;
}

.department-section {
    margin-top: 20px;
}

.dashboard-icon-widget .widget-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.timeline-container {
    max-height: 400px;
    overflow-y: auto;
}

/* Team Member Table Styling */
#department-team-table .avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

#department-team-table .avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

#department-team-table .d-flex {
    align-items: center;
}

#department-team-table .badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
}

#department-team-table .dropdown-item {
    padding: 8px 16px;
    display: flex;
    align-items: center;
}

#department-team-table .dropdown-item i {
    margin-right: 8px;
}

#department-team-table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Round Action Buttons */
#department-team-table .btn.rounded-circle {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

#department-team-table .btn.rounded-circle .icon-16 {
    width: 16px;
    height: 16px;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.timeline-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.timeline-marker {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.timeline-content {
    flex-grow: 1;
}

.deadline-item {
    transition: all 0.2s ease;
}

.deadline-item:hover {
    background-color: #f8f9fa !important;
}

.announcement-item {
    transition: all 0.2s ease;
    border-radius: 8px;
    padding: 15px;
    background-color: #f8f9fa;
}

.team-member-item {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

/* Department color theming */
.page-title h1 {
    color: <?php echo $department_info->color; ?>;
}

.card-header h4 {
    color: #333;
    margin: 0;
}

.widget-details h1 {
    font-size: 2.5rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}
</style>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize feather icons
    feather.replace();
    
    // Tab persistence functionality
    var currentTabKey = 'my_department_active_tab';
    var currentTab = localStorage.getItem(currentTabKey);
    
    // Check URL hash first, then localStorage, then default
    var hash = window.location.hash.substring(1); // Remove #
    var targetSection = null;
    
    if (hash) {
        // Map hash names to sections
        var hashToSection = {
            'overview': 'overview',
            'projects': 'projects', 
            'tasks': 'tasks',
            'team': 'team'
        };
        targetSection = hashToSection[hash];
    }
    
    // Fallback to localStorage if no valid hash
    if (!targetSection && currentTab) {
        targetSection = currentTab;
    }
    
    // Default to overview if nothing else
    if (!targetSection) {
        targetSection = 'overview';
    }
    
    // Load the determined section
    showSection(targetSection);
    
    // Initialize tables only for the active section
    // Tables will be initialized lazily when tabs are clicked
    var initializedTables = {};
    
    // Initialize table for the initially visible section
    initializeTableForSection(targetSection);
    
    // Store section persistence on button clicks
    $('.department-nav-btn').on('click', function() {
        var sectionName = $(this).attr('id').replace('-btn', '');
        localStorage.setItem(currentTabKey, sectionName);
        
        // Initialize table for this section if not already done
        initializeTableForSection(sectionName);
        
        // Update URL hash without triggering scroll
        if (sectionName && sectionName !== 'overview') {
            history.replaceState(null, null, '#' + sectionName);
        } else {
            history.replaceState(null, null, window.location.pathname);
        }
    });
    
    // Helper function to initialize tables based on section
    function initializeTableForSection(sectionName) {
        if (initializedTables[sectionName]) {
            return; // Already initialized
        }
        
        switch(sectionName) {
            case 'projects':
                loadDepartmentProjects();
                break;
            case 'tasks':
                loadDepartmentTasks();
                break;
            case 'team':
                loadDepartmentTeam();
                break;
        }
        
        initializedTables[sectionName] = true;
    }
    
    // Handle browser back/forward navigation
    $(window).on('popstate', function(e) {
        var hash = window.location.hash.substring(1);
        if (hash) {
            var hashToSection = {
                'overview': 'overview',
                'projects': 'projects',
                'tasks': 'tasks', 
                'team': 'team'
            };
            var targetSection = hashToSection[hash];
            if (targetSection) {
                showSection(targetSection);
            }
        }
    });
});

function showSection(sectionName) {
    // Hide all sections
    $('.department-section').hide();
    
    // Show the selected section
    $('#' + sectionName + '-section').show();
    
    // Update button states
    $('.department-nav-btn').removeClass('active btn-primary').addClass('btn-default');
    $('#' + sectionName + '-btn').removeClass('btn-default').addClass('btn-primary active');
    
    // Store current section for persistence
    localStorage.setItem('my_department_active_tab', sectionName);
    
    // Update URL hash without triggering scroll
    if (sectionName && sectionName !== 'overview') {
        history.replaceState(null, null, '#' + sectionName);
    } else {
        history.replaceState(null, null, window.location.pathname);
    }
    
    // Initialize specific table for the section
    if (sectionName === 'projects') {
        loadDepartmentProjects();
    } else if (sectionName === 'tasks') {
        loadDepartmentTasks();
    } else if (sectionName === 'team') {
        loadDepartmentTeam();
    }
    
    // Replace feather icons after DOM changes
    safeFeatherReplace();
}

function safeFeatherReplace() {
    if (typeof feather !== 'undefined' && feather.replace) {
        feather.replace();
    }
}

function loadDepartmentProjects() {
    if ($("#department-projects-table").length && !$.fn.DataTable.isDataTable('#department-projects-table')) {
        $("#department-projects-table").appTable({
            source: '<?php echo_uri("projects/list_data") ?>',
            filterParams: {department_id: "<?php echo $department_info->id; ?>"},
            columns: [
                {title: '<?php echo app_lang("id") ?>', "class": "text-center w50"},
                {title: '<?php echo app_lang("title") ?>'},
                {title: '<?php echo app_lang("client") ?>'},
                {title: '<?php echo app_lang("department") ?>', visible: false, searchable: false},
                {title: '<?php echo app_lang("price") ?>', visible: false, searchable: false},
                {title: '<?php echo app_lang("start_date") ?>', visible: false, searchable: false},
                {title: '<?php echo app_lang("start_date") ?>', "class": "text-center w100", "iDataSort": 5},
                {title: '<?php echo app_lang("deadline") ?>', visible: false, searchable: false},
                {title: '<?php echo app_lang("deadline") ?>', "class": "text-center w100", "iDataSort": 7},
                {title: '<?php echo app_lang("progress") ?>', "class": "text-center w100"},
                {title: '<?php echo app_lang("status") ?>', "class": "text-center w100"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            columnDefs: [
                {targets: [3, 4], visible: false}
            ],
            printColumns: [0, 1, 2, 6, 8, 9, 10],
            xlsColumns: [0, 1, 2, 6, 8, 9, 10],
            onInitComplete: function() {
                // Initialize feather icons
                if (typeof feather !== 'undefined') {
                    setTimeout(function() {
                        feather.replace();
                    }, 100);
                }
            }
        });
    }
}

function loadDepartmentTasks() {
    if ($("#department-tasks-table").length && !$.fn.DataTable.isDataTable('#department-tasks-table')) {
        $("#department-tasks-table").appTable({
            source: '<?php echo_uri("tasks/list_data/department/" . $department_info->id) ?>',
            columns: [
                {visible: false, searchable: false}, // 0: status_color
                {title: '<?php echo app_lang("id") ?>', "class": "text-center w50"}, // 1: id
                {title: '<?php echo app_lang("title") ?>'}, // 2: title (formatted)
                {title: '<?php echo app_lang("title") ?>', visible: false, searchable: false}, // 3: title (raw)
                {title: '<?php echo app_lang("label") ?>', visible: false, searchable: false}, // 4: labels
                {title: '<?php echo app_lang("priority") ?>', visible: false, searchable: false}, // 5: priority
                {title: '<?php echo app_lang("points") ?>', visible: false, searchable: false}, // 6: points
                {visible: false, searchable: false}, // 7: start_date (raw)
                {title: '<?php echo app_lang("start_date") ?>', "class": "text-center", "iDataSort": 7}, // 8: start_date (formatted)
                {visible: false, searchable: false}, // 9: deadline (raw)
                {title: '<?php echo app_lang("deadline") ?>', "class": "text-center", "iDataSort": 9}, // 10: deadline (formatted)
                {title: '<?php echo app_lang("milestone") ?>', visible: false}, // 11: milestone
                {title: '<?php echo app_lang("project") ?>'}, // 12: context_title (project/client/etc)
                {title: '<?php echo app_lang("assigned_to") ?>', "class": "text-center w150"}, // 13: assigned_to
                {title: '<?php echo app_lang("collaborators") ?>', visible: false}, // 14: collaborators
                {title: '<?php echo app_lang("status") ?>', "class": "text-center"}, // 15: status
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w150"} // 16: actions
            ],
            printColumns: [1, 2, 8, 10, 12, 13, 15],
            xlsColumns: [1, 2, 8, 10, 12, 13, 15],
            onInitComplete: function() {
                // Initialize feather icons after table loads
                if (typeof feather !== 'undefined') {
                    setTimeout(function() {
                        feather.replace();
                    }, 100);
                }
            }
        });
    }
}

function loadDepartmentTeam() {
    if ($("#department-team-table").length && !$.fn.DataTable.isDataTable('#department-team-table')) {
        $("#department-team-table").appTable({
            source: '<?php echo_uri("departments/department_team_list_data/" . $department_info->id) ?>',
            order: [[0, "asc"]],
            columns: [
                {title: "<?php echo app_lang("member") ?>", "class": "w200 all"},
                {title: "<?php echo app_lang("role") ?>", "class": "w15p"},
                {title: "<?php echo app_lang("status") ?>", "class": "w15p text-center"},
                {title: "<?php echo app_lang("email") ?>", "class": "w20p"},
                {title: "<?php echo app_lang("actions") ?>", "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3],
            xlsColumns: [0, 1, 2, 3],
            onInitComplete: function() {
                // Handle set as primary
                $('body').on('click', '.set-as-primary', function(e) {
                    e.preventDefault();
                    var userId = $(this).data('user-id');
                    var departmentId = $(this).data('department-id');
                    var userName = $(this).data('user-name');
                    
                    if (confirm('<?php echo app_lang("set_as_primary_confirmation"); ?>'.replace('{member}', userName))) {
                        $.ajax({
                            url: '<?php echo_uri("departments/set_primary_department") ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                user_id: userId,
                                department_id: departmentId
                            },
                            success: function(result) {
                                if (result.success) {
                                    appAlert.success(result.message, {duration: 10000});
                                    // Reload department team table
                                    $("#department-team-table").appTable({reload: true});
                                } else {
                                    appAlert.error(result.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                appAlert.error('Error: ' + error);
                            }
                        });
                    }
                });
                
                // Handle remove user from department
                $('body').on('click', '.remove-user-from-department', function(e) {
                    e.preventDefault();
                    var userId = $(this).data('user-id');
                    var departmentId = $(this).data('department-id');
                    var userName = $(this).data('user-name');
                    
                    if (confirm('<?php echo app_lang("remove_member_from_department_confirmation"); ?>'.replace('{member}', userName))) {
                        $.ajax({
                            url: '<?php echo_uri("departments/remove_user_from_department") ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                user_id: userId,
                                department_id: departmentId
                            },
                            success: function(result) {
                                if (result.success) {
                                    appAlert.success(result.message, {duration: 10000});
                                    // Reload department team table
                                    $("#department-team-table").appTable({reload: true});
                                } else {
                                    appAlert.error(result.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                appAlert.error('Error: ' + error);
                            }
                        });
                    }
                });
                
                // Initialize feather icons after table loads
                if (typeof feather !== 'undefined') {
                    setTimeout(function() {
                        feather.replace();
                    }, 100);
                }
            }
        });
    }
}

function switchDepartment(departmentId) {
    $.ajax({
        url: '<?php echo get_uri("my_department/switch_department"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {department_id: departmentId},
        success: function (result) {
            if (result.success) {
                appAlert.success(result.message);
                if (result.refresh_page) {
                    location.reload();
                }
            } else {
                appAlert.error(result.message);
            }
        }
    });
}

// Custom confirmation handler for department user removal
$(document).ready(function() {
    // Override the confirmation dialog for remove user from department actions
    $('body').on('click', 'a[data-action-url*="remove_user_from_department"]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $element = $(this);
        var confirmationTitle = $element.attr('data-confirmation-title') || '<?php echo app_lang('remove_user_confirmation_title'); ?>';
        var userName = $element.attr('data-user-name') || 'this user';
        var departmentName = $element.attr('data-department-name') || 'this department';
        
        // Create personalized confirmation message
        var confirmationMessage = 'Are you sure you want to remove ' + userName + ' from this ' + departmentName + ' department?';
        
        // Update modal content
        $("#confirmationModalTitle").html(confirmationTitle);
        $("#confirmationModalContent .container-fluid").html(confirmationMessage);
        $("#confirmDeleteButton").html('<i data-feather="user-minus" class="icon-16"></i> <?php echo app_lang("remove"); ?>');
        
        // Set up the delete button click handler
        $("#confirmDeleteButton").unbind("click");
        $("#confirmDeleteButton").on("click", function() {
            $.ajax({
                url: $element.attr('data-action-url'),
                type: 'POST',
                dataType: 'json',
                data: {id: $element.attr('data-id')},
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message);
                        // Refresh the team table
                        if ($("#department-team-table").DataTable) {
                            $("#department-team-table").DataTable().ajax.reload();
                        }
                    } else {
                        appAlert.error(result.message);
                    }
                },
                error: function() {
                    appAlert.error('<?php echo app_lang('error_occurred'); ?>');
                }
            });
            $("#confirmationModal").modal('hide');
        });
        
        // Show the modal
        $("#confirmationModal").modal('show');
        
        // Replace feather icons
        setTimeout(function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }, 100);
        
        return false;
    });
});
</script>