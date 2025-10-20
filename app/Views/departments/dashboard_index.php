<?php
/**
 * Departments Dashboard - Main Entry Point
 * Comprehensive overview of all departments with statistics, cards, and quick actions
 */

// Extract statistics
$total_departments = $statistics->total_departments ?? 0;
$active_departments = $statistics->active_departments ?? 0;
$total_members = $statistics->total_members ?? 0;
$total_projects = $statistics->total_projects ?? 0;
$active_projects = $statistics->active_projects ?? 0;
$total_tasks = $statistics->total_tasks ?? 0;
$departments_without_head = $statistics->departments_without_head ?? 0;
$departments_without_members = $statistics->departments_without_members ?? 0;

// Calculate derived metrics
$inactive_departments = $total_departments - $active_departments;
$avg_members_per_dept = $total_departments > 0 ? round($total_members / $total_departments, 1) : 0;
$avg_projects_per_dept = $total_departments > 0 ? round($total_projects / $total_departments, 1) : 0;
?>

<div id="page-content" class="page-wrapper clearfix departments-dashboard">
    <!-- Page Header -->
    <div class="page-title clearfix mb-4">
        <h1>
            <i data-feather="grid" class="icon-24 me-2"></i>
            <?php echo app_lang('departments_dashboard'); ?>
        </h1>
        <div class="title-button-group">
            <?php echo anchor(get_uri("departments/list_view"), "<i data-feather='list' class='icon-16'></i> " . app_lang('list_view'), array("class" => "btn btn-default", "title" => app_lang('view_as_list'))); ?>
            <?php echo modal_anchor(get_uri("departments/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_department'), array("class" => "btn btn-primary", "title" => app_lang('add_department'))); ?>
        </div>
    </div>

    <!-- Key Metrics Overview -->
    <div class="row mb-4">
        <!-- Total Departments -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-light me-3">
                            <i data-feather="grid" class="icon-24 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase small mb-1"><?php echo app_lang('total_departments'); ?></h6>
                            <h2 class="mb-0 fw-bold"><?php echo $total_departments; ?></h2>
                            <small class="text-success">
                                <i data-feather="check-circle" class="icon-12"></i> 
                                <?php echo $active_departments; ?> <?php echo app_lang('active'); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Members -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-light me-3">
                            <i data-feather="users" class="icon-24 text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase small mb-1"><?php echo app_lang('team_members'); ?></h6>
                            <h2 class="mb-0 fw-bold"><?php echo $total_members; ?></h2>
                            <small class="text-muted">
                                <i data-feather="trending-up" class="icon-12"></i>
                                <?php echo $avg_members_per_dept; ?> <?php echo app_lang('avg_per_department'); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Projects -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-light me-3">
                            <i data-feather="command" class="icon-24 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase small mb-1"><?php echo app_lang('total_projects'); ?></h6>
                            <h2 class="mb-0 fw-bold"><?php echo $total_projects; ?></h2>
                            <small class="text-info">
                                <i data-feather="activity" class="icon-12"></i>
                                <?php echo $active_projects; ?> <?php echo app_lang('active'); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-light me-3">
                            <i data-feather="check-square" class="icon-24 text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted text-uppercase small mb-1"><?php echo app_lang('total_tasks'); ?></h6>
                            <h2 class="mb-0 fw-bold"><?php echo $total_tasks; ?></h2>
                            <small class="text-muted">
                                <?php echo app_lang('across_all_departments'); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Alerts (if any) -->
    <?php if ($departments_without_head > 0 || $departments_without_members > 0) { ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-warning-light">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i data-feather="alert-triangle" class="icon-16 text-warning me-2"></i>
                        <?php echo app_lang('attention_required'); ?>
                    </h6>
                    <div class="row">
                        <?php if ($departments_without_head > 0) { ?>
                        <div class="col-md-6 mb-2">
                            <div class="alert-item">
                                <i data-feather="user-x" class="icon-14 me-2 text-warning"></i>
                                <span class="fw-medium"><?php echo $departments_without_head; ?></span> 
                                <?php echo app_lang('departments_without_head'); ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if ($departments_without_members > 0) { ?>
                        <div class="col-md-6 mb-2">
                            <div class="alert-item">
                                <i data-feather="users" class="icon-14 me-2 text-warning"></i>
                                <span class="fw-medium"><?php echo $departments_without_members; ?></span> 
                                <?php echo app_lang('departments_without_members'); ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- Filter and Search -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i data-feather="search" class="icon-16"></i>
                </span>
                <input type="text" class="form-control" id="department-search" placeholder="<?php echo app_lang('search_departments'); ?>">
            </div>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="dept-filter" id="filter-all" value="all" checked>
                <label class="btn btn-outline-secondary" for="filter-all"><?php echo app_lang('all'); ?></label>
                
                <input type="radio" class="btn-check" name="dept-filter" id="filter-active" value="active">
                <label class="btn btn-outline-secondary" for="filter-active"><?php echo app_lang('active'); ?></label>
                
                <input type="radio" class="btn-check" name="dept-filter" id="filter-has-members" value="has-members">
                <label class="btn btn-outline-secondary" for="filter-has-members"><?php echo app_lang('has_members'); ?></label>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="row" id="departments-grid">
        <?php if (!empty($departments)) { ?>
            <?php foreach ($departments as $dept) { 
                // Calculate completion rate
                $task_completion_rate = $dept->total_tasks > 0 ? round(($dept->completed_tasks / $dept->total_tasks) * 100, 0) : 0;
                $has_members = $dept->total_members > 0;
                $is_active = $dept->is_active ?? 1;
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4 department-card-wrapper" 
                 data-department-name="<?php echo strtolower($dept->title); ?>"
                 data-has-members="<?php echo $has_members ? 'yes' : 'no'; ?>"
                 data-is-active="<?php echo $is_active ? 'yes' : 'no'; ?>">
                <div class="card department-card border-0 shadow-sm h-100" style="border-top: 4px solid <?php echo $dept->color; ?> !important;">
                    <div class="card-body">
                        <!-- Department Header -->
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">
                                    <span class="color-badge me-2" style="background-color: <?php echo $dept->color; ?>; width: 12px; height: 12px; display: inline-block; border-radius: 50%;"></span>
                                    <?php echo $dept->title; ?>
                                </h5>
                                <?php if ($dept->head_user_name) { ?>
                                <small class="text-muted">
                                    <i data-feather="user" class="icon-12"></i> 
                                    <?php echo $dept->head_user_name; ?>
                                </small>
                                <?php } else { ?>
                                <small class="text-warning">
                                    <i data-feather="alert-circle" class="icon-12"></i>
                                    <?php echo app_lang('no_head_assigned'); ?>
                                </small>
                                <?php } ?>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i data-feather="more-vertical" class="icon-16"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo get_uri('departments/view/' . $dept->id); ?>">
                                            <i data-feather="eye" class="icon-16 me-2"></i>
                                            <?php echo app_lang('view_details'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php echo modal_anchor(get_uri("departments/modal_form"), '<i data-feather="edit" class="icon-16 me-2"></i>' . app_lang('edit'), array("class" => "dropdown-item", "title" => app_lang('edit_department'), "data-post-id" => $dept->id)); ?>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-action="delete-confirmation" data-id="<?php echo $dept->id; ?>" data-action-url="<?php echo get_uri('departments/delete'); ?>">
                                            <i data-feather="trash-2" class="icon-16 me-2"></i>
                                            <?php echo app_lang('delete'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Department Description -->
                        <?php if ($dept->description) { ?>
                        <p class="card-text text-muted small mb-3" style="height: 40px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            <?php echo $dept->description; ?>
                        </p>
                        <?php } else { ?>
                        <p class="card-text text-muted small mb-3" style="height: 40px;">
                            <em><?php echo app_lang('no_description'); ?></em>
                        </p>
                        <?php } ?>

                        <!-- Statistics -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="stat-box text-center p-2 rounded" style="background-color: <?php echo $dept->color; ?>15;">
                                    <div class="stat-number fw-bold" style="color: <?php echo $dept->color; ?>;"><?php echo $dept->total_members; ?></div>
                                    <div class="stat-label text-muted small">
                                        <i data-feather="users" class="icon-12"></i>
                                        <?php echo app_lang('members'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box text-center p-2 rounded" style="background-color: <?php echo $dept->color; ?>15;">
                                    <div class="stat-number fw-bold" style="color: <?php echo $dept->color; ?>;"><?php echo $dept->total_projects; ?></div>
                                    <div class="stat-label text-muted small">
                                        <i data-feather="command" class="icon-12"></i>
                                        <?php echo app_lang('projects'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Task Completion Progress -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted"><?php echo app_lang('task_completion'); ?></small>
                                <small class="fw-bold" style="color: <?php echo $dept->color; ?>;"><?php echo $task_completion_rate; ?>%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?php echo $task_completion_rate; ?>%; background-color: <?php echo $dept->color; ?>;" 
                                     aria-valuenow="<?php echo $task_completion_rate; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="d-flex gap-2">
                            <a href="<?php echo get_uri('departments/view/' . $dept->id); ?>" 
                               class="btn btn-sm btn-outline-primary flex-grow-1" 
                               style="border-color: <?php echo $dept->color; ?>; color: <?php echo $dept->color; ?>;">
                                <i data-feather="eye" class="icon-14"></i>
                                <?php echo app_lang('view'); ?>
                            </a>
                            <?php echo modal_anchor(get_uri("departments/modal_form"), '<i data-feather="edit" class="icon-14"></i> ' . app_lang('edit'), array("class" => "btn btn-sm btn-outline-secondary flex-grow-1", "title" => app_lang('edit_department'), "data-post-id" => $dept->id)); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i data-feather="inbox" class="icon-48 text-muted mb-3"></i>
                        <h5 class="text-muted"><?php echo app_lang('no_departments_found'); ?></h5>
                        <p class="text-muted"><?php echo app_lang('create_your_first_department'); ?></p>
                        <?php echo modal_anchor(get_uri("departments/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_department'), array("class" => "btn btn-primary mt-3", "title" => app_lang('add_department'))); ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- No Results Message -->
    <div id="no-results-message" class="row" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i data-feather="search" class="icon-48 text-muted mb-3"></i>
                    <h5 class="text-muted"><?php echo app_lang('no_departments_match_filter'); ?></h5>
                    <p class="text-muted"><?php echo app_lang('try_different_search_criteria'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Styles */
.departments-dashboard {
    background: #f8f9fa;
    padding: 20px;
}

.dashboard-stat-card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.dashboard-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-light {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.bg-success-light {
    background-color: rgba(var(--bs-success-rgb), 0.1);
}

.bg-info-light {
    background-color: rgba(var(--bs-info-rgb), 0.1);
}

.bg-warning-light {
    background-color: rgba(var(--bs-warning-rgb), 0.1);
}

/* Department Cards */
.department-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    cursor: default;
}

.department-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.department-card .card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
}

.stat-box {
    background-color: #f8f9fa;
    transition: all 0.2s ease;
}

.department-card:hover .stat-box {
    transform: scale(1.05);
}

.stat-number {
    font-size: 1.5rem;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    line-height: 1;
    margin-top: 4px;
}

.color-badge {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Progress bars */
.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

/* Alert item */
.alert-item {
    display: flex;
    align-items: center;
    padding: 8px;
    border-radius: 6px;
    background: white;
}

/* Responsive */
@media (max-width: 768px) {
    .departments-dashboard {
        padding: 10px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
    }
    
    .dashboard-stat-card h2 {
        font-size: 1.5rem;
    }
}

/* Filter buttons */
.btn-check:checked + .btn-outline-secondary {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}
</style>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize feather icons
    setTimeout(function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }, 100);

    // Search functionality
    $('#department-search').on('keyup', function() {
        filterDepartments();
    });

    // Filter buttons
    $('input[name="dept-filter"]').on('change', function() {
        filterDepartments();
    });

    function filterDepartments() {
        var searchTerm = $('#department-search').val().toLowerCase();
        var filter = $('input[name="dept-filter"]:checked').val();
        var visibleCount = 0;

        $('.department-card-wrapper').each(function() {
            var $card = $(this);
            var departmentName = $card.data('department-name');
            var hasMembers = $card.data('has-members');
            var isActive = $card.data('is-active');
            var show = true;

            // Search filter
            if (searchTerm && departmentName.indexOf(searchTerm) === -1) {
                show = false;
            }

            // Status filter
            if (filter === 'active' && isActive !== 'yes') {
                show = false;
            } else if (filter === 'has-members' && hasMembers !== 'yes') {
                show = false;
            }

            if (show) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            $('#no-results-message').show();
        } else {
            $('#no-results-message').hide();
        }
    }

    // Delete confirmation
    $('[data-action="delete-confirmation"]').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        var actionUrl = $this.data('action-url');
        
        appAlert.confirm("<?php echo app_lang('delete_department_confirmation'); ?>", function() {
            $.ajax({
                url: actionUrl,
                type: 'POST',
                dataType: 'json',
                data: {id: id, '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'},
                success: function(result) {
                    if (result.success) {
                        appAlert.success(result.message);
                        location.reload();
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });
        });
    });
});
</script>
