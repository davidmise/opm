-- Department Enhancement Migration Script for Overland Project Manager (OPM)
-- This script adds advanced department features as requested

-- 1. Add missing fields to departments table
ALTER TABLE `overland_pm`.`opm_departments`
ADD COLUMN `icon` VARCHAR(50) DEFAULT 'ti ti-building' AFTER `color`,
ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `icon`,
ADD COLUMN `head_user_id` INT NULL DEFAULT NULL AFTER `is_active`,
ADD INDEX `head_user_id` (`head_user_id`);

-- 2. Create user_departments junction table for multi-department support
CREATE TABLE IF NOT EXISTS `overland_pm`.`opm_user_departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_dept_unique` (`user_id`, `department_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- 3. Migrate existing single-department assignments to multi-department
INSERT INTO `overland_pm`.`opm_user_departments` (`user_id`, `department_id`, `is_primary`)
SELECT 
    user_id, 
    department_id, 
    1 AS is_primary
FROM `overland_pm`.`opm_team_member_job_info`
WHERE department_id IS NOT NULL 
AND department_id != 0;

-- 4. Add default IT, Accounts, Finance, Tracking, Operations, HR departments
INSERT INTO `overland_pm`.`opm_departments` (`title`, `description`, `color`, `icon`, `created_by`) VALUES 
('IT', 'Information Technology department', '#607D8B', 'ti ti-device-laptop', 1),
('Accounts', 'Accounting and financial operations', '#795548', 'ti ti-calculator', 1),
('Finance', 'Financial planning and analysis', '#4CAF50', 'ti ti-chart-line', 1),
('Tracking', 'Project tracking and monitoring', '#FF5722', 'ti ti-map-pin', 1),
('Operations', 'Daily operations and workflow management', '#9C27B0', 'ti ti-settings', 1),
('HR', 'Human Resources department', '#E91E63', 'ti ti-users', 1)
ON DUPLICATE KEY UPDATE 
title = VALUES(title),
description = VALUES(description),
color = VALUES(color),
icon = VALUES(icon);

-- 5. Create department_permissions table for granular access control
CREATE TABLE IF NOT EXISTS `overland_pm`.`opm_department_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `permission_type` enum('view','manage','admin') DEFAULT 'view',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_dept_perm_unique` (`user_id`, `department_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- 6. Add department filters to various modules
ALTER TABLE `overland_pm`.`opm_projects` 
ADD COLUMN `department_filter_enabled` TINYINT(1) DEFAULT 1 AFTER `department_id`;

ALTER TABLE `overland_pm`.`opm_tasks` 
ADD COLUMN `department_id` INT NULL DEFAULT NULL AFTER `project_id`,
ADD INDEX `department_id` (`department_id`);

ALTER TABLE `overland_pm`.`opm_expenses` 
ADD COLUMN `department_id` INT NULL DEFAULT NULL AFTER `project_id`,
ADD INDEX `department_id` (`department_id`);

ALTER TABLE `overland_pm`.`opm_tickets` 
ADD COLUMN `department_id` INT NULL DEFAULT NULL AFTER `client_id`,
ADD INDEX `department_id` (`department_id`);

-- 7. Update settings for new department features
INSERT INTO `overland_pm`.`opm_settings` (`setting_name`, `setting_value`, `type`) VALUES 
('enable_department_dashboards', '1', 'app'),
('enable_multi_department_users', '1', 'app'),
('enable_department_permissions', '1', 'app'),
('default_department_permissions', 'view', 'app')
ON DUPLICATE KEY UPDATE 
setting_value = VALUES(setting_value);

-- 8. Create sample department heads (assign first admin to IT)
UPDATE `overland_pm`.`opm_departments` 
SET `head_user_id` = (
    SELECT u.id 
    FROM `overland_pm`.`opm_users` u 
    WHERE u.is_admin = 1 
    AND u.deleted = 0 
    LIMIT 1
) 
WHERE `title` = 'IT' AND `head_user_id` IS NULL;

-- 9. Ensure department module toggle exists and is enabled by default
INSERT INTO `overland_pm`.`opm_settings` (setting_name, setting_value, type, deleted)
SELECT 'module_departments', '1', 'app', '0'
WHERE NOT EXISTS (
    SELECT 1 FROM `overland_pm`.`opm_settings`
    WHERE setting_name = 'module_departments' AND deleted = 0
);

-- 10. Verify the module is active
SELECT * FROM `overland_pm`.`opm_settings` 
WHERE setting_name = 'module_departments' AND deleted = 0;
