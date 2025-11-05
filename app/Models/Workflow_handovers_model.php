<?php

namespace App\Models;

/**
 * Workflow_handovers_model
 * 
 * Manages department-to-department handover workflow
 * Handles phase transitions with checklist approval system
 */
class Workflow_handovers_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'workflow_handovers';
        parent::__construct($this->table);
    }

    /**
     * Get handover details with related data
     */
    function get_details($options = array()) {
        $handovers_table = $this->db->prefixTable('workflow_handovers');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $phases_table = $this->db->prefixTable('workflow_phases');
        $departments_table = $this->db->prefixTable('departments');
        $users_table = $this->db->prefixTable('users');
        $clients_table = $this->db->prefixTable('clients');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $handovers_table.id=$id";
        }
        
        $shipment_id = $this->_get_clean_value($options, "shipment_id");
        if ($shipment_id) {
            $where .= " AND $handovers_table.shipment_id=$shipment_id";
        }
        
        $status = $this->_get_clean_value($options, "handover_status");
        if ($status) {
            $where .= " AND $handovers_table.handover_status='$status'";
        }
        
        $to_department = $this->_get_clean_value($options, "to_department_id");
        if ($to_department) {
            $where .= " AND $handovers_table.to_department_id=$to_department";
        }
        
        $sql = "SELECT $handovers_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name,
                from_phase.name as from_phase_name,
                to_phase.name as to_phase_name,
                from_dept.title as from_department_name,
                to_dept.title as to_department_name,
                CONCAT(initiated_user.first_name, ' ', initiated_user.last_name) as initiated_by_name,
                CONCAT(approved_user.first_name, ' ', approved_user.last_name) as approved_by_name
                FROM $handovers_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $handovers_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                LEFT JOIN $phases_table as from_phase ON from_phase.id = $handovers_table.from_phase_id
                LEFT JOIN $phases_table as to_phase ON to_phase.id = $handovers_table.to_phase_id
                LEFT JOIN $departments_table as from_dept ON from_dept.id = $handovers_table.from_department_id
                LEFT JOIN $departments_table as to_dept ON to_dept.id = $handovers_table.to_department_id
                LEFT JOIN $users_table as initiated_user ON initiated_user.id = $handovers_table.initiated_by
                LEFT JOIN $users_table as approved_user ON approved_user.id = $handovers_table.approved_by
                WHERE $handovers_table.deleted=0 $where
                ORDER BY $handovers_table.initiated_at DESC";
        
        return $this->db->query($sql);
    }

    /**
     * Initiate handover
     */
    function initiate_handover($data) {
        // Generate default checklist if not provided
        if (empty($data['checklist_json'])) {
            $data['checklist_json'] = $this->get_default_checklist($data['from_phase_id'], $data['to_phase_id']);
        }
        
        $data['handover_status'] = 'pending';
        $data['initiated_at'] = get_current_utc_time();
        
        $handover_id = $this->ci_save($data);
        
        if ($handover_id) {
            // Lock shipment phase
            $Shipments_model = model('App\Models\Workflow_shipments_model');
            $Shipments_model->lock_phase($data['shipment_id']);
            
            // Send notification to receiving department
            $this->send_handover_notification($handover_id, 'initiated');
            
            // Log activity
            log_notification("handover_initiated", array(
                "handover_id" => $handover_id,
                "shipment_id" => $data['shipment_id']
            ));
        }
        
        return $handover_id;
    }

    /**
     * Approve handover
     */
    function approve_handover($handover_id, $approved_by) {
        $handover = $this->get_one($handover_id);
        
        if (!$handover || $handover->handover_status !== 'pending') {
            return false;
        }
        
        $data = array(
            'handover_status' => 'accepted',
            'approved_by' => $approved_by,
            'completed_at' => get_current_utc_time()
        );
        
        $result = $this->ci_save($data, $handover_id);
        
        if ($result) {
            // Update shipment phase
            $Shipments_model = model('App\Models\Workflow_shipments_model');
            $Shipments_model->transition_to_phase($handover->shipment_id, $handover->to_phase_id);
            $Shipments_model->unlock_phase($handover->shipment_id);
            
            // Send notification
            $this->send_handover_notification($handover_id, 'approved');
            
            // Log activity
            log_notification("handover_approved", array(
                "handover_id" => $handover_id,
                "shipment_id" => $handover->shipment_id,
                "approved_by" => $approved_by
            ));
        }
        
        return $result;
    }

    /**
     * Reject handover
     */
    function reject_handover($handover_id, $rejected_by, $reason) {
        $handover = $this->get_one($handover_id);
        
        if (!$handover || $handover->handover_status !== 'pending') {
            return false;
        }
        
        $data = array(
            'handover_status' => 'rejected',
            'approved_by' => $rejected_by,
            'rejection_reason' => $reason,
            'completed_at' => get_current_utc_time()
        );
        
        $result = $this->ci_save($data, $handover_id);
        
        if ($result) {
            // Unlock shipment phase
            $Shipments_model = model('App\Models\Workflow_shipments_model');
            $Shipments_model->unlock_phase($handover->shipment_id);
            
            // Send notification back to initiating department
            $this->send_handover_notification($handover_id, 'rejected');
            
            // Log activity
            log_notification("handover_rejected", array(
                "handover_id" => $handover_id,
                "shipment_id" => $handover->shipment_id,
                "rejected_by" => $rejected_by,
                "reason" => $reason
            ));
        }
        
        return $result;
    }

    /**
     * Update checklist item
     */
    function update_checklist($handover_id, $checklist_json) {
        return $this->ci_save(array('checklist_json' => $checklist_json), $handover_id);
    }

    /**
     * Get default checklist based on phase transition
     */
    function get_default_checklist($from_phase_id, $to_phase_id) {
        $checklists = array(
            // Phase 1 → Phase 2
            '1_2' => array(
                array('item' => 'All documents received from client', 'completed' => false),
                array('item' => 'Master file created and populated', 'completed' => false),
                array('item' => 'Tasks delegated to specialists', 'completed' => false)
            ),
            // Phase 2 → Phase 3
            '2_3' => array(
                array('item' => 'Declaration document obtained', 'completed' => false),
                array('item' => 'Customs release order received', 'completed' => false),
                array('item' => 'All regulatory processing complete', 'completed' => false)
            ),
            // Phase 3 → Phase 4
            '3_4' => array(
                array('item' => 'All documents reviewed and approved', 'completed' => false),
                array('item' => 'Payment verification completed', 'completed' => false),
                array('item' => 'No outstanding issues', 'completed' => false),
                array('item' => 'Supervisor authorization obtained', 'completed' => false),
                array('item' => 'Port cost clearance confirmed', 'completed' => false)
            ),
            // Phase 4 → Phase 5
            '4_5' => array(
                array('item' => 'Trucks allocated and confirmed', 'completed' => false),
                array('item' => 'Loading completed', 'completed' => false),
                array('item' => 'T1 form and shipping order prepared', 'completed' => false),
                array('item' => 'Final authorization given', 'completed' => false),
                array('item' => 'Trucks nominated for transport', 'completed' => false)
            )
        );
        
        $key = $from_phase_id . '_' . $to_phase_id;
        $checklist = isset($checklists[$key]) ? $checklists[$key] : array(
            array('item' => 'All tasks completed', 'completed' => false),
            array('item' => 'Documentation verified', 'completed' => false),
            array('item' => 'Ready for next phase', 'completed' => false)
        );
        
        return json_encode($checklist);
    }

    /**
     * Get pending handovers for department
     */
    function get_pending_for_department($department_id) {
        $handovers_table = $this->db->prefixTable('workflow_handovers');
        
        $sql = "SELECT * FROM $handovers_table
                WHERE to_department_id=$department_id 
                AND handover_status='pending'
                AND deleted=0
                ORDER BY initiated_at ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Get handover statistics
     */
    function get_statistics($department_id = null) {
        $handovers_table = $this->db->prefixTable('workflow_handovers');
        
        $where = "";
        if ($department_id) {
            $where = " AND (to_department_id=$department_id OR from_department_id=$department_id)";
        }
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN handover_status='pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN handover_status='accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN handover_status='rejected' THEN 1 ELSE 0 END) as rejected
                FROM $handovers_table
                WHERE deleted=0 $where";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Send handover notification
     */
    private function send_handover_notification($handover_id, $action) {
        $handover = $this->get_one($handover_id);
        
        if ($handover) {
            // Determine recipients based on action
            $recipient_department = ($action === 'initiated') ? $handover->to_department_id : $handover->from_department_id;
            
            // Send email/notification to department members
            // TODO: Implement notification system
            log_notification("handover_$action", array(
                "handover_id" => $handover_id,
                "department_id" => $recipient_department
            ));
        }
    }

    /**
     * Check if checklist is complete
     */
    function is_checklist_complete($checklist_json) {
        $checklist = json_decode($checklist_json, true);
        
        if (empty($checklist)) {
            return false;
        }
        
        foreach ($checklist as $item) {
            if (empty($item['completed'])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get handover history for shipment
     */
    function get_history($shipment_id) {
        return $this->get_details(array('shipment_id' => $shipment_id));
    }
}
