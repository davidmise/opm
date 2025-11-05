<?php

namespace App\Models;

/**
 * Workflow_tasks_model
 * 
 * Manages workflow tasks with parallel assignment support
 * Supports single and multiple assignees per task (Task 4 requirement)
 */
class Workflow_tasks_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'workflow_tasks';
        parent::__construct($this->table);
    }

    /**
     * Get task details with related data and assignees
     */
    function get_details($options = array()) {
        $tasks_table = $this->db->prefixTable('workflow_tasks');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $phases_table = $this->db->prefixTable('workflow_phases');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $departments_table = $this->db->prefixTable('departments');
        $assignees_table = $this->db->prefixTable('workflow_task_assignees');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $tasks_table.id=$id";
        }
        
        $shipment_id = $this->_get_clean_value($options, "shipment_id");
        if ($shipment_id) {
            $where .= " AND $tasks_table.shipment_id=$shipment_id";
        }
        
        $phase_id = $this->_get_clean_value($options, "phase_id");
        if ($phase_id) {
            $where .= " AND $tasks_table.phase_id=$phase_id";
        }
        
        $status = $this->_get_clean_value($options, "task_status");
        if ($status) {
            $where .= " AND $tasks_table.task_status='$status'";
        }
        
        $assigned_to = $this->_get_clean_value($options, "assigned_to");
        if ($assigned_to) {
            // Check both primary assignee and parallel assignees
            $where .= " AND ($tasks_table.assigned_to=$assigned_to OR EXISTS (
                SELECT 1 FROM $assignees_table 
                WHERE $assignees_table.task_id = $tasks_table.id 
                AND $assignees_table.user_id = $assigned_to
                AND $assignees_table.deleted = 0
            ))";
        }
        
        $department_id = $this->_get_clean_value($options, "department_id");
        if ($department_id) {
            $where .= " AND $tasks_table.department_id=$department_id";
        }
        
        $sql = "SELECT $tasks_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name,
                $phases_table.name as phase_name,
                $phases_table.sequence as phase_sequence,
                $departments_table.title as department_name,
                CONCAT(assigned_user.first_name, ' ', assigned_user.last_name) as assigned_to_name,
                CONCAT(created_user.first_name, ' ', created_user.last_name) as created_by_name,
                (SELECT COUNT(*) FROM $assignees_table 
                 WHERE $assignees_table.task_id = $tasks_table.id 
                 AND $assignees_table.deleted = 0) as assignee_count
                FROM $tasks_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $tasks_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                LEFT JOIN $phases_table ON $phases_table.id = $tasks_table.phase_id
                LEFT JOIN $departments_table ON $departments_table.id = $tasks_table.department_id
                LEFT JOIN $users_table as assigned_user ON assigned_user.id = $tasks_table.assigned_to
                LEFT JOIN $users_table as created_user ON created_user.id = $tasks_table.created_by
                WHERE $tasks_table.deleted=0 $where
                ORDER BY $phases_table.sequence ASC, $tasks_table.task_order ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Create task with optional parallel assignees
     */
    function create_task($data, $additional_assignees = array()) {
        $data['task_status'] = isset($data['task_status']) ? $data['task_status'] : 'pending';
        $data['created_at'] = get_current_utc_time();
        
        // Determine assignment type
        if (!empty($additional_assignees)) {
            $data['assignment_type'] = 'parallel';
        } else {
            $data['assignment_type'] = isset($data['assignment_type']) ? $data['assignment_type'] : 'single';
        }
        
        $task_id = $this->ci_save($data);
        
        if ($task_id && !empty($additional_assignees)) {
            // Add parallel assignees
            $this->add_assignees($task_id, $additional_assignees);
        }
        
        if ($task_id) {
            // Send notifications
            $this->send_task_notifications($task_id, 'created');
            
            // Log activity
            log_notification("task_created", array(
                "task_id" => $task_id,
                "shipment_id" => $data['shipment_id']
            ));
        }
        
        return $task_id;
    }

    /**
     * Add additional assignees to task
     */
    function add_assignees($task_id, $user_ids) {
        $Assignees_model = model('App\Models\Workflow_task_assignees_model');
        
        foreach ($user_ids as $user_id) {
            $assignee_data = array(
                'task_id' => $task_id,
                'user_id' => $user_id,
                'assigned_at' => get_current_utc_time(),
                'assignee_status' => 'pending'
            );
            
            $Assignees_model->ci_save($assignee_data);
        }
        
        // Update task assignment type
        $this->ci_save(array('assignment_type' => 'parallel'), $task_id);
        
        // Send notifications to new assignees
        $this->send_task_notifications($task_id, 'assigned');
    }

    /**
     * Remove assignee from task
     */
    function remove_assignee($task_id, $user_id) {
        $Assignees_model = model('App\Models\Workflow_task_assignees_model');
        
        $assignees_table = $this->db->prefixTable('workflow_task_assignees');
        
        $sql = "UPDATE $assignees_table 
                SET deleted = 1 
                WHERE task_id = $task_id AND user_id = $user_id";
        
        return $this->db->query($sql);
    }

    /**
     * Get all assignees for task
     */
    function get_task_assignees($task_id) {
        $assignees_table = $this->db->prefixTable('workflow_task_assignees');
        $users_table = $this->db->prefixTable('users');
        
        $sql = "SELECT $assignees_table.*,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) as user_name,
                $users_table.email
                FROM $assignees_table
                LEFT JOIN $users_table ON $users_table.id = $assignees_table.user_id
                WHERE $assignees_table.task_id = $task_id 
                AND $assignees_table.deleted = 0
                ORDER BY $assignees_table.assigned_at ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Update assignee status
     */
    function update_assignee_status($task_id, $user_id, $status) {
        $Assignees_model = model('App\Models\Workflow_task_assignees_model');
        
        $assignees_table = $this->db->prefixTable('workflow_task_assignees');
        
        $data = array('assignee_status' => $status);
        
        if ($status === 'completed') {
            $data['completed_at'] = get_current_utc_time();
        }
        
        $sql = "UPDATE $assignees_table 
                SET assignee_status = '$status'" . 
                ($status === 'completed' ? ", completed_at = '" . get_current_utc_time() . "'" : "") .
                " WHERE task_id = $task_id AND user_id = $user_id";
        
        $result = $this->db->query($sql);
        
        // Check if all assignees completed
        if ($result && $status === 'completed') {
            $this->check_task_completion($task_id);
        }
        
        return $result;
    }

    /**
     * Check if all assignees completed task
     */
    private function check_task_completion($task_id) {
        $task = $this->get_one($task_id);
        
        if ($task->assignment_type === 'parallel') {
            $assignees_table = $this->db->prefixTable('workflow_task_assignees');
            
            $sql = "SELECT COUNT(*) as total,
                    SUM(CASE WHEN assignee_status='completed' THEN 1 ELSE 0 END) as completed
                    FROM $assignees_table
                    WHERE task_id = $task_id AND deleted = 0";
            
            $result = $this->db->query($sql)->getRow();
            
            if ($result->total > 0 && $result->total == $result->completed) {
                // All assignees completed - mark task as complete
                $this->complete_task($task_id);
            }
        }
    }

    /**
     * Update task status
     */
    function update_status($task_id, $status, $user_id = null) {
        $data = array('task_status' => $status);
        
        switch ($status) {
            case 'in_progress':
                $data['started_at'] = get_current_utc_time();
                break;
            
            case 'completed':
                $data['completed_at'] = get_current_utc_time();
                if ($user_id) {
                    $data['completed_by'] = $user_id;
                }
                break;
            
            case 'cancelled':
                $data['cancelled_at'] = get_current_utc_time();
                break;
        }
        
        $result = $this->ci_save($data, $task_id);
        
        if ($result) {
            // Send notifications
            $this->send_task_notifications($task_id, 'status_changed');
            
            // Log activity
            log_notification("task_status_changed", array(
                "task_id" => $task_id,
                "status" => $status
            ));
        }
        
        return $result;
    }

    /**
     * Complete task
     */
    function complete_task($task_id, $user_id = null) {
        return $this->update_status($task_id, 'completed', $user_id);
    }

    /**
     * Get task statistics for shipment
     */
    function get_shipment_statistics($shipment_id) {
        $tasks_table = $this->db->prefixTable('workflow_tasks');
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN task_status='pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN task_status='in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN task_status='completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN task_status='cancelled' THEN 1 ELSE 0 END) as cancelled,
                ROUND((SUM(CASE WHEN task_status='completed' THEN 1 ELSE 0 END) * 100.0) / COUNT(*), 2) as completion_percentage
                FROM $tasks_table
                WHERE shipment_id = $shipment_id AND deleted = 0";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Get task statistics for phase
     */
    function get_phase_statistics($shipment_id, $phase_id) {
        $tasks_table = $this->db->prefixTable('workflow_tasks');
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN task_status='completed' THEN 1 ELSE 0 END) as completed,
                ROUND((SUM(CASE WHEN task_status='completed' THEN 1 ELSE 0 END) * 100.0) / COUNT(*), 2) as completion_percentage
                FROM $tasks_table
                WHERE shipment_id = $shipment_id 
                AND phase_id = $phase_id 
                AND deleted = 0";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Check if phase tasks are complete
     */
    function is_phase_complete($shipment_id, $phase_id) {
        $stats = $this->get_phase_statistics($shipment_id, $phase_id);
        return ($stats->total > 0 && $stats->total == $stats->completed);
    }

    /**
     * Get my tasks
     */
    function get_my_tasks($user_id, $status = null) {
        $options = array('assigned_to' => $user_id);
        
        if ($status) {
            $options['task_status'] = $status;
        }
        
        return $this->get_details($options);
    }

    /**
     * Get overdue tasks
     */
    function get_overdue_tasks() {
        $tasks_table = $this->db->prefixTable('workflow_tasks');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $users_table = $this->db->prefixTable('users');
        
        $current_date = get_current_utc_time();
        
        $sql = "SELECT $tasks_table.*,
                $shipments_table.shipment_number,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) as assigned_to_name,
                DATEDIFF('$current_date', $tasks_table.due_date) as days_overdue
                FROM $tasks_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $tasks_table.shipment_id
                LEFT JOIN $users_table ON $users_table.id = $tasks_table.assigned_to
                WHERE $tasks_table.due_date < '$current_date'
                AND $tasks_table.task_status IN ('pending', 'in_progress')
                AND $tasks_table.deleted = 0
                ORDER BY $tasks_table.due_date ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Send task notifications
     */
    private function send_task_notifications($task_id, $action) {
        $task = $this->get_one($task_id);
        
        if ($task) {
            // Send email/notification to assignees
            // TODO: Implement notification system
            log_notification("task_$action", array(
                "task_id" => $task_id,
                "assigned_to" => $task->assigned_to
            ));
            
            // Notify parallel assignees
            if ($task->assignment_type === 'parallel') {
                $assignees = $this->get_task_assignees($task_id);
                foreach ($assignees->getResult() as $assignee) {
                    log_notification("task_$action", array(
                        "task_id" => $task_id,
                        "assigned_to" => $assignee->user_id
                    ));
                }
            }
        }
    }

    /**
     * Clone tasks from template for new shipment
     */
    function clone_phase_tasks($phase_id, $shipment_id) {
        // TODO: Implement task template system
        // This would clone standard tasks for a phase to new shipment
    }
}
