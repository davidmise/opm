-- ===================================================================
-- FIX TEAM MEMBER DROPDOWN ERROR - MISSING CANONICAL TABLES
-- ===================================================================
-- This script creates the missing canonical tables that the application expects
-- and migrates data from prefixed tables (opm_*) to canonical tables
-- 
-- Run this BEFORE the main consolidation script
-- ===================================================================

-- Step 1: Create the missing team_member_job_info table (THIS WAS MISSING!)
-- ===================================================================
CREATE TABLE IF NOT EXISTS `team_member_job_info` (
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

-- Step 2: Check if omp_team_member_job_info exists and migrate data
-- ===================================================================
INSERT IGNORE INTO `team_member_job_info` (`user_id`, `department_id`, `date_of_hire`, `deleted`, `salary`, `salary_term`)
SELECT `user_id`, `department_id`, `date_of_hire`, `deleted`, `salary`, `salary_term`
FROM `opm_team_member_job_info`
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'opm_team_member_job_info');

-- Step 3: Ensure canonical departments table exists (in case it doesn't)
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

-- Step 4: Migrate departments from opm_departments to canonical departments
-- ===================================================================
INSERT IGNORE INTO `departments` (`id`, `title`, `description`, `color`, `icon`, `head_user_id`, `is_active`, `created_by`, `created_at`)
SELECT `id`, `title`, `description`, `color`, 
       COALESCE(`icon`, 'grid') as icon,
       `head_user_id`, 
       COALESCE(`is_active`, 1) as is_active,
       `created_by`, `created_at`
FROM `opm_departments` 
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'opm_departments');

-- Step 5: Ensure default departments exist
-- ===================================================================
INSERT IGNORE INTO `departments` (`id`, `title`, `description`, `color`, `icon`, `created_by`) VALUES 
(1, 'General', 'Default department for all members', '#4CAF50', 'grid', 1),
(2, 'Engineering', 'Software development and technical team', '#2196F3', 'code', 1),
(3, 'Marketing', 'Marketing and communications team', '#FF9800', 'megaphone', 1),
(4, 'Sales', 'Sales and business development team', '#9C27B0', 'trending-up', 1),
(5, 'Support', 'Customer support and service team', '#00BCD4', 'headphones', 1),
(6, 'Management', 'Executive and management team', '#F44336', 'users', 1);

-- Step 6: Create user_departments junction table
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

-- Step 7: Migrate user-department relationships from prefixed tables
-- ===================================================================
-- From opm_user_departments
INSERT IGNORE INTO `user_departments` (`user_id`, `department_id`, `is_primary`, `created_at`)
SELECT `user_id`, `department_id`, COALESCE(`is_primary`, 0), COALESCE(`created_at`, NOW())
FROM `opm_user_departments` 
WHERE EXISTS (SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'opm_user_departments');

-- From team_member_job_info (create primary department relationships)
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

-- ===================================================================
-- VERIFICATION QUERIES
-- ===================================================================
SELECT 'Canonical Tables Check' as Status;
SELECT 'team_member_job_info' as Table_Name, COUNT(*) as Record_Count FROM team_member_job_info
UNION ALL
SELECT 'departments', COUNT(*) FROM departments  
UNION ALL
SELECT 'user_departments', COUNT(*) FROM user_departments;

-- Check if team member job info has proper department linkage
SELECT 'Team Members with Departments' as Info, COUNT(*) as Count 
FROM team_member_job_info 
WHERE department_id IS NOT NULL;

SELECT 'Department Titles' as Info;
SELECT id, title FROM departments ORDER BY id;

-- ===================================================================
-- SUCCESS MESSAGE
-- ===================================================================
-- Canonical tables created and data migrated!
-- 
-- ✅ team_member_job_info table created
-- ✅ Data migrated from opm_team_member_job_info  
-- ✅ departments table populated
-- ✅ user_departments junction table created
--
-- The team member dropdown error should now be fixed!
-- ===================================================================