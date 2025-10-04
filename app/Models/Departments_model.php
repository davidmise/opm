<?php

namespace App\Models;

/**
 * Departments_model
 * 
 * Manages department data for the organization
 * Departments are used to organize team members and assign projects
 * 
 * @package App\Models
 * @author O.P.M Development Team
 * @version 1.0.0
 */
class Departments_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'departments';
        parent::__construct($this->table);
    }

    /**
     * Get department details with enhanced information
     * 
     * @param array $options Options for filtering and sorting
     * @return object Query result
     */
    function get_details($options = array()) {
        $departments_table = $this->db->prefixTable('departments');
        $users_table = $this->db->prefixTable('users');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        
        if ($id) {
            $where .= " AND $departments_table.id=$id";
        }

        // Order by
        $order_by = get_array_value($options, "order_by");
        $order = " ORDER BY $departments_table.title ASC";
        
        if ($order_by) {
            $order_dir = get_array_value($options, "order_dir");
            if (!$order_dir) {
                $order_dir = "ASC";
            }
            $order = " ORDER BY $departments_table.$order_by $order_dir";
        }

        // Build query
        $sql = "SELECT $departments_table.*, 
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user,
                (SELECT COUNT(*) FROM " . $this->db->prefixTable('team_member_job_info') . " 
                 WHERE department_id = $departments_table.id AND deleted = 0) AS total_members,
                (SELECT COUNT(*) FROM " . $this->db->prefixTable('projects') . " 
                 WHERE department_id = $departments_table.id AND deleted = 0) AS total_projects
                FROM $departments_table
                LEFT JOIN $users_table ON $users_table.id = $departments_table.created_by
                WHERE $departments_table.deleted=0 $where
                $order";
        
        return $this->db->query($sql);
    }

    /**
     * Get departments for Select2 dropdown (simple key-value format)
     * Use this for form_dropdown() helper
     * 
     * @param bool $show_header Whether to show "Select Department" option
     * @return array Simple key-value dropdown array
     */
    function get_departments_dropdown($show_header = true) {
        $departments_table = $this->db->prefixTable('departments');
        
        // Order by ID first (IT is ID 1), then by title
        $sql = "SELECT $departments_table.id, $departments_table.title
                FROM $departments_table
                WHERE $departments_table.deleted=0
                ORDER BY $departments_table.id ASC";
        
        $result = $this->db->query($sql)->getResult();
        
        $dropdown = array();
        if ($show_header) {
            $dropdown[""] = "- " . app_lang("department") . " -";
        }
        
        foreach ($result as $department) {
            $dropdown[$department->id] = $department->title;
        }
        
        return $dropdown;
    }
    
    /**
     * Get departments for AJAX dropdown (JSON format)
     * Use this for appDropdown() jQuery plugin
     * 
     * @return string JSON encoded array
     */
    function get_departments_dropdown_json() {
        $departments_table = $this->db->prefixTable('departments');
        
        $sql = "SELECT $departments_table.id, $departments_table.title
                FROM $departments_table
                WHERE $departments_table.deleted=0
                ORDER BY $departments_table.title ASC";
        
        $result = $this->db->query($sql)->getResult();
        
        $dropdown = array();
        foreach ($result as $department) {
            $dropdown[$department->id] = $department->title;
        }
        
        return json_encode($dropdown);
    }

    /**
     * Get all departments as simple array
     * 
     * @return object Query result
     */
    function get_all_departments() {
        $departments_table = $this->db->prefixTable('departments');
        
        $sql = "SELECT * FROM $departments_table 
                WHERE deleted=0 
                ORDER BY title ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Check if department has any members or projects assigned
     * 
     * @param int $department_id Department ID
     * @return bool True if department has dependencies
     */
    function has_dependencies($department_id) {
        $team_member_job_info_table = $this->db->prefixTable('team_member_job_info');
        $projects_table = $this->db->prefixTable('projects');
        
        // Check for team members
        $sql = "SELECT COUNT(*) as count FROM $team_member_job_info_table 
                WHERE department_id=$department_id AND deleted=0";
        $member_count = $this->db->query($sql)->getRow()->count;
        
        // Check for projects
        $sql = "SELECT COUNT(*) as count FROM $projects_table 
                WHERE department_id=$department_id AND deleted=0";
        $project_count = $this->db->query($sql)->getRow()->count;
        
        return ($member_count > 0 || $project_count > 0);
    }

    /**
     * Get statistics for a department
     * 
     * @param int $department_id Department ID
     * @return object Statistics object
     */
    function get_statistics($department_id) {
        $team_member_job_info_table = $this->db->prefixTable('team_member_job_info');
        $projects_table = $this->db->prefixTable('projects');
        
        $sql = "SELECT 
                (SELECT COUNT(*) FROM $team_member_job_info_table 
                 WHERE department_id=$department_id AND deleted=0) as member_count,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$department_id AND deleted=0 AND status='open') as active_projects,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$department_id AND deleted=0 AND status='completed') as completed_projects,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$department_id AND deleted=0) as total_projects";
        
        return $this->db->query($sql)->getRow();
    }
}
