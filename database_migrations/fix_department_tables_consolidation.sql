-- ===================================================================
-- DEPARTMENT TABLES CONSOLIDATION AND SYNCHRONIZATION SCRIPT
-- ===================================================================
-- This script fixes the department table inconsistencies and creates
-- a unified department system with proper foreign key relationships
-- 
-- Issues Fixed:
-- 1. Multiple conflicting department tables
-- 2. Missing user_departments junction table
-- 3. Missing department_id in tasks table
-- 4. Inconsistent table prefixes
-- ===================================================================

-- Step 1: Ensure main departments table exists with proper structure
-- ===================================================================
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '#4CAF50',
  `icon` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'grid',
  `head_user_id` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `head_user_id` (`head_user_id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Step 2: Migrate data from opm_departments if it exists
-- ===================================================================
INSERT IGNORE INTO `departments` (`id`, `title`, `description`, `color`, `icon`, `head_user_id`, `is_active`, `created_by`, `created_at`)
SELECT `id`, `title`, `description`, `color`, 
       COALESCE(`icon`, 'grid') as icon,
       `head_user_id`, 
       COALESCE(`is_active`, 1) as is_active,
       `created_by`, `created_at`
FROM `opm_departments` 
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'opm_departments');

-- Step 3: Ensure default departments exist
-- ===================================================================
INSERT IGNORE INTO `departments` (`id`, `title`, `description`, `color`, `icon`, `created_by`) VALUES 
(1, 'General', 'Default department for all members', '#4CAF50', 'grid', 1),
(2, 'Engineering', 'Software development and technical team', '#2196F3', 'code', 1),
(3, 'Marketing', 'Marketing and communications team', '#FF9800', 'megaphone', 1),
(4, 'Sales', 'Sales and business development team', '#9C27B0', 'trending-up', 1),
(5, 'Support', 'Customer support and service team', '#00BCD4', 'headphones', 1),
(6, 'Management', 'Executive and management team', '#F44336', 'users', 1);

-- Step 4: Create user_departments junction table for multi-department support
-- ===================================================================
CREATE TABLE IF NOT EXISTS `user_departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_department` (`user_id`, `department_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  KEY `is_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Step 5: Migrate existing user-department relationships
-- ===================================================================
-- From team_member_job_info table
INSERT IGNORE INTO `user_departments` (`user_id`, `department_id`, `is_primary`)
SELECT `user_id`, `department_id`, 1 as is_primary
FROM `team_member_job_info` 
WHERE `department_id` IS NOT NULL 
AND `department_id` > 0
AND NOT EXISTS (
    SELECT 1 FROM `user_departments` 
    WHERE `user_departments`.`user_id` = `team_member_job_info`.`user_id` 
    AND `user_departments`.`department_id` = `team_member_job_info`.`department_id`
);

-- From opm_user_departments if it exists
INSERT IGNORE INTO `user_departments` (`user_id`, `department_id`, `is_primary`)
SELECT `user_id`, `department_id`, `is_primary`
FROM `opm_user_departments` 
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'opm_user_departments')
AND NOT EXISTS (
    SELECT 1 FROM `user_departments` 
    WHERE `user_departments`.`user_id` = `omp_user_departments`.`user_id` 
    AND `user_departments`.`department_id` = `opm_user_departments`.`department_id`
);

-- Step 6: Add department_id to tasks table for better organization
-- ===================================================================
ALTER TABLE `tasks` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `project_id`,
ADD KEY `department_id` (`department_id`);

-- Update tasks department_id from their projects
UPDATE `tasks` t
INNER JOIN `projects` p ON t.`project_id` = p.`id`
SET t.`department_id` = p.`department_id`
WHERE p.`department_id` IS NOT NULL AND t.`department_id` IS NULL;

-- Step 7: Add department_id to other relevant tables
-- ===================================================================

-- Add to announcements if not exists
ALTER TABLE `announcements` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `created_by`,
ADD KEY `department_id` (`department_id`);

-- Add to client_groups if not exists  
ALTER TABLE `client_groups` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `title`,
ADD KEY `department_id` (`department_id`);

-- Add to custom_fields if not exists
ALTER TABLE `custom_fields` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `context`,
ADD KEY `department_id` (`department_id`);

-- Step 8: Create department permissions table for advanced access control
-- ===================================================================
CREATE TABLE IF NOT EXISTS `department_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `permission_type` enum('view','manage','admin') NOT NULL DEFAULT 'view',
  `granted_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_dept_permission` (`user_id`, `department_id`, `permission_type`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  KEY `permission_type` (`permission_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Step 9: Create foreign key constraints (optional - enable if you want strict referential integrity)
-- ===================================================================
-- Uncomment these if you want to enforce foreign key relationships

-- ALTER TABLE `user_departments` 
-- ADD CONSTRAINT `fk_user_departments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `fk_user_departments_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `team_member_job_info` 
-- ADD CONSTRAINT `fk_team_member_job_info_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `projects` 
-- ADD CONSTRAINT `fk_projects_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `tasks` 
-- ADD CONSTRAINT `fk_tasks_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `department_permissions` 
-- ADD CONSTRAINT `fk_department_permissions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `fk_department_permissions_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

-- Step 10: Clean up - Remove redundant tables (DANGEROUS - Backup first!)
-- ===================================================================
-- WARNING: Only run these if you're sure opm_departments and opm_user_departments are no longer needed

-- DROP TABLE IF EXISTS `opm_departments`;
-- DROP TABLE IF EXISTS `opm_user_departments`;

-- ===================================================================
-- VERIFICATION QUERIES
-- ===================================================================
-- Run these to verify the migration worked correctly:

-- SELECT 'Departments Count' as Info, COUNT(*) as Count FROM departments;
-- SELECT 'User Departments Count' as Info, COUNT(*) as Count FROM user_departments;  
-- SELECT 'Projects with Departments' as Info, COUNT(*) as Count FROM projects WHERE department_id IS NOT NULL;
-- SELECT 'Tasks with Departments' as Info, COUNT(*) as Count FROM tasks WHERE department_id IS NOT NULL;

-- ===================================================================
-- COMPLETION MESSAGE
-- ===================================================================
-- Department table consolidation completed successfully!
-- 
-- Tables unified:
-- ✅ departments (main table)
-- ✅ user_departments (junction table)  
-- ✅ tasks.department_id (added)
-- ✅ Various tables enhanced with department_id
--
-- Next steps:
-- 1. Update your models to use the unified 'departments' table
-- 2. Test the team member form submission
-- 3. Verify dropdown population works correctly
-- 4. Consider enabling foreign key constraints
-- ===================================================================































-- Step 1: Create departments table
-- ===================================================================
-- CREATE TABLE IF NOT EXISTS `opm_departments` (
--   `id` int NOT NULL AUTO_INCREMENT,
--   `title` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
--   `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
--   `color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '#4CAF50',
--   `icon` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'grid',
--   `head_user_id` int DEFAULT NULL,
--   `is_active` tinyint(1) NOT NULL DEFAULT '1',
--   `created_by` int DEFAULT NULL,
--   `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   `deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`),
--   KEY `created_by` (`created_by`),
--   KEY `head_user_id` (`head_user_id`),
--   KEY `is_active` (`is_active`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- -- Step 2: Migrate data from opm_departments if it exists
-- -- ===================================================================
-- INSERT IGNORE INTO `opm_departments` (`id`, `title`, `description`, `color`, `icon`, `head_user_id`, `is_active`, `created_by`, `created_at`)
-- SELECT `id`, `title`, `description`, `color`, 
--        COALESCE(`icon`, 'grid') as icon,
--        `head_user_id`, 
--        COALESCE(`is_active`, 1) as is_active,
--        `created_by`, `created_at`
-- FROM `opm_departments` 
-- WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'opm_departments');

-- -- Step 3: Ensure default departments exist
-- -- ===================================================================
-- INSERT IGNORE INTO `opm_departments` (`id`, `title`, `description`, `color`, `icon`, `created_by`) VALUES 
-- (1, 'General', 'Default department for all members', '#4CAF50', 'grid', 1),
-- (2, 'Engineering', 'Software development and technical team', '#2196F3', 'code', 1),
-- (3, 'Marketing', 'Marketing and communications team', '#FF9800', 'megaphone', 1),
-- (4, 'Sales', 'Sales and business development team', '#9C27B0', 'trending-up', 1),
-- (5, 'Support', 'Customer support and service team', '#00BCD4', 'headphones', 1),
-- (6, 'Management', 'Executive and management team', '#F44336', 'users', 1);

-- -- Step 4: Create user_departments junction table for multi-department support
-- -- ===================================================================
-- CREATE TABLE IF NOT EXISTS `opm_user_departments` (
--   `id` int NOT NULL AUTO_INCREMENT,
--   `user_id` int NOT NULL,
--   `department_id` int NOT NULL,
--   `is_primary` tinyint(1) NOT NULL DEFAULT '0',
--   `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `unique_user_department` (`user_id`, `department_id`),
--   KEY `user_id` (`user_id`),
--   KEY `department_id` (`department_id`),
--   KEY `is_primary` (`is_primary`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- -- Step 5: Migrate existing user-department relationships
-- -- ===================================================================
-- -- From team_member_job_info table
-- INSERT IGNORE INTO `opm_user_departments` (`user_id`, `department_id`, `is_primary`)
-- SELECT `user_id`, `department_id`, 1 as is_primary
-- FROM `opm_team_member_job_info` 
-- WHERE `department_id` IS NOT NULL 
-- AND `department_id` > 0
-- AND NOT EXISTS (
--     SELECT 1 FROM `opm_user_departments` 
--     WHERE `opm_user_departments`.`user_id` = `opm_team_member_job_info`.`user_id` 
--     AND `opm_user_departments`.`department_id` = `opm_team_member_job_info`.`department_id`
-- );

-- -- From opm_user_departments if it exists
-- INSERT IGNORE INTO `opm_user_departments` (`user_id`, `department_id`, `is_primary`)
-- SELECT `user_id`, `department_id`, `is_primary`
-- FROM `opm_user_departments` 
-- WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'opm_user_departments')
-- AND NOT EXISTS (
--     SELECT 1 FROM `opm_user_departments` 
--     WHERE `opm_user_departments`.`user_id` = `opm_user_departments`.`user_id` 
--     AND `opm_user_departments`.`department_id` = `opm_user_departments`.`department_id`
-- );

-- -- Step 6: Add department_id to tasks table for better organization
-- -- ===================================================================
-- ALTER TABLE `opm_tasks` 
-- ADD COLUMN `department_id` int DEFAULT NULL AFTER `project_id`,
-- ADD KEY `department_id` (`department_id`);

-- -- Update tasks department_id from their projects
-- UPDATE `opm_tasks` t
-- INNER JOIN `opm_projects` p ON t.`project_id` = p.`id`
-- SET t.`department_id` = p.`department_id`
-- WHERE p.`department_id` IS NOT NULL AND t.`department_id` IS NULL;

-- -- Step 7: Add department_id to other relevant tables
-- -- ===================================================================

-- -- Add to announcements if not exists
-- ALTER TABLE `opm_announcements` 
-- ADD COLUMN `department_id` int DEFAULT NULL AFTER `created_by`,
-- ADD KEY `department_id` (`department_id`);

-- -- Add to client_groups if not exists  
-- ALTER TABLE `opm_client_groups` 
-- ADD COLUMN `department_id` int DEFAULT NULL AFTER `title`,
-- ADD KEY `department_id` (`department_id`);

-- -- Add to custom_fields if not exists
-- ALTER TABLE `opm_custom_fields` 
-- ADD COLUMN `department_id` int DEFAULT NULL AFTER `context`,
-- ADD KEY `department_id` (`department_id`);

-- -- Step 8: Create department permissions table for advanced access control
-- -- ===================================================================
-- CREATE TABLE IF NOT EXISTS `opm_department_permissions` (
--   `id` int NOT NULL AUTO_INCREMENT,
--   `user_id` int NOT NULL,
--   `department_id` int NOT NULL,
--   `permission_type` enum('view','manage','admin') NOT NULL DEFAULT 'view',
--   `granted_by` int NOT NULL,
--   `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `unique_user_dept_permission` (`user_id`, `department_id`, `permission_type`),
--   KEY `user_id` (`user_id`),
--   KEY `department_id` (`department_id`),
--   KEY `permission_type` (`permission_type`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
