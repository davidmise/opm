<?php

namespace App\Models;

/**
 * Workflow_shipments_model
 * 
 * Manages workflow shipments with phase transitions and locking
 */
class Workflow_shipments_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'workflow_shipments';
        parent::__construct($this->table);
    }

    /**
     * Get shipment details with related data
     */
    function get_details($options = array()) {
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $clients_table = $this->db->prefixTable('clients');
        $phases_table = $this->db->prefixTable('workflow_phases');
        $users_table = $this->db->prefixTable('users');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $shipments_table.id=$id";
        }
        
        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $shipments_table.client_id=$client_id";
        }
        
        $status = $this->_get_clean_value($options, "shipment_status");
        if ($status) {
            $where .= " AND $shipments_table.shipment_status='$status'";
        }
        
        $phase_id = $this->_get_clean_value($options, "current_phase_id");
        if ($phase_id) {
            $where .= " AND $shipments_table.current_phase_id=$phase_id";
        }
        
        $sql = "SELECT $shipments_table.*,
                $clients_table.company_name as client_name,
                $clients_table.primary_contact as client_contact,
                $phases_table.name as current_phase_name,
                $phases_table.sequence as current_phase_sequence,
                CONCAT(created_user.first_name, ' ', created_user.last_name) as created_by_name
                FROM $shipments_table
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                LEFT JOIN $phases_table ON $phases_table.id = $shipments_table.current_phase_id
                LEFT JOIN $users_table as created_user ON created_user.id = $shipments_table.created_by
                WHERE $shipments_table.deleted=0 $where
                ORDER BY $shipments_table.created_at DESC";
        
        return $this->db->query($sql);
    }

    /**
     * Create shipment with initial phase
     */
    function create_shipment($data) {
        // Set initial phase (Phase 1 - Clearing Intake)
        if (empty($data['current_phase_id'])) {
            $data['current_phase_id'] = 1;
        }
        
        $data['shipment_status'] = isset($data['shipment_status']) ? $data['shipment_status'] : 'active';
        $data['created_at'] = get_current_utc_time();
        $data['phase_locked'] = 0;
        $data['costs_cleared'] = 0;
        
        // Generate shipment number if not provided
        if (empty($data['shipment_number'])) {
            $data['shipment_number'] = $this->generate_shipment_number();
        }
        
        $shipment_id = $this->ci_save($data);
        
        if ($shipment_id) {
            // Create initial tasks for Phase 1
            $this->create_phase_tasks($shipment_id, $data['current_phase_id']);
            
            // Log activity
            log_notification("shipment_created", array(
                "shipment_id" => $shipment_id,
                "shipment_number" => $data['shipment_number']
            ));
        }
        
        return $shipment_id;
    }

    /**
     * Transition to next phase
     */
    function transition_to_phase($shipment_id, $new_phase_id) {
        $shipment = $this->get_one($shipment_id);
        
        if (!$shipment) {
            return false;
        }
        
        // Check if phase is locked
        if ($shipment->phase_locked == 1) {
            return false;
        }
        
        // Check if current phase tasks are complete
        $Tasks_model = model('App\Models\Workflow_tasks_model');
        if (!$Tasks_model->is_phase_complete($shipment_id, $shipment->current_phase_id)) {
            return false;
        }
        
        // Update phase
        $data = array(
            'current_phase_id' => $new_phase_id,
            'phase_transitioned_at' => get_current_utc_time()
        );
        
        $result = $this->ci_save($data, $shipment_id);
        
        if ($result) {
            // Create tasks for new phase
            $this->create_phase_tasks($shipment_id, $new_phase_id);
            
            // Log activity
            log_notification("shipment_phase_transition", array(
                "shipment_id" => $shipment_id,
                "old_phase_id" => $shipment->current_phase_id,
                "new_phase_id" => $new_phase_id
            ));
        }
        
        return $result;
    }

    /**
     * Lock phase (during handover/approval)
     */
    function lock_phase($shipment_id) {
        return $this->ci_save(array('phase_locked' => 1), $shipment_id);
    }

    /**
     * Unlock phase
     */
    function unlock_phase($shipment_id) {
        return $this->ci_save(array('phase_locked' => 0), $shipment_id);
    }

    /**
     * Complete/close shipment
     */
    function complete_shipment($shipment_id, $completed_by = null) {
        $data = array(
            'shipment_status' => 'completed',
            'completed_at' => get_current_utc_time()
        );
        
        if ($completed_by) {
            $data['completed_by'] = $completed_by;
        }
        
        $result = $this->ci_save($data, $shipment_id);
        
        if ($result) {
            log_notification("shipment_completed", array(
                "shipment_id" => $shipment_id,
                "completed_by" => $completed_by
            ));
        }
        
        return $result;
    }

    /**
     * Generate unique shipment number
     */
    private function generate_shipment_number() {
        $prefix = "SHP";
        $year = date('Y');
        $month = date('m');
        
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        
        $sql = "SELECT COUNT(*) as count 
                FROM $shipments_table 
                WHERE YEAR(created_at) = $year 
                AND MONTH(created_at) = $month";
        
        $result = $this->db->query($sql)->getRow();
        $sequence = $result->count + 1;
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create phase tasks for shipment
     */
    private function create_phase_tasks($shipment_id, $phase_id) {
        // Get phase default tasks
        $task_templates = $this->get_phase_task_templates($phase_id);
        
        if (!empty($task_templates)) {
            $Tasks_model = model('App\Models\Workflow_tasks_model');
            
            foreach ($task_templates as $template) {
                $task_data = array(
                    'shipment_id' => $shipment_id,
                    'phase_id' => $phase_id,
                    'task_name' => $template['task_name'],
                    'task_order' => $template['task_order'],
                    'department_id' => $template['department_id'],
                    'assigned_to' => isset($template['assigned_to']) ? $template['assigned_to'] : null,
                    'assignment_type' => $template['assignment_type'],
                    'due_days' => $template['due_days']
                );
                
                // Create task with additional assignees if parallel
                $additional_assignees = isset($template['additional_assignees']) ? $template['additional_assignees'] : array();
                $Tasks_model->create_task($task_data, $additional_assignees);
            }
        }
    }

    /**
     * Get phase task templates
     */
    private function get_phase_task_templates($phase_id) {
        // Define standard task templates for each phase
        // This should eventually come from database
        $templates = array(
            // Phase 1: Clearing Intake (4 tasks)
            1 => array(
                array('task_name' => 'Receive goods & documents from client', 'task_order' => 1, 'department_id' => 1, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Create master file', 'task_order' => 2, 'department_id' => 1, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Delegate tasks to clearing specialists', 'task_order' => 3, 'department_id' => 1, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Prepare declaration document', 'task_order' => 4, 'department_id' => 2, 'assignment_type' => 'parallel', 'due_days' => 2)
            ),
            // Phase 2: Regulatory Processing (4 tasks)
            2 => array(
                array('task_name' => 'Submit to customs', 'task_order' => 5, 'department_id' => 2, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Physical inspection (if required)', 'task_order' => 6, 'department_id' => 2, 'assignment_type' => 'single', 'due_days' => 2),
                array('task_name' => 'Obtain customs release order', 'task_order' => 7, 'department_id' => 2, 'assignment_type' => 'single', 'due_days' => 3),
                array('task_name' => 'Receive customs release order', 'task_order' => 8, 'department_id' => 2, 'assignment_type' => 'single', 'due_days' => 1)
            ),
            // Phase 3: Internal Review (3 tasks)
            3 => array(
                array('task_name' => 'Internal team review', 'task_order' => 9, 'department_id' => 1, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Payment verification', 'task_order' => 10, 'department_id' => 1, 'assignment_type' => 'single', 'due_days' => 2),
                array('task_name' => 'Supervisor authorization', 'task_order' => 11, 'department_id' => 1, 'assignment_type' => 'single', 'due_days' => 1)
            ),
            // Phase 4: Transport Loading (7 tasks)
            4 => array(
                array('task_name' => 'Port cost clearance check', 'task_order' => 12, 'department_id' => 3, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Allocate trucks', 'task_order' => 13, 'department_id' => 3, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Prepare loading documents', 'task_order' => 14, 'department_id' => 3, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Confirm truck readiness', 'task_order' => 15, 'department_id' => 3, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Load goods onto trucks', 'task_order' => 16, 'department_id' => 3, 'assignment_type' => 'single', 'due_days' => 2),
                array('task_name' => 'Final authorization to depart', 'task_order' => 17, 'department_id' => 1, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Generate loading order document', 'task_order' => 18, 'department_id' => 3, 'assignment_type' => 'single', 'due_days' => 1)
            ),
            // Phase 5: Tracking (4 tasks)
            5 => array(
                array('task_name' => 'Nominate trucks for transport', 'task_order' => 19, 'department_id' => 4, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Generate tracking report', 'task_order' => 20, 'department_id' => 4, 'assignment_type' => 'single', 'due_days' => 1),
                array('task_name' => 'Upload POD document', 'task_order' => 21, 'department_id' => 4, 'assignment_type' => 'single', 'due_days' => 3),
                array('task_name' => 'Auto-close shipment', 'task_order' => 22, 'department_id' => 4, 'assignment_type' => 'single', 'due_days' => 1)
            )
        );
        
        return isset($templates[$phase_id]) ? $templates[$phase_id] : array();
    }

    /**
     * Get shipment statistics
     */
    function get_statistics($status = null) {
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        
        $where = "";
        if ($status) {
            $where = " AND shipment_status='$status'";
        }
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN shipment_status='active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN shipment_status='completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN shipment_status='cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN phase_locked=1 THEN 1 ELSE 0 END) as locked,
                SUM(CASE WHEN costs_cleared=1 THEN 1 ELSE 0 END) as costs_cleared
                FROM $shipments_table
                WHERE deleted=0 $where";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Get shipments by phase
     */
    function get_shipments_by_phase($phase_id) {
        return $this->get_details(array('current_phase_id' => $phase_id));
    }
}
