-- ===================================================================
-- FINAL CLEANUP: Remove duplicate tables and ensure clean state
-- ===================================================================

-- Drop the incorrectly named table (if it exists)
DROP TABLE IF EXISTS `omp_team_member_job_info`;

-- Drop the canonical table (shouldn't exist in prefixed system)  
DROP TABLE IF EXISTS `team_member_job_info`;

-- Verify opm_team_member_job_info exists and has proper structure
SELECT 'Verification: opm_team_member_job_info table status' as Info;

SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'overland_pm_workflow' 
AND TABLE_NAME = 'opm_team_member_job_info';

-- Show current data in the correct table
SELECT 'Current job info data:' as Info;
SELECT 
    tmji.id,
    tmji.user_id,
    u.first_name,
    u.last_name,
    tmji.department_id,
    d.title as department_name,
    tmji.salary,
    tmji.salary_term
FROM opm_team_member_job_info tmji
LEFT JOIN opm_users u ON tmji.user_id = u.id  
LEFT JOIN opm_departments d ON tmji.department_id = d.id
ORDER BY tmji.id;