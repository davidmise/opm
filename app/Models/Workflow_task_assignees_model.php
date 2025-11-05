<?php

namespace App\Models;

/**
 * Workflow_task_assignees_model
 * 
 * Manages multiple assignees for parallel task assignment
 * Supports Task 4 requirement (Pendo AND Edson)
 */
class Workflow_task_assignees_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'workflow_task_assignees';
        parent::__construct($this->table);
    }

    /**
     * Get assignee details with related data
     */
    function get_details($options = array()) {
        $assignees_table = $this->db->prefixTable('workflow_task_assignees');
        $tasks_table = $this->db->prefixTable('workflow_tasks');
        $users_table = $this->db->prefixTable('users');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $assignees_table.id=$id";
        }
        
        $task_id = $this->_get_clean_value($options, "task_id");
        if ($task_id) {
            $where .= " AND $assignees_table.task_id=$task_id";
        }
        
        $user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $assignees_table.user_id=$user_id";
        }
        
        $status = $this->_get_clean_value($options, "assignee_status");
        if ($status) {
            $where .= " AND $assignees_table.assignee_status='$status'";
        }
        
        $sql = "SELECT $assignees_table.*,
                $tasks_table.task_name,
                $tasks_table.task_status,
                $tasks_table.due_date as task_due_date,
                $shipments_table.shipment_number,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) as user_name,
                $users_table.email as user_email
                FROM $assignees_table
                LEFT JOIN $tasks_table ON $tasks_table.id = $assignees_table.task_id
                LEFT JOIN $users_table ON $users_table.id = $assignees_table.user_id
                LEFT JOIN $shipments_table ON $shipments_table.id = $tasks_table.shipment_id
                WHERE $assignees_table.deleted=0 $where
                ORDER BY $assignees_table.assigned_at DESC";
        
        return $this->db->query($sql);
    }

    /**
     * Get user's assigned tasks
     */
    function get_user_assignments($user_id, $status = null) {
        $options = array('user_id' => $user_id);
        
        if ($status) {
            $options['assignee_status'] = $status;
        }
        
        return $this->get_details($options);
    }

    /**
     * Get task assignees
     */
    function get_task_assignees($task_id) {
        return $this->get_details(array('task_id' => $task_id));
    }

    /**
     * Update assignee status
     */
    function update_status($assignee_id, $status, $notes = '') {
        $data = array('assignee_status' => $status);
        
        if ($status === 'completed') {
            $data['completed_at'] = get_current_utc_time();
        }
        
        if ($notes) {
            $data['notes'] = $notes;
        }
        
        return $this->ci_save($data, $assignee_id);
    }

    /**
     * Check if user is assigned to task
     */
    function is_user_assigned($task_id, $user_id) {
        $assignees_table = $this->db->prefixTable('workflow_task_assignees');
        
        $sql = "SELECT COUNT(*) as count 
                FROM $assignees_table 
                WHERE task_id = $task_id 
                AND user_id = $user_id 
                AND deleted = 0";
        
        $result = $this->db->query($sql)->getRow();
        return ($result->count > 0);
    }

    /**
     * Get completion statistics for task
     */
    function get_task_completion_stats($task_id) {
        $assignees_table = $this->db->prefixTable('workflow_task_assignees');
        
        $sql = "SELECT 
                COUNT(*) as total_assignees,
                SUM(CASE WHEN assignee_status='completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN assignee_status='in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN assignee_status='pending' THEN 1 ELSE 0 END) as pending_count,
                ROUND((SUM(CASE WHEN assignee_status='completed' THEN 1 ELSE 0 END) * 100.0) / COUNT(*), 2) as completion_percentage
                FROM $assignees_table
                WHERE task_id = $task_id AND deleted = 0";
        
        return $this->db->query($sql)->getRow();
    }
}
