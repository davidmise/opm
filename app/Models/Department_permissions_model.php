<?php

namespace App\Models;

/**
 * Department_permissions_model
 * 
 * Manages RBAC permissions for the departments module
 * 
 * @package App\Models
 * @author O.P.M Development Team
 * @version 1.0.0
 */
class Department_permissions_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'department_permissions';
        parent::__construct($this->table);
    }

    /**
     * Get permissions for a specific role
     */
    function get_role_permissions($role) {
        $department_permissions_table = $this->db->prefixTable('department_permissions');
        
        $sql = "SELECT * FROM $department_permissions_table WHERE role = ? AND deleted = 0";
        return $this->db->query($sql, array($role));
    }

    /**
     * Save permissions for a role
     */
    function save_role_permissions($role, $permissions) {
        $department_permissions_table = $this->db->prefixTable('department_permissions');
        
        // First, delete existing permissions for this role
        $delete_sql = "UPDATE $department_permissions_table SET deleted = 1 WHERE role = ?";
        $this->db->query($delete_sql, array($role));
        
        // Insert new permissions
        foreach ($permissions as $permission => $value) {
            if ($value) {
                $data = array(
                    'role' => $role,
                    'permission' => $permission,
                    'value' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'deleted' => 0
                );
                
                $this->ci_save($data);
            }
        }
        
        return true;
    }

    /**
     * Check if a user has a specific department permission
     */
    function has_permission($user_id, $permission) {
        $users_table = $this->db->prefixTable('users');
        $department_permissions_table = $this->db->prefixTable('department_permissions');
        
        // Get user's role
        $user_sql = "SELECT user_type, is_admin FROM $users_table WHERE id = ? AND deleted = 0";
        $user_result = $this->db->query($user_sql, array($user_id));
        
        if ($user_result->getNumRows() === 0) {
            return false;
        }
        
        $user = $user_result->getRow();
        
        // Admin always has all permissions
        if ($user->is_admin == 1) {
            return true;
        }
        
        // Check role-based permission
        $role = $user->user_type;
        $permission_sql = "SELECT value FROM $department_permissions_table 
                          WHERE role = ? AND permission = ? AND deleted = 0";
        $permission_result = $this->db->query($permission_sql, array($role, $permission));
        
        if ($permission_result->getNumRows() > 0) {
            $perm_data = $permission_result->getRow();
            return $perm_data->value == 1;
        }
        
        return false;
    }

    /**
     * Get all available department permissions
     */
    function get_available_permissions() {
        return array(
            'can_view_all_departments' => app_lang('can_view_all_departments'),
            'can_create_departments' => app_lang('can_create_departments'),
            'can_edit_departments' => app_lang('can_edit_departments'),
            'can_delete_departments' => app_lang('can_delete_departments'),
            'can_manage_department_users' => app_lang('can_manage_department_users'),
            'can_view_department_reports' => app_lang('can_view_department_reports'),
            'can_export_department_data' => app_lang('can_export_department_data'),
            'can_manage_department_settings' => app_lang('can_manage_department_settings')
        );
    }

    /**
     * Set default permissions for roles
     */
    function set_default_permissions() {
        $default_permissions = array(
            'admin' => array(
                'can_view_all_departments' => 1,
                'can_create_departments' => 1,
                'can_edit_departments' => 1,
                'can_delete_departments' => 1,
                'can_manage_department_users' => 1,
                'can_view_department_reports' => 1,
                'can_export_department_data' => 1,
                'can_manage_department_settings' => 1
            ),
            'manager' => array(
                'can_view_all_departments' => 1,
                'can_create_departments' => 0,
                'can_edit_departments' => 1,
                'can_delete_departments' => 0,
                'can_manage_department_users' => 1,
                'can_view_department_reports' => 1,
                'can_export_department_data' => 1,
                'can_manage_department_settings' => 0
            ),
            'member' => array(
                'can_view_all_departments' => 0,
                'can_create_departments' => 0,
                'can_edit_departments' => 0,
                'can_delete_departments' => 0,
                'can_manage_department_users' => 0,
                'can_view_department_reports' => 0,
                'can_export_department_data' => 0,
                'can_manage_department_settings' => 0
            ),
            'client' => array(
                'can_view_all_departments' => 0,
                'can_create_departments' => 0,
                'can_edit_departments' => 0,
                'can_delete_departments' => 0,
                'can_manage_department_users' => 0,
                'can_view_department_reports' => 0,
                'can_export_department_data' => 0,
                'can_manage_department_settings' => 0
            )
        );

        foreach ($default_permissions as $role => $permissions) {
            $this->save_role_permissions($role, $permissions);
        }
        
        return true;
    }
}