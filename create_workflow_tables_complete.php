<?php
/**
 * Complete Workflow Tables Setup
 * Creates ALL workflow tables needed for the system
 */

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'overland_pm';

echo "=======================================================\n";
echo "COMPLETE WORKFLOW TABLES SETUP\n";
echo "=======================================================\n\n";

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("[✗] CONNECTION FAILED: " . $mysqli->connect_error . "\n");
}

echo "[✓] Connected to database\n\n";

// Workflow Phases Table
$sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_phases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `phase_order` int NOT NULL DEFAULT '1',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#3498db',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'package',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `department_id` int DEFAULT NULL COMMENT 'Primary department for this phase',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `phase_order` (`phase_order`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "[✓] Created opm_workflow_phases\n";
    
    // Insert default phases
    $check = $mysqli->query("SELECT COUNT(*) as cnt FROM opm_workflow_phases WHERE deleted=0");
    $row = $check->fetch_assoc();
    if ($row['cnt'] == 0) {
        $mysqli->query("INSERT INTO `opm_workflow_phases` (`id`, `name`, `description`, `phase_order`, `color`, `icon`, `status`) VALUES
        (1, 'Clearing & Documentation Intake', 'Initial document submission and file creation phase', 1, '#e74c3c', 'file-text', 'active'),
        (2, 'Regulatory & Release Processing', 'TRA portal processing and shipping line release', 2, '#f39c12', 'check-circle', 'active'),
        (3, 'Internal Review & Handover', 'Document review and operations handover', 3, '#3498db', 'eye', 'active'),
        (4, 'Transport Operations & Loading', 'Truck allocation and cargo loading', 4, '#9b59b6', 'truck', 'active'),
        (5, 'Tracking', 'Shipment tracking and client communication', 5, '#27ae60', 'map-pin', 'active');");
        echo "[✓] Inserted default workflow phases\n";
    }
} else {
    echo "[✗] Error creating opm_workflow_phases: " . $mysqli->error . "\n";
}

// Workflow Shipments Table
$sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_shipments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` int NOT NULL,
  `current_phase_id` int DEFAULT '1',
  `phase_locked` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','in_progress','completed','hold','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `cargo_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo_weight` decimal(10,2) DEFAULT NULL,
  `cargo_value` decimal(12,2) DEFAULT NULL,
  `origin_port` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_port` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_destination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimated_arrival` date DEFAULT NULL,
  `actual_arrival` date DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipment_number` (`shipment_number`),
  KEY `client_id` (`client_id`),
  KEY `current_phase_id` (`current_phase_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "[✓] Created opm_workflow_shipments\n";
} else {
    echo "[✗] Error creating opm_workflow_shipments: " . $mysqli->error . "\n";
}

// Workflow Documents Table
$sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `document_type` enum('bill_of_lading','commercial_invoice','packing_list','declaration_document','customs_release_order','t1_form','shipping_order','custom_pre_alert_document','proof_of_delivery','loading_order','tracking_report','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `uploaded_by` int NOT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','received','approved','processed','archived','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `approved_by` int DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `document_type` (`document_type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "[✓] Created opm_workflow_documents\n";
} else {
    echo "[✗] Error creating opm_workflow_documents: " . $mysqli->error . "\n";
}

// Workflow Tasks Table
$sql = "CREATE TABLE IF NOT EXISTS `opm_workflow_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `phase_id` int NOT NULL,
  `task_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_description` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` int DEFAULT NULL,
  `assignment_type` enum('single','parallel','any_one') COLLATE utf8mb4_unicode_ci DEFAULT 'single',
  `assigned_by` int NOT NULL,
  `status` enum('pending','in_progress','completed','escalated','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `due_date` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `escalated_to` int DEFAULT NULL,
  `escalated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `phase_id` (`phase_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "[✓] Created opm_workflow_tasks\n";
} else {
    echo "[✗] Error creating opm_workflow_tasks: " . $mysqli->error . "\n";
}

// Trucks Table
$sql = "CREATE TABLE IF NOT EXISTS `opm_trucks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `truck_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_license` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `truck_capacity` decimal(10,2) DEFAULT NULL,
  `truck_type` enum('container','flatbed','tanker','refrigerated','general') COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `status` enum('available','assigned','in_transit','maintenance','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `current_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `truck_number` (`truck_number`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "[✓] Created opm_trucks\n";
} else {
    echo "[✗] Error creating opm_trucks: " . $mysqli->error . "\n";
}

// Truck Allocations Table
$sql = "CREATE TABLE IF NOT EXISTS `opm_truck_allocations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `truck_id` int NOT NULL,
  `allocated_by` int NOT NULL,
  `allocation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `loading_date` datetime DEFAULT NULL,
  `departure_date` datetime DEFAULT NULL,
  `estimated_delivery` datetime DEFAULT NULL,
  `actual_delivery` datetime DEFAULT NULL,
  `status` enum('allocated','loading','in_transit','delivered','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'allocated',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `truck_id` (`truck_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "[✓] Created opm_truck_allocations\n";
} else {
    echo "[✗] Error creating opm_truck_allocations: " . $mysqli->error . "\n";
}

// Tracking Reports Table
$sql = "CREATE TABLE IF NOT EXISTS `opm_tracking_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `truck_allocation_id` int DEFAULT NULL,
  `bl_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_update` text COLLATE utf8mb4_unicode_ci,
  `border_entry_date` datetime DEFAULT NULL,
  `border_exit_date` datetime DEFAULT NULL,
  `border_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` int NOT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `client_notified` tinyint(1) DEFAULT '0',
  `notification_sent_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `truck_allocation_id` (`truck_allocation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($mysqli->query($sql)) {
    echo "[✓] Created opm_tracking_reports\n";
} else {
    echo "[✗] Error creating opm_tracking_reports: " . $mysqli->error . "\n";
}

echo "\n=======================================================\n";
echo "ALL WORKFLOW TABLES CREATED SUCCESSFULLY\n";
echo "=======================================================\n\n";

// Show all workflow tables
$result = $mysqli->query("SHOW TABLES LIKE '%workflow%' OR SHOW TABLES LIKE '%truck%' OR SHOW TABLES LIKE '%tracking%' OR SHOW TABLES LIKE '%shipment%'");
echo "Total workflow tables created: " . $result->num_rows . "\n\n";

$mysqli->close();
?>
