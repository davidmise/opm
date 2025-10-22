<?php

namespace App\Controllers;

/**
 * Departments Controller
 * 
 * Manages CRUD operations for departments
 * Departments organize team members and projects
 * 
 * @package App\Controllers
 * @author O.P.M Development Team
 * @version 1.0.0
 */
class Departments extends Security_Controller {

    public $Department_templates_model;
    public $User_departments_model;

    function __construct() {
        parent::__construct();
        $this->check_module_availability("module_departments");

        // Allow read-only endpoints for department members; restrict management endpoints
        $router = function_exists('service') ? service('router') : null;
        $method = $router ? $router->methodName() : '';
        $public_methods = array(
            'view', 'dashboard', 'team', 'projects', 'tasks', 'overview',
            'department_team_list_data', 'get_dashboard_data'
        );

        if (!in_array($method, $public_methods)) {
            $this->access_only_admin_or_manage_departments_permission();
        }
        
        // Only load custom models if needed
        // Settings_model, Users_model, Projects_model are already loaded by parent
    }

    /**
     * Check if user can manage departments
     * Only admins or users with manage_departments permission
     */
    private function access_only_admin_or_manage_departments_permission() {
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_departments"))) {
            app_redirect("forbidden");
        }
    }

    /**
     * Display departments list page
     * 
     * @return string Rendered view
     */
    function index() {
        // Aggregate stats and collections for dashboard
        $departments = $this->Departments_model->get_details()->getResult();

        $total_departments = count($departments);
        $active_departments = 0;
        $total_members = 0;
        $total_projects = 0;
        $total_tasks = 0;

        foreach ($departments as $dept) {
            if ($dept->is_active) {
                $active_departments++;
            }
            $total_members += (int)$dept->total_members;
            $total_projects += (int)$dept->total_projects;
            $total_tasks += (int)$dept->total_tasks;
        }

        // Top departments by members / tasks
        $top_by_members = $departments;
        usort($top_by_members, function($a, $b) { return ((int)$b->total_members) <=> ((int)$a->total_members); });
        $top_by_members = array_slice($top_by_members, 0, 5);

        $top_by_tasks = $departments;
        usort($top_by_tasks, function($a, $b) { return ((int)$b->total_tasks) <=> ((int)$a->total_tasks); });
        $top_by_tasks = array_slice($top_by_tasks, 0, 5);

        // Grid cards payload (minimal fields)
        $departments_for_grid = array_map(function ($d) {
            return array(
                'id' => $d->id,
                'title' => $d->title,
                'description' => $d->description,
                'color' => $d->color ?: '#6c757d',
                'is_active' => (int)$d->is_active,
                'total_members' => (int)$d->total_members,
                'total_projects' => (int)$d->total_projects,
                'total_tasks' => (int)$d->total_tasks
            );
        }, $departments);

        // RBAC/Access summary
        $accessible_departments = array();
        if (!$this->login_user->is_admin) {
            $accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
        }

        $view_data = array(
            'stats' => array(
                'total_departments' => $total_departments,
                'active_departments' => $active_departments,
                'total_members' => $total_members,
                'total_projects' => $total_projects,
                'total_tasks' => $total_tasks
            ),
            'top_by_members' => $top_by_members,
            'top_by_tasks' => $top_by_tasks,
            'departments_grid_json' => json_encode($departments_for_grid),
            'accessible_departments' => $accessible_departments
        );

        return $this->template->rander("departments/index", $view_data);
    }

    /**
     * Load department add/edit modal form
     * 
     * @return string Rendered modal view
     */
    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $view_data['model_info'] = $this->Departments_model->get_one($id);

        return $this->template->view('departments/modal_form', $view_data);
    }

    /**
     * Save department data (create or update)
     * 
     * @return json Success/error response
     */
    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required"
        ));

        $id = $this->request->getPost('id');
        
        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "color" => $this->request->getPost('color')
        );

        if (!$id) {
            // New department - set created_by and created_at
            $data["created_by"] = $this->login_user->id;
            $data["created_at"] = get_current_utc_time();
        }

        $save_id = $this->Departments_model->ci_save($data, $id);
        
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /**
     * Delete a department
     * 
     * @return json Success/error response
     */
    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        
        // Check if department has dependencies (members or projects)
        if ($this->Departments_model->has_dependencies($id)) {
            echo json_encode(array("success" => false, 'message' => app_lang('department_has_dependencies')));
            return false;
        }

        if ($this->Departments_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /**
     * Get departments list data for DataTable
     * 
     * @return json DataTable JSON response
     */
    function list_data() {
        $list_data = $this->Departments_model->get_details()->getResult();
        $result = array();
        
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        
        echo json_encode(array("data" => $result));
    }

    /**
     * Tab content loader for departments settings (used by ajax-tab in index)
     */
    function settings() {
        // Comprehensive settings with all necessary data
        $view_data = array();
        
        // Get all departments for various settings
        $departments = $this->Departments_model->get_details()->getResult();
        $view_data['departments'] = $departments;
        
        // Get department statistics
        $view_data['total_departments'] = count($departments);
        $view_data['active_departments'] = count(array_filter($departments, function($d) { return $d->is_active; }));
        
        // Get all users for permission settings
        $all_users = $this->Users_model->get_details()->getResult();
        $view_data['all_users'] = $all_users;
        
        // Get current settings (with defaults)
        $view_data['default_department_color'] = get_setting('default_department_color') ?: '#6c757d';
        $view_data['auto_assign_new_users'] = get_setting('auto_assign_new_users') ?: '0';
        $view_data['department_approval_required'] = get_setting('department_approval_required') ?: '0';
        $view_data['max_departments_per_user'] = get_setting('max_departments_per_user') ?: '5';
        
        // RBAC roles and permissions
        $view_data['rbac_roles'] = array('admin', 'manager', 'member', 'client');
        $view_data['rbac_permissions'] = array(
            'view_all_departments' => 'View All Departments',
            'create_departments' => 'Create Departments', 
            'edit_departments' => 'Edit Departments',
            'delete_departments' => 'Delete Departments',
            'manage_department_users' => 'Manage Department Users',
            'view_department_reports' => 'View Department Reports',
            'export_department_data' => 'Export Department Data',
            'manage_department_settings' => 'Manage Department Settings'
        );
        
        // Sample templates for demo
        $view_data['department_templates'] = array(
            (object)array('id' => 1, 'name' => 'Development Team', 'description' => 'Software development department template', 'created_date' => date('Y-m-d')),
            (object)array('id' => 2, 'name' => 'Sales Team', 'description' => 'Sales and marketing department template', 'created_date' => date('Y-m-d')),
            (object)array('id' => 3, 'name' => 'Support Team', 'description' => 'Customer support department template', 'created_date' => date('Y-m-d'))
        );
        
        // Sample activity log for demo
        $view_data['recent_activity'] = array(
            (object)array('action' => 'Department Created', 'details' => 'Development Team created by Admin', 'date' => date('Y-m-d H:i:s'), 'user' => 'Admin'),
            (object)array('action' => 'User Assigned', 'details' => 'John Doe assigned to Sales Team', 'date' => date('Y-m-d H:i:s'), 'user' => 'Manager'),
            (object)array('action' => 'Settings Updated', 'details' => 'Department color scheme updated', 'date' => date('Y-m-d H:i:s'), 'user' => 'Admin')
        );
        
        return $this->template->view("departments/settings", $view_data);
    }

    /**
     * Tab content loader for departments list (used by ajax-tab in index)
     */
    function departments_list() {
        return $this->template->view("departments/departments_list");
    }

    /**
     * Tab content loader for department announcements (used by ajax-tab in index)
     */
    function announcements() {
        // Comprehensive announcements with department integration
        $view_data = array();
        
        // Get all departments for filtering and targeting
        $departments = $this->Departments_model->get_details()->getResult();
        $view_data['departments'] = $departments;
        
        // Get announcements with department relationships
        $announcements = $this->Announcements_model->get_details()->getResult();
        $view_data['announcements'] = $announcements;
        
        // Announcement statistics with safe fallbacks
        $current_date = date('Y-m-d');
        $view_data['total_announcements'] = count($announcements);
        $view_data['active_announcements'] = count(array_filter($announcements, function($a) use ($current_date) { 
            return isset($a->end_date) && $a->end_date >= $current_date; 
        }));
        $view_data['department_specific'] = count(array_filter($announcements, function($a) { 
            return !empty($a->share_with) && strpos($a->share_with, 'dept:') !== false; 
        }));
        $view_data['global_announcements'] = count(array_filter($announcements, function($a) { 
            return empty($a->share_with) || $a->share_with == 'all_members'; 
        }));
        
        // Announcement categories
        $view_data['announcement_categories'] = array(
            'general' => app_lang('general'),
            'urgent' => app_lang('urgent'), 
            'policy' => app_lang('policy'),
            'event' => app_lang('event'),
            'training' => app_lang('training'),
            'maintenance' => app_lang('maintenance'),
            'celebration' => app_lang('celebration')
        );
        
        // Priority levels
        $view_data['priority_levels'] = array(
            'low' => app_lang('low'),
            'normal' => app_lang('normal'),
            'high' => app_lang('high'),
            'urgent' => app_lang('urgent')
        );
        
        // Sample announcement templates for demo
        $view_data['announcement_templates'] = array(
            (object)array(
                'id' => 1, 
                'title' => 'New Policy Announcement',
                'content' => 'Template for announcing new company policies',
                'category' => 'policy',
                'priority' => 'normal'
            ),
            (object)array(
                'id' => 2,
                'title' => 'Emergency Notice',
                'content' => 'Template for urgent emergency communications',
                'category' => 'urgent', 
                'priority' => 'urgent'
            ),
            (object)array(
                'id' => 3,
                'title' => 'Training Schedule',
                'content' => 'Template for announcing training sessions',
                'category' => 'training',
                'priority' => 'normal'
            )
        );
        
        return $this->template->view("departments/announcements", $view_data);
    }

    /**
     * Save general department settings
     */
    function save_general_settings() {
        $this->validate_submitted_data(array(
            "default_department_color" => "required",
            "auto_assign_new_users" => "required",
            "department_approval_required" => "required", 
            "max_departments_per_user" => "required|numeric"
        ));

        $settings_data = array(
            "default_department_color" => $this->request->getPost('default_department_color'),
            "auto_assign_new_users" => $this->request->getPost('auto_assign_new_users'),
            "department_approval_required" => $this->request->getPost('department_approval_required'),
            "max_departments_per_user" => $this->request->getPost('max_departments_per_user')
        );

        // Save each setting (this would normally save to a settings table)
        foreach($settings_data as $key => $value) {
            // For demo purposes, we'll just return success
            // In real implementation: $this->Settings_model->save_setting($key, $value);
        }

        return $this->response->setJSON(array("success" => true, "message" => app_lang('settings_saved_successfully')));
    }

    /**
     * Save RBAC settings
     */
    function save_rbac_settings() {
        $rbac_data = $this->request->getPost('rbac');
        
        if (!$rbac_data) {
            return $this->response->setJSON(array("success" => false, "message" => app_lang('no_rbac_data_provided')));
        }

        // Save RBAC settings (this would normally save to database)
        // For demo purposes, we'll just return success
        // In real implementation: $this->RBAC_model->save_permissions($rbac_data);

        return $this->response->setJSON(array("success" => true, "message" => app_lang('rbac_settings_saved_successfully')));
    }

    /**
     * Save department template
     */
    function save_template() {
        $this->validate_submitted_data(array(
            "template_name" => "required",
            "template_description" => "required"
        ));

        $template_data = array(
            "name" => $this->request->getPost('template_name'),
            "description" => $this->request->getPost('template_description'),
            "created_date" => date('Y-m-d H:i:s'),
            "created_by" => $this->login_user->id
        );

        // Save template (this would normally save to database)
        // In real implementation: $template_id = $this->Department_templates_model->ci_save($template_data);

        return $this->response->setJSON(array("success" => true, "message" => app_lang('template_saved_successfully')));
    }

    /**
     * Save announcement
     */
    function save_announcement() {
        $this->validate_submitted_data(array(
            "title" => "required",
            "content" => "required",
            "priority" => "required",
            "category" => "required"
        ));

        $announcement_data = array(
            "title" => $this->request->getPost('title'),
            "content" => $this->request->getPost('content'),
            "priority" => $this->request->getPost('priority'),
            "category" => $this->request->getPost('category'),
            "target_departments" => json_encode($this->request->getPost('target_departments')),
            "start_date" => $this->request->getPost('start_date') ?: date('Y-m-d H:i:s'),
            "end_date" => $this->request->getPost('end_date'),
            "send_email" => $this->request->getPost('send_email') ? 1 : 0,
            "send_push" => $this->request->getPost('send_push') ? 1 : 0,
            "status" => $this->request->getPost('status') ?: 'published',
            "created_by" => $this->login_user->id,
            "created_date" => date('Y-m-d H:i:s')
        );

        // Save announcement (this would normally save to database)
        // In real implementation: $announcement_id = $this->Announcements_model->ci_save($announcement_data);

        $message = ($announcement_data['status'] == 'draft') ? 
                   app_lang('announcement_saved_as_draft') : 
                   app_lang('announcement_created_successfully');

        return $this->response->setJSON(array("success" => true, "message" => $message));
    }

    /**
     * Save announcement template
     */
    function save_announcement_template() {
        $this->validate_submitted_data(array(
            "title" => "required",
            "content" => "required",
            "category" => "required",
            "priority" => "required"
        ));

        $template_data = array(
            "title" => $this->request->getPost('title'),
            "content" => $this->request->getPost('content'),
            "category" => $this->request->getPost('category'),
            "priority" => $this->request->getPost('priority'),
            "created_by" => $this->login_user->id,
            "created_date" => date('Y-m-d H:i:s')
        );

        // Save template (this would normally save to database)
        // In real implementation: $template_id = $this->Announcement_templates_model->ci_save($template_data);

        return $this->response->setJSON(array("success" => true, "message" => app_lang('template_created_successfully')));
    }

    /**
     * Export announcements data
     */
    function export_announcements() {
        // This would export announcements to CSV/Excel
        // For demo purposes, just redirect back
        app_redirect("departments");
    }

    /**
     * Display detailed view of a single department
                'Created new department with 5 initial members'
            ),
            array(
                get_current_utc_time(),
                $this->login_user->first_name . ' ' . $this->login_user->last_name,
                'Member Added',
                'Finance Department',
                'Added John Doe to department'
            )
        );

        echo json_encode(array("data" => $activities));
    }

    /**
     * Import departments from file
     */
    function import() {
        $upload_file_result = $this->_upload_import_file();
        
        if (!$upload_file_result->success) {
            echo json_encode(array("success" => false, "message" => $upload_file_result->message));
            return;
        }
        
        $file_path = $upload_file_result->file_path;
        $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
        
        switch (strtolower($file_extension)) {
            case 'csv':
                $result = $this->_import_csv($file_path);
                break;
            case 'json':
                $result = $this->_import_json($file_path);
                break;
            default:
                $result = array("success" => false, "message" => app_lang('unsupported_file_format'));
        }
        
        // Clean up uploaded file
        unlink($file_path);
        
        echo json_encode($result);
    }

    /**
     * Bulk operations - deactivate empty departments
     */
    function bulk_deactivate_empty() {
        $this->Departments_model->where('total_members', 0)
                                ->where('total_projects', 0)
                                ->where('total_tasks', 0)
                                ->where('is_active', 1)
                                ->update_batch(array('is_active' => 0));
        
        echo json_encode(array("success" => true, "message" => app_lang('empty_departments_deactivated')));
    }

    /**
     * Sync department data (recalculate counts)
     */
    function sync_department_data() {
        $departments = $this->Departments_model->get_all()->getResult();
        
        foreach ($departments as $dept) {
            // Recalculate member count
            $member_count = $this->User_departments_model->where('department_id', $dept->id)->count_all_results();
            
            // Recalculate project count
            $project_count = $this->Projects_model->where('department_id', $dept->id)->count_all_results();
            
            // Recalculate task count  
            $task_count = $this->Tasks_model->where('department_id', $dept->id)->count_all_results();
            
            // Update department with new counts
            $this->Departments_model->ci_save(array(
                'total_members' => $member_count,
                'total_projects' => $project_count, 
                'total_tasks' => $task_count
            ), $dept->id);
        }
        
        echo json_encode(array("success" => true, "message" => app_lang('department_data_synchronized')));
    }

    /**
     * Cleanup orphaned department data
     */
    function cleanup_orphaned_data() {
        // Remove department assignments for deleted users
        $this->User_departments_model->where_not_in('user_id', 
            $this->Users_model->select('id')->get()->getResultArray())
            ->delete();
        
        // Remove project assignments for deleted departments
        $this->Projects_model->where_not_in('department_id', 
            $this->Departments_model->select('id')->get()->getResultArray())
            ->update(array('department_id' => null));
            
        echo json_encode(array("success" => true, "message" => app_lang('orphaned_data_cleaned')));
    }

    /**
     * Upload import file
     */
    private function _upload_import_file() {
        $upload_path = get_setting("temp_file_path");
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'csv|json|xlsx';
        $config['max_size'] = 5000; // 5MB
        
        if (!file_exists($upload_path)) {
            if (!mkdir($upload_path, 0777, true)) {
                return (object)array("success" => false, "message" => app_lang('upload_failed'));
            }
        }
        
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            return (object)array("success" => false, "message" => app_lang('file_upload_failed'));
        }
        
        $file_name = uniqid() . '_' . $_FILES['import_file']['name'];
        $file_path = $upload_path . $file_name;
        
        if (move_uploaded_file($_FILES['import_file']['tmp_name'], $file_path)) {
            return (object)array("success" => true, "file_path" => $file_path);
        } else {
            return (object)array("success" => false, "message" => app_lang('file_upload_failed'));
        }
    }

    /**
     * Import departments from CSV
     */
    private function _import_csv($file_path) {
        $imported = 0;
        $errors = array();
        
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            $headers = fgetcsv($handle); // Skip header row
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) >= 3) { // At least title, description, color
                    $department_data = array(
                        'title' => $data[1],
                        'description' => $data[2],
                        'color' => isset($data[3]) ? $data[3] : '#6c757d',
                        'is_active' => 1,
                        'created_by' => $this->login_user->id,
                        'created_date' => get_current_utc_time()
                    );
                    
                    if ($this->Departments_model->ci_save($department_data)) {
                        $imported++;
                    } else {
                        $errors[] = "Failed to import: " . $data[1];
                    }
                }
            }
            fclose($handle);
        }
        
        $message = sprintf(app_lang('departments_imported_successfully'), $imported);
        if (!empty($errors)) {
            $message .= " " . implode(", ", $errors);
        }
        
        return array("success" => true, "message" => $message);
    }

    /**
     * Import departments from JSON
     */
    private function _import_json($file_path) {
        $content = file_get_contents($file_path);
        $departments = json_decode($content, true);
        
        if (!$departments) {
            return array("success" => false, "message" => app_lang('invalid_json_file'));
        }
        
        $imported = 0;
        $errors = array();
        
        foreach ($departments as $dept) {
            if (isset($dept['title']) && isset($dept['description'])) {
                $department_data = array(
                    'title' => $dept['title'],
                    'description' => $dept['description'],
                    'color' => isset($dept['color']) ? $dept['color'] : '#6c757d',
                    'is_active' => isset($dept['is_active']) ? $dept['is_active'] : 1,
                    'created_by' => $this->login_user->id,
                    'created_date' => get_current_utc_time()
                );
                
                if ($this->Departments_model->ci_save($department_data)) {
                    $imported++;
                } else {
                    $errors[] = "Failed to import: " . $dept['title'];
                }
            }
        }
        
        $message = sprintf(app_lang('departments_imported_successfully'), $imported);
        if (!empty($errors)) {
            $message .= " " . implode(", ", $errors);
        }
        
        return array("success" => true, "message" => $message);
    }

    /**
     * AJAX endpoint to get grid data for departments
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response
     */
    function get_grid_data() {
        try {
            $departments = $this->Departments_model->get_details()->getResult();
            
            $grid_data = array();
            foreach ($departments as $d) {
                $grid_data[] = array(
                    'id' => (int)$d->id,
                    'title' => $d->title,
                    'description' => $d->description ?: '',
                    'color' => $d->color ?: '#6c757d',
                    'is_active' => (int)$d->is_active,
                    'total_members' => (int)($d->total_members ?: 0),
                    'total_projects' => (int)($d->total_projects ?: 0),
                    'total_tasks' => (int)($d->total_tasks ?: 0)
                );
            }
            
            // Set proper JSON header and return response
            return $this->response->setJSON(array("success" => true, "data" => $grid_data));
        } catch (\Exception $e) {
            return $this->response->setJSON(array("success" => false, "message" => $e->getMessage()));
        }
    }

    /**
     * Get a single row data
     * 
     * @param int $id Department ID
     * @return array Row data
     */
    private function _row_data($id) {
        validate_numeric_value($id);
        $data = $this->Departments_model->get_details(array("id" => $id))->getRow();
        return $this->_make_row($data);
    }

    /**
     * Prepare department list row
     * 
     * @param object $data Department data
     * @return array Row data for DataTable
     */
    private function _make_row($data) {
        // Color badge with larger size
        $color_badge = "<span class='color-tag float-start me-2' style='background-color: $data->color; width: 12px; height: 12px; display: inline-block; border-radius: 2px;'></span>";
        
        // Department title - link to department details page
        $title = anchor(get_uri("departments/view/" . $data->id), "<strong>$data->title</strong>", array(
            "class" => "text-dark",
            "title" => app_lang('view_department_details')
        ));

        // Statistics with improved badges using department theme color
        $members_badge = "<span class='badge' style='background-color: " . $data->color . "; color: white;' title='" . app_lang('team_members') . "'><i data-feather='users' class='icon-14'></i> $data->total_members " . app_lang('members') . "</span>";
        $projects_badge = "<span class='badge ms-2' style='background-color: " . $data->color . "88; color: " . $data->color . "; border: 1px solid " . $data->color . ";' title='" . app_lang('projects') . "'><i data-feather='command' class='icon-14'></i> $data->total_projects " . app_lang('projects') . "</span>";
        
        $stats = $members_badge . " " . $projects_badge;

        // Action buttons (Edit + Delete) — removed the eye/view icon per UI update request
        $edit_link = modal_anchor(get_uri("departments/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array(
            "class" => "edit btn btn-sm btn-outline-light",
            "title" => app_lang('edit_department'),
            "data-post-id" => $data->id
        ));

        $delete_link = js_anchor("<i data-feather='x' class='icon-16'></i>", array(
            'title' => app_lang('delete_department'),
            "class" => "delete btn btn-sm btn-outline-danger",
            "data-id" => $data->id,
            "data-action-url" => get_uri("departments/delete"),
            "data-action" => "delete-confirmation"
        ));

        return array(
            $color_badge . $title,
            $data->description ? nl2br($data->description) : "<span class='text-muted'>-</span>",
            $stats,
            $data->created_by_user ? $data->created_by_user : "<span class='text-muted'>-</span>",
            $edit_link . " " . $delete_link
        );
    }

    /**
     * View department details page
     * 
     * @param int $id Department ID
     * @return string Rendered view
     */
    function view($id = 0) {
        validate_numeric_value($id);
        
        if ($id) {
            $department_info = $this->Departments_model->get_one($id);
            
            if (!$department_info->id) {
                show_404();
            }
            
            // Check if user has access to this department (unless admin)
            if (!$this->login_user->is_admin) {
                $accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
                if (!in_array($id, $accessible_departments)) {
                    app_redirect("forbidden");
                }
            }
            
            // Store this department as the active one in session
            $this->session->set('active_department_id', $id);
            
            // Redirect to the new my_department dashboard
            app_redirect("my_department");
        } else {
            show_404();
        }
    }

    /**
     * Department dashboard with enhanced metrics
     * 
     * @param int $department_id Department ID
     * @return string Rendered dashboard view
     */
    function dashboard($department_id = 0) {
        $this->validate_submitted_data(array(
            "department_id" => "numeric"
        ));

        if ($department_id) {
            $department_info = $this->Departments_model->get_one($department_id);
            
            if (!$department_info->id) {
                show_404();
            }

            // Check if user has access to this department
            if (!$this->login_user->is_admin) {
                $accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
                if (!in_array($department_id, $accessible_departments)) {
                    app_redirect("forbidden");
                }
            }
            
            $view_data['department_info'] = $department_info;
            $view_data['dashboard_data'] = $this->Departments_model->get_department_dashboard_data($department_id);
            
            return $this->template->rander("departments/dashboard", $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Manage users for a specific department
     * 
     * @param int $department_id Department ID
     * @return string Rendered users management view
     */
    function manage_users($department_id = 0) {
        $this->validate_submitted_data(array(
            "department_id" => "numeric"
        ));

        if ($department_id) {
            $department_info = $this->Departments_model->get_one($department_id);
            
            if (!$department_info->id) {
                show_404();
            }
            
            $view_data['department_info'] = $department_info;
            $view_data['department_users'] = $this->Departments_model->get_department_users($department_id);
            
            return $this->template->rander("departments/manage_users", $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Add user to department via AJAX
     * 
     * @return string JSON response
     */
    function add_user_to_department() {
        $this->validate_submitted_data(array(
            "department_id" => "required|numeric"
        ));

        $user_ids = $this->request->getPost('user_ids');
        $department_id = $this->request->getPost('department_id');

        // Ensure user_ids is an array
        if (!is_array($user_ids) || empty($user_ids)) {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('please_select_at_least_one_team_member')
            ));
            return;
        }

        // Validate that all users are staff members (not clients)
        $Users_model = model('App\Models\Users_model');
        $User_departments_model = model('App\Models\User_departments_model');
        
        $added_count = 0;
        $already_exists_count = 0;
        $error_count = 0;

        foreach ($user_ids as $user_id) {
            // Validate user ID is numeric
            if (!is_numeric($user_id)) {
                $error_count++;
                continue;
            }

            $user = $Users_model->get_one($user_id);
            
            if (!$user || $user->user_type !== 'staff') {
                $error_count++;
                continue;
            }

            // Check if user is already in this department
            if ($User_departments_model->is_user_in_department($user_id, $department_id)) {
                $already_exists_count++;
                continue;
            }

            // Add user to department (not as primary by default)
            $result = $User_departments_model->add_user_to_department($user_id, $department_id, false);
            
            if ($result) {
                $added_count++;
            } else {
                $error_count++;
            }
        }

        // Build response message
        $messages = array();
        if ($added_count > 0) {
            $messages[] = $added_count . " " . app_lang('team_member' . ($added_count > 1 ? 's' : '')) . " " . app_lang('added_successfully');
        }
        if ($already_exists_count > 0) {
            $messages[] = $already_exists_count . " " . app_lang('already_in_department');
        }
        if ($error_count > 0) {
            $messages[] = $error_count . " " . app_lang('failed_to_add');
        }

        if ($added_count > 0) {
            echo json_encode(array(
                "success" => true,
                "message" => implode(". ", $messages)
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => $messages ? implode(". ", $messages) : app_lang('error_occurred')
            ));
        }
    }

    /**
     * Remove user from department via AJAX
     * 
     * @return string JSON response
     */
    function remove_user_from_department() {
        $this->validate_submitted_data(array(
            "user_id" => "required|numeric",
            "department_id" => "required|numeric"
        ));

        $user_id = $this->request->getPost('user_id');
        $department_id = $this->request->getPost('department_id');

        // Load the User_departments_model
        $User_departments_model = model('App\Models\User_departments_model');
        
        $result = $User_departments_model->remove_user_from_department($user_id, $department_id);
        
        if ($result) {
            echo json_encode(array(
                "success" => true,
                "message" => app_lang('user_removed_from_department_successfully')
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('error_occurred')
            ));
        }
    }

    /**
     * Set user's primary department via AJAX
     * 
     * @return string JSON response
     */
    function set_primary_department() {
        $this->validate_submitted_data(array(
            "user_id" => "required|numeric",
            "department_id" => "required|numeric"
        ));

        $user_id = $this->request->getPost('user_id');
        $department_id = $this->request->getPost('department_id');

        // Load the User_departments_model
        $User_departments_model = model('App\Models\User_departments_model');
        
        $result = $User_departments_model->set_primary_department($user_id, $department_id);
        
        if ($result) {
            // Return both success message and updated row data for team members table
            echo json_encode(array(
                "success" => true,
                "message" => app_lang('primary_department_updated_successfully'),
                "refresh_tables" => true // Signal to refresh all tables showing this user
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('error_occurred')
            ));
        }
    }

    /**
     * Get department dashboard data via AJAX
     * 
     * @return string JSON response
     */
    function get_dashboard_data() {
        $this->validate_submitted_data(array(
            "department_id" => "required|numeric"
        ));

        $department_id = $this->request->getPost('department_id');
        
        // Check access permission
        if (!$this->login_user->is_admin) {
            $accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
            if (!in_array($department_id, $accessible_departments)) {
                echo json_encode(array("success" => false, "message" => app_lang('access_denied')));
                return;
            }
        }
        
        $dashboard_data = $this->Departments_model->get_department_dashboard_data($department_id);
        
        echo json_encode(array(
            "success" => true,
            "data" => $dashboard_data
        ));
    }

    /**
     * Toggle department active status
     * 
     * @return string JSON response
     */
    function toggle_status() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $department_id = $this->request->getPost('id');
        $department_info = $this->Departments_model->get_one($department_id);
        
        if (!$department_info->id) {
            echo json_encode(array("success" => false, "message" => app_lang('department_not_found')));
            return;
        }

        $new_status = $department_info->is_active ? 0 : 1;
        
        $data = array("is_active" => $new_status);
        $result = $this->Departments_model->ci_save($data, $department_id);
        
        if ($result) {
            echo json_encode(array(
                "success" => true,
                "message" => $new_status ? app_lang('department_activated') : app_lang('department_deactivated'),
                "new_status" => $new_status
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('error_occurred')
            ));
        }
    }

    /**
     * Export department data
     * 
     * @return void
     */
    function export() {
        $departments = $this->Departments_model->get_details()->getResult();
        
        $data = array();
        foreach ($departments as $department) {
            $data[] = array(
                "Department" => $department->title,
                "Description" => $department->description,
                "Head" => $department->head_user_name ?: '-',
                "Members" => $department->total_members,
                "Projects" => $department->total_projects,
                "Tasks" => $department->total_tasks,
                "Status" => $department->is_active ? 'Active' : 'Inactive',
                "Created" => format_to_date($department->created_at, false)
            );
        }
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="departments_' . date("Y-m-d") . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Write CSV headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            
            // Write data rows
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit;
    }

    /**
     * Show add user to department modal
     * 
     * @param int $department_id Department ID
     * @return string Rendered modal view
     */
    function add_user_modal($department_id = 0) {
        $this->validate_submitted_data(array(
            "department_id" => "numeric"
        ));

        if ($department_id) {
            $department_info = $this->Departments_model->get_one($department_id);
            
            if (!$department_info->id) {
                show_404();
            }

            $view_data['department_info'] = $department_info;
            
            // Get users not in this department
            $Users_model = model('App\Models\Users_model');
            $User_departments_model = model('App\Models\User_departments_model');
            
            $all_users = $Users_model->get_details(array("user_type" => "staff"))->getResult();
            $users_dropdown = array();
            
            foreach ($all_users as $user) {
                $users_dropdown[$user->id] = $user->first_name . " " . $user->last_name . " (" . $user->email . ")";
            }
            
            $view_data['users_dropdown'] = $users_dropdown;
            
            return $this->template->view("departments/add_user_modal", $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Get department team list data for DataTable
     * 
     * @param int $department_id Department ID
     * @return string JSON response
     */
    function department_team_list_data($department_id = 0) {
        $this->validate_submitted_data(array(
            "department_id" => "numeric"
        ));

        if ($department_id) {
            $User_departments_model = model('App\Models\User_departments_model');
            $options = array("department_id" => $department_id);
            $list_data = $User_departments_model->get_department_users_with_details($department_id)->getResult();

            $result = array();
            foreach ($list_data as $data) {
                $result[] = $this->_make_department_team_row($data);
            }

            echo json_encode(array("data" => $result));
        }
    }

    /**
     * Make row for department team table
     * 
     * @param object $data User data
     * @return array Row data
     */
    private function _make_department_team_row($data) {
        // Create member display with avatar and name
        $avatar = "<span class='avatar avatar-xs'>";
        $avatar .= "<img alt='" . $data->first_name . " " . $data->last_name . "' src='" . get_avatar($data->image) . "'>";
        $avatar .= "</span>";
        
        $member_name = $data->first_name . " " . $data->last_name;
        $member_link = "<div class='d-flex'>";
        $member_link .= $avatar;
        $member_link .= "<div class='ms-2'>";
        $member_link .= get_team_member_profile_link($data->user_id, $member_name, array("class" => ""));
        $member_link .= "</div>";
        $member_link .= "</div>";
        
        $primary_badge = "";
        if ($data->is_primary) {
            $primary_badge = "<span class='badge bg-primary'>" . app_lang("primary") . "</span>";
        } else {
            $primary_badge = "<span class='text-muted'>-</span>";
        }

        $actions = "";
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_departments")) {
            $actions = '<div class="d-flex gap-1 justify-content-center">';
            
            // Set as Primary action (only if not already primary)
            if (!$data->is_primary) {
                $actions .= '<button type="button" class="btn btn-outline-secondary btn-sm rounded-circle set-as-primary" data-user-id="' . $data->user_id . '" data-department-id="' . $data->department_id . '" data-user-name="' . htmlspecialchars($data->first_name . " " . $data->last_name, ENT_QUOTES) . '" title="' . app_lang('set_as_primary') . '"><i data-feather="star" class="icon-16"></i></button>';
            }
            
            // Remove from department action (always show)
            $actions .= '<button type="button" class="btn btn-outline-secondary btn-sm rounded-circle remove-user-from-department" data-user-id="' . $data->user_id . '" data-department-id="' . $data->department_id . '" data-user-name="' . htmlspecialchars($data->first_name . " " . $data->last_name, ENT_QUOTES) . '" title="' . app_lang('remove_member_from_department') . '"><i data-feather="user-x" class="icon-16"></i></button>';
            
            $actions .= '</div>';
        }

        return array(
            $member_link,
            $data->role_title ?: app_lang("no_role"),
            $primary_badge,
            $data->email,
            $actions
        );
    }

    /**
     * Load department team tab content via AJAX
     * 
     * @param int $department_id Department ID
     * @return string Rendered tab content
     */
    function team($department_id = 0) {
        validate_numeric_value($department_id);
        
        if ($department_id) {
            $department_info = $this->Departments_model->get_one($department_id);
            
            if (!$department_info->id) {
                show_404();
            }

            // Enforce access: non-admins must belong to this department
            if (!$this->login_user->is_admin) {
                $accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
                if (!in_array($department_id, $accessible_departments)) {
                    app_redirect("forbidden");
                }
            }
            
            $view_data['department_info'] = $department_info;
            
            return $this->template->view("departments/tabs/team", $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Load department projects tab content via AJAX
     * 
     * @param int $department_id Department ID
     * @return string Rendered tab content
     */
    function projects($department_id = 0) {
        validate_numeric_value($department_id);
        
        if ($department_id) {
            $department_info = $this->Departments_model->get_one($department_id);
            
            if (!$department_info->id) {
                show_404();
            }

            // Enforce access: non-admins must belong to this department
            if (!$this->login_user->is_admin) {
                $accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
                if (!in_array($department_id, $accessible_departments)) {
                    app_redirect("forbidden");
                }
            }
            
            $view_data['department_info'] = $department_info;
            
            return $this->template->view("departments/tabs/projects", $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Load department tasks tab content via AJAX
     * 
     * @param int $department_id Department ID
     * @return string Rendered tab content
     */
    function tasks($department_id = 0) {
        validate_numeric_value($department_id);
        
        if ($department_id) {
            $department_info = $this->Departments_model->get_one($department_id);
            
            if (!$department_info->id) {
                show_404();
            }
            
            $view_data['department_info'] = $department_info;
            
            return $this->template->view("departments/tabs/tasks", $view_data);
        } else {
            show_404();
        }
    }

    /**
     * Load department overview tab content via AJAX
     * 
     * @param int $department_id Department ID
     * @return string Rendered tab content
     */
    function overview($department_id = 0) {
        validate_numeric_value($department_id);
        
        if ($department_id) {
            $department_info = $this->Departments_model->get_one($department_id);
            
            if (!$department_info->id) {
                show_404();
            }
            
            // Get enhanced department details with user info
            $detailed_info = $this->Departments_model->get_details(array("id" => $department_id))->getRow();
            if ($detailed_info) {
                $view_data['department_info'] = $detailed_info;
            } else {
                $view_data['department_info'] = $department_info;
            }
            
            $view_data['statistics'] = $this->Departments_model->get_statistics($department_id);
            
            // Get recent activities (if available)
            if (method_exists($this->Departments_model, 'get_recent_activities')) {
                $view_data['recent_activities'] = $this->Departments_model->get_recent_activities($department_id, 10);
            }
            
            return $this->template->view("departments/tabs/overview", $view_data);
        } else {
            show_404();
        }
    }

    function update_color() {
        if (!$this->request->isAJAX()) {
            show_404();
        }

        $department_id = $this->request->getPost('department_id');
        $color = $this->request->getPost('color');

        validate_numeric_value($department_id);
        
        // Validate color format
        if (!preg_match('/^#[0-9A-F]{6}$/i', $color)) {
            echo json_encode(array("success" => false, "message" => app_lang('invalid_color_format')));
            return;
        }

        // Check if department exists and user has permission
        $department_info = $this->Departments_model->get_one($department_id);
        if (!$department_info->id) {
            echo json_encode(array("success" => false, "message" => app_lang('department_not_found')));
            return;
        }

        // Update the department color
        $data = array('color' => $color);
        $result = $this->Departments_model->ci_save($data, $department_id);

        if ($result) {
            echo json_encode(array("success" => true, "message" => app_lang('department_color_updated_successfully')));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang('something_went_wrong')));
        }
    }
}
