-- ===============================================
-- WORKFLOW MODULE DATABASE SCHEMA
-- Generated on: November 14, 2025
-- ===============================================

-- Drop existing tables if they exist (in reverse dependency order)
DROP TABLE IF EXISTS `opm_workflow_tracking`;
DROP TABLE IF EXISTS `opm_workflow_truck_allocations`;
DROP TABLE IF EXISTS `opm_workflow_tasks`;
DROP TABLE IF EXISTS `opm_workflow_documents`;
DROP TABLE IF EXISTS `opm_workflow_shipments`;
DROP TABLE IF EXISTS `opm_workflow_trucks`;

-- ===============================================
-- WORKFLOW TRUCKS TABLE
-- ===============================================
CREATE TABLE `opm_workflow_trucks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `truck_number` varchar(50) NOT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `driver_phone` varchar(20) DEFAULT NULL,
  `truck_capacity` decimal(8,2) DEFAULT '0.00',
  `truck_type` enum('container','flatbed','tanker','refrigerated') DEFAULT 'container',
  `status` enum('available','assigned','in_transit','maintenance') DEFAULT 'available',
  `current_location` varchar(200) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `truck_number` (`truck_number`),
  KEY `idx_status` (`status`),
  KEY `idx_truck_type` (`truck_type`),
  KEY `idx_current_location` (`current_location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- WORKFLOW SHIPMENTS TABLE
-- ===============================================
CREATE TABLE `opm_workflow_shipments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_number` varchar(50) NOT NULL,
  `client_id` int DEFAULT NULL,
  `cargo_type` varchar(100) DEFAULT NULL,
  `cargo_weight` decimal(10,2) DEFAULT '0.00',
  `cargo_value` decimal(12,2) DEFAULT '0.00',
  `origin_port` varchar(100) DEFAULT NULL,
  `destination_port` varchar(100) DEFAULT NULL,
  `final_destination` varchar(200) DEFAULT NULL,
  `estimated_arrival` datetime DEFAULT NULL,
  `actual_arrival` datetime DEFAULT NULL,
  `current_phase` enum('clearing_intake','regulatory_processing','internal_review','transport_loading','tracking') DEFAULT 'clearing_intake',
  `assigned_to` int DEFAULT NULL,
  `assigned_task_id` int DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipment_number` (`shipment_number`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_current_phase` (`current_phase`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_status` (`status`),
  KEY `idx_estimated_arrival` (`estimated_arrival`),
  CONSTRAINT `fk_shipments_client` FOREIGN KEY (`client_id`) REFERENCES `opm_clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_shipments_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- WORKFLOW DOCUMENTS TABLE
-- ===============================================
CREATE TABLE `opm_workflow_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `document_name` varchar(200) NOT NULL,
  `document_type` enum('bill_of_lading','commercial_invoice','packing_list','customs_declaration','tax_certificate','permits','inspection_report','other') DEFAULT 'other',
  `file_path` varchar(500) DEFAULT NULL,
  `uploaded_by` int DEFAULT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `assigned_to` int DEFAULT NULL,
  `assigned_task_id` int DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shipment_id` (`shipment_id`),
  KEY `idx_document_type` (`document_type`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_status` (`status`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_upload_date` (`upload_date`),
  CONSTRAINT `fk_documents_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_documents_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_documents_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- WORKFLOW TASKS TABLE
-- ===============================================
CREATE TABLE `opm_workflow_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `task_id` int DEFAULT NULL,
  `workflow_type` enum('shipment','document','tracking','truck') DEFAULT 'shipment',
  `reference_id` int DEFAULT NULL,
  `shipment_id` int DEFAULT NULL,
  `task_name` varchar(200) NOT NULL,
  `task_description` text,
  `assigned_to` int DEFAULT NULL,
  `phase` enum('clearing_intake','regulatory_processing','internal_review','transport_loading','tracking') DEFAULT 'clearing_intake',
  `status` enum('pending','in_progress','completed','on_hold') DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `due_date` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_task_id` (`task_id`),
  KEY `idx_workflow_type` (`workflow_type`),
  KEY `idx_shipment_id` (`shipment_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_phase` (`phase`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`),
  KEY `idx_due_date` (`due_date`),
  CONSTRAINT `fk_workflow_tasks_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_workflow_tasks_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_workflow_tasks_main` FOREIGN KEY (`task_id`) REFERENCES `opm_tasks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- WORKFLOW TRACKING TABLE
-- ===============================================
CREATE TABLE `opm_workflow_tracking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `status_update` text,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `assigned_task_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shipment_id` (`shipment_id`),
  KEY `idx_updated_by` (`updated_by`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_timestamp` (`timestamp`),
  KEY `idx_location` (`location`),
  CONSTRAINT `fk_tracking_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tracking_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_tracking_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `opm_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- WORKFLOW TRUCK ALLOCATIONS TABLE
-- ===============================================
CREATE TABLE `opm_workflow_truck_allocations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `truck_id` int NOT NULL,
  `allocated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `departure_time` datetime DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `status` enum('allocated','in_transit','delivered','cancelled') DEFAULT 'allocated',
  `mileage_start` decimal(8,2) DEFAULT '0.00',
  `mileage_end` decimal(8,2) DEFAULT '0.00',
  `fuel_cost` decimal(8,2) DEFAULT '0.00',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shipment_id` (`shipment_id`),
  KEY `idx_truck_id` (`truck_id`),
  KEY `idx_status` (`status`),
  KEY `idx_allocated_at` (`allocated_at`),
  KEY `idx_departure_time` (`departure_time`),
  CONSTRAINT `fk_allocations_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `opm_workflow_shipments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_allocations_truck` FOREIGN KEY (`truck_id`) REFERENCES `opm_workflow_trucks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- SAMPLE DATA INSERTION
-- ===============================================

-- Insert sample trucks
INSERT INTO `opm_workflow_trucks` (`truck_number`, `driver_name`, `driver_phone`, `truck_capacity`, `truck_type`, `status`, `current_location`) VALUES
('TRK-001', 'John Smith', '+255-789-123456', 25.00, 'container', 'available', 'Dar es Salaam Port'),
('TRK-002', 'Michael Johnson', '+255-789-234567', 30.00, 'flatbed', 'available', 'Warehouse A'),
('TRK-003', 'David Wilson', '+255-789-345678', 20.00, 'refrigerated', 'maintenance', 'Service Center'),
('TRK-004', 'James Brown', '+255-789-456789', 35.00, 'container', 'available', 'Mombasa Port'),
('TRK-005', 'Robert Davis', '+255-789-567890', 28.00, 'tanker', 'assigned', 'En Route to Nairobi');

-- Insert sample shipments
INSERT INTO `opm_workflow_shipments` (`shipment_number`, `client_id`, `cargo_type`, `cargo_weight`, `cargo_value`, `origin_port`, `destination_port`, `final_destination`, `estimated_arrival`, `current_phase`, `status`) VALUES
('SHP-2025-001', 1, 'Electronics', 2500.50, 125000.00, 'Dar es Salaam', 'Mombasa', 'Nairobi', '2025-11-20 10:00:00', 'clearing_intake', 'active'),
('SHP-2025-002', 2, 'Textiles', 1800.25, 75000.00, 'Dar es Salaam', 'Kilifi', 'Mombasa', '2025-11-18 14:30:00', 'regulatory_processing', 'active'),
('SHP-2025-003', 3, 'Machinery', 5200.00, 350000.00, 'Mombasa', 'Dar es Salaam', 'Dodoma', '2025-11-25 09:15:00', 'internal_review', 'active'),
('SHP-2025-004', 1, 'Food Products', 3100.75, 85000.00, 'Tanga', 'Mombasa', 'Nakuru', '2025-11-22 16:45:00', 'transport_loading', 'active'),
('SHP-2025-005', 4, 'Automotive Parts', 1950.30, 190000.00, 'Dar es Salaam', 'Mombasa', 'Kampala', '2025-11-28 11:20:00', 'tracking', 'active');

-- Insert sample documents
INSERT INTO `opm_workflow_documents` (`shipment_id`, `document_name`, `document_type`, `uploaded_by`, `status`) VALUES
(1, 'Bill of Lading - SHP-2025-001.pdf', 'bill_of_lading', 1, 'approved'),
(1, 'Commercial Invoice - SHP-2025-001.pdf', 'commercial_invoice', 1, 'pending'),
(1, 'Packing List - SHP-2025-001.pdf', 'packing_list', 1, 'approved'),
(2, 'Bill of Lading - SHP-2025-002.pdf', 'bill_of_lading', 2, 'approved'),
(2, 'Customs Declaration - SHP-2025-002.pdf', 'customs_declaration', 2, 'pending'),
(3, 'Commercial Invoice - SHP-2025-003.pdf', 'commercial_invoice', 1, 'rejected'),
(4, 'Inspection Report - SHP-2025-004.pdf', 'inspection_report', 3, 'approved'),
(5, 'Tax Certificate - SHP-2025-005.pdf', 'tax_certificate', 2, 'pending');

-- Insert sample workflow tasks
INSERT INTO `opm_workflow_tasks` (`workflow_type`, `shipment_id`, `task_name`, `task_description`, `assigned_to`, `phase`, `status`, `priority`, `due_date`) VALUES
('shipment', 1, 'Document Verification', 'Verify all shipping documents for SHP-2025-001', 1, 'clearing_intake', 'in_progress', 'high', '2025-11-16 17:00:00'),
('shipment', 1, 'Customs Clearance', 'Process customs clearance for electronics shipment', 2, 'regulatory_processing', 'pending', 'high', '2025-11-17 12:00:00'),
('document', 2, 'Invoice Review', 'Review commercial invoice for accuracy', 1, 'internal_review', 'pending', 'medium', '2025-11-16 15:00:00'),
('shipment', 3, 'Quality Inspection', 'Conduct quality inspection for machinery', 3, 'internal_review', 'pending', 'urgent', '2025-11-17 10:00:00'),
('truck', 4, 'Transport Assignment', 'Assign truck for food products shipment', 2, 'transport_loading', 'pending', 'medium', '2025-11-18 08:00:00'),
('tracking', 5, 'Location Update', 'Update current location of shipment', 3, 'tracking', 'completed', 'low', '2025-11-15 14:00:00');

-- Insert sample tracking data
INSERT INTO `opm_workflow_tracking` (`shipment_id`, `location`, `status_update`, `latitude`, `longitude`, `updated_by`) VALUES
(1, 'Dar es Salaam Port', 'Shipment received at port, awaiting customs clearance', -6.8235, 39.2695, 1),
(2, 'Customs Office - Dar es Salaam', 'Documents submitted for regulatory processing', -6.8160, 39.2803, 2),
(3, 'Warehouse B - Mombasa', 'Cargo unloaded and stored, pending quality inspection', -4.0435, 39.6682, 1),
(4, 'Loading Bay A', 'Cargo loaded onto transport truck TRK-002', -6.8000, 39.2500, 3),
(5, 'Highway A104 - En Route', 'In transit to final destination, ETA 2 hours', -1.2921, 36.8219, 2);

-- Insert sample truck allocations
INSERT INTO `opm_workflow_truck_allocations` (`shipment_id`, `truck_id`, `departure_time`, `status`, `mileage_start`, `notes`) VALUES
(1, 1, '2025-11-17 08:00:00', 'allocated', 45230.50, 'Scheduled for electronics shipment pickup'),
(2, 2, NULL, 'allocated', 38750.25, 'Waiting for regulatory approval'),
(4, 2, '2025-11-16 14:30:00', 'in_transit', 38850.25, 'Food products loaded, en route to destination'),
(5, 5, '2025-11-15 10:00:00', 'delivered', 62100.00, 'Automotive parts successfully delivered');

-- ===============================================
-- TRIGGERS FOR AUTOMATION
-- ===============================================

-- Trigger to automatically update shipment phase when tasks are completed
DELIMITER ;;
CREATE TRIGGER `tr_update_shipment_phase` 
AFTER UPDATE ON `opm_workflow_tasks` 
FOR EACH ROW 
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        -- Update shipment phase progression logic can be added here
        UPDATE `opm_workflow_shipments` 
        SET `updated_at` = NOW() 
        WHERE `id` = NEW.shipment_id;
    END IF;
END;;
DELIMITER ;

-- Trigger to log tracking updates
DELIMITER ;;
CREATE TRIGGER `tr_log_tracking_update` 
AFTER INSERT ON `opm_workflow_tracking` 
FOR EACH ROW 
BEGIN
    UPDATE `opm_workflow_shipments` 
    SET `updated_at` = NOW() 
    WHERE `id` = NEW.shipment_id;
END;;
DELIMITER ;

-- ===============================================
-- ADDITIONAL INDEXES FOR PERFORMANCE
-- ===============================================

-- Composite indexes for common queries
CREATE INDEX `idx_shipments_client_status` ON `opm_workflow_shipments` (`client_id`, `status`);
CREATE INDEX `idx_shipments_phase_status` ON `opm_workflow_shipments` (`current_phase`, `status`);
CREATE INDEX `idx_tasks_shipment_status` ON `opm_workflow_tasks` (`shipment_id`, `status`);
CREATE INDEX `idx_tasks_assigned_status` ON `opm_workflow_tasks` (`assigned_to`, `status`);
CREATE INDEX `idx_documents_shipment_status` ON `opm_workflow_documents` (`shipment_id`, `status`);
CREATE INDEX `idx_tracking_shipment_timestamp` ON `opm_workflow_tracking` (`shipment_id`, `timestamp`);
CREATE INDEX `idx_allocations_truck_status` ON `opm_workflow_truck_allocations` (`truck_id`, `status`);

-- ===============================================
-- VIEWS FOR REPORTING
-- ===============================================

-- View for shipment overview
CREATE VIEW `view_shipment_overview` AS
SELECT 
    s.id,
    s.shipment_number,
    c.company_name as client_name,
    s.cargo_type,
    s.cargo_weight,
    s.origin_port,
    s.destination_port,
    s.current_phase,
    s.status,
    s.estimated_arrival,
    CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name,
    s.created_at
FROM `opm_workflow_shipments` s
LEFT JOIN `opm_clients` c ON s.client_id = c.id
LEFT JOIN `opm_users` u ON s.assigned_to = u.id;

-- View for active workflow tasks
CREATE VIEW `view_active_tasks` AS
SELECT 
    t.id,
    t.task_name,
    t.task_description,
    s.shipment_number,
    t.phase,
    t.status,
    t.priority,
    t.due_date,
    CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name,
    t.created_at
FROM `opm_workflow_tasks` t
LEFT JOIN `opm_workflow_shipments` s ON t.shipment_id = s.id
LEFT JOIN `opm_users` u ON t.assigned_to = u.id
WHERE t.status IN ('pending', 'in_progress');

-- ===============================================
-- COMMENTS FOR DOCUMENTATION
-- ===============================================

ALTER TABLE `opm_workflow_shipments` 
COMMENT = 'Main table for tracking shipment workflow and progress';

ALTER TABLE `opm_workflow_documents` 
COMMENT = 'Stores documents associated with shipments and their approval status';

ALTER TABLE `opm_workflow_tasks` 
COMMENT = 'Workflow tasks assigned to users for various shipment processes';

ALTER TABLE `opm_workflow_tracking` 
COMMENT = 'Real-time tracking information and location updates for shipments';

ALTER TABLE `opm_workflow_trucks` 
COMMENT = 'Fleet management for trucks used in transportation';

ALTER TABLE `opm_workflow_truck_allocations` 
COMMENT = 'Allocation and assignment of trucks to specific shipments';