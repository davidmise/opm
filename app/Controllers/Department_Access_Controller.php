<?php

namespace App\Controllers;

/**
 * Department_Access_Controller
 * 
 * Base controller for department-based access control
 * Filters data based on user's department assignments and permissions
 * 
 * @package App\Controllers
 * @author O.P.M Development Team
 * @version 1.0.0
 */
class Department_Access_Controller extends Security_Controller {

    public $User_departments_model;
    public $Departments_model;
    protected $user_accessible_departments = array();
    protected $user_primary_department = null;

    function __construct() {
        parent::__construct();
        
        // Load department models
        $this->User_departments_model = model('App\Models\User_departments_model');
        $this->Departments_model = model('App\Models\Departments_model');
        
        // Check if departments module is enabled
        $this->check_module_availability("module_departments");
        
        // Initialize user department access
        $this->_init_department_access();
    }

    /**
     * Initialize department access for current user
     */
    private function _init_department_access() {
        if ($this->login_user->user_type == "staff") {
            $this->user_accessible_departments = $this->Departments_model->get_user_accessible_departments($this->login_user->id);
            $this->user_primary_department = $this->User_departments_model->get_user_primary_department($this->login_user->id);
        }
    }

    /**
     * Check if user has access to specific department
     * 
     * @param int $department_id Department ID
     * @param string $permission_level view|manage|admin
     * @return bool Access status
     */
    protected function has_department_access($department_id, $permission_level = 'view') {
        // Admins have access to all departments
        if ($this->login_user->is_admin) {
            return true;
        }

        // Check if user has access to this department
        if (!in_array($department_id, $this->user_accessible_departments)) {
            return false;
        }

        // Additional permission checks based on level
        if ($permission_level == 'manage') {
            return $this->_has_department_manage_permission($department_id);
        } elseif ($permission_level == 'admin') {
            return $this->login_user->is_admin;
        }

        return true;
    }

    /**
     * Check if user can manage specific department
     * 
     * @param int $department_id Department ID
     * @return bool Permission status
     */
    private function _has_department_manage_permission($department_id) {
        // Check if user is department head
        $department_info = $this->Departments_model->get_one($department_id);
        if ($department_info && $department_info->head_user_id == $this->login_user->id) {
            return true;
        }

        // For now, allow users to manage departments they are assigned to
        // This can be enhanced later with proper permission system
        return true;
    }

    /**
     * Get department filter for SQL queries
     * 
     * @param string $table_alias Table alias for department_id column
     * @return string SQL WHERE clause
     */
    protected function get_department_filter_sql($table_alias = '') {
        // Admins see all departments
        if ($this->login_user->is_admin) {
            return "";
        }

        // No department access for this user
        if (empty($this->user_accessible_departments)) {
            return " AND 1=0"; // Block all results
        }

        $prefix = $table_alias ? $table_alias . '.' : '';
        $department_ids = implode(',', $this->user_accessible_departments);
        
        return " AND ({$prefix}department_id IN ($department_ids) OR {$prefix}department_id IS NULL)";
    }

    /**
     * Filter array of items by department access
     * 
     * @param array $items Array of items with department_id property
     * @return array Filtered items
     */
    protected function filter_items_by_department($items) {
        if ($this->login_user->is_admin) {
            return $items;
        }

        return array_filter($items, function($item) {
            return !isset($item->department_id) || 
                   $item->department_id === null || 
                   in_array($item->department_id, $this->user_accessible_departments);
        });
    }

    /**
     * Get department dropdown for forms (filtered by user access)
     * 
     * @param bool $show_header Show "Select Department" option
     * @param bool $include_all Include "All Departments" option for admins
     * @return array Dropdown options
     */
    protected function get_accessible_departments_dropdown($show_header = true, $include_all = false) {
        $dropdown = array();
        
        if ($show_header) {
            $dropdown[""] = "- " . app_lang("department") . " -";
        }

        if ($include_all && $this->login_user->is_admin) {
            $dropdown["all"] = app_lang("all_departments");
        }

        if ($this->login_user->is_admin) {
            // Admin sees all departments
            $departments = $this->Departments_model->get_all_departments()->getResult();
        } else {
            // User sees only accessible departments
            $accessible_ids = $this->user_accessible_departments;
            if (!empty($accessible_ids)) {
                $departments = $this->Departments_model->get_details(array(
                    "where_in" => array("id" => $accessible_ids)
                ))->getResult();
            } else {
                $departments = array();
            }
        }

        foreach ($departments as $department) {
            $dropdown[$department->id] = $department->title;
        }

        return $dropdown;
    }

    /**
     * Ensure user has department access or redirect
     * 
     * @param int $department_id Department ID
     * @param string $permission_level view|manage|admin
     * @param string $redirect_url URL to redirect if no access
     */
    protected function ensure_department_access($department_id, $permission_level = 'view', $redirect_url = 'forbidden') {
        if (!$this->has_department_access($department_id, $permission_level)) {
            if ($redirect_url == 'forbidden') {
                app_redirect("forbidden");
            } else {
                redirect($redirect_url);
            }
        }
    }

    /**
     * Get user's primary department ID
     * 
     * @return int|null Primary department ID or null
     */
    protected function get_user_primary_department_id() {
        return $this->user_primary_department ? $this->user_primary_department->id : null;
    }

    /**
     * Check if departments module is enabled
     */
    protected function check_departments_module() {
        $this->check_module_availability("module_departments");
    }

    /**
     * Add department filter to model options
     * 
     * @param array $options Existing model options
     * @param string $department_field Field name for department filter
     * @return array Modified options
     */
    protected function add_department_filter_to_options($options, $department_field = 'department_id') {
        if ($this->login_user->is_admin) {
            return $options; // No filter for admins
        }

        if (!empty($this->user_accessible_departments)) {
            $options["department_ids"] = $this->user_accessible_departments;
        } else {
            // User has no department access, return empty results
            $options["where_in"] = array("id" => array(0)); // Force empty result
        }

        return $options;
    }

    /**
     * Log department access attempt for security auditing
     * 
     * @param int $department_id Department ID
     * @param string $action Action attempted
     * @param bool $success Whether access was granted
     */
    protected function log_department_access($department_id, $action, $success) {
        // This could be implemented to log to a security audit table
        if (!$success) {
            log_message('warning', "Department access denied: User {$this->login_user->id} attempted '$action' on department $department_id");
        }
    }
}