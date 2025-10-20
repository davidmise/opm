<?php

namespace App\Models;

/**
 * Department Templates Model
 * 
 * Manages department templates for easy department creation
 * Templates define default settings, permissions, and configurations
 * 
 * @package App\Models
 * @author O.P.M Development Team
 * @version 1.0.0
 */
class Department_templates_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'department_templates';
        parent::__construct($this->table);
    }

    /**
     * Get template details with additional information
     * 
     * @param array $options Query options
     * @return object Query result
     */
    function get_details($options = array()) {
        $department_templates_table = $this->db->prefixTable('department_templates');
        $users_table = $this->db->prefixTable('users');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $department_templates_table.id=$id";
        }

        $sql = "SELECT $department_templates_table.*, 
                       CONCAT($users_table.first_name, ' ', $users_table.last_name) as created_by_user
                FROM $department_templates_table
                LEFT JOIN $users_table ON $users_table.id = $department_templates_table.created_by
                WHERE $department_templates_table.deleted=0 $where";
        
        return $this->db->query($sql);
    }

    /**
     * Get template by name
     * 
     * @param string $name Template name
     * @return object Template data
     */
    function get_template_by_name($name) {
        return $this->get_one_where(array('template_name' => $name, 'deleted' => 0));
    }

    /**
     * Get all active templates
     * 
     * @return array List of templates
     */
    function get_active_templates() {
        return $this->get_all_where(array('deleted' => 0))->getResult();
    }

    /**
     * Apply template to department
     * 
     * @param int $template_id Template ID
     * @param int $department_id Department ID
     * @return bool Success status
     */
    function apply_template_to_department($template_id, $department_id) {
        $template = $this->get_one($template_id);
        if (!$template) {
            return false;
        }

        $Departments_model = model('App\Models\Departments_model');
        
        // Update department with template settings
        $department_data = array(
            'color' => $template->template_color,
            'description' => $template->template_description
        );

        $result = $Departments_model->ci_save($department_data, $department_id);
        
        if ($result && $template->template_permissions) {
            // Apply template permissions (this would need to be implemented based on your permission system)
            $this->_apply_template_permissions($template->template_permissions, $department_id);
        }

        return $result;
    }

    /**
     * Apply template permissions to department
     * 
     * @param string $permissions JSON encoded permissions
     * @param int $department_id Department ID
     * @return bool Success status
     */
    private function _apply_template_permissions($permissions, $department_id) {
        $permission_list = json_decode($permissions, true);
        if (!$permission_list) {
            return false;
        }

        // This would integrate with your permission system
        // For now, just return true as a placeholder
        return true;
    }
}