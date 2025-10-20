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
        
        // Filter by active status
        $active_only = $this->_get_clean_value($options, "active_only");
        if ($active_only) {
            $where .= " AND $departments_table.is_active=1";
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

        // Build query with enhanced fields
        $sql = "SELECT $departments_table.*, 
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user,
                CONCAT(head_users.first_name, ' ', head_users.last_name) AS head_user_name,
                head_users.image AS head_user_image,
                (SELECT COUNT(*) FROM " . $this->db->prefixTable('user_departments') . " 
                 WHERE department_id = $departments_table.id) AS total_members,
                (SELECT COUNT(*) FROM " . $this->db->prefixTable('projects') . " 
                 WHERE department_id = $departments_table.id AND deleted = 0) AS total_projects,
                (SELECT COUNT(DISTINCT t.id) FROM " . $this->db->prefixTable('tasks') . " t
                 LEFT JOIN " . $this->db->prefixTable('projects') . " p ON t.project_id = p.id
                 WHERE (t.department_id = $departments_table.id OR p.department_id = $departments_table.id) 
                 AND t.deleted = 0) AS total_tasks
                FROM $departments_table
                LEFT JOIN $users_table ON $users_table.id = $departments_table.created_by
                LEFT JOIN " . $this->db->prefixTable('users') . " head_users ON head_users.id = $departments_table.head_user_id
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
        // die($departments_table);
        
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
        $user_departments_table = $this->db->prefixTable('user_departments');
        $projects_table = $this->db->prefixTable('projects');
        
        // Check for team members (new multi-department table)
        $sql = "SELECT COUNT(*) as count FROM $user_departments_table 
                WHERE department_id=$department_id";
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
        $user_departments_table = $this->db->prefixTable('user_departments');
        $projects_table = $this->db->prefixTable('projects');
        $tasks_table = $this->db->prefixTable('tasks');
        
        $sql = "SELECT 
                (SELECT COUNT(*) FROM $user_departments_table 
                 WHERE department_id=$department_id) as member_count,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$department_id AND deleted=0 AND status='open') as active_projects,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$department_id AND deleted=0 AND status='completed') as completed_projects,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$department_id AND deleted=0) as total_projects,
                (SELECT COUNT(*) FROM $tasks_table 
                 LEFT JOIN $projects_table ON $tasks_table.project_id=$projects_table.id
                 WHERE ($tasks_table.department_id=$department_id OR $projects_table.department_id=$department_id) 
                 AND $tasks_table.deleted=0 AND $tasks_table.status_id != 3) as active_tasks,
                (SELECT COUNT(*) FROM $tasks_table 
                 LEFT JOIN $projects_table ON $tasks_table.project_id=$projects_table.id
                 WHERE ($tasks_table.department_id=$department_id OR $projects_table.department_id=$department_id) 
                 AND $tasks_table.deleted=0 AND $tasks_table.status_id = 3) as completed_tasks";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Get global dashboard statistics across all departments
     * 
     * @return object Dashboard statistics
     */
    function get_dashboard_statistics() {
        $departments_table = $this->db->prefixTable('departments');
        $user_departments_table = $this->db->prefixTable('user_departments');
        $projects_table = $this->db->prefixTable('projects');
        $tasks_table = $this->db->prefixTable('tasks');
        
        $sql = "SELECT 
                (SELECT COUNT(*) FROM $departments_table WHERE deleted=0) as total_departments,
                (SELECT COUNT(*) FROM $departments_table WHERE deleted=0 AND is_active=1) as active_departments,
                (SELECT COUNT(DISTINCT user_id) FROM $user_departments_table 
                 INNER JOIN $departments_table ON $user_departments_table.department_id=$departments_table.id 
                 WHERE $departments_table.deleted=0) as total_members,
                (SELECT COUNT(*) FROM $projects_table WHERE department_id IS NOT NULL AND deleted=0) as total_projects,
                (SELECT COUNT(*) FROM $projects_table WHERE department_id IS NOT NULL AND deleted=0 AND status='open') as active_projects,
                (SELECT COUNT(DISTINCT t.id) FROM $tasks_table t
                 LEFT JOIN $projects_table p ON t.project_id=p.id
                 WHERE (t.department_id IS NOT NULL OR p.department_id IS NOT NULL) AND t.deleted=0) as total_tasks,
                (SELECT COUNT(*) FROM $departments_table WHERE deleted=0 AND head_user_id IS NULL) as departments_without_head,
                (SELECT COUNT(*) FROM $departments_table d WHERE deleted=0 
                 AND (SELECT COUNT(*) FROM $user_departments_table WHERE department_id=d.id) = 0) as departments_without_members";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Get all departments with enhanced statistics for dashboard
     * 
     * @return array Array of department objects with statistics
     */
    function get_all_departments_with_stats() {
        $departments_table = $this->db->prefixTable('departments');
        $users_table = $this->db->prefixTable('users');
        $user_departments_table = $this->db->prefixTable('user_departments');
        $projects_table = $this->db->prefixTable('projects');
        $tasks_table = $this->db->prefixTable('tasks');
        
        $sql = "SELECT $departments_table.*,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user,
                CONCAT(head_users.first_name, ' ', head_users.last_name) AS head_user_name,
                head_users.image AS head_user_image,
                (SELECT COUNT(DISTINCT user_id) FROM $user_departments_table 
                 WHERE department_id=$departments_table.id) AS total_members,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$departments_table.id AND deleted=0) AS total_projects,
                (SELECT COUNT(*) FROM $projects_table 
                 WHERE department_id=$departments_table.id AND deleted=0 AND status='open') AS active_projects,
                (SELECT COUNT(DISTINCT t.id) FROM $tasks_table t
                 LEFT JOIN $projects_table p ON t.project_id=p.id
                 WHERE (t.department_id=$departments_table.id OR p.department_id=$departments_table.id) 
                 AND t.deleted=0) AS total_tasks,
                (SELECT COUNT(DISTINCT t.id) FROM $tasks_table t
                 LEFT JOIN $projects_table p ON t.project_id=p.id
                 WHERE (t.department_id=$departments_table.id OR p.department_id=$departments_table.id) 
                 AND t.deleted=0 AND t.status_id=3) AS completed_tasks
                FROM $departments_table
                LEFT JOIN $users_table ON $users_table.id=$departments_table.created_by
                LEFT JOIN $users_table head_users ON head_users.id=$departments_table.head_user_id
                WHERE $departments_table.deleted=0
                ORDER BY $departments_table.title ASC";
        
        return $this->db->query($sql)->getResult();
    }

    /**
     * Get users assigned to a department
     * 
     * @param int $department_id Department ID
     * @param array $options Additional options
     * @return object Query result
     */
    function get_department_users($department_id, $options = array()) {
        $users_table = $this->db->prefixTable('users');
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        $where = "";
        $primary_only = $this->_get_clean_value($options, "primary_only");
        if ($primary_only) {
            $where .= " AND $user_departments_table.is_primary=1";
        }
        
        // Only return staff users as department members (exclude client contacts)
        $sql = "SELECT $users_table.*, $user_departments_table.is_primary
                FROM $users_table
                INNER JOIN $user_departments_table ON $user_departments_table.user_id = $users_table.id
                WHERE $user_departments_table.department_id=$department_id 
                AND $users_table.deleted=0 AND $users_table.user_type='staff' $where
                ORDER BY $user_departments_table.is_primary DESC, $users_table.first_name ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Get user's departments
     * 
     * @param int $user_id User ID
     * @param bool $primary_only Get only primary department
     * @return object Query result
     */
    function get_user_departments($user_id, $primary_only = false) {
        $departments_table = $this->db->prefixTable('departments');
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        $where = "";
        if ($primary_only) {
            $where = " AND $user_departments_table.is_primary=1";
        }
        
        $sql = "SELECT $departments_table.*, $user_departments_table.is_primary
                FROM $departments_table
                INNER JOIN $user_departments_table ON $user_departments_table.department_id = $departments_table.id
                WHERE $user_departments_table.user_id=$user_id 
                AND $departments_table.deleted=0 
                AND $departments_table.is_active=1 $where
                ORDER BY $user_departments_table.is_primary DESC, $departments_table.title ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Assign user to department
     * 
     * @param int $user_id User ID
     * @param int $department_id Department ID
     * @param bool $is_primary Is this the primary department?
     * @return bool Success status
     */
    function assign_user_to_department($user_id, $department_id, $is_primary = false) {
        $user_departments_table = $this->db->prefixTable('user_departments');
        
        // If this is primary, unset other primary assignments
        if ($is_primary) {
            $sql = "UPDATE $user_departments_table 
                    SET is_primary=0 
                    WHERE user_id=$user_id";
            $this->db->query($sql);
        }
        
        $data = array(
            'user_id' => $user_id,
            'department_id' => $department_id,
            'is_primary' => $is_primary ? 1 : 0
        );
        
        return $this->db->table('user_departments')->insert($data);
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
     * Get departments accessible by user (based on permissions)
     * 
     * @param int $user_id User ID
     * @param string $permission_type view|manage|admin
     * @return array Array of department IDs
     */
    function get_user_accessible_departments($user_id, $permission_type = 'view') {
        $departments_table = $this->db->prefixTable('departments');
        $user_departments_table = $this->db->prefixTable('user_departments');
        $department_permissions_table = $this->db->prefixTable('department_permissions');
        $users_table = $this->db->prefixTable('users');
        
        // Check if user is admin (has access to all departments)
        $sql = "SELECT is_admin FROM $users_table WHERE id=$user_id";
        $user = $this->db->query($sql)->getRow();
        
        if ($user && $user->is_admin) {
            // Admin has access to all departments
            $sql = "SELECT id FROM $departments_table 
                    WHERE deleted=0 AND is_active=1 
                    ORDER BY title ASC";
            $result = $this->db->query($sql)->getResult();
            return array_column($result, 'id');
        }
        
        // For non-admin users, check their department assignments and permissions
        $sql = "SELECT DISTINCT $departments_table.id
                FROM $departments_table
                LEFT JOIN $user_departments_table ON $user_departments_table.department_id = $departments_table.id
                LEFT JOIN $department_permissions_table ON $department_permissions_table.department_id = $departments_table.id
                WHERE $departments_table.deleted=0 AND $departments_table.is_active=1
                AND (
                    ($user_departments_table.user_id=$user_id) OR
                    ($department_permissions_table.user_id=$user_id AND $department_permissions_table.permission_type='$permission_type')
                )
                ORDER BY $departments_table.title ASC";
        
        $result = $this->db->query($sql)->getResult();
        return array_column($result, 'id');
    }

    /**
     * Get department dashboard data
     * 
     * @param int $department_id Department ID
     * @return array Dashboard data
     */
    function get_department_dashboard_data($department_id) {
        $stats = $this->get_statistics($department_id);
        $projects_table = $this->db->prefixTable('projects');
        $tasks_table = $this->db->prefixTable('tasks');
        $timesheets_table = $this->db->prefixTable('project_time');
        
        // Get recent projects
        $sql = "SELECT id, title, start_date, deadline, status
                FROM $projects_table 
                WHERE department_id=$department_id AND deleted=0 
                ORDER BY created_date DESC 
                LIMIT 5";
        $recent_projects = $this->db->query($sql)->getResult();
        
        // Get overdue tasks
        $sql = "SELECT COUNT(*) as count 
                FROM $tasks_table 
                WHERE department_id=$department_id 
                AND deleted=0 
                AND status_id != 3 
                AND deadline < CURDATE()";
        $overdue_tasks = $this->db->query($sql)->getRow()->count;
        
        // Get total hours logged this month
        $sql = "SELECT SUM(hours) as total_hours
                FROM $timesheets_table 
                INNER JOIN $tasks_table ON $tasks_table.id = $timesheets_table.task_id
                WHERE $tasks_table.department_id=$department_id 
                AND $timesheets_table.deleted=0
                AND MONTH($timesheets_table.start_time) = MONTH(CURDATE())
                AND YEAR($timesheets_table.start_time) = YEAR(CURDATE())";
        $monthly_hours = $this->db->query($sql)->getRow()->total_hours ?: 0;
        
        return array(
            'statistics' => $stats,
            'recent_projects' => $recent_projects,
            'overdue_tasks' => $overdue_tasks,
            'monthly_hours' => round($monthly_hours, 2)
        );
    }
}
