-- Workflow Tables for Clearing & Transport Management System

-- Workflow phases table
CREATE TABLE IF NOT EXISTS `omp_workflow_phases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `phase_order` int NOT NULL DEFAULT '1',
  `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '#3498db',
  `status` enum('active','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Insert default workflow phases
INSERT INTO `omp_workflow_phases` (`id`, `name`, `description`, `phase_order`, `color`, `status`, `created_at`, `deleted`) VALUES
(1, 'Clearing & Documentation Intake', 'Initial document submission and file creation phase', 1, '#e74c3c', 'active', NOW(), 0),
(2, 'Regulatory & Release Processing', 'TRA portal processing and shipping line release', 2, '#f39c12', 'active', NOW(), 0),
(3, 'Internal Review & Handover', 'Document review and operations handover', 3, '#3498db', 'active', NOW(), 0),
(4, 'Transport Operations & Loading', 'Truck allocation and cargo loading', 4, '#9b59b6', 'active', NOW(), 0),
(5, 'Tracking', 'Shipment tracking and client communication', 5, '#27ae60', 'active', NOW(), 0);

-- Shipments table
CREATE TABLE IF NOT EXISTS `omp_shipments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_number` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL UNIQUE,
  `client_id` int NOT NULL,
  `current_phase_id` int DEFAULT '1',
  `status` enum('pending','in_progress','completed','hold','cancelled') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'medium',
  `cargo_type` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cargo_weight` decimal(10,2) DEFAULT NULL,
  `cargo_value` decimal(12,2) DEFAULT NULL,
  `origin_port` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `destination_port` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `final_destination` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estimated_arrival` date DEFAULT NULL,
  `actual_arrival` date DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_number` (`shipment_number`),
  KEY `client_id` (`client_id`),
  KEY `current_phase_id` (`current_phase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Documents table
CREATE TABLE IF NOT EXISTS `omp_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `document_type` enum('bill_of_lading','commercial_invoice','packing_list','declaration_document','customs_release_order','t1_form','shipping_order','custom_pre_alert_document','proof_of_delivery','other') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `document_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_path` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `uploaded_by` int NOT NULL,
  `uploaded_at` datetime DEFAULT NULL,
  `status` enum('pending','received','processed','archived') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `document_type` (`document_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Workflow tasks table
CREATE TABLE IF NOT EXISTS `omp_workflow_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `phase_id` int NOT NULL,
  `task_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `task_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `assigned_to` int NOT NULL,
  `assigned_by` int NOT NULL,
  `status` enum('pending','in_progress','completed','escalated','cancelled') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'medium',
  `due_date` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `escalated_to` int DEFAULT NULL,
  `escalated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `phase_id` (`phase_id`),
  KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Trucks table
CREATE TABLE IF NOT EXISTS `omp_trucks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `truck_number` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL UNIQUE,
  `driver_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `driver_phone` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `driver_license` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `truck_capacity` decimal(10,2) DEFAULT NULL,
  `truck_type` enum('container','flatbed','tanker','refrigerated','general') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'general',
  `status` enum('available','assigned','in_transit','maintenance','inactive') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'available',
  `current_location` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `truck_number` (`truck_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Truck allocations table
CREATE TABLE IF NOT EXISTS `omp_truck_allocations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `truck_id` int NOT NULL,
  `allocated_by` int NOT NULL,
  `allocation_date` datetime DEFAULT NULL,
  `loading_date` datetime DEFAULT NULL,
  `departure_date` datetime DEFAULT NULL,
  `estimated_delivery` datetime DEFAULT NULL,
  `actual_delivery` datetime DEFAULT NULL,
  `status` enum('allocated','loading','in_transit','delivered','cancelled') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'allocated',
  `notes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `truck_id` (`truck_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Tracking reports table
CREATE TABLE IF NOT EXISTS `omp_tracking_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `truck_allocation_id` int DEFAULT NULL,
  `bl_number` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `current_location` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status_update` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `border_entry_date` datetime DEFAULT NULL,
  `border_exit_date` datetime DEFAULT NULL,
  `border_location` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `updated_by` int NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `client_notified` tinyint(1) DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `truck_allocation_id` (`truck_allocation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;