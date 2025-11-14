-- ===============================================
-- DEPARTMENT MODULE DATABASE SCHEMA
-- Generated on: November 14, 2025
-- ===============================================

-- Drop existing tables if they exist (in reverse dependency order)
DROP TABLE IF EXISTS `opm_user_departments`;
DROP TABLE IF EXISTS `opm_department_permissions`;
DROP TABLE IF EXISTS `opm_department_templates`;
DROP TABLE IF EXISTS `opm_departments`;

-- ===============================================
-- DEPARTMENTS TABLE
-- ===============================================
CREATE TABLE `opm_departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text,
  `department_head` int DEFAULT NULL,
  `parent_department` int DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `department_code` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_code` (`department_code`),
  KEY `idx_parent_department` (`parent_department`),
  KEY `idx_department_head` (`department_head`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- DEPARTMENT PERMISSIONS TABLE
-- ===============================================
CREATE TABLE `opm_department_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `department_id` int NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `permission_value` text,
  `permission_type` enum('read','write','delete','admin') DEFAULT 'read',
  `module_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_department_id` (`department_id`),
  KEY `idx_permission_name` (`permission_name`),
  KEY `idx_module_name` (`module_name`),
  CONSTRAINT `fk_dept_permissions_department` FOREIGN KEY (`department_id`) REFERENCES `opm_departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- DEPARTMENT TEMPLATES TABLE
-- ===============================================
CREATE TABLE `opm_department_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(200) NOT NULL,
  `description` text,
  `permissions_config` json,
  `default_budget` decimal(15,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_template_name` (`template_name`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- USER DEPARTMENTS TABLE
-- ===============================================
CREATE TABLE `opm_user_departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `role_in_department` varchar(100) DEFAULT 'member',
  `is_primary` tinyint(1) DEFAULT '0',
  `access_level` enum('read','write','manage','admin') DEFAULT 'read',
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_department` (`user_id`,`department_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_department_id` (`department_id`),
  KEY `idx_is_primary` (`is_primary`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_assigned_by` (`assigned_by`),
  CONSTRAINT `fk_user_departments_department` FOREIGN KEY (`department_id`) REFERENCES `opm_departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_departments_user` FOREIGN KEY (`user_id`) REFERENCES `opm_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================
-- SAMPLE DATA INSERTION
-- ===============================================

-- Insert default departments
INSERT INTO `opm_departments` (`title`, `description`, `department_code`, `is_active`, `sort_order`) VALUES
('Management', 'Executive Management and Administration', 'MGMT', 1, 1),
('Human Resources', 'HR Department for staff management', 'HR', 1, 2),
('Finance', 'Financial operations and accounting', 'FIN', 1, 3),
('Operations', 'Daily operations management', 'OPS', 1, 4),
('IT Support', 'Information Technology and Support', 'IT', 1, 5),
('Sales', 'Sales and Customer Relations', 'SALES', 1, 6),
('Marketing', 'Marketing and Promotions', 'MKT', 1, 7),
('Logistics', 'Shipping and Logistics Coordination', 'LOG', 1, 8),
('Quality Assurance', 'Quality Control and Assurance', 'QA', 1, 9),
('Customer Service', 'Customer Support and Service', 'CS', 1, 10),
('Legal', 'Legal Affairs and Compliance', 'LEGAL', 1, 11),
('Procurement', 'Purchasing and Vendor Management', 'PROC', 1, 12),
('Warehouse', 'Warehouse Operations', 'WH', 1, 13),
('Security', 'Security and Safety Management', 'SEC', 1, 14),
('Training', 'Staff Training and Development', 'TRN', 1, 15),
('Research & Development', 'Research and Development', 'RND', 1, 16),
('Maintenance', 'Equipment and Facility Maintenance', 'MAINT', 1, 17);

-- Insert default department permissions
INSERT INTO `opm_department_permissions` (`department_id`, `permission_name`, `permission_value`, `permission_type`, `module_name`) VALUES
(1, 'can_manage_all', '1', 'admin', 'system'),
(1, 'can_view_reports', '1', 'read', 'reports'),
(1, 'can_manage_users', '1', 'admin', 'users'),
(2, 'can_manage_staff', '1', 'write', 'hr'),
(2, 'can_view_payroll', '1', 'read', 'payroll'),
(3, 'can_manage_finances', '1', 'write', 'finance'),
(3, 'can_view_budgets', '1', 'read', 'budget'),
(4, 'can_manage_operations', '1', 'write', 'operations'),
(5, 'can_manage_system', '1', 'admin', 'system'),
(6, 'can_manage_clients', '1', 'write', 'clients'),
(8, 'can_manage_workflow', '1', 'write', 'workflow');

-- ===============================================
-- INDEXES FOR PERFORMANCE
-- ===============================================

-- Additional indexes for better performance
CREATE INDEX `idx_departments_title` ON `opm_departments` (`title`);
CREATE INDEX `idx_departments_sort_order` ON `opm_departments` (`sort_order`);
CREATE INDEX `idx_dept_permissions_type` ON `opm_department_permissions` (`permission_type`);
CREATE INDEX `idx_user_dept_role` ON `opm_user_departments` (`role_in_department`);
CREATE INDEX `idx_user_dept_access` ON `opm_user_departments` (`access_level`);

-- ===============================================
-- COMMENTS FOR DOCUMENTATION
-- ===============================================

ALTER TABLE `opm_departments` 
COMMENT = 'Stores department information and hierarchy';

ALTER TABLE `opm_department_permissions` 
COMMENT = 'Stores permissions assigned to departments for different modules';

ALTER TABLE `opm_department_templates` 
COMMENT = 'Template configurations for creating new departments';

ALTER TABLE `opm_user_departments` 
COMMENT = 'Links users to departments with specific roles and access levels';