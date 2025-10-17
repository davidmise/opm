<?php
/**
 * Workflow My Tasks View
 */
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <div class="title-button-group">
            <h1 class="page-title"><?php echo app_lang('my_tasks'); ?></h1>
            <button type="button" class="btn btn-primary" onclick="addTask()">
                <i data-feather="plus" class="icon-16"></i> <?php echo app_lang('add_task'); ?>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('my_workflow_tasks'); ?></h4>
                    <div class="card-header-actions">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary active" data-filter="all">
                                <?php echo app_lang('all'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="pending">
                                <?php echo app_lang('pending'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="in_progress">
                                <?php echo app_lang('in_progress'); ?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="completed">
                                <?php echo app_lang('completed'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tasks-table">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('task'); ?></th>
                                    <th><?php echo app_lang('shipment'); ?></th>
                                    <th><?php echo app_lang('priority'); ?></th>
                                    <th><?php echo app_lang('status'); ?></th>
                                    <th><?php echo app_lang('due_date'); ?></th>
                                    <th><?php echo app_lang('progress'); ?></th>
                                    <th><?php echo app_lang('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data -->
                                <tr data-status="pending">
                                    <td>
                                        <div>
                                            <strong>Review customs documentation</strong>
                                            <br><small class="text-muted">Verify all customs forms are complete and accurate</small>
                                        </div>
                                    </td>
                                    <td><a href="#" class="text-primary">SHP-2025-001</a></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('+2 days')), false); ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="startTask(1)">
                                                <i data-feather="play" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editTask(1)">
                                                <i data-feather="edit" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="in_progress">
                                    <td>
                                        <div>
                                            <strong>Update shipment tracking</strong>
                                            <br><small class="text-muted">Input latest tracking information from carrier</small>
                                        </div>
                                    </td>
                                    <td><a href="#" class="text-primary">SHP-2025-002</a></td>
                                    <td><span class="badge bg-warning">Medium</span></td>
                                    <td><span class="badge bg-primary">In Progress</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('+1 day')), false); ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">60%</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="completeTask(2)">
                                                <i data-feather="check" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editTask(2)">
                                                <i data-feather="edit" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="completed">
                                    <td>
                                        <div>
                                            <strong>Process payment verification</strong>
                                            <br><small class="text-muted">Confirm payment received and update status</small>
                                        </div>
                                    </td>
                                    <td><a href="#" class="text-primary">SHP-2025-003</a></td>
                                    <td><span class="badge bg-info">Low</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('-1 day')), false); ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewTask(3)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr data-status="pending">
                                    <td>
                                        <div>
                                            <strong>Schedule delivery appointment</strong>
                                            <br><small class="text-muted">Coordinate with client for delivery time</small>
                                        </div>
                                    </td>
                                    <td><a href="#" class="text-primary">SHP-2025-004</a></td>
                                    <td><span class="badge bg-warning">Medium</span></td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('+3 days')), false); ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="startTask(4)">
                                                <i data-feather="play" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editTask(4)">
                                                <i data-feather="edit" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addTask() {
    alert('Add Task feature coming soon!');
}

function startTask(id) {
    if (confirm('Start working on this task?')) {
        alert('Start Task feature coming soon!');
    }
}

function completeTask(id) {
    if (confirm('Mark this task as completed?')) {
        alert('Complete Task feature coming soon!');
    }
}

function editTask(id) {
    alert('Edit Task feature coming soon!');
}

function viewTask(id) {
    alert('View Task feature coming soon!');
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active', 'btn-primary'));
            filterButtons.forEach(btn => btn.classList.add('btn-outline-secondary'));
            
            // Add active class to clicked button
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary', 'active');
            
            const filter = this.getAttribute('data-filter');
            filterTasks(filter);
        });
    });
});

function filterTasks(status) {
    const rows = document.querySelectorAll('#tasks-table tbody tr');
    rows.forEach(row => {
        if (status === 'all') {
            row.style.display = '';
        } else {
            const rowStatus = row.getAttribute('data-status');
            if (rowStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}
</script>

<style>
.progress {
    height: 8px;
    border-radius: 4px;
}

.card-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-group .btn.active {
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

#tasks-table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 11px;
    padding: 6px 12px;
}

.btn-group .btn {
    margin-right: 2px;
}
</style>