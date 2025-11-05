<?php
/**
 * Database Setup Script - Creates all missing workflow tables
 * Run this script to set up the complete workflow system infrastructure
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'overland_pm';

echo "=======================================================\n";
echo "WORKFLOW SYSTEM DATABASE SETUP\n";
echo "=======================================================\n\n";

// Connect to database
$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("[✗] CONNECTION FAILED: " . $mysqli->connect_error . "\n");
}

echo "[✓] Connected to database: $database\n\n";

// Function to execute SQL and show result
function executeSQL($mysqli, $sql, $description) {
    echo "[$description]\n";
    if ($mysqli->query($sql)) {
        echo "[✓] Success\n\n";
        return true;
    } else {
        echo "[✗] Error: " . $mysqli->error . "\n\n";
        return false;
    }
}

// Check if tables exist
function tableExists($mysqli, $table) {
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

echo "=======================================================\n";
echo "CHECKING EXISTING TABLES\n";
echo "=======================================================\n\n";

$tables_to_check = [
    'opm_workflow_phases',
    'opm_workflow_shipments',
    'opm_workflow_tasks',
    'opm_workflow_documents',
    'opm_workflow_escalations',
    'opm_workflow_handovers',
    'opm_workflow_approvals',
    'opm_shipment_costs',
    'opm_workflow_task_assignees',
    'opm_trucks',
    'opm_truck_allocations',
    'opm_tracking_reports'
];

$existing_tables = [];
$missing_tables = [];

foreach ($tables_to_check as $table) {
    if (tableExists($mysqli, $table)) {
        echo "[✓] $table EXISTS\n";
        $existing_tables[] = $table;
    } else {
        echo "[✗] $table MISSING\n";
        $missing_tables[] = $table;
    }
}

echo "\nExisting: " . count($existing_tables) . " | Missing: " . count($missing_tables) . "\n\n";

if (empty($missing_tables)) {
    echo "=======================================================\n";
    echo "ALL TABLES EXIST - NO ACTION NEEDED\n";
    echo "=======================================================\n\n";
    $mysqli->close();
    exit(0);
}

echo "=======================================================\n";
echo "CREATING MISSING TABLES\n";
echo "=======================================================\n\n";

// 1. Workflow Escalations Table
if (in_array('opm_workflow_escalations', $missing_tables)) {
    $sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_escalations` (
      `id` int NOT NULL AUTO_INCREMENT,
      `shipment_id` int DEFAULT NULL,
      `task_id` int DEFAULT NULL,
      `escalated_by` int NOT NULL,
      `escalated_from` int NOT NULL,
      `escalated_to` int NOT NULL,
      `escalation_level` int NOT NULL DEFAULT '1' COMMENT '1=Supervisor, 2=GM, 3=Management',
      `escalation_reason` text COLLATE utf8mb4_unicode_ci,
      `escalation_status` enum('pending','acknowledged','in_review','resolved','re-escalated') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
      `resolution` text COLLATE utf8mb4_unicode_ci,
      `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
      `escalated_at` datetime DEFAULT CURRENT_TIMESTAMP,
      `acknowledged_at` datetime DEFAULT NULL,
      `resolved_at` datetime DEFAULT NULL,
      `deleted` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `shipment_id` (`shipment_id`),
      KEY `task_id` (`task_id`),
      KEY `escalated_to` (`escalated_to`),
      KEY `escalation_status` (`escalation_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Escalation workflow for issues and tasks';";
    
    executeSQL($mysqli, $sql, "Creating opm_workflow_escalations");
}

// 2. Workflow Handovers Table
if (in_array('opm_workflow_handovers', $missing_tables)) {
    $sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_handovers` (
      `id` int NOT NULL AUTO_INCREMENT,
      `shipment_id` int NOT NULL,
      `from_phase_id` int NOT NULL,
      `to_phase_id` int NOT NULL,
      `from_department_id` int DEFAULT NULL,
      `to_department_id` int DEFAULT NULL,
      `initiated_by` int NOT NULL,
      `handover_status` enum('pending','accepted','rejected','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
      `checklist_json` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of checklist items with completion status',
      `rejection_reason` text COLLATE utf8mb4_unicode_ci,
      `handover_notes` text COLLATE utf8mb4_unicode_ci,
      `approved_by` int DEFAULT NULL,
      `initiated_at` datetime DEFAULT CURRENT_TIMESTAMP,
      `completed_at` datetime DEFAULT NULL,
      `deleted` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `shipment_id` (`shipment_id`),
      KEY `from_phase_id` (`from_phase_id`),
      KEY `to_phase_id` (`to_phase_id`),
      KEY `handover_status` (`handover_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Department-to-department handover workflow';";
    
    executeSQL($mysqli, $sql, "Creating opm_workflow_handovers");
}

// 3. Workflow Approvals Table
if (in_array('opm_workflow_approvals', $missing_tables)) {
    $sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_approvals` (
      `id` int NOT NULL AUTO_INCREMENT,
      `shipment_id` int NOT NULL,
      `task_id` int DEFAULT NULL,
      `approval_type` enum('phase_transition','document','cost','escalation','handover','truck_nomination','final_authorization','other') COLLATE utf8mb4_unicode_ci NOT NULL,
      `approval_level` int NOT NULL DEFAULT '1',
      `required_approver_role` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `required_approver_department` int DEFAULT NULL,
      `approver_id` int DEFAULT NULL,
      `approval_status` enum('pending','approved','rejected','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
      `approval_notes` text COLLATE utf8mb4_unicode_ci,
      `rejection_reason` text COLLATE utf8mb4_unicode_ci,
      `requested_by` int NOT NULL,
      `requested_at` datetime DEFAULT CURRENT_TIMESTAMP,
      `responded_at` datetime DEFAULT NULL,
      `deleted` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `shipment_id` (`shipment_id`),
      KEY `task_id` (`task_id`),
      KEY `approver_id` (`approver_id`),
      KEY `approval_status` (`approval_status`),
      KEY `approval_type` (`approval_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Approval workflow for critical actions';";
    
    executeSQL($mysqli, $sql, "Creating opm_workflow_approvals");
}

// 4. Shipment Costs Table
if (in_array('opm_shipment_costs', $missing_tables)) {
    $sql = "CREATE TABLE IF NOT EXISTS `opm_shipment_costs` (
      `id` int NOT NULL AUTO_INCREMENT,
      `shipment_id` int NOT NULL,
      `cost_category` enum('port_fees','customs_duties','storage_fees','transport_fees','documentation_fees','inspection_fees','other') COLLATE utf8mb4_unicode_ci NOT NULL,
      `cost_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `cost_amount` decimal(12,2) NOT NULL,
      `currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'TZS',
      `payment_status` enum('pending','paid','verified','disputed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
      `paid_by` int DEFAULT NULL,
      `verified_by` int DEFAULT NULL,
      `payment_date` date DEFAULT NULL,
      `verification_date` date DEFAULT NULL,
      `payment_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
      `receipt_document_id` int DEFAULT NULL,
      `notes` text COLLATE utf8mb4_unicode_ci,
      `created_by` int NOT NULL,
      `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
      `deleted` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `shipment_id` (`shipment_id`),
      KEY `payment_status` (`payment_status`),
      KEY `cost_category` (`cost_category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cost tracking and payment verification for shipments';";
    
    executeSQL($mysqli, $sql, "Creating opm_shipment_costs");
}

// 5. Workflow Task Assignees Table (for parallel assignment)
if (in_array('opm_workflow_task_assignees', $missing_tables)) {
    $sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_task_assignees` (
      `id` int NOT NULL AUTO_INCREMENT,
      `task_id` int NOT NULL,
      `user_id` int NOT NULL,
      `assignment_status` enum('pending','in_progress','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
      `assigned_at` datetime DEFAULT CURRENT_TIMESTAMP,
      `started_at` datetime DEFAULT NULL,
      `completed_at` datetime DEFAULT NULL,
      `completion_notes` text COLLATE utf8mb4_unicode_ci,
      `deleted` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`),
      KEY `task_id` (`task_id`),
      KEY `user_id` (`user_id`),
      KEY `assignment_status` (`assignment_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Multiple assignees for parallel task execution';";
    
    executeSQL($mysqli, $sql, "Creating opm_workflow_task_assignees");
}

// Add assignment_type column to workflow_tasks if it exists
if (tableExists($mysqli, 'opm_workflow_tasks')) {
    $check = $mysqli->query("SHOW COLUMNS FROM `opm_workflow_tasks` LIKE 'assignment_type'");
    if ($check->num_rows == 0) {
        $sql = "ALTER TABLE `opm_workflow_tasks` 
                ADD COLUMN `assignment_type` enum('single','parallel','any_one') COLLATE utf8mb4_unicode_ci DEFAULT 'single' 
                AFTER `assigned_to`;";
        executeSQL($mysqli, $sql, "Adding assignment_type column to opm_workflow_tasks");
    }
}

// Add phase_locked column to workflow_shipments if it exists
if (tableExists($mysqli, 'opm_workflow_shipments')) {
    $check = $mysqli->query("SHOW COLUMNS FROM `opm_workflow_shipments` LIKE 'phase_locked'");
    if ($check->num_rows == 0) {
        $sql = "ALTER TABLE `opm_workflow_shipments` 
                ADD COLUMN `phase_locked` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Lock phase until handover approved' 
                AFTER `current_phase_id`;";
        executeSQL($mysqli, $sql, "Adding phase_locked column to opm_workflow_shipments");
    }
}

// Create indexes for performance
echo "=======================================================\n";
echo "OPTIMIZING INDEXES\n";
echo "=======================================================\n\n";

// Check and create composite indexes for better query performance
if (tableExists($mysqli, 'opm_workflow_escalations')) {
    $check = $mysqli->query("SHOW INDEX FROM opm_workflow_escalations WHERE Key_name = 'idx_escalation_lookup'");
    if ($check->num_rows == 0) {
        $sql = "CREATE INDEX idx_escalation_lookup ON opm_workflow_escalations (shipment_id, escalation_status, deleted);";
        $mysqli->query($sql);
    }
    echo "[✓] Optimized opm_workflow_escalations indexes\n";
}

if (tableExists($mysqli, 'opm_workflow_handovers')) {
    $check = $mysqli->query("SHOW INDEX FROM opm_workflow_handovers WHERE Key_name = 'idx_handover_lookup'");
    if ($check->num_rows == 0) {
        $sql = "CREATE INDEX idx_handover_lookup ON opm_workflow_handovers (shipment_id, handover_status, deleted);";
        $mysqli->query($sql);
    }
    echo "[✓] Optimized opm_workflow_handovers indexes\n";
}

if (tableExists($mysqli, 'opm_workflow_approvals')) {
    $check = $mysqli->query("SHOW INDEX FROM opm_workflow_approvals WHERE Key_name = 'idx_approval_lookup'");
    if ($check->num_rows == 0) {
        $sql = "CREATE INDEX idx_approval_lookup ON opm_workflow_approvals (shipment_id, approval_status, approver_id, deleted);";
        $mysqli->query($sql);
    }
    echo "[✓] Optimized opm_workflow_approvals indexes\n";
}

if (tableExists($mysqli, 'opm_shipment_costs')) {
    $check = $mysqli->query("SHOW INDEX FROM opm_shipment_costs WHERE Key_name = 'idx_cost_lookup'");
    if ($check->num_rows == 0) {
        $sql = "CREATE INDEX idx_cost_lookup ON opm_shipment_costs (shipment_id, payment_status, deleted);";
        $mysqli->query($sql);
    }
    echo "[✓] Optimized opm_shipment_costs indexes\n";
}

echo "\n";

// Summary
echo "=======================================================\n";
echo "SETUP COMPLETE\n";
echo "=======================================================\n\n";

// Count all workflow tables
$result = $mysqli->query("SHOW TABLES LIKE '%workflow%'");
$workflow_table_count = $result->num_rows;

echo "Total workflow-related tables: $workflow_table_count\n";
echo "Database: $database\n";
echo "Status: ✓ Ready for workflow operations\n\n";

echo "Next steps:\n";
echo "1. Run the model creation script to generate PHP models\n";
echo "2. Update Workflow controller with new methods\n";
echo "3. Create UI views for new features\n";
echo "4. Test escalation workflow\n";
echo "5. Test handover workflow\n\n";

$mysqli->close();

echo "=======================================================\n";
?>
