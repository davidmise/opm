<?php

namespace App\Models;

/**
 * Shipment_costs_model
 * 
 * Manages cost tracking and payment verification for shipments
 * Handles payment verification before transport phase (Task 10 requirement)
 */
class Shipment_costs_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'shipment_costs';
        parent::__construct($this->table);
    }

    /**
     * Get cost details with related data
     */
    function get_details($options = array()) {
        $costs_table = $this->db->prefixTable('shipment_costs');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $costs_table.id=$id";
        }
        
        $shipment_id = $this->_get_clean_value($options, "shipment_id");
        if ($shipment_id) {
            $where .= " AND $costs_table.shipment_id=$shipment_id";
        }
        
        $cost_type = $this->_get_clean_value($options, "cost_type");
        if ($cost_type) {
            $where .= " AND $costs_table.cost_type='$cost_type'";
        }
        
        $payment_status = $this->_get_clean_value($options, "payment_status");
        if ($payment_status) {
            $where .= " AND $costs_table.payment_status='$payment_status'";
        }
        
        $sql = "SELECT $costs_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name,
                CONCAT(added_user.first_name, ' ', added_user.last_name) as added_by_name,
                CONCAT(verified_user.first_name, ' ', verified_user.last_name) as verified_by_name
                FROM $costs_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $costs_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                LEFT JOIN $users_table as added_user ON added_user.id = $costs_table.added_by
                LEFT JOIN $users_table as verified_user ON verified_user.id = $costs_table.verified_by
                WHERE $costs_table.deleted=0 $where
                ORDER BY $costs_table.created_at DESC";
        
        return $this->db->query($sql);
    }

    /**
     * Add cost entry
     */
    function add_cost($data) {
        $data['payment_status'] = 'unpaid';
        $data['created_at'] = get_current_utc_time();
        
        $cost_id = $this->ci_save($data);
        
        if ($cost_id) {
            // Log activity
            log_notification("cost_added", array(
                "cost_id" => $cost_id,
                "shipment_id" => $data['shipment_id'],
                "amount" => $data['amount']
            ));
        }
        
        return $cost_id;
    }

    /**
     * Update payment status
     */
    function update_payment_status($cost_id, $status, $user_id, $notes = '') {
        $cost = $this->get_one($cost_id);
        
        if (!$cost) {
            return false;
        }
        
        $data = array('payment_status' => $status);
        
        switch ($status) {
            case 'paid':
                $data['paid_at'] = get_current_utc_time();
                break;
            
            case 'verified':
                $data['verified_by'] = $user_id;
                $data['verified_at'] = get_current_utc_time();
                $data['verification_notes'] = $notes;
                break;
        }
        
        $result = $this->ci_save($data, $cost_id);
        
        if ($result) {
            // Send notification
            $this->send_cost_notification($cost_id, $status);
            
            // Log activity
            log_notification("cost_payment_updated", array(
                "cost_id" => $cost_id,
                "status" => $status,
                "user_id" => $user_id
            ));
            
            // Check if all costs verified for shipment
            if ($status === 'verified') {
                $this->check_shipment_clearance($cost->shipment_id);
            }
        }
        
        return $result;
    }

    /**
     * Verify payment
     */
    function verify_payment($cost_id, $verified_by, $notes = '') {
        return $this->update_payment_status($cost_id, 'verified', $verified_by, $notes);
    }

    /**
     * Get cost summary for shipment
     */
    function get_shipment_summary($shipment_id) {
        $costs_table = $this->db->prefixTable('shipment_costs');
        
        $sql = "SELECT 
                COUNT(*) as total_items,
                SUM(amount) as total_amount,
                SUM(CASE WHEN payment_status='unpaid' THEN amount ELSE 0 END) as unpaid_amount,
                SUM(CASE WHEN payment_status='paid' THEN amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN payment_status='verified' THEN amount ELSE 0 END) as verified_amount,
                SUM(CASE WHEN payment_status='unpaid' THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN payment_status='paid' THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN payment_status='verified' THEN 1 ELSE 0 END) as verified_count
                FROM $costs_table
                WHERE shipment_id=$shipment_id AND deleted=0";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Check if shipment cleared for transport (Task 10 requirement)
     */
    function is_cleared_for_transport($shipment_id) {
        $summary = $this->get_shipment_summary($shipment_id);
        
        // All costs must be verified
        return ($summary->total_items > 0 && $summary->unpaid_count == 0 && $summary->paid_count == 0);
    }

    /**
     * Check shipment clearance and trigger notification
     */
    private function check_shipment_clearance($shipment_id) {
        if ($this->is_cleared_for_transport($shipment_id)) {
            // All costs verified - shipment cleared for transport
            log_notification("shipment_costs_cleared", array(
                "shipment_id" => $shipment_id
            ));
            
            // Update shipment status
            $Shipments_model = model('App\Models\Workflow_shipments_model');
            $Shipments_model->ci_save(array('costs_cleared' => 1), $shipment_id);
        }
    }

    /**
     * Get costs by type
     */
    function get_by_cost_type($shipment_id, $cost_type) {
        return $this->get_details(array(
            'shipment_id' => $shipment_id,
            'cost_type' => $cost_type
        ));
    }

    /**
     * Get pending verifications
     */
    function get_pending_verifications() {
        $costs_table = $this->db->prefixTable('shipment_costs');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $clients_table = $this->db->prefixTable('clients');
        
        $sql = "SELECT $costs_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name
                FROM $costs_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $costs_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                WHERE $costs_table.payment_status='paid' 
                AND $costs_table.deleted=0
                ORDER BY $costs_table.paid_at ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Get overdue payments
     */
    function get_overdue_payments($days = 7) {
        $costs_table = $this->db->prefixTable('shipment_costs');
        $shipments_table = $this->db->prefixTable('workflow_shipments');
        $clients_table = $this->db->prefixTable('clients');
        
        $date_threshold = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        $sql = "SELECT $costs_table.*,
                $shipments_table.shipment_number,
                $clients_table.company_name as client_name,
                DATEDIFF(NOW(), $costs_table.created_at) as days_overdue
                FROM $costs_table
                LEFT JOIN $shipments_table ON $shipments_table.id = $costs_table.shipment_id
                LEFT JOIN $clients_table ON $clients_table.id = $shipments_table.client_id
                WHERE $costs_table.payment_status='unpaid' 
                AND $costs_table.created_at < '$date_threshold'
                AND $costs_table.deleted=0
                ORDER BY $costs_table.created_at ASC";
        
        return $this->db->query($sql);
    }

    /**
     * Get cost statistics
     */
    function get_statistics($shipment_id = null) {
        $costs_table = $this->db->prefixTable('shipment_costs');
        
        $where = "";
        if ($shipment_id) {
            $where = " AND shipment_id=$shipment_id";
        }
        
        $sql = "SELECT 
                COUNT(*) as total_items,
                SUM(amount) as total_amount,
                SUM(CASE WHEN payment_status='unpaid' THEN 1 ELSE 0 END) as unpaid_count,
                SUM(CASE WHEN payment_status='paid' THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN payment_status='verified' THEN 1 ELSE 0 END) as verified_count,
                SUM(CASE WHEN cost_type='customs' THEN amount ELSE 0 END) as customs_total,
                SUM(CASE WHEN cost_type='port' THEN amount ELSE 0 END) as port_total,
                SUM(CASE WHEN cost_type='transport' THEN amount ELSE 0 END) as transport_total,
                SUM(CASE WHEN cost_type='other' THEN amount ELSE 0 END) as other_total
                FROM $costs_table
                WHERE deleted=0 $where";
        
        return $this->db->query($sql)->getRow();
    }

    /**
     * Bulk update costs
     */
    function bulk_update_status($cost_ids, $status, $user_id) {
        $success_count = 0;
        
        foreach ($cost_ids as $cost_id) {
            if ($this->update_payment_status($cost_id, $status, $user_id)) {
                $success_count++;
            }
        }
        
        return $success_count;
    }

    /**
     * Send cost notification
     */
    private function send_cost_notification($cost_id, $status) {
        $cost = $this->get_one($cost_id);
        
        if ($cost) {
            // Send email/notification
            // TODO: Implement notification system
            log_notification("cost_status_changed", array(
                "cost_id" => $cost_id,
                "status" => $status
            ));
        }
    }

    /**
     * Get cost history for shipment
     */
    function get_cost_history($shipment_id) {
        return $this->get_details(array('shipment_id' => $shipment_id));
    }

    /**
     * Calculate total costs for shipment by type
     */
    function calculate_totals_by_type($shipment_id) {
        $costs_table = $this->db->prefixTable('shipment_costs');
        
        $sql = "SELECT 
                cost_type,
                COUNT(*) as count,
                SUM(amount) as total,
                SUM(CASE WHEN payment_status='verified' THEN amount ELSE 0 END) as verified_total
                FROM $costs_table
                WHERE shipment_id=$shipment_id AND deleted=0
                GROUP BY cost_type";
        
        return $this->db->query($sql);
    }
}
