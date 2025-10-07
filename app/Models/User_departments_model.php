<?php

namespace App\Models;

/**
 * User_departments_model
 * 
 * Manages user-department relationships (multi-department support)
 * Handles the many-to-many relationship between users and departments
 * 
 * @package App\Models
 * @author O.P.M Development Team
 * @version 1.0.0
 */
class User_departments_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'user_departments';
        parent::__construct($this->table);
    }

    /**
     * Get user's departments with details
     * 
     * @param int $user_id User ID
     * @param array $options Additional options
     * @return object Query result
     */
    function get_user_departments_with_details($user_id, $options = array()) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        $departments_table = $this->db->prefixTable('departments');
        
        $where = "";
        $primary_only = $this->_get_clean_value($options, "primary_only");
        if ($primary_only) {
            $where .= " AND $user_departments_table.is_primary=1";
        }
        
        $active_only = $this->_get_clean_value($options, "active_only");
        if ($active_only) {
            $where .= " AND $departments_table.is_active=1";
        }
        
        $sql = "SELECT $user_departments_table.*, 
                $departments_table.title as department_title,
                $departments_table.description as department_description,
                $departments_table.color as department_color,
                $departments_table.icon as department_icon
                FROM $user_departments_table
                INNER JOIN $departments_table ON $departments_table.id = $user_departments_table.department_id
                WHERE $user_departments_table.user_id=$user_id 
                AND $departments_table.deleted=0 $where
                ORDER BY $user_departments_table.is_primary DESC, $departments_table.title ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Get department users with details
     * 
     * @param int $department_id Department ID
     * @param array $options Additional options
     * @return object Query result
     */
    function get_department_users_with_details($department_id, $options = array()) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        $users_table = $this->db->prefixTable('users');
        $roles_table = $this->db->prefixTable('roles');
        
        $where = "";
        $primary_only = $this->_get_clean_value($options, "primary_only");
        if ($primary_only) {
            $where .= " AND $user_departments_table.is_primary=1";
        }
        
        $active_only = $this->_get_clean_value($options, "active_only");
        if ($active_only) {
            $where .= " AND $users_table.status='active'";
        }
        
        $sql = "SELECT $user_departments_table.*, 
                $users_table.first_name, $users_table.last_name, 
                $users_table.email, $users_table.image, $users_table.is_admin,
                $roles_table.title as role_title
                FROM $user_departments_table
                INNER JOIN $users_table ON $users_table.id = $user_departments_table.user_id
                LEFT JOIN $roles_table ON $roles_table.id = $users_table.role_id
                WHERE $user_departments_table.department_id=$department_id 
                AND $users_table.deleted=0 $where
                ORDER BY $user_departments_table.is_primary DESC, $users_table.first_name ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Set user's primary department
     * 
     * @param int $user_id User ID
     * @param int $department_id Department ID
     * @return bool Success status
     */
    function set_primary_department($user_id, $department_id) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        // Start transaction
        $this->db->transStart();
        
        // Remove primary flag from all user's departments
        $sql = "UPDATE $user_departments_table 
                SET is_primary=0 
                WHERE user_id=$user_id";
        $this->db->query($sql);
        
        // Set new primary department
        $sql = "UPDATE $user_departments_table 
                SET is_primary=1 
                WHERE user_id=$user_id AND department_id=$department_id";
        $this->db->query($sql);
        
        // Complete transaction
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Add user to department (with duplicate check)
     * 
     * @param int $user_id User ID
     * @param int $department_id Department ID
     * @param bool $is_primary Is this the primary department?
     * @return bool|int Success status or insert ID
     */
    function add_user_to_department($user_id, $department_id, $is_primary = false) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        // Check if relationship already exists
        $sql = "SELECT id FROM $user_departments_table 
                WHERE user_id=$user_id AND department_id=$department_id";
        $existing = $this->db->query($sql)->getRow();
        
        if ($existing) {
            // Update existing relationship
            if ($is_primary) {
                $this->set_primary_department($user_id, $department_id);
            }
            return $existing->id;
        }
        
        // If this is primary, remove primary flag from other departments
        if ($is_primary) {
            $sql = "UPDATE $user_departments_table 
                    SET is_primary=0 
                    WHERE user_id=$user_id";
            $this->db->query($sql);
        }
        
        // Insert new relationship
        $data = array(
            'user_id' => $user_id,
            'department_id' => $department_id,
            'is_primary' => $is_primary ? 1 : 0
        );
        
        return $this->ci_save($data);
    }

    /**
     * Remove user from department
     * 
     * @param int $user_id User ID
     * @param int $department_id Department ID
     * @return bool Success status
     */
    function remove_user_from_department($user_id, $department_id) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        $sql = "DELETE FROM $user_departments_table 
                WHERE user_id=$user_id AND department_id=$department_id";
        
        return $this->db->query($sql);
    }

    /**
     * Get user's primary department
     * 
     * @param int $user_id User ID
     * @return object|null Department data or null
     */
    function get_user_primary_department($user_id) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        $departments_table = $this->db->prefixTable('departments');
        
        $sql = "SELECT $departments_table.*
                FROM $user_departments_table
                INNER JOIN $departments_table ON $departments_table.id = $user_departments_table.department_id
                WHERE $user_departments_table.user_id=$user_id 
                AND $user_departments_table.is_primary=1
                AND $departments_table.deleted=0 
                AND $departments_table.is_active=1";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Get users without any department assignment
     * 
     * @return object Query result
     */
    function get_users_without_department() {
        $users_table = $this->db->prefixTable('users');
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        $sql = "SELECT $users_table.*
                FROM $users_table
                LEFT JOIN $user_departments_table ON $user_departments_table.user_id = $users_table.id
                WHERE $users_table.deleted=0 
                AND $users_table.user_type='staff'
                AND $user_departments_table.id IS NULL
                ORDER BY $users_table.first_name ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Sync user departments (replace all with new list)
     * 
     * @param int $user_id User ID
     * @param array $department_ids Array of department IDs
     * @param int $primary_department_id Primary department ID
     * @return bool Success status
     */
    function sync_user_departments($user_id, $department_ids, $primary_department_id = null) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        // Start transaction
        $this->db->transStart();
        
        // Remove all existing relationships
        $sql = "DELETE FROM $user_departments_table WHERE user_id=$user_id";
        $this->db->query($sql);
        
        // Add new relationships
        if (!empty($department_ids)) {
            foreach ($department_ids as $dept_id) {
                $is_primary = ($dept_id == $primary_department_id) ? 1 : 0;
                $data = array(
                    'user_id' => $user_id,
                    'department_id' => $dept_id,
                    'is_primary' => $is_primary
                );
                $this->db->table('user_departments')->insert($data);
            }
        }
        
        // Complete transaction
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Get department statistics for users
     * 
     * @return array Department stats
     */
    function get_department_user_statistics() {
        $user_departments_table = $this->db->prefixTable('user_departments');
        $departments_table = $this->db->prefixTable('departments');
        
        $sql = "SELECT $departments_table.id, $departments_table.title, $departments_table.color,
                COUNT($user_departments_table.user_id) as total_users,
                SUM($user_departments_table.is_primary) as primary_users
                FROM $departments_table
                LEFT JOIN $user_departments_table ON $user_departments_table.department_id = $departments_table.id
                WHERE $departments_table.deleted=0 AND $departments_table.is_active=1
                GROUP BY $departments_table.id, $departments_table.title, $departments_table.color
                ORDER BY total_users DESC, $departments_table.title ASC";
        
        return $this->db->query($sql)->getResult();
    }
}