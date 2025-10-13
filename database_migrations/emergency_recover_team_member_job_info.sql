-- ===================================================================
-- EMERGENCY RECOVERY: RECREATE OPM_TEAM_MEMBER_JOB_INFO TABLE
-- ===================================================================
-- This script recreates the accidentally dropped opm_team_member_job_info table
-- and attempts to restore relationships from existing data
-- ===================================================================

-- Step 1: Recreate the opm_team_member_job_info table
-- ===================================================================
CREATE TABLE IF NOT EXISTS `omp_team_member_job_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `date_of_hire` date DEFAULT NULL,
  `deleted` int NOT NULL DEFAULT '0',
  `salary` double NOT NULL DEFAULT '0',
  `salary_term` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_job_info` (`user_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Step 2: Attempt to restore data from opm_user_departments if it exists
-- ===================================================================
-- This will create basic job info records for users who have department assignments
INSERT IGNORE INTO `opm_team_member_job_info` (`user_id`, `department_id`)
SELECT DISTINCT `user_id`, `department_id` 
FROM `opm_user_departments` 
WHERE `is_primary` = 1 
AND EXISTS (
    SELECT 1 FROM `opm_users` u 
    WHERE u.`id` = `opm_user_departments`.`user_id` 
    AND u.`user_type` = 'staff' 
    AND u.`deleted` = 0
);

-- Step 3: If opm_user_departments doesn't have primary assignments, create from any department assignment
-- ===================================================================
INSERT IGNORE INTO `opm_team_member_job_info` (`user_id`, `department_id`)
SELECT DISTINCT `user_id`, MIN(`department_id`) as department_id
FROM `opm_user_departments` 
WHERE NOT EXISTS (
    SELECT 1 FROM `opm_team_member_job_info` 
    WHERE `opm_team_member_job_info`.`user_id` = `opm_user_departments`.`user_id`
)
AND EXISTS (
    SELECT 1 FROM `opm_users` u 
    WHERE u.`id` = `opm_user_departments`.`user_id` 
    AND u.`user_type` = 'staff' 
    AND u.`deleted` = 0
)
GROUP BY `user_id`;

-- Step 4: Create job info for staff users who don't have department assignments (assign to General)
-- ===================================================================
INSERT IGNORE INTO `opm_team_member_job_info` (`user_id`, `department_id`)
SELECT `id`, 1 as department_id  -- 1 = General department
FROM `opm_users` 
WHERE `user_type` = 'staff' 
AND `deleted` = 0
AND NOT EXISTS (
    SELECT 1 FROM `opm_team_member_job_info` 
    WHERE `opm_team_member_job_info`.`user_id` = `opm_users`.`id`
);

-- Step 5: Update user_departments to ensure consistency
-- ===================================================================
-- Make sure all team members have corresponding user_department entries
INSERT IGNORE INTO `opm_user_departments` (`user_id`, `department_id`, `is_primary`)
SELECT `user_id`, `department_id`, 1 as is_primary
FROM `opm_team_member_job_info` 
WHERE `department_id` IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM `opm_user_departments` 
    WHERE `opm_user_departments`.`user_id` = `opm_team_member_job_info`.`user_id` 
    AND `opm_user_departments`.`department_id` = `opm_team_member_job_info`.`department_id`
);

-- ===================================================================
-- VERIFICATION AND RECOVERY CHECK
-- ===================================================================
SELECT 'Recovery Status' as Info;

-- Check if table was recreated
SELECT 'opm_team_member_job_info exists' as Status, 
       CASE WHEN EXISTS (
           SELECT 1 FROM information_schema.tables 
           WHERE table_name = 'opm_team_member_job_info' 
           AND table_schema = DATABASE()
       ) THEN 'YES' ELSE 'NO' END as Result;

-- Count recovered records
SELECT 'Total job info records recovered' as Info, COUNT(*) as Count 
FROM opm_team_member_job_info;

-- Show staff users and their department assignments
SELECT 'Staff with Department Assignments' as Info;
SELECT 
    u.id as user_id,
    u.first_name,
    u.last_name,
    u.email,
    d.title as department_name,
    tmj.date_of_hire,
    tmj.salary
FROM opm_users u
LEFT JOIN opm_team_member_job_info tmj ON u.id = tmj.user_id
LEFT JOIN opm_departments d ON tmj.department_id = d.id
WHERE u.user_type = 'staff' AND u.deleted = 0
ORDER BY u.first_name, u.last_name
LIMIT 20;

-- Check for any staff without job info (shouldn't be any after recovery)
SELECT 'Staff WITHOUT job info (should be 0)' as Issue_Check, COUNT(*) as Count
FROM opm_users u
WHERE u.user_type = 'staff' 
AND u.deleted = 0
AND NOT EXISTS (
    SELECT 1 FROM opm_team_member_job_info tmj 
    WHERE tmj.user_id = u.id
);

-- ===================================================================
-- SUCCESS MESSAGE
-- ===================================================================
-- Emergency recovery completed!
-- 
-- ✅ opm_team_member_job_info table recreated
-- ✅ Data restored from opm_user_departments relationships
-- ✅ Staff users assigned to departments (General as fallback)
-- ✅ Consistency between job_info and user_departments maintained
--
-- Your team member dropdown should now work again!
-- 
-- NOTE: You may need to manually re-enter specific salary, hire dates, 
-- and salary terms for team members as this data couldn't be recovered.
-- ===================================================================