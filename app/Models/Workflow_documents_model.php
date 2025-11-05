<?php

namespace App\Models;

/**
 * Workflow_documents_model
 * 
 * Manages workflow documents with POD auto-closure support
 * Implements Task 22 requirement
 */
class Workflow_documents_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'workflow_documents';
        parent::__construct($this->table);
    }

    /**
     * Get document details with related data
     */
    function get_details($options = array()) {
        $documents_table = $this->db->prefixTable('workflow_documents');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $documents_table.id=$id";
        }
        
        $shipment_id = $this->_get_clean_value($options, "shipment_id");
        if ($shipment_id) {
            $where .= " AND $documents_table.shipment_id=$shipment_id";
        }
        
        $document_type = $this->_get_clean_value($options, "document_type");
        if ($document_type) {
            $where .= " AND $documents_table.document_type='$document_type'";
        }
        
        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $documents_table.status='$status'";
        }
        
        $sql = "SELECT $documents_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name,
                CONCAT(uploaded_user.first_name, ' ', uploaded_user.last_name) as uploaded_by_name,
                CONCAT(verified_user.first_name, ' ', verified_user.last_name) as verified_by_name
                FROM $documents_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $documents_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                LEFT JOIN $users_table as uploaded_user ON uploaded_user.id = $documents_table.uploaded_by
                LEFT JOIN $users_table as verified_user ON verified_user.id = $documents_table.verified_by
                WHERE $documents_table.deleted=0 $where
                ORDER BY $documents_table.uploaded_at DESC";
        
        return $this->db->query($sql);
    }

    /**
     * Upload document
     */
    function upload_document($data, $file_info = null) {
        $data['status'] = isset($data['status']) ? $data['status'] : 'pending';
        $data['uploaded_at'] = get_current_utc_time();
        
        if ($file_info) {
            $data['file_name'] = $file_info['file_name'];
            $data['file_path'] = $file_info['file_path'];
            $data['file_size'] = $file_info['file_size'];
        }
        
        $document_id = $this->ci_save($data);
        
        if ($document_id) {
            // Check if POD document - trigger auto-closure (Task 22)
            if ($data['document_type'] === 'POD' || $data['document_type'] === 'proof_of_delivery') {
                $this->handle_pod_upload($document_id, $data['shipment_id']);
            }
            
            // Log activity
            log_notification("document_uploaded", array(
                "document_id" => $document_id,
                "shipment_id" => $data['shipment_id'],
                "document_type" => $data['document_type']
            ));
        }
        
        return $document_id;
    }

    /**
     * Handle POD upload - Task 22: Auto-close shipment
     */
    private function handle_pod_upload($document_id, $shipment_id) {
        // Create approval for POD verification
        $Approvals_model = model('App\Models\Workflow_approvals_model');
        
        $approval_data = array(
            'shipment_id' => $shipment_id,
            'document_id' => $document_id,
            'approval_type' => 'shipment_closure',
            'requested_by' => get_user_id(),
            'description' => 'POD uploaded - requesting shipment closure approval'
        );
        
        $approval_id = $Approvals_model->request_approval($approval_data);
        
        // Log POD upload event
        log_notification("pod_uploaded", array(
            "document_id" => $document_id,
            "shipment_id" => $shipment_id,
            "approval_id" => $approval_id
        ));
        
        // Update document status
        $this->ci_save(array('requires_approval' => 1), $document_id);
    }

    /**
     * Auto-close shipment after POD approval
     */
    function auto_close_shipment($shipment_id, $approved_by) {
        $Shipments_model = model('App\Models\Workflow_shipments_model');
        
        // Check if POD document exists and is approved
        $pod_approved = $this->is_pod_approved($shipment_id);
        
        if ($pod_approved) {
            // Complete shipment
            $result = $Shipments_model->complete_shipment($shipment_id, $approved_by);
            
            if ($result) {
                log_notification("shipment_auto_closed", array(
                    "shipment_id" => $shipment_id,
                    "closed_by" => $approved_by
                ));
            }
            
            return $result;
        }
        
        return false;
    }

    /**
     * Check if POD is approved
     */
    function is_pod_approved($shipment_id) {
        $documents_table = $this->db->prefixTable('workflow_documents');
        
        $sql = "SELECT COUNT(*) as count 
                FROM $documents_table 
                WHERE shipment_id = $shipment_id 
                AND document_type IN ('POD', 'proof_of_delivery')
                AND status = 'approved'
                AND deleted = 0";
        
        $result = $this->db->query($sql)->getRow();
        return ($result->count > 0);
    }

    /**
     * Verify/approve document
     */
    function verify_document($document_id, $verified_by, $notes = '') {
        $data = array(
            'status' => 'approved',
            'verified_by' => $verified_by,
            'verified_at' => get_current_utc_time(),
            'verification_notes' => $notes
        );
        
        $result = $this->ci_save($data, $document_id);
        
        if ($result) {
            $document = $this->get_one($document_id);
            
            // If POD, trigger auto-closure
            if ($document->document_type === 'POD' || $document->document_type === 'proof_of_delivery') {
                $this->auto_close_shipment($document->shipment_id, $verified_by);
            }
            
            log_notification("document_verified", array(
                "document_id" => $document_id,
                "verified_by" => $verified_by
            ));
        }
        
        return $result;
    }

    /**
     * Reject document
     */
    function reject_document($document_id, $rejected_by, $reason) {
        $data = array(
            'status' => 'rejected',
            'verified_by' => $rejected_by,
            'verified_at' => get_current_utc_time(),
            'rejection_reason' => $reason,
            'verification_notes' => $reason
        );
        
        $result = $this->ci_save($data, $document_id);
        
        if ($result) {
            log_notification("document_rejected", array(
                "document_id" => $document_id,
                "rejected_by" => $rejected_by,
                "reason" => $reason
            ));
        }
        
        return $result;
    }

    /**
     * Generate document from template
     */
    function generate_from_template($template_type, $shipment_id, $data = array()) {
        $shipment = model('App\Models\Workflow_shipments_model')->get_one($shipment_id);
        
        if (!$shipment) {
            return false;
        }
        
        $template_content = '';
        $file_name = '';
        
        switch ($template_type) {
            case 'loading_order': // Task 18
                $file_name = 'Loading_Order_' . $shipment->shipment_number . '.pdf';
                $template_content = $this->generate_loading_order($shipment, $data);
                break;
            
            case 'tracking_report': // Task 20
                $file_name = 'Tracking_Report_' . $shipment->shipment_number . '.pdf';
                $template_content = $this->generate_tracking_report($shipment, $data);
                break;
            
            case 'customs_declaration':
                $file_name = 'Customs_Declaration_' . $shipment->shipment_number . '.pdf';
                $template_content = $this->generate_customs_declaration($shipment, $data);
                break;
            
            default:
                return false;
        }
        
        // Save generated document
        // TODO: Implement actual PDF generation
        $file_path = 'files/shipment_files/' . $shipment_id . '/' . $file_name;
        
        $document_data = array(
            'shipment_id' => $shipment_id,
            'document_type' => $template_type,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'status' => 'generated',
            'uploaded_by' => get_user_id(),
            'is_generated' => 1
        );
        
        return $this->upload_document($document_data);
    }

    /**
     * Generate Loading Order template (Task 18)
     */
    private function generate_loading_order($shipment, $data) {
        // TODO: Implement actual template rendering
        $content = array(
            'shipment_number' => $shipment->shipment_number,
            'client_name' => $shipment->client_name,
            'loading_date' => isset($data['loading_date']) ? $data['loading_date'] : date('Y-m-d'),
            'trucks' => isset($data['trucks']) ? $data['trucks'] : array(),
            'items' => isset($data['items']) ? $data['items'] : array(),
            'total_weight' => isset($data['total_weight']) ? $data['total_weight'] : 0,
            'destination' => isset($data['destination']) ? $data['destination'] : ''
        );
        
        return $content;
    }

    /**
     * Generate Tracking Report template (Task 20)
     */
    private function generate_tracking_report($shipment, $data) {
        // TODO: Implement actual template rendering
        $content = array(
            'shipment_number' => $shipment->shipment_number,
            'client_name' => $shipment->client_name,
            'report_date' => date('Y-m-d H:i:s'),
            'current_location' => isset($data['current_location']) ? $data['current_location'] : '',
            'status' => isset($data['status']) ? $data['status'] : 'in_transit',
            'eta' => isset($data['eta']) ? $data['eta'] : '',
            'tracking_history' => isset($data['tracking_history']) ? $data['tracking_history'] : array()
        );
        
        return $content;
    }

    /**
     * Generate Customs Declaration
     */
    private function generate_customs_declaration($shipment, $data) {
        // TODO: Implement actual template rendering
        return array();
    }

    /**
     * Get documents by type
     */
    function get_by_type($shipment_id, $document_type) {
        return $this->get_details(array(
            'shipment_id' => $shipment_id,
            'document_type' => $document_type
        ));
    }

    /**
     * Get pending approvals
     */
    function get_pending_approvals() {
        return $this->get_details(array('status' => 'pending'));
    }

    /**
     * Check if required documents uploaded
     */
    function are_required_documents_complete($shipment_id, $phase_id) {
        $required_docs = $this->get_required_documents_for_phase($phase_id);
        
        foreach ($required_docs as $doc_type) {
            $docs = $this->get_by_type($shipment_id, $doc_type);
            
            if ($docs->getNumRows() == 0) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get required documents for phase
     */
    private function get_required_documents_for_phase($phase_id) {
        $required = array(
            1 => array('client_documents', 'bill_of_lading'),
            2 => array('customs_declaration', 'customs_release_order'),
            3 => array(),
            4 => array('loading_order', 'T1_form'),
            5 => array('tracking_report', 'POD')
        );
        
        return isset($required[$phase_id]) ? $required[$phase_id] : array();
    }

    /**
     * Get document statistics
     */
    function get_statistics($shipment_id = null) {
        $documents_table = $this->db->prefixTable('workflow_documents');
        
        $where = "";
        if ($shipment_id) {
            $where = " AND shipment_id=$shipment_id";
        }
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN is_generated=1 THEN 1 ELSE 0 END) as generated,
                SUM(CASE WHEN document_type IN ('POD', 'proof_of_delivery') THEN 1 ELSE 0 END) as pod_count
                FROM $documents_table
                WHERE deleted=0 $where";
        
        return $this->db->query($sql)->getRow();
    }
}
