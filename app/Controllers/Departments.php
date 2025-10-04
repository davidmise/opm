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
        
        // Department title (clean, no edit icon)
        $title = modal_anchor(get_uri("departments/modal_form"), "<strong>$data->title</strong>", array(
            "class" => "text-dark",
            "title" => app_lang('edit_department'),
            "data-post-id" => $data->id
        ));

        // Statistics with improved badges
        $members_badge = "<span class='badge bg-info' title='" . app_lang('team_members') . "'><i data-feather='users' class='icon-14'></i> $data->total_members " . app_lang('members') . "</span>";
        $projects_badge = "<span class='badge bg-success ms-2' title='" . app_lang('projects') . "'><i data-feather='command' class='icon-14'></i> $data->total_projects " . app_lang('projects') . "</span>";
        
        $stats = $members_badge . " " . $projects_badge;

        // Action buttons (Edit + Delete)
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
            $view_data['department_info'] = $this->Departments_model->get_one($id);
            
            if (!$view_data['department_info']->id) {
                show_404();
            }
            
            $view_data['statistics'] = $this->Departments_model->get_statistics($id);
            
            return $this->template->rander("departments/view", $view_data);
        } else {
            show_404();
        }
    }
}
