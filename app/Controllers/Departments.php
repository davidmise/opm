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

    function __construct() {
        parent::__construct();
        $this->check_module_availability("module_departments");
        $this->access_only_admin_or_manage_departments_permission();
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
        return $this->template->rander("departments/index");
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
            
            $view_data['department_info'] = $department_info;
            
            // Get enhanced department details with user info
            $detailed_info = $this->Departments_model->get_details(array("id" => $id))->getRow();
            if ($detailed_info) {
                $view_data['department_info'] = $detailed_info;
            }
            
            // Get department statistics
            $view_data['statistics'] = $this->Departments_model->get_statistics($id);
            
            // Get recent activities (if available)
            if (method_exists($this->Departments_model, 'get_recent_activities')) {
                $view_data['recent_activities'] = $this->Departments_model->get_recent_activities($id, 10);
            }
            
            return $this->template->rander("departments/view", $view_data);
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
            "user_id" => "required|numeric",
            "department_id" => "required|numeric",
            "is_primary" => "numeric"
        ));

        $user_id = $this->request->getPost('user_id');
        $department_id = $this->request->getPost('department_id');
        $is_primary = $this->request->getPost('is_primary') ? true : false;

        // Validate that the user is a staff member (not a client)
        $Users_model = model('App\Models\Users_model');
        $user = $Users_model->get_one($user_id);
        
        if (!$user || $user->user_type !== 'staff') {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('only_team_members_can_be_added_to_departments')
            ));
            return;
        }

        // Load the User_departments_model
        $User_departments_model = model('App\Models\User_departments_model');
        
        $result = $User_departments_model->add_user_to_department($user_id, $department_id, $is_primary);
        
        if ($result) {
            echo json_encode(array(
                "success" => true,
                "message" => app_lang('user_added_to_department_successfully')
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('error_occurred')
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
        $member_link = get_team_member_profile_link($data->user_id, $data->first_name . " " . $data->last_name);
        
        $primary_badge = "";
        if ($data->is_primary) {
            $primary_badge = "<span class='badge bg-success'><i class='ti ti-star'></i> " . app_lang("primary") . "</span>";
        } else {
            $primary_badge = "<span class='text-muted'>-</span>";
        }

        $actions = "";
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_departments")) {
            // Build Set as Primary action
            $primaryAction = '';
            if (!$data->is_primary) {
                $primaryAction = '<li><a class="dropdown-item set-as-primary" href="#" data-user-id="' . $data->user_id . '" data-department-id="' . $data->department_id . '" data-user-name="' . htmlspecialchars($data->first_name . " " . $data->last_name, ENT_QUOTES) . '" title="' . app_lang('set_as_primary') . '"><i data-feather="star" class="icon-16"></i></a></li>';
            }
            
            $actions = '<div class="dropdown">
                <button class="btn btn-outline-light btn-sm dropdown-toggle caret-option" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i data-feather="more-vertical" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>' . modal_anchor(get_uri("team_members/view/" . $data->user_id), '<i data-feather="eye" class="icon-16"></i>', array("class" => "dropdown-item", "title" => app_lang('view_member'))) . '</li>
                    ' . $primaryAction . '
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger remove-user-from-department" href="#" data-user-id="' . $data->user_id . '" data-department-id="' . $data->department_id . '" data-user-name="' . htmlspecialchars($data->first_name . " " . $data->last_name, ENT_QUOTES) . '" title="' . app_lang('remove') . '"><i data-feather="user-x" class="icon-16"></i></a></li>
                </ul>
            </div>';
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
