<?php

namespace App\Models;

/**
 * Workflow_escalations_model
 * 
 * Manages escalation workflow for issues and tasks
 * Handles multi-level escalation chains (User → Supervisor → GM → Management)
 */
class Workflow_escalations_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'workflow_escalations';
        parent::__construct($this->table);
    }

    /**
     * Get escalation details with related data
     */
    function get_details($options = array()) {
        $escalations_table = $this->db->prefixTable('workflow_escalations');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $tasks_table = $this->db->prefixTable('workflow_tasks');
        $users_table = $this->db->prefixTable('users');
        $clients_table = $this->db->prefixTable('clients');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $escalations_table.id=$id";
        }
        
        $shipment_id = $this->_get_clean_value($options, "shipment_id");
        if ($shipment_id) {
            $where .= " AND $escalations_table.shipment_id=$shipment_id";
        }
        
        $escalated_to = $this->_get_clean_value($options, "escalated_to");
        if ($escalated_to) {
            $where .= " AND $escalations_table.escalated_to=$escalated_to";
        }
        
        $status = $this->_get_clean_value($options, "escalation_status");
        if ($status) {
            $where .= " AND $escalations_table.escalation_status='$status'";
        }
        
        $sql = "SELECT $escalations_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name,
                $tasks_table.task_name,
                CONCAT(escalated_by_user.first_name, ' ', escalated_by_user.last_name) as escalated_by_name,
                CONCAT(escalated_from_user.first_name, ' ', escalated_from_user.last_name) as escalated_from_name,
                CONCAT(escalated_to_user.first_name, ' ', escalated_to_user.last_name) as escalated_to_name
                FROM $escalations_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $escalations_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                LEFT JOIN $tasks_table ON $tasks_table.id = $escalations_table.task_id
                LEFT JOIN $users_table as escalated_by_user ON escalated_by_user.id = $escalations_table.escalated_by
                LEFT JOIN $users_table as escalated_from_user ON escalated_from_user.id = $escalations_table.escalated_from
                LEFT JOIN $users_table as escalated_to_user ON escalated_to_user.id = $escalations_table.escalated_to
                WHERE $escalations_table.deleted=0 $where
                ORDER BY $escalations_table.escalated_at DESC";
        
        return $this->db->query($sql);
    }

    /**
     * Create new escalation
     */
    function create_escalation($data) {
        $data['escalated_at'] = get_current_utc_time();
        $data['escalation_status'] = 'pending';
        
        $escalation_id = $this->ci_save($data);
        
        if ($escalation_id) {
            // Log activity
            $this->log_activity($escalation_id, $data);
            
            // Send notification
            $this->send_escalation_notification($escalation_id);
        }
        
        return $escalation_id;
    }

    /**
     * Update escalation status
     */
    function update_status($escalation_id, $status, $user_id, $notes = '') {
        $data = array(
            'escalation_status' => $status
        );
        
        if ($status === 'acknowledged') {
            $data['acknowledged_at'] = get_current_utc_time();
        } else if ($status === 'resolved') {
            $data['resolved_at'] = get_current_utc_time();
            $data['resolution'] = $notes;
        }
        
        $result = $this->ci_save($data, $escalation_id);
        
        if ($result) {
            // Update related shipment/task status
            $escalation = $this->get_one($escalation_id);
            if ($status === 'resolved' && $escalation->task_id) {
                $Tasks_model = model('App\Models\Workflow_tasks_model');
                $Tasks_model->ci_save(array('status' => 'completed'), $escalation->task_id);
            }
        }
        
        return $result;
    }

    /**
     * Re-escalate to higher level
     */
    function re_escalate($escalation_id, $new_escalated_to, $reason) {
        $escalation = $this->get_one($escalation_id);
        
        $new_data = array(
            'shipment_id' => $escalation->shipment_id,
            'task_id' => $escalation->task_id,
            'escalated_by' => $escalation->escalated_to,
            'escalated_from' => $escalation->escalated_to,
            'escalated_to' => $new_escalated_to,
            'escalation_level' => $escalation->escalation_level + 1,
            'escalation_reason' => $reason,
            'priority' => 'high'
        );
        
        $new_escalation_id = $this->create_escalation($new_data);
        
        // Update original escalation
        $this->ci_save(array('escalation_status' => 're-escalated'), $escalation_id);
        
        return $new_escalation_id;
    }

    /**
     * Get pending escalations for user
     */
    function get_my_pending_escalations($user_id) {
        $escalations_table = $this->db->prefixTable('workflow_escalations');
        
        $sql = "SELECT * FROM $escalations_table
                WHERE escalated_to=$user_id 
                AND escalation_status IN ('pending', 'acknowledged')
                AND deleted=0
                ORDER BY priority DESC, escalated_at ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Get escalation statistics
     */
    function get_statistics($user_id = null) {
        $escalations_table = $this->db->prefixTable('workflow_escalations');
        
        $where = "";
        if ($user_id) {
            $where = " AND escalated_to=$user_id";
        }
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN escalation_status='pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN escalation_status='acknowledged' THEN 1 ELSE 0 END) as acknowledged,
                SUM(CASE WHEN escalation_status='resolved' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN priority='urgent' THEN 1 ELSE 0 END) as urgent
                FROM $escalations_table
                WHERE deleted=0 $where";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Send escalation notification
     */
    private function send_escalation_notification($escalation_id) {
        $escalation = $this->get_one($escalation_id);
        
        if ($escalation && $escalation->escalated_to) {
            // Send email/notification to escalated_to user
            // TODO: Implement notification system
            log_notification("escalation_created", array(
                "escalation_id" => $escalation_id,
                "to_user_id" => $escalation->escalated_to
            ));
        }
    }

    /**
     * Log escalation activity
     */
    private function log_activity($escalation_id, $data) {
        $escalation = $this->get_one($escalation_id);
        
        if ($escalation && $escalation->shipment_id) {
            $action = "escalated_issue";
            $log_for = "shipment";
            $log_for_id = $escalation->shipment_id;
            
            $log_data = array(
                "log_type" => "escalation",
                "log_for" => $log_for,
                "log_for_id" => $log_for_id,
                "action" => $action,
                "log_data" => json_encode($data)
            );
            
            // Save to activity log
            // $Activity_logs_model = model('App\Models\Activity_logs_model');
            // $Activity_logs_model->ci_save($log_data);
        }
    }

    /**
     * Get escalation chain for shipment
     */
    function get_escalation_chain($shipment_id) {
        $escalations_table = $this->db->prefixTable('workflow_escalations');
        $users_table = $this->db->prefixTable('users');
        
        $sql = "SELECT $escalations_table.*,
                CONCAT(escalated_to_user.first_name, ' ', escalated_to_user.last_name) as escalated_to_name,
                escalated_to_user.job_title as escalated_to_role
                FROM $escalations_table
                LEFT JOIN $users_table as escalated_to_user ON escalated_to_user.id = $escalations_table.escalated_to
                WHERE $escalations_table.shipment_id=$shipment_id
                AND $escalations_table.deleted=0
                ORDER BY $escalations_table.escalation_level ASC, $escalations_table.escalated_at ASC";
        
        return $this->db->query($sql);
    }
}
