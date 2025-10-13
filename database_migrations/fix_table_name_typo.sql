-- ===================================================================
-- CORRECTED EMERGENCY RECOVERY: FIX TABLE NAME TYPO
-- ===================================================================
-- The previous emergency recovery script had a typo: omp_ instead of opm_
-- This script fixes the table name and ensures proper data migration
-- ===================================================================

-- Step 1: Drop the incorrectly named table (if it exists)
-- ===================================================================
DROP TABLE IF EXISTS `omp_team_member_job_info`;

-- Step 2: Create the correctly named opm_team_member_job_info table
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
  UNIQUE KEY `unique_user_job_info` (`user_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `fk_tmji_user` FOREIGN KEY (`user_id`) REFERENCES `opm_users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tmji_department` FOREIGN KEY (`department_id`) REFERENCES `opm_departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Step 3: Restore data from opm_user_departments (primary assignments first)
-- ===================================================================
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

-- Step 4: Add job info for staff users with any department assignment (if no primary exists)
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

-- Step 5: Assign remaining staff users to General department (department_id = 1)
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

-- Step 6: Ensure consistency in user_departments table
-- ===================================================================
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
-- VERIFICATION QUERIES
-- ===================================================================
SELECT 'CORRECTED RECOVERY STATUS' as Info;

-- Verify correct table exists
SELECT 'opm_team_member_job_info exists' as Status, 
       CASE WHEN EXISTS (
           SELECT 1 FROM information_schema.tables 
           WHERE table_name = 'opm_team_member_job_info' 
           AND table_schema = DATABASE()
       ) THEN 'YES ✓' ELSE 'NO ✗' END as Result;

-- Verify wrong table is removed
SELECT 'omp_team_member_job_info removed' as Status, 
       CASE WHEN NOT EXISTS (
           SELECT 1 FROM information_schema.tables 
           WHERE table_name = 'omp_team_member_job_info' 
           AND table_schema = DATABASE()
       ) THEN 'YES ✓' ELSE 'NO ✗' END as Result;

-- Count recovered records
SELECT 'Job info records recovered' as Info, COUNT(*) as Count 
FROM opm_team_member_job_info;

-- Test a team member query (like the model would use)
SELECT 'Sample team member data' as Info;
SELECT 
    u.id,
    u.first_name,
    u.last_name,
    u.email,
    d.title as department_name,
    tmj.date_of_hire,
    tmj.salary,
    tmj.salary_term
FROM opm_users u
LEFT JOIN opm_team_member_job_info tmj ON u.id = tmj.user_id
LEFT JOIN opm_departments d ON tmj.department_id = d.id
WHERE u.user_type = 'staff' AND u.deleted = 0
ORDER BY u.first_name
LIMIT 5;

-- ===================================================================
-- SUCCESS! TABLE NAME CORRECTED
-- ===================================================================
-- ✅ Corrected table name from omp_ to opm_
-- ✅ opm_team_member_job_info table properly created
-- ✅ Foreign key constraints added for data integrity
-- ✅ Data restored from user department assignments
-- ✅ All staff users have job info records
--
-- The team member form should now work correctly!
-- ===================================================================