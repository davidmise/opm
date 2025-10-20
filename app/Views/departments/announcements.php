<!-- COMPREHENSIVE DEPARTMENT ANNOUNCEMENTS TAB -->
<div class="row">
    <div class="col-12">
        
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
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#templates-tab" role="tab">
                            <i data-feather="layers" class="icon-14"></i> <?php echo app_lang('templates'); ?>
                        </a>
                    </li>
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
                                    <option value=""><?php echo app_lang('filter_by_department'); ?></option>
                                    <option value="global"><?php echo app_lang('global_announcements'); ?></option>
                                    <?php foreach($departments as $dept): ?>
                                        <option value="<?php echo $dept->id; ?>"><?php echo $dept->title; ?></option>
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
                                        <th class="text-center"><?php echo app_lang('actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Sample data for demo -->
                                    <tr>
                                        <td><input type="checkbox" class="announcement-checkbox" value="1"></td>
                                        <td>
                                            <strong>New Department Policy Update</strong>
                                            <br><small class="text-muted">Updates to remote work policies</small>
                                        </td>
                                        <td><span class="badge bg-info"><?php echo app_lang('policy'); ?></span></td>
                                        <td><span class="badge bg-warning"><?php echo app_lang('high'); ?></span></td>
                                        <td>
                                            <span class="badge bg-primary">HR Department</span>
                                            <span class="badge bg-secondary">IT Department</span>
                                        </td>
                                        <td><span class="badge bg-success"><?php echo app_lang('active'); ?></span></td>
                                        <td><small><?php echo date('Y-m-d H:i'); ?></small></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" title="<?php echo app_lang('view'); ?>">
                                                    <i data-feather="eye" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" title="<?php echo app_lang('edit'); ?>">
                                                    <i data-feather="edit-2" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-success" title="<?php echo app_lang('duplicate'); ?>">
                                                    <i data-feather="copy" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="<?php echo app_lang('delete'); ?>">
                                                    <i data-feather="trash-2" class="icon-14"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="announcement-checkbox" value="2"></td>
                                        <td>
                                            <strong>System Maintenance Schedule</strong>
                                            <br><small class="text-muted">Planned downtime this weekend</small>
                                        </td>
                                        <td><span class="badge bg-warning"><?php echo app_lang('maintenance'); ?></span></td>
                                        <td><span class="badge bg-danger"><?php echo app_lang('urgent'); ?></span></td>
                                        <td><span class="badge bg-light text-dark"><?php echo app_lang('all_departments'); ?></span></td>
                                        <td><span class="badge bg-success"><?php echo app_lang('active'); ?></span></td>
                                        <td><small><?php echo date('Y-m-d H:i', strtotime('-1 day')); ?></small></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" title="<?php echo app_lang('view'); ?>">
                                                    <i data-feather="eye" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" title="<?php echo app_lang('edit'); ?>">
                                                    <i data-feather="edit-2" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-success" title="<?php echo app_lang('duplicate'); ?>">
                                                    <i data-feather="copy" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="<?php echo app_lang('delete'); ?>">
                                                    <i data-feather="trash-2" class="icon-14"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="announcement-checkbox" value="3"></td>
                                        <td>
                                            <strong>Team Building Event</strong>
                                            <br><small class="text-muted">Annual company picnic announcement</small>
                                        </td>
                                        <td><span class="badge bg-success"><?php echo app_lang('event'); ?></span></td>
                                        <td><span class="badge bg-info"><?php echo app_lang('normal'); ?></span></td>
                                        <td><span class="badge bg-primary">Sales Department</span></td>
                                        <td><span class="badge bg-success"><?php echo app_lang('active'); ?></span></td>
                                        <td><small><?php echo date('Y-m-d H:i', strtotime('-3 days')); ?></small></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" title="<?php echo app_lang('view'); ?>">
                                                    <i data-feather="eye" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary" title="<?php echo app_lang('edit'); ?>">
                                                    <i data-feather="edit-2" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-success" title="<?php echo app_lang('duplicate'); ?>">
                                                    <i data-feather="copy" class="icon-14"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="<?php echo app_lang('delete'); ?>">
                                                    <i data-feather="trash-2" class="icon-14"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
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
                            <div class="col-lg-6 col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <div class="color-tag me-2" style="background-color: <?php echo $dept->color ?: '#6c757d'; ?>;"></div>
                                            <?php echo $dept->title; ?>
                                        </h6>
                                        <span class="badge bg-secondary"><?php echo rand(1, 5); ?> <?php echo app_lang('announcements'); ?></span>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex justify-content-between align-items-start p-2">
                                                <div>
                                                    <strong>Department Meeting</strong>
                                                    <br><small class="text-muted">Weekly sync meeting scheduled</small>
                                                </div>
                                                <span class="badge bg-info">Normal</span>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between align-items-start p-2">
                                                <div>
                                                    <strong>Budget Review</strong>
                                                    <br><small class="text-muted">Q4 budget planning session</small>
                                                </div>
                                                <span class="badge bg-warning">High</span>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i data-feather="plus" class="icon-14"></i> <?php echo app_lang('add_announcement'); ?>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary">
                                                <i data-feather="eye" class="icon-14"></i> <?php echo app_lang('view_all'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

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
                                            <button class="btn btn-outline-primary">
                                                <i data-feather="copy" class="icon-14"></i> <?php echo app_lang('use_template'); ?>
                                            </button>
                                            <button class="btn btn-outline-secondary">
                                                <i data-feather="edit-2" class="icon-14"></i> <?php echo app_lang('edit'); ?>
                                            </button>
                                            <button class="btn btn-outline-danger">
                                                <i data-feather="trash-2" class="icon-14"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ANNOUNCEMENTS ANALYTICS TAB -->
                    <div class="tab-pane fade" id="analytics-tab" role="tabpanel">
                        <h6><?php echo app_lang('announcement_analytics'); ?></h6>
                        <p class="text-muted"><?php echo app_lang('track_announcement_performance_and_engagement'); ?></p>

                        <!-- Analytics Dashboard -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-primary">85%</h4>
                                        <small><?php echo app_lang('read_rate'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-success">92%</h4>
                                        <small><?php echo app_lang('delivery_rate'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-info">15</h4>
                                        <small><?php echo app_lang('avg_response_time'); ?> (<?php echo app_lang('minutes'); ?>)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h4 class="text-warning">73%</h4>
                                        <small><?php echo app_lang('engagement_rate'); ?></small>
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
                                            <tr>
                                                <td>
                                                    <div class="color-tag me-2" style="background-color: <?php echo $dept->color ?: '#6c757d'; ?>;"></div>
                                                    <?php echo $dept->title; ?>
                                                </td>
                                                <td><?php echo rand(5, 25); ?></td>
                                                <td>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar" style="width: <?php echo rand(70, 95); ?>%"></div>
                                                    </div>
                                                    <small><?php echo rand(70, 95); ?>%</small>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-success" style="width: <?php echo rand(60, 90); ?>%"></div>
                                                    </div>
                                                    <small><?php echo rand(60, 90); ?>%</small>
                                                </td>
                                                <td><?php echo rand(10, 30); ?> <?php echo app_lang('minutes'); ?></td>
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
                            <select name="target_departments[]" id="announcement_departments" class="form-control select2" multiple>
                                <option value=""><?php echo app_lang('all_departments'); ?></option>
                                <?php foreach($departments as $dept): ?>
                                    <option value="<?php echo $dept->id; ?>"><?php echo $dept->title; ?></option>
                                <?php endforeach; ?>
                            </select>
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
                <button type="submit" form="announcement-form" class="btn btn-primary"><?php echo app_lang('publish_announcement'); ?></button>
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

<script>
$(document).ready(function() {
    // Initialize DataTables
    $('#announcements-table').DataTable({
        "pageLength": 10,
        "searching": true,
        "ordering": true,
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] }
        ]
    });

    // Initialize Select2
    $('.select2').select2({
        dropdownParent: $('#announcement-modal')
    });

    // Form submissions
    $('#announcement-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    $('#announcement-modal').modal('hide');
                    appAlert.success(response.message || '<?php echo app_lang('announcement_created_successfully'); ?>');
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

    // Filter functionality
    $('#filter-category, #filter-priority, #filter-department').on('change', function() {
        var table = $('#announcements-table').DataTable();
        table.draw();
    });

    $('#filter-search').on('keyup', function() {
        var table = $('#announcements-table').DataTable();
        table.search($(this).val()).draw();
    });

    // Initialize feather icons
    if (window.feather && typeof feather.replace === 'function') {
        feather.replace();
    }
});

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