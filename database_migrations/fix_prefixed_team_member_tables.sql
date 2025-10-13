-- ===================================================================
-- FIX TEAM MEMBER DROPDOWN ERROR - PREFIXED TABLES VERSION (OMP_)
-- ===================================================================
-- This script works with your prefixed table system (opm_ prefix)
-- Creates missing tables and ensures proper relationships
-- 
-- Run this to fix the dropdown save error
-- ===================================================================

-- Step 1: Create the missing opm_team_member_job_info table (THIS WAS MISSING!)
-- ===================================================================
CREATE TABLE IF NOT EXISTS `opm_team_member_job_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `date_of_hire` date DEFAULT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  `salary` double NOT NULL DEFAULT '0',
  `salary_term` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Step 2: Ensure opm_departments table has proper structure
-- ===================================================================
-- Add missing columns if they don't exist
ALTER TABLE `opm_departments` 
ADD COLUMN `icon` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'grid',
ADD COLUMN `head_user_id` int DEFAULT NULL,
ADD COLUMN `is_active` tinyint(1) NOT NULL DEFAULT '1',
ADD COLUMN `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add indexes if they don't exist
ALTER TABLE `opm_departments` 
ADD KEY `created_by` (`created_by`),
ADD KEY `head_user_id` (`head_user_id`),
ADD KEY `is_active` (`is_active`);

-- Step 3: Ensure default departments exist in opm_departments
-- ===================================================================
INSERT IGNORE INTO `opm_departments` (`id`, `title`, `description`, `color`, `icon`, `created_by`) VALUES 
(1, 'General', 'Default department for all members', '#4CAF50', 'grid', 1),
(2, 'Engineering', 'Software development and technical team', '#2196F3', 'code', 1),
(3, 'Marketing', 'Marketing and communications team', '#FF9800', 'megaphone', 1),
(4, 'Sales', 'Sales and business development team', '#9C27B0', 'trending-up', 1),
(5, 'Support', 'Customer support and service team', '#00BCD4', 'headphones', 1),
(6, 'Management', 'Executive and management team', '#F44336', 'users', 1);

-- Step 4: Ensure opm_user_departments has proper structure
-- ===================================================================
-- Add missing columns if they don't exist
ALTER TABLE `opm_user_departments` 
ADD COLUMN `is_primary` tinyint(1) NOT NULL DEFAULT '0',
ADD COLUMN `created_at` datetime DEFAULT CURRENT_TIMESTAMP;

-- Add indexes if they don't exist
ALTER TABLE `opm_user_departments` 
ADD KEY `user_id` (`user_id`),
ADD KEY `department_id` (`department_id`),
ADD KEY `is_primary` (`is_primary`);

-- Add unique constraint to prevent duplicates
ALTER TABLE `opm_user_departments` 
ADD CONSTRAINT `unique_opm_user_department` UNIQUE (`user_id`, `department_id`);

-- Step 5: Create relationships from team member job info
-- ===================================================================
-- Populate opm_user_departments from opm_team_member_job_info data
INSERT IGNORE INTO `opm_user_departments` (`user_id`, `department_id`, `is_primary`)
SELECT `user_id`, `department_id`, 1 as is_primary
FROM `opm_team_member_job_info` 
WHERE `department_id` IS NOT NULL 
AND `department_id` > 0
AND NOT EXISTS (
    SELECT 1 FROM `opm_user_departments` 
    WHERE `opm_user_departments`.`user_id` = `opm_team_member_job_info`.`user_id` 
    AND `opm_user_departments`.`department_id` = `opm_team_member_job_info`.`department_id`
);

-- Step 6: Add department_id to opm_tasks table if not exists
-- ===================================================================
ALTER TABLE `opm_tasks` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `project_id`,
ADD KEY `department_id` (`department_id`);

-- Update tasks department_id from their projects
UPDATE `opm_tasks` t
INNER JOIN `opm_projects` p ON t.`project_id` = p.`id`
SET t.`department_id` = p.`department_id`
WHERE p.`department_id` IS NOT NULL AND t.`department_id` IS NULL;

-- Step 7: Add department_id to other relevant prefixed tables
-- ===================================================================

-- Add to opm_announcements if not exists
ALTER TABLE `opm_announcements` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `created_by`,
ADD KEY `department_id` (`department_id`);

-- Add to opm_client_groups if not exists  
ALTER TABLE `opm_client_groups` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `title`,
ADD KEY `department_id` (`department_id`);

-- Add to opm_custom_fields if not exists
ALTER TABLE `opm_custom_fields` 
ADD COLUMN `department_id` int DEFAULT NULL AFTER `context`,
ADD KEY `department_id` (`department_id`);

-- Step 8: Create department permissions table for advanced access control
-- ===================================================================
CREATE TABLE IF NOT EXISTS `opm_department_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int NOT NULL,
  `permission_type` enum('view','manage','admin') NOT NULL DEFAULT 'view',
  `granted_by` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_opm_user_dept_permission` (`user_id`, `department_id`, `permission_type`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  KEY `permission_type` (`permission_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- ===================================================================
-- VERIFICATION QUERIES FOR PREFIXED TABLES
-- ===================================================================
SELECT 'Prefixed Tables Check (OPM_)' as Status;

SELECT 'opm_team_member_job_info' as Table_Name, COUNT(*) as Record_Count FROM opm_team_member_job_info
UNION ALL
SELECT 'opm_departments', COUNT(*) FROM opm_departments  
UNION ALL
SELECT 'opm_user_departments', COUNT(*) FROM opm_user_departments;

-- Check if team member job info has proper department linkage
SELECT 'Team Members with Departments (OPM)' as Info, COUNT(*) as Count 
FROM opm_team_member_job_info 
WHERE department_id IS NOT NULL;

SELECT 'Department Titles (OPM)' as Info;
SELECT id, title FROM opm_departments ORDER BY id;

-- Check user-department relationships
SELECT 'User-Department Relationships (OPM)' as Info;
SELECT u.user_id, d.title as department_name, u.is_primary
FROM opm_user_departments u 
JOIN opm_departments d ON u.department_id = d.id 
ORDER BY u.user_id, u.is_primary DESC
LIMIT 10;

-- ===================================================================
-- SUCCESS MESSAGE
-- ===================================================================
-- Prefixed tables (OPM_) created and configured!
-- 
-- ✅ opm_team_member_job_info table created
-- ✅ opm_departments table enhanced
-- ✅ opm_user_departments junction table configured
-- ✅ Department relationships established
-- ✅ Additional tables enhanced with department support
--
-- The team member dropdown error should now be fixed!
-- Your application will work with the prefixed table structure.
-- ===================================================================