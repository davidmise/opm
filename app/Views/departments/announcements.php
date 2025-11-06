<!-- COMPREHENSIVE DEPARTMENT ANNOUNCEMENTS TAB -->
<style>
    /* Fix Select2 multi-select styling */
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
        padding: 3px 8px;
        margin-top: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-search__field {
        margin-top: 5px;
    }
    
    /* Department announcement items styling */
    .announcement-item {
        transition: all 0.3s ease;
        padding: 12px;
        border-radius: 6px;
    }
    .announcement-item:hover {
        background-color: #f8f9fa;
    }
    .announcement-item h6 {
        color: #2c3e50;
        font-size: 0.95rem;
        font-weight: 600;
    }
    .announcement-item .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    
    /* Collapse transition */
    .collapse {
        transition: height 0.35s ease;
    }
    
    /* Department modal header - black background */
    #department-announcement-modal .modal-header {
        background-color: #1a1a1a !important;
        border-bottom: 2px solid #333;
    }
    
    /* Color tag styling */
    .color-tag {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        vertical-align: middle;
    }
</style>

<div class="row">
    <div class="col-12"
        
        <!-- Announcements Management Header -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5><i data-feather="megaphone" class="icon-16"></i> <?php echo app_lang('department_announcements'); ?></h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcement-modal">
                            <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('create_announcement'); ?>
                        </button>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i data-feather="settings" class="icon-16"></i> <?php echo app_lang('manage'); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="bulkManageAnnouncements()">
                                <i data-feather="check-square" class="icon-14"></i> <?php echo app_lang('bulk_actions'); ?>
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportAnnouncements()">
                                <i data-feather="download" class="icon-14"></i> <?php echo app_lang('export_announcements'); ?>
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="announcementSettings()">
                                <i data-feather="settings" class="icon-14"></i> <?php echo app_lang('announcement_settings'); ?>
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements Statistics Dashboard -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <i data-feather="megaphone" class="icon-32 mb-2"></i>
                        <h4 class="card-title"><?php echo $total_announcements; ?></h4>
                        <p class="card-text"><?php echo app_lang('total_announcements'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <i data-feather="check-circle" class="icon-32 mb-2"></i>
                        <h4 class="card-title"><?php echo $active_announcements; ?></h4>
                        <p class="card-text"><?php echo app_lang('active_announcements'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <i data-feather="users" class="icon-32 mb-2"></i>
                        <h4 class="card-title"><?php echo $department_specific; ?></h4>
                        <p class="card-text"><?php echo app_lang('department_specific'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <i data-feather="globe" class="icon-32 mb-2"></i>
                        <h4 class="card-title"><?php echo $global_announcements; ?></h4>
                        <p class="card-text"><?php echo app_lang('global_announcements'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Announcements Navigation Tabs -->
        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all-announcements-tab" role="tab">
                            <i data-feather="list" class="icon-14"></i> <?php echo app_lang('all_announcements'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#department-targeted-tab" role="tab">
                            <i data-feather="target" class="icon-14"></i> <?php echo app_lang('department_targeted'); ?>
                        </a>
                    </li>
                    <?php /* Temporarily hidden
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#templates-tab" role="tab">
                            <i data-feather="layers" class="icon-14"></i> <?php echo app_lang('templates'); ?>
                        </a>
                    </li>
                    */ ?>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#analytics-tab" role="tab">
                            <i data-feather="bar-chart-2" class="icon-14"></i> <?php echo app_lang('analytics'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- ALL ANNOUNCEMENTS TAB -->
                    <div class="tab-pane fade show active" id="all-announcements-tab" role="tabpanel">
                        
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select class="form-control form-control-sm" id="filter-category">
                                    <option value=""><?php echo app_lang('filter_by_category'); ?></option>
                                    <?php foreach($announcement_categories as $key => $category): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $category; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control form-control-sm" id="filter-priority">
                                    <option value=""><?php echo app_lang('filter_by_priority'); ?></option>
                                    <?php foreach($priority_levels as $key => $priority): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $priority; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control form-control-sm" id="filter-department">
                                    <option value="" <?php echo (!isset($selected_department_id) || $selected_department_id == 0) ? 'selected' : ''; ?>><?php echo app_lang('all_departments'); ?></option>
                                    <option value="global"><?php echo app_lang('global_announcements_only'); ?></option>
                                    <?php foreach($departments as $dept): ?>
                                        <option value="<?php echo $dept->id; ?>" <?php echo (isset($selected_department_id) && $selected_department_id == $dept->id) ? 'selected' : ''; ?>><?php echo $dept->title; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" id="filter-search" 
                                       placeholder="<?php echo app_lang('search_announcements'); ?>">
                            </div>
                        </div>

                        <!-- Announcements List -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="announcements-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all-announcements"></th>
                                        <th><?php echo app_lang('title'); ?></th>
                                        <th><?php echo app_lang('category'); ?></th>
                                        <th><?php echo app_lang('priority'); ?></th>
                                        <th><?php echo app_lang('target_departments'); ?></th>
                                        <th><?php echo app_lang('status'); ?></th>
                                        <th><?php echo app_lang('created_date'); ?></th>
                                        <th class="text-center option w150"><i data-feather="menu" class="icon-16"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($announcements)): ?>
                                        <?php foreach ($announcements as $announcement): ?>
                                            <tr>
                                                <td><input type="checkbox" class="announcement-checkbox" value="<?php echo $announcement->id; ?>"></td>
                                                <td>
                                                    <strong><?php echo $announcement->title; ?></strong>
                                                    <br><small class="text-muted"><?php echo (strlen($announcement->description) > 60) ? substr($announcement->description, 0, 60) . '...' : $announcement->description; ?></small>
                                                </td>
                                                <td>
                                                    <?php 
                                                    // Display announcement category if available
                                                    $category = isset($announcement->category) ? $announcement->category : 'general';
                                                    $category_badge_class = 'bg-info';
                                                    if ($category == 'urgent') $category_badge_class = 'bg-danger';
                                                    elseif ($category == 'policy') $category_badge_class = 'bg-warning';
                                                    elseif ($category == 'event') $category_badge_class = 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?php echo $category_badge_class; ?>"><?php echo ucfirst($category); ?></span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    // Display announcement priority if available
                                                    $priority = isset($announcement->priority) ? $announcement->priority : 'normal';
                                                    $priority_badge_class = 'bg-info';
                                                    if ($priority == 'high') $priority_badge_class = 'bg-warning';
                                                    elseif ($priority == 'urgent') $priority_badge_class = 'bg-danger';
                                                    elseif ($priority == 'low') $priority_badge_class = 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?php echo $priority_badge_class; ?>"><?php echo ucfirst($priority); ?></span>
                                                </td>
                                                <td>
                                                    <?php if (empty($announcement->share_with) || $announcement->share_with == 'all_members'): ?>
                                                        <span class="badge bg-light text-dark"><?php echo app_lang('all_departments'); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary"><?php echo app_lang('department_specific'); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    // If no end_date, announcement never expires (always active)
                                                    $is_active = empty($announcement->end_date) || $announcement->end_date >= date('Y-m-d');
                                                    ?>
                                                    <?php if ($is_active): ?>
                                                        <span class="badge bg-success"><?php echo app_lang('active'); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?php echo app_lang('expired'); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><small><?php echo isset($announcement->start_date) ? format_to_datetime($announcement->start_date) : ''; ?></small></td>
                                                <td class="text-center">
                                                    <?php 
                                                        echo anchor(get_uri("announcements/view/" . $announcement->id), "<i data-feather='eye' class='icon-16'></i>", array(
                                                            "class" => "btn btn-default btn-sm",
                                                            "title" => app_lang('view'),
                                                            "data-post-id" => $announcement->id
                                                        ));
                                                        
                                                        echo modal_anchor(get_uri("departments/announcement_modal_form/" . $announcement->id . "?mode=edit"), "<i data-feather='edit' class='icon-16'></i>", array(
                                                            "class" => "btn btn-default btn-sm",
                                                            "title" => app_lang('edit'),
                                                            "data-post-id" => $announcement->id
                                                        ));
                                                        
                                                        echo js_anchor("<i data-feather='copy' class='icon-16'></i>", array(
                                                            'title' => app_lang('duplicate'),
                                                            "class" => "btn btn-default btn-sm duplicate-announcement",
                                                            "data-id" => $announcement->id,
                                                            "data-action-url" => get_uri("departments/duplicate_announcement"),
                                                            "data-action" => "duplicate-announcement"
                                                        ));
                                                        
                                                        echo js_anchor("<i data-feather='x' class='icon-16'></i>", array(
                                                            'title' => app_lang('delete'),
                                                            "class" => "btn btn-default btn-sm delete-announcement",
                                                            "data-id" => $announcement->id,
                                                            "data-action-url" => get_uri("departments/delete_announcement"),
                                                            "data-action" => "delete-confirmation"
                                                        ));
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i data-feather="megaphone" class="icon-48 text-muted"></i>
                                                <p class="text-muted mt-2"><?php echo app_lang('no_announcements_found'); ?></p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- DEPARTMENT TARGETED ANNOUNCEMENTS TAB -->
                    <div class="tab-pane fade" id="department-targeted-tab" role="tabpanel">
                        <h6><?php echo app_lang('department_specific_announcements'); ?></h6>
                        <p class="text-muted"><?php echo app_lang('announcements_targeted_to_specific_departments'); ?></p>

                        <!-- Department-wise Announcements -->
                        <div class="row">
                            <?php foreach($departments as $dept): ?>
                                <?php 
                                // Get announcements for this specific department
                                $dept_announcements = array_filter($announcements, function($a) use ($dept) {
                                    return !empty($a->share_with) && strpos($a->share_with, 'dept:' . $dept->id) !== false;
                                });
                                $dept_announcements = array_values($dept_announcements); // Re-index array
                                $announcement_count = count($dept_announcements);
                                ?>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <div class="color-tag me-2" style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo $dept->color ?: '#6c757d'; ?>;"></div>
                                            <?php echo $dept->title; ?>
                                        </h6>
                                        <span class="badge bg-secondary"><?php echo $announcement_count; ?> <?php echo app_lang('announcements'); ?></span>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($dept_announcements)): ?>
                                            <!-- Show first announcement -->
                                            <div class="dept-announcements-list" id="dept-announcements-<?php echo $dept->id; ?>">
                                                <div class="announcement-item border-bottom pb-2 mb-2">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1"><?php echo $dept_announcements[0]->title; ?></h6>
                                                            <p class="text-muted small mb-1">
                                                                <?php echo (strlen($dept_announcements[0]->description) > 80) ? substr(strip_tags($dept_announcements[0]->description), 0, 80) . '...' : strip_tags($dept_announcements[0]->description); ?>
                                                            </p>
                                                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                                                <span class="badge bg-<?php echo ($dept_announcements[0]->priority == 'urgent' || $dept_announcements[0]->priority == 'high') ? 'danger' : 'info'; ?>">
                                                                    <?php echo ucfirst($dept_announcements[0]->priority ?: 'normal'); ?>
                                                                </span>
                                                                <span class="badge bg-secondary">
                                                                    <?php echo ucfirst($dept_announcements[0]->category ?: 'general'); ?>
                                                                </span>
                                                                <small class="text-muted">
                                                                    <i data-feather="clock" class="icon-12"></i>
                                                                    <?php echo format_to_relative_time($dept_announcements[0]->created_at); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="ms-2">
                                                            <!-- Quick Action Buttons -->
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <?php 
                                                                    echo anchor(get_uri("announcements/view/" . $dept_announcements[0]->id), "<i data-feather='eye' class='icon-14'></i>", array(
                                                                        "class" => "btn btn-outline-secondary btn-sm",
                                                                        "title" => app_lang('view'),
                                                                        "data-bs-toggle" => "tooltip"
                                                                    ));
                                                                    
                                                                    echo modal_anchor(get_uri("departments/announcement_modal_form/" . $dept_announcements[0]->id . "?mode=edit"), "<i data-feather='edit' class='icon-14'></i>", array(
                                                                        "class" => "btn btn-outline-primary btn-sm",
                                                                        "title" => app_lang('edit'),
                                                                        "data-bs-toggle" => "tooltip"
                                                                    ));
                                                                    
                                                                    echo js_anchor("<i data-feather='copy' class='icon-14'></i>", array(
                                                                        'title' => app_lang('duplicate'),
                                                                        "class" => "btn btn-outline-info btn-sm duplicate-announcement",
                                                                        "data-id" => $dept_announcements[0]->id,
                                                                        "data-bs-toggle" => "tooltip"
                                                                    ));
                                                                    
                                                                    echo js_anchor("<i data-feather='x' class='icon-14'></i>", array(
                                                                        'title' => app_lang('delete'),
                                                                        "class" => "btn btn-outline-danger btn-sm delete-announcement",
                                                                        "data-id" => $dept_announcements[0]->id,
                                                                        "data-bs-toggle" => "tooltip"
                                                                    ));
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Hidden additional announcements (collapsed by default) -->
                                                <div class="collapse" id="collapse-dept-<?php echo $dept->id; ?>">
                                                    <?php for($i = 1; $i < $announcement_count; $i++): ?>
                                                        <div class="announcement-item border-bottom pb-2 mb-2">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1"><?php echo $dept_announcements[$i]->title; ?></h6>
                                                                    <p class="text-muted small mb-1">
                                                                        <?php echo (strlen($dept_announcements[$i]->description) > 80) ? substr(strip_tags($dept_announcements[$i]->description), 0, 80) . '...' : strip_tags($dept_announcements[$i]->description); ?>
                                                                    </p>
                                                                    <div class="d-flex gap-2 flex-wrap align-items-center">
                                                                        <span class="badge bg-<?php echo ($dept_announcements[$i]->priority == 'urgent' || $dept_announcements[$i]->priority == 'high') ? 'danger' : 'info'; ?>">
                                                                            <?php echo ucfirst($dept_announcements[$i]->priority ?: 'normal'); ?>
                                                                        </span>
                                                                        <span class="badge bg-secondary">
                                                                            <?php echo ucfirst($dept_announcements[$i]->category ?: 'general'); ?>
                                                                        </span>
                                                                        <small class="text-muted">
                                                                            <i data-feather="clock" class="icon-12"></i>
                                                                            <?php echo format_to_relative_time($dept_announcements[$i]->created_at); ?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                                <div class="ms-2">
                                                                    <!-- Quick Action Buttons -->
                                                                    <div class="btn-group btn-group-sm" role="group">
                                                                        <?php 
                                                                            echo anchor(get_uri("announcements/view/" . $dept_announcements[$i]->id), "<i data-feather='eye' class='icon-14'></i>", array(
                                                                                "class" => "btn btn-outline-secondary btn-sm",
                                                                                "title" => app_lang('view'),
                                                                                "data-bs-toggle" => "tooltip"
                                                                            ));
                                                                            
                                                                            echo modal_anchor(get_uri("departments/announcement_modal_form/" . $dept_announcements[$i]->id . "?mode=edit"), "<i data-feather='edit' class='icon-14'></i>", array(
                                                                                "class" => "btn btn-outline-primary btn-sm",
                                                                                "title" => app_lang('edit'),
                                                                                "data-bs-toggle" => "tooltip"
                                                                            ));
                                                                            
                                                                            echo js_anchor("<i data-feather='copy' class='icon-14'></i>", array(
                                                                                'title' => app_lang('duplicate'),
                                                                                "class" => "btn btn-outline-info btn-sm duplicate-announcement",
                                                                                "data-id" => $dept_announcements[$i]->id,
                                                                                "data-bs-toggle" => "tooltip"
                                                                            ));
                                                                            
                                                                            echo js_anchor("<i data-feather='x' class='icon-14'></i>", array(
                                                                                'title' => app_lang('delete'),
                                                                                "class" => "btn btn-outline-danger btn-sm delete-announcement",
                                                                                "data-id" => $dept_announcements[$i]->id,
                                                                                "data-bs-toggle" => "tooltip"
                                                                            ));
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i data-feather="megaphone" class="icon-32 text-muted mb-2"></i>
                                                <p class="text-muted"><?php echo app_lang('no_announcements_for_department'); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3 d-flex gap-2">
                                            <button class="btn btn-sm btn-primary" 
                                                    data-dept-id="<?php echo $dept->id; ?>" 
                                                    data-dept-name="<?php echo htmlspecialchars($dept->title, ENT_QUOTES); ?>"
                                                    onclick="openDepartmentAnnouncementModal(this.getAttribute('data-dept-id'), this.getAttribute('data-dept-name'))">
                                                <i data-feather="plus" class="icon-14"></i> <?php echo app_lang('add_announcement'); ?>
                                            </button>
                                            <?php if ($announcement_count > 1): ?>
                                                <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapse-dept-<?php echo $dept->id; ?>" 
                                                        aria-expanded="false" 
                                                        onclick="toggleViewAll(this, <?php echo $dept->id; ?>)">
                                                    <i data-feather="chevron-down" class="icon-14"></i>
                                                    <span class="toggle-text"><?php echo app_lang('view_all_announcements'); ?></span>
                                                    <span class="badge bg-secondary ms-1"><?php echo $announcement_count - 1; ?></span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php /* Temporarily hidden templates tab
                    <!-- ANNOUNCEMENT TEMPLATES TAB -->
                    <div class="tab-pane fade" id="templates-tab" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6><?php echo app_lang('announcement_templates'); ?></h6>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#template-modal">
                                <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('create_template'); ?>
                            </button>
                        </div>

                        <div class="row">
                            <?php foreach($announcement_templates as $template): ?>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0"><?php echo $template->title; ?></h6>
                                        <span class="badge bg-<?php echo ($template->priority == 'urgent') ? 'danger' : 'secondary'; ?>">
                                            <?php echo ucfirst($template->priority); ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text text-muted"><?php echo $template->content; ?></p>
                                        <span class="badge bg-light text-dark"><?php echo ucfirst($template->category); ?></span>
                                    </div>
                                    <div class="card-footer">
                                        <div class="btn-group btn-group-sm w-100">
                                            <button class="btn btn-outline-primary use-template" data-template-id="<?php echo $template->id; ?>" data-title="<?php echo htmlspecialchars($template->title); ?>" data-content="<?php echo htmlspecialchars($template->content); ?>" data-category="<?php echo $template->category; ?>" data-priority="<?php echo $template->priority; ?>">
                                                <i data-feather="copy" class="icon-14"></i> <?php echo app_lang('use_template'); ?>
                                            </button>
                                            <?php
                                            echo modal_anchor(get_uri("departments/edit_announcement_template/" . $template->id), '<i data-feather="edit-2" class="icon-14"></i> ' . app_lang('edit'), array(
                                                "class" => "btn btn-outline-secondary btn-sm",
                                                "title" => app_lang('edit_template')
                                            ));
                                            ?>
                                            <button class="btn btn-outline-danger delete-template" data-template-id="<?php echo $template->id; ?>">
                                                <i data-feather="trash-2" class="icon-14"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    */ ?>

                    <!-- ANNOUNCEMENTS ANALYTICS TAB -->
                    <div class="tab-pane fade" id="analytics-tab" role="tabpanel">
                        <h6><?php echo app_lang('announcement_analytics'); ?></h6>
                        <p class="text-muted"><?php echo app_lang('track_announcement_performance_and_engagement'); ?></p>

                        <!-- Analytics Dashboard -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-primary">-</h4>
                                        <small><?php echo app_lang('read_rate'); ?></small>
                                        <br><small class="text-muted"><?php echo app_lang('coming_soon'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-success">-</h4>
                                        <small><?php echo app_lang('delivery_rate'); ?></small>
                                        <br><small class="text-muted"><?php echo app_lang('coming_soon'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-info">-</h4>
                                        <small><?php echo app_lang('avg_response_time'); ?></small>
                                        <br><small class="text-muted"><?php echo app_lang('coming_soon'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-warning">-</h4>
                                        <small><?php echo app_lang('engagement_rate'); ?></small>
                                        <br><small class="text-muted"><?php echo app_lang('coming_soon'); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance by Department -->
                        <div class="card">
                            <div class="card-header">
                                <h6><?php echo app_lang('performance_by_department'); ?></h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th><?php echo app_lang('department'); ?></th>
                                                <th><?php echo app_lang('announcements_sent'); ?></th>
                                                <th><?php echo app_lang('read_rate'); ?></th>
                                                <th><?php echo app_lang('engagement'); ?></th>
                                                <th><?php echo app_lang('avg_response_time'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($departments as $dept): ?>
                                                <?php 
                                                // Get announcements count for this department
                                                $dept_announcements_count = count(array_filter($announcements, function($a) use ($dept) {
                                                    return !empty($a->share_with) && strpos($a->share_with, 'dept:' . $dept->id) !== false;
                                                }));
                                                ?>
                                            <tr>
                                                <td>
                                                    <div class="color-tag me-2" style="background-color: <?php echo $dept->color ?: '#6c757d'; ?>;"></div>
                                                    <?php echo $dept->title; ?>
                                                </td>
                                                <td><?php echo $dept_announcements_count; ?></td>
                                                <td>
                                                    <small class="text-muted"><?php echo app_lang('not_available'); ?></small>
                                                </td>
                                                <td>
                                                    <small class="text-muted"><?php echo app_lang('not_available'); ?></small>
                                                </td>
                                                <td><small class="text-muted"><?php echo app_lang('not_available'); ?></small></td>
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
    </div>
</div>

<!-- Create Announcement Modal -->
<div class="modal fade" id="announcement-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo app_lang('create_announcement'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php echo form_open(base_url("index.php/departments/save_announcement"), array("id" => "announcement-form")); ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="announcement_title"><?php echo app_lang('title'); ?> *</label>
                            <input type="text" name="title" id="announcement_title" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="announcement_priority"><?php echo app_lang('priority'); ?></label>
                            <select name="priority" id="announcement_priority" class="form-control">
                                <?php foreach($priority_levels as $key => $priority): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $priority; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="announcement_category"><?php echo app_lang('category'); ?></label>
                            <select name="category" id="announcement_category" class="form-control">
                                <?php foreach($announcement_categories as $key => $category): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $category; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="announcement_departments"><?php echo app_lang('target_departments'); ?></label>
                            <select name="target_departments[]" id="announcement_departments" class="form-control select2" multiple data-placeholder="<?php echo app_lang('all_departments'); ?>">
                                <?php foreach($departments as $dept): ?>
                                    <option value="<?php echo $dept->id; ?>"><?php echo $dept->title; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted"><?php echo app_lang('leave_empty_for_all_departments'); ?></small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="announcement_content"><?php echo app_lang('content'); ?> *</label>
                    <textarea name="content" id="announcement_content" class="form-control" rows="6" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date"><?php echo app_lang('start_date'); ?></label>
                            <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date"><?php echo app_lang('end_date'); ?></label>
                            <input type="datetime-local" name="end_date" id="end_date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_email" id="send_email" value="1" checked>
                            <label class="form-check-label" for="send_email">
                                <?php echo app_lang('send_email_notification'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_push" id="send_push" value="1">
                            <label class="form-check-label" for="send_push">
                                <?php echo app_lang('send_push_notification'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app_lang('cancel'); ?></button>
                <button type="button" class="btn btn-outline-primary" onclick="saveAsDraft()"><?php echo app_lang('save_as_draft'); ?></button>
                <button type="button" class="btn btn-primary" id="publish-announcement-btn"><?php echo app_lang('publish_announcement'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Template Creation Modal -->
<div class="modal fade" id="template-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo app_lang('create_announcement_template'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php echo form_open(base_url("index.php/departments/save_announcement_template"), array("id" => "template-form")); ?>
                
                <div class="form-group">
                    <label for="template_title"><?php echo app_lang('template_title'); ?> *</label>
                    <input type="text" name="title" id="template_title" class="form-control" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="template_category"><?php echo app_lang('category'); ?></label>
                            <select name="category" id="template_category" class="form-control">
                                <?php foreach($announcement_categories as $key => $category): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $category; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="template_priority"><?php echo app_lang('default_priority'); ?></label>
                            <select name="priority" id="template_priority" class="form-control">
                                <?php foreach($priority_levels as $key => $priority): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $priority; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="template_content"><?php echo app_lang('template_content'); ?> *</label>
                    <textarea name="content" id="template_content" class="form-control" rows="5" required 
                              placeholder="<?php echo app_lang('use_placeholders_like_department_name'); ?>"></textarea>
                    <small class="form-text text-muted">
                        <?php echo app_lang('available_placeholders'); ?>: {department_name}, {user_name}, {date}, {time}
                    </small>
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

<!-- Department-Specific Announcement Modal -->
<div class="modal fade" id="department-announcement-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i data-feather="megaphone" class="icon-16"></i>
                    <span id="dept-modal-title"><?php echo app_lang('add_announcement_for'); ?></span>
                    <strong id="dept-modal-name"></strong>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php echo form_open(base_url("index.php/departments/save_announcement"), array("id" => "department-announcement-form")); ?>
                <input type="hidden" name="department_id" id="dept_announcement_dept_id" value="">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="dept_announcement_title"><?php echo app_lang('title'); ?> *</label>
                            <input type="text" name="title" id="dept_announcement_title" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="dept_announcement_priority"><?php echo app_lang('priority'); ?></label>
                            <select name="priority" id="dept_announcement_priority" class="form-control">
                                <?php foreach($priority_levels as $key => $priority): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $priority; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="dept_announcement_category"><?php echo app_lang('category'); ?></label>
                    <select name="category" id="dept_announcement_category" class="form-control">
                        <?php foreach($announcement_categories as $key => $category): ?>
                            <option value="<?php echo $key; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dept_announcement_content"><?php echo app_lang('content'); ?> *</label>
                    <textarea name="content" id="dept_announcement_content" class="form-control" rows="6" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dept_start_date"><?php echo app_lang('start_date'); ?></label>
                            <input type="datetime-local" name="start_date" id="dept_start_date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dept_end_date"><?php echo app_lang('end_date'); ?></label>
                            <input type="datetime-local" name="end_date" id="dept_end_date" class="form-control">
                            <small class="form-text text-muted"><?php echo app_lang('leave_empty_for_never_expire'); ?></small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i data-feather="info" class="icon-16"></i>
                    <?php echo app_lang('this_announcement_will_be_targeted_to'); ?> <strong id="dept-target-name"></strong>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_email" id="dept_send_email" value="1" checked>
                            <label class="form-check-label" for="dept_send_email">
                                <?php echo app_lang('send_email_notification'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="send_push" id="dept_send_push" value="1">
                            <label class="form-check-label" for="dept_send_push">
                                <?php echo app_lang('send_push_notification'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo app_lang('cancel'); ?></button>
                <button type="button" class="btn btn-primary" id="publish-dept-announcement-btn">
                    <i data-feather="send" class="icon-16"></i>
                    <?php echo app_lang('publish_announcement'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== Announcements page loaded ===');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    
    // Diagnostic: Log announcement count on page load
    var announcementCount = $('#announcements-table tbody tr').length;
    console.log('Page loaded with ' + announcementCount + ' announcements');
    
    // Check if department modal exists
    console.log('Department modal exists:', $('#department-announcement-modal').length > 0);
    console.log('Department form exists:', $('#department-announcement-form').length > 0);
    console.log('Publish button exists:', $('#publish-dept-announcement-btn').length > 0);
    
    // Department announcement submission handler - Professional implementation
    $(document).on('click', '#publish-dept-announcement-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('=== Department Announcement Submit Started ===');
        
        var form = $('#department-announcement-form')[0];
        var $form = $('#department-announcement-form');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }
        
        // Get department ID
        var deptId = $('#dept_announcement_dept_id').val();
        console.log('Target Department ID:', deptId);
        
        if (!deptId) {
            alert('Error: No department selected');
            return false;
        }
        
        // Prepare form data manually to ensure clean department targeting
        var formData = {
            title: $('#dept_announcement_title').val(),
            priority: $('#dept_announcement_priority').val(),
            category: $('#dept_announcement_category').val(),
            content: $('#dept_announcement_content').val(),
            start_date: $('#dept_start_date').val(),
            end_date: $('#dept_end_date').val(),
            send_email: $('#dept_send_email').is(':checked') ? 1 : 0,
            send_push: $('#dept_send_push').is(':checked') ? 1 : 0,
            'target_departments[]': deptId, // Send as array parameter
            department_id: deptId // Also send individual ID for fallback
        };
        
        console.log('Form data being sent:', formData);
        
        // Submit via AJAX
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            beforeSend: function() {
                $('#publish-dept-announcement-btn')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Publishing...');
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response && response.success) {
                    // Close modal
                    var modal = bootstrap.Modal.getInstance(document.getElementById('department-announcement-modal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Show success message
                    if (typeof appAlert !== 'undefined') {
                        appAlert.success(response.message || 'Announcement published successfully');
                    } else {
                        alert(response.message || 'Announcement published successfully');
                    }
                    
                    // Reload page to show new announcement
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    handleError(response.message || 'Failed to publish announcement');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
                handleError('Network error: ' + error);
            },
            complete: function() {
                // Reset button
                $('#publish-dept-announcement-btn')
                    .prop('disabled', false)
                    .html('<i data-feather="send" class="icon-16"></i> Publish Announcement');
            }
        });
        
        function handleError(message) {
            if (typeof appAlert !== 'undefined') {
                appAlert.error(message);
            } else {
                alert('Error: ' + message);
            }
        }
        
        return false;
    });
    
    // Initialize DataTables
    var announcementsTable = $('#announcements-table').DataTable({
        "pageLength": 10,
        "searching": true,
        "ordering": true,
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] }
        ],
        "initComplete": function() {
            // Initialize feather icons after table loads
            setTimeout(function() {
                initializeFeatherIcons();
                console.log('DataTable initComplete - icons initialized');
            }, 200);
        },
        "drawCallback": function() {
            // Reinitialize feather icons after each table redraw
            setTimeout(function() {
                initializeFeatherIcons();
                console.log('DataTable drawCallback - icons reinitialized');
            }, 100);
        }
    });

    // Force icon initialization after table is fully rendered
    setTimeout(function() {
        initializeFeatherIcons();
        console.log('Force icon initialization after 1 second');
    }, 1000);

    // Initialize Select2 with proper configuration for multi-select
    $('#announcement_departments').select2({
        dropdownParent: $('#announcement-modal'),
        placeholder: '<?php echo app_lang('all_departments'); ?>',
        allowClear: true,
        width: '100%'
    });

    // Form submissions - Handle announcement form
    $('#announcement-form').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Submitting announcement form...');
        console.log('Form data:', $(this).serialize());
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                console.log('Announcement save response:', response);
                if(response && response.success) {
                    console.log('Success! Closing modal and reloading page...');
                    $('#announcement-modal').modal('hide');
                    appAlert.success(response.message || '<?php echo app_lang('announcement_created_successfully'); ?>');
                    // Reload current page to stay on the same tab
                    setTimeout(function() {
                        console.log('Reloading page now...');
                        location.reload();
                    }, 800);
                } else {
                    console.log('Save failed - response.success is false');
                    appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Announcement save error:', {status: jqXHR.status, error: errorThrown, response: jqXHR.responseText});
                appAlert.error('<?php echo app_lang('error_occurred'); ?>');
            }
        });
        
        return false;
    });

    $('#template-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST', 
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    $('#template-modal').modal('hide');
                    appAlert.success(response.message || '<?php echo app_lang('template_created_successfully'); ?>');
                    location.reload();
                } else {
                    appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
                }
            },
            error: function() {
                appAlert.error('<?php echo app_lang('error_occurred'); ?>');
            }
        });
    });

    // Select all functionality
    $('#select-all-announcements').on('change', function() {
        $('.announcement-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Publish announcement button click handler
    $('#publish-announcement-btn').on('click', function(e) {
        e.preventDefault();
        console.log('Publish button clicked');
        $('#announcement-form').submit();
    });

    // Filter functionality - all filters trigger AJAX reload
    $('#filter-category, #filter-priority, #filter-department').on('change', function() {
        var category = $('#filter-category').val();
        var priority = $('#filter-priority').val();
        var department = $('#filter-department').val();
        loadAnnouncementsByFilters(department, category, priority);
    });

    $('#filter-search').on('keyup', function() {
        var table = $('#announcements-table').DataTable();
        table.search($(this).val()).draw();
    });

    // Initialize feather icons safely
    function initializeFeatherIcons() {
        try {
            if (typeof feather !== 'undefined' && feather.replace) {
                // Check for any broken icons first
                var brokenIcons = document.querySelectorAll('[data-feather]');
                console.log('Found ' + brokenIcons.length + ' feather icons to render');
                
                feather.replace();
                
                // Double-check that icons were replaced
                var stillBrokenIcons = document.querySelectorAll('[data-feather]');
                if (stillBrokenIcons.length > 0) {
                    console.warn('Some icons still not rendered, trying again...');
                    setTimeout(function() {
                        feather.replace();
                    }, 100);
                }
            } else {
                console.warn('Feather library not available');
            }
        } catch (e) {
            console.error('Feather icon replacement failed:', e);
        }
    }

    // Initialize feather icons on page load - multiple attempts
    initializeFeatherIcons();
    
    // Reinitialize feather icons after DataTable draw
    $('#announcements-table').on('draw.dt', function() {
        setTimeout(function() {
            initializeFeatherIcons();
            console.log('Icons reinitialized after DataTable draw');
        }, 50);
    });
    
    // Force feather icons update after short delays (for initial page load)
    setTimeout(function() {
        initializeFeatherIcons();
        console.log('Force icon initialization after 500ms');
    }, 500);
    
    setTimeout(function() {
        initializeFeatherIcons();
        console.log('Force icon initialization after 1500ms');
    }, 1500);

    // Also reinitialize when any modal is shown
    $(document).on('shown.bs.modal', function() {
        setTimeout(function() {
            initializeFeatherIcons();
            console.log('Icons reinitialized after modal shown');
        }, 100);
    });

});

// Function to open department-specific announcement modal
function openDepartmentAnnouncementModal(deptId, deptName) {
    console.log('=== openDepartmentAnnouncementModal called ===');
    console.log('Opening department modal for:', deptId, deptName);
    
    // Check if form exists
    var form = $('#department-announcement-form');
    console.log('Form found:', form.length);
    console.log('Form action:', form.attr('action'));
    
    // Reset form
    $('#department-announcement-form')[0].reset();
    
    // Set department info
    $('#dept_announcement_dept_id').val(deptId);
    $('#dept-modal-name').text(deptName);
    $('#dept-target-name').text(deptName);
    
    console.log('Department ID set to:', $('#dept_announcement_dept_id').val());
    
    // Set default start date
    var now = new Date();
    var year = now.getFullYear();
    var month = String(now.getMonth() + 1).padStart(2, '0');
    var day = String(now.getDate()).padStart(2, '0');
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');
    var dateTimeValue = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
    $('#dept_start_date').val(dateTimeValue);
    
    console.log('Showing modal...');
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('department-announcement-modal'));
    modal.show();
    
    console.log('Modal shown');
    
    // Reinitialize feather icons
    setTimeout(function() {
        initializeFeatherIcons();
        console.log('Feather icons reinitialized');
    }, 200);
}

// Function to toggle view all/collapse for department announcements
function toggleViewAll(button, deptId) {
    var $button = $(button);
    var $icon = $button.find('i[data-feather]');
    var $text = $button.find('.toggle-text');
    var isExpanded = $button.attr('aria-expanded') === 'true';
    
    // Toggle button text and icon after collapse animation
    setTimeout(function() {
        if (!isExpanded) {
            // Was collapsed, now expanding
            $text.text('<?php echo app_lang("collapse_announcements"); ?>');
            $icon.attr('data-feather', 'chevron-up');
        } else {
            // Was expanded, now collapsing
            $text.text('<?php echo app_lang("view_all_announcements"); ?>');
            $icon.attr('data-feather', 'chevron-down');
        }
        
        // Reinitialize feather icons
        initializeFeatherIcons();
    }, 350);
}

// Test function - call from console: testDepartmentSubmit()
function testDepartmentSubmit() {
    console.log('=== Manual Test Submit ===');
    console.log('Button element:', $('#publish-dept-announcement-btn')[0]);
    console.log('Form element:', $('#department-announcement-form')[0]);
    console.log('Department ID value:', $('#dept_announcement_dept_id').val());
    console.log('Form action URL:', $('#department-announcement-form').attr('action'));
    
    // Try to manually trigger submit
    console.log('Attempting manual submit...');
    $('#department-announcement-form').trigger('submit');
}

function saveAsDraft() {
    var formData = $('#announcement-form').serialize() + '&status=draft';
    
    $.ajax({
        url: $('#announcement-form').attr('action'),
        type: 'POST',
        data: formData,
        success: function(response) {
            if(response.success) {
                $('#announcement-modal').modal('hide');
                appAlert.success('<?php echo app_lang('announcement_saved_as_draft'); ?>');
                location.reload();
            } else {
                appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
            }
        },
        error: function() {
            appAlert.error('<?php echo app_lang('error_occurred'); ?>');
        }
    });
}

function bulkManageAnnouncements() {
    var selected = $('.announcement-checkbox:checked').length;
    if(selected === 0) {
        appAlert.error('<?php echo app_lang('please_select_announcements'); ?>');
        return;
    }
    
    // Implement bulk actions
    appAlert.success(selected + ' <?php echo app_lang('announcements_selected'); ?>');
}

function exportAnnouncements() {
    window.location.href = '<?php echo base_url("index.php/departments/export_announcements"); ?>';
}

function announcementSettings() {
    appAlert.info('<?php echo app_lang('announcement_settings_coming_soon'); ?>');
}

function loadAnnouncementsByFilters(department_id, category, priority) {
    $.ajax({
        url: '<?php echo base_url("index.php/departments/get_filtered_announcements"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            department_id: department_id || 0,
            category: category || '',
            priority: priority || ''
        },
        beforeSend: function() {
            $('#announcements-table tbody').html('<tr><td colspan="8" class="text-center"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo app_lang('loading'); ?>...</td></tr>');
        },
        success: function(response) {
            console.log('Filter response:', response);
            if(response && response.success) {
                $('#announcements-table tbody').html(response.html);
                // Re-initialize feather icons for the new content
                setTimeout(function() {
                    initializeFeatherIcons();
                    console.log('Icons reinitialized after filter');
                }, 200);
            } else {
                var errorMsg = (response && response.message) ? response.message : '<?php echo app_lang('error_occurred'); ?>';
                appAlert.error(errorMsg);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Filter AJAX Error:', {status: jqXHR.status, textStatus: textStatus, error: errorThrown, response: jqXHR.responseText});
            var errorMsg = '<?php echo app_lang('error_occurred'); ?>';
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                errorMsg = jqXHR.responseJSON.message;
            } else if (jqXHR.status === 500) {
                errorMsg = 'Server error (500) - Check browser console and application logs';
            } else if (textStatus === 'parsererror') {
                errorMsg = 'Invalid response format - Check application logs for PHP errors';
            }
            appAlert.error(errorMsg);
            $('#announcements-table tbody').html('<tr><td colspan="8" class="text-center text-danger"><?php echo app_lang('error_loading_announcements'); ?></td></tr>');
        }
    });
}

function loadAnnouncementsByDepartment(department_id) {
    loadAnnouncementsByFilters(department_id, '', '');
}

// Announcement action button handlers
$(document).on('click', '.duplicate-announcement', function(e) {
    e.preventDefault();
    var announcement_id = $(this).data('id');
    var $button = $(this);
    
    // Show loading state
    $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
    
    $.ajax({
        url: '<?php echo get_uri("departments/duplicate_announcement"); ?>',
        type: 'POST',
        data: { id: announcement_id },
        dataType: 'json',
        success: function(response) {
            if(response && response.success) {
                appAlert.success(response.message || '<?php echo app_lang('announcement_duplicated_successfully'); ?>');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Duplicate error:', error);
            appAlert.error('<?php echo app_lang('error_occurred'); ?>');
        },
        complete: function() {
            // Reset button state
            $button.prop('disabled', false).html('<i data-feather="copy" class="icon-16"></i>');
            initializeFeatherIcons();
        }
    });
});

$(document).on('click', '.delete-announcement', function(e) {
    e.preventDefault();
    var announcement_id = $(this).data('id');
    var $button = $(this);
    
    // Function to perform the delete
    function performDelete() {
        // Show loading state
        $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        
        $.ajax({
            url: '<?php echo get_uri("departments/delete_announcement"); ?>',
            type: 'POST',
            data: { id: announcement_id },
            dataType: 'json',
            success: function(response) {
                if(response && response.success) {
                    appAlert.success(response.message || '<?php echo app_lang('announcement_deleted_successfully'); ?>');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
                    // Reset button state on error
                    $button.prop('disabled', false).html('<i data-feather="x" class="icon-16"></i>');
                    initializeFeatherIcons();
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete error:', error);
                appAlert.error('<?php echo app_lang('error_occurred'); ?>');
                // Reset button state on error
                $button.prop('disabled', false).html('<i data-feather="x" class="icon-16"></i>');
                initializeFeatherIcons();
            }
        });
    }
    
    // Use confirmationModal if available, otherwise fall back to browser confirm
    if (typeof confirmationModal === 'function') {
        confirmationModal({
            title: '<?php echo app_lang('delete_announcement'); ?>',
            message: '<?php echo app_lang('confirm_delete_announcement'); ?>',
            confirmText: '<?php echo app_lang('delete'); ?>',
            cancelText: '<?php echo app_lang('cancel'); ?>',
            onConfirm: performDelete
        });
    } else {
        // Fallback to browser confirm if confirmationModal is not available
        if (confirm('<?php echo app_lang('confirm_delete_announcement'); ?>')) {
            performDelete();
        }
    }
});

// Delegated AJAX submit handler for announcement form (edit/create) - prevents full redirect
$(document).on('submit', '#announcement-form', function(e) {
    e.preventDefault();
    var $form = $(this);
    var action = $form.attr('action');

    $.ajax({
        url: action,
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                // Close ajax modal if present
                try {
                    var modalEl = document.getElementById('ajaxModal');
                    if (modalEl) {
                        var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modalInstance.hide();
                    }
                } catch (err) {
                    console.warn('Error closing modal:', err);
                }

                appAlert.success(response.message || '<?php echo app_lang('announcement_created_successfully'); ?>');
                setTimeout(function() { location.reload(); }, 600);
            } else {
                appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
            }
        },
        error: function() {
            appAlert.error('<?php echo app_lang('error_occurred'); ?>');
        }
    });
});

// Delegated AJAX submit handler for department-specific announcement form
$(document).on('submit', '#department-announcement-form', function(e) {
    e.preventDefault();
    var $form = $(this);
    var action = $form.attr('action');

    $.ajax({
        url: action,
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                try {
                    var modalEl = document.getElementById('department-announcement-modal');
                    if (modalEl) {
                        var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modalInstance.hide();
                    }
                } catch (err) {
                    console.warn('Error closing department modal:', err);
                }

                appAlert.success(response.message || '<?php echo app_lang('announcement_created_successfully'); ?>');
                setTimeout(function() { location.reload(); }, 600);
            } else {
                appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
            }
        },
        error: function() {
            appAlert.error('<?php echo app_lang('error_occurred'); ?>');
        }
    });
});

// Tab persistence functionality - handles both main tabs and nested tabs
$(document).ready(function() {
    // Get the active tab from URL hash, localStorage, or default to first tab
    var urlHash = window.location.hash;
    var activeTab = '';
    
    if (urlHash) {
        // Convert URL hash to tab format (e.g., #all-announcements -> #all-announcements-tab)
        if (urlHash === '#all-announcements') {
            activeTab = '#all-announcements-tab';
        } else if (urlHash === '#department-targeted') {
            activeTab = '#department-targeted-tab';
        } else if (urlHash === '#templates') {
            activeTab = '#templates-tab';
        } else if (urlHash === '#analytics') {
            activeTab = '#analytics-tab';
        } else {
            activeTab = urlHash + '-tab';
        }
        localStorage.setItem('announcements_active_tab', activeTab);
    } else {
        activeTab = localStorage.getItem('announcements_active_tab') || '#all-announcements-tab';
    }
    
    // Show the active announcements tab (nested within departments)
    if (activeTab && $('.nav-tabs-horizontal a[href="' + activeTab + '"]').length) {
        $('.nav-tabs-horizontal a[href="' + activeTab + '"]').tab('show');
    }
    
    // Save active tab to localStorage when tab is changed (for nested tabs only)
    $('.nav-tabs-horizontal a').on('shown.bs.tab', function(e) {
        var targetTab = $(e.target).attr('href');
        localStorage.setItem('announcements_active_tab', targetTab);
        
        // Update URL hash without page reload
        var hashName = targetTab.replace('#', '').replace('-tab', '');
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, '#' + hashName);
        }
    });
});

// Function to return to departments with active tab preserved
function returnToDepartments() {
    var activeTab = localStorage.getItem('announcements_active_tab') || '#all-announcements-tab';
    window.location.href = '<?php echo get_uri("departments"); ?>' + activeTab.replace('-tab', '');
}

// Template functionality
$(document).on('click', '.use-template', function(e) {
    e.preventDefault();
    var templateData = {
        title: $(this).data('title'),
        content: $(this).data('content'),
        category: $(this).data('category'),
        priority: $(this).data('priority')
    };
    
    // Open the main announcement modal and populate with template data
    var modalUrl = '<?php echo get_uri("departments/announcement_modal_form"); ?>';
    
    $.get(modalUrl, function(data) {
        $('#ajaxModal .modal-content').html(data);
        $('#ajaxModal').modal('show');
        
        // Populate form with template data
        setTimeout(function() {
            $('#announcement_title').val(templateData.title);
            $('#announcement_description').val(templateData.content);
            $('#announcement_category').val(templateData.category).trigger('change');
            $('#announcement_priority').val(templateData.priority).trigger('change');
            
            // Replace placeholders if needed
            var content = templateData.content;
            content = content.replace('{department_name}', '{{DEPARTMENT_NAME}}');
            content = content.replace('{user_name}', '<?php echo isset($login_user) ? $login_user->first_name . " " . $login_user->last_name : "Current User"; ?>');
            content = content.replace('{date}', new Date().toLocaleDateString());
            content = content.replace('{time}', new Date().toLocaleTimeString());
            
            $('#announcement_description').val(content);
            
            // Re-initialize feather icons in modal
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }, 500);
    });
});

// Delete template functionality
$(document).on('click', '.delete-template', function(e) {
    e.preventDefault();
    var templateId = $(this).data('template-id');
    var $button = $(this);
    
    if (typeof confirmationModal === 'function') {
        confirmationModal({
            title: '<?php echo app_lang('delete_template'); ?>',
            message: '<?php echo app_lang('confirm_delete_template'); ?>',
            confirmText: '<?php echo app_lang('delete'); ?>',
            callback: function() {
                performTemplateDelete(templateId, $button);
            }
        });
    } else if (confirm('<?php echo app_lang('confirm_delete_template'); ?>')) {
        performTemplateDelete(templateId, $button);
    }
});

function performTemplateDelete(templateId, $button) {
    $.ajax({
        url: '<?php echo get_uri("departments/delete_announcement_template"); ?>',
        type: 'POST',
        data: { id: templateId },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                $button.closest('.col-lg-4').fadeOut(300, function() {
                    $(this).remove();
                });
                appAlert.success(response.message || '<?php echo app_lang('template_deleted_successfully'); ?>');
            } else {
                appAlert.error(response.message || '<?php echo app_lang('error_occurred'); ?>');
            }
        },
        error: function() {
            appAlert.error('<?php echo app_lang('error_occurred'); ?>');
        }
    });
}
</script>

<style>
.color-tag {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.card-body .badge {
    font-size: 0.75em;
}

.progress {
    background-color: #e9ecef;
}

.nav-tabs-horizontal .nav-link {
    border-radius: 0.375rem 0.375rem 0 0;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>