<?php

namespace App\Models;

/**
 * Workflow_approvals_model
 * 
 * Manages approval gates system for workflow operations
 * Supports 8 approval types with multi-step approval chains
 */
class Workflow_approvals_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'workflow_approvals';
        parent::__construct($this->table);
    }

    /**
     * Get approval details with related data
     */
    function get_details($options = array()) {
        $approvals_table = $this->db->prefixTable('workflow_approvals');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $tasks_table = $this->db->prefixTable('workflow_tasks');
        $documents_table = $this->db->prefixTable('workflow_documents');
        $users_table = $this->db->prefixTable('users');
        $clients_table = $this->db->prefixTable('clients');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $approvals_table.id=$id";
        }
        
        $shipment_id = $this->_get_clean_value($options, "shipment_id");
        if ($shipment_id) {
            $where .= " AND $approvals_table.shipment_id=$shipment_id";
        }
        
        $approval_type = $this->_get_clean_value($options, "approval_type");
        if ($approval_type) {
            $where .= " AND $approvals_table.approval_type='$approval_type'";
        }
        
        $status = $this->_get_clean_value($options, "approval_status");
        if ($status) {
            $where .= " AND $approvals_table.approval_status='$status'";
        }
        
        $approver_id = $this->_get_clean_value($options, "current_approver_id");
        if ($approver_id) {
            $where .= " AND $approvals_table.current_approver_id=$approver_id";
        }
        
        $sql = "SELECT $approvals_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name,
                $tasks_table.task_name,
                $documents_table.file_name as document_name,
                CONCAT(requested_user.first_name, ' ', requested_user.last_name) as requested_by_name,
                CONCAT(approver_user.first_name, ' ', approver_user.last_name) as current_approver_name,
                CONCAT(approved_user.first_name, ' ', approved_user.last_name) as approved_by_name
                FROM $approvals_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $approvals_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                LEFT JOIN $tasks_table ON $tasks_table.id = $approvals_table.task_id
                LEFT JOIN $documents_table ON $documents_table.id = $approvals_table.document_id
                LEFT JOIN $users_table as requested_user ON requested_user.id = $approvals_table.requested_by
                LEFT JOIN $users_table as approver_user ON approver_user.id = $approvals_table.current_approver_id
                LEFT JOIN $users_table as approved_user ON approved_user.id = $approvals_table.approved_by
                WHERE $approvals_table.deleted=0 $where
                ORDER BY $approvals_table.requested_at DESC";
        
        return $this->db->query($sql);
    }

    /**
     * Request approval
     */
    function request_approval($data) {
        // Set initial status
        $data['approval_status'] = 'pending';
        $data['requested_at'] = get_current_utc_time();
        
        // Get approval chain for type
        if (empty($data['approval_chain'])) {
            $data['approval_chain'] = $this->get_default_approval_chain($data['approval_type']);
        }
        
        // Set first approver
        $chain = json_decode($data['approval_chain'], true);
        if (!empty($chain) && isset($chain[0]['approver_id'])) {
            $data['current_approver_id'] = $chain[0]['approver_id'];
            $data['current_step'] = 1;
        }
        
        $approval_id = $this->ci_save($data);
        
        if ($approval_id) {
            // Send notification to current approver
            $this->send_approval_notification($approval_id, 'requested');
            
            // Lock related entity if needed
            $this->lock_entity($data);
            
            // Log activity
            log_notification("approval_requested", array(
                "approval_id" => $approval_id,
                "approval_type" => $data['approval_type']
            ));
        }
        
        return $approval_id;
    }

    /**
     * Approve step
     */
    function approve($approval_id, $approver_id, $comments = '') {
        $approval = $this->get_one($approval_id);
        
        if (!$approval || $approval->approval_status !== 'pending') {
            return false;
        }
        
        if ($approval->current_approver_id != $approver_id) {
            return false; // Unauthorized
        }
        
        $chain = json_decode($approval->approval_chain, true);
        $current_step = $approval->current_step;
        
        // Mark current step as approved
        if (isset($chain[$current_step - 1])) {
            $chain[$current_step - 1]['approved_at'] = get_current_utc_time();
            $chain[$current_step - 1]['approved_by'] = $approver_id;
            $chain[$current_step - 1]['comments'] = $comments;
        }
        
        // Check if there are more steps
        if (isset($chain[$current_step])) {
            // Move to next step
            $data = array(
                'current_step' => $current_step + 1,
                'current_approver_id' => $chain[$current_step]['approver_id'],
                'approval_chain' => json_encode($chain),
                'approver_comments' => $comments
            );
            
            $result = $this->ci_save($data, $approval_id);
            
            if ($result) {
                $this->send_approval_notification($approval_id, 'step_approved');
            }
        } else {
            // Final approval
            $data = array(
                'approval_status' => 'approved',
                'approved_by' => $approver_id,
                'approved_at' => get_current_utc_time(),
                'approval_chain' => json_encode($chain),
                'approver_comments' => $comments
            );
            
            $result = $this->ci_save($data, $approval_id);
            
            if ($result) {
                // Unlock entity and trigger post-approval actions
                $this->unlock_entity($approval);
                $this->trigger_post_approval_action($approval);
                
                $this->send_approval_notification($approval_id, 'approved');
                
                log_notification("approval_completed", array(
                    "approval_id" => $approval_id,
                    "approved_by" => $approver_id
                ));
            }
        }
        
        return $result;
    }

    /**
     * Reject approval
     */
    function reject($approval_id, $approver_id, $reason) {
        $approval = $this->get_one($approval_id);
        
        if (!$approval || $approval->approval_status !== 'pending') {
            return false;
        }
        
        if ($approval->current_approver_id != $approver_id) {
            return false; // Unauthorized
        }
        
        $data = array(
            'approval_status' => 'rejected',
            'approved_by' => $approver_id,
            'approved_at' => get_current_utc_time(),
            'rejection_reason' => $reason,
            'approver_comments' => $reason
        );
        
        $result = $this->ci_save($data, $approval_id);
        
        if ($result) {
            // Unlock entity
            $this->unlock_entity($approval);
            
            // Send notification
            $this->send_approval_notification($approval_id, 'rejected');
            
            log_notification("approval_rejected", array(
                "approval_id" => $approval_id,
                "rejected_by" => $approver_id,
                "reason" => $reason
            ));
        }
        
        return $result;
    }

    /**
     * Get default approval chain for approval type
     */
    function get_default_approval_chain($approval_type) {
        // This should be configurable per company
        // For now, returning standard chains
        $chains = array(
            'phase_transition' => array(
                array('approver_id' => 0, 'role' => 'Supervisor', 'required' => true),
                array('approver_id' => 0, 'role' => 'Manager', 'required' => true)
            ),
            'document_approval' => array(
                array('approver_id' => 0, 'role' => 'Reviewer', 'required' => true)
            ),
            'cost_approval' => array(
                array('approver_id' => 0, 'role' => 'Finance Manager', 'required' => true),
                array('approver_id' => 0, 'role' => 'General Manager', 'required' => true)
            ),
            'task_completion' => array(
                array('approver_id' => 0, 'role' => 'Supervisor', 'required' => true)
            ),
            'handover_approval' => array(
                array('approver_id' => 0, 'role' => 'Department Head', 'required' => true)
            ),
            'shipment_closure' => array(
                array('approver_id' => 0, 'role' => 'Operations Manager', 'required' => true),
                array('approver_id' => 0, 'role' => 'General Manager', 'required' => true)
            ),
            'exception_approval' => array(
                array('approver_id' => 0, 'role' => 'Supervisor', 'required' => true),
                array('approver_id' => 0, 'role' => 'General Manager', 'required' => true)
            ),
            'document_release' => array(
                array('approver_id' => 0, 'role' => 'Document Controller', 'required' => true)
            )
        );
        
        $chain = isset($chains[$approval_type]) ? $chains[$approval_type] : array(
            array('approver_id' => 0, 'role' => 'Manager', 'required' => true)
        );
        
        return json_encode($chain);
    }

    /**
     * Get pending approvals for user
     */
    function get_my_pending_approvals($user_id) {
        return $this->get_details(array(
            'current_approver_id' => $user_id,
            'approval_status' => 'pending'
        ));
    }

    /**
     * Get approval statistics
     */
    function get_statistics($user_id = null) {
        $approvals_table = $this->db->prefixTable('workflow_approvals');
        
        $where = "";
        if ($user_id) {
            $where = " AND current_approver_id=$user_id";
        }
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN approval_status='pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN approval_status='approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN approval_status='rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN approval_type='phase_transition' AND approval_status='pending' THEN 1 ELSE 0 END) as pending_phase,
                SUM(CASE WHEN approval_type='cost_approval' AND approval_status='pending' THEN 1 ELSE 0 END) as pending_cost
                FROM $approvals_table
                WHERE deleted=0 $where";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Lock entity during approval
     */
    private function lock_entity($data) {
        if (!empty($data['shipment_id'])) {
            $Shipments_model = model('App\Models\Workflow_shipments_model');
            $Shipments_model->lock_phase($data['shipment_id']);
        }
    }

    /**
     * Unlock entity after approval decision
     */
    private function unlock_entity($approval) {
        if (!empty($approval->shipment_id)) {
            $Shipments_model = model('App\Models\Workflow_shipments_model');
            $Shipments_model->unlock_phase($approval->shipment_id);
        }
    }

    /**
     * Trigger post-approval actions
     */
    private function trigger_post_approval_action($approval) {
        switch ($approval->approval_type) {
            case 'phase_transition':
                // Auto-advance phase
                if (!empty($approval->metadata_json)) {
                    $metadata = json_decode($approval->metadata_json, true);
                    if (isset($metadata['target_phase_id'])) {
                        $Shipments_model = model('App\Models\Workflow_shipments_model');
                        $Shipments_model->transition_to_phase($approval->shipment_id, $metadata['target_phase_id']);
                    }
                }
                break;
            
            case 'task_completion':
                // Mark task as complete
                if (!empty($approval->task_id)) {
                    $Tasks_model = model('App\Models\Workflow_tasks_model');
                    $Tasks_model->complete_task($approval->task_id);
                }
                break;
            
            case 'document_release':
                // Release document
                if (!empty($approval->document_id)) {
                    $Documents_model = model('App\Models\Workflow_documents_model');
                    $Documents_model->ci_save(array('status' => 'released'), $approval->document_id);
                }
                break;
        }
    }

    /**
     * Send approval notification
     */
    private function send_approval_notification($approval_id, $action) {
        $approval = $this->get_one($approval_id);
        
        if ($approval) {
            // Send email/notification
            // TODO: Implement notification system
            log_notification("approval_$action", array(
                "approval_id" => $approval_id,
                "approver_id" => $approval->current_approver_id
            ));
        }
    }

    /**
     * Get approval history for entity
     */
    function get_approval_history($shipment_id = null, $task_id = null, $document_id = null) {
        $options = array();
        
        if ($shipment_id) {
            $options['shipment_id'] = $shipment_id;
        }
        if ($task_id) {
            $options['task_id'] = $task_id;
        }
        if ($document_id) {
            $options['document_id'] = $document_id;
        }
        
        return $this->get_details($options);
    }
}
