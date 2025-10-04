-- =====================================================
-- O.P.M Overland Project Manager - Departments Feature
-- Migration Script: 001_add_departments.sql
-- Date: 2025-10-04
-- Description: Adds departments functionality to the system
-- =====================================================

-- STEP 1: Create departments table
-- =====================================================
CREATE TABLE IF NOT EXISTS `opm_departments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(191) NOT NULL,
  `description` TEXT,
  `color` VARCHAR(7) DEFAULT '#4CAF50',
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- STEP 2: Add department_id to team_member_job_info table
-- =====================================================
ALTER TABLE `opm_team_member_job_info`
ADD COLUMN `department_id` INT NULL AFTER `user_id`,
ADD KEY `department_id` (`department_id`);

-- STEP 3: Add department_id to projects table
-- =====================================================
ALTER TABLE `opm_projects`
ADD COLUMN `department_id` INT NULL AFTER `client_id`,
ADD KEY `department_id` (`department_id`);

-- STEP 4: Insert default departments (optional - for demo/testing)
-- =====================================================
INSERT INTO `opm_departments` (`id`, `title`, `description`, `color`, `created_by`) VALUES 
(1, 'IT', 'IT Department - Default for all administrators and technical staff', '#2196F3', 1),
(2, 'General', 'General department for all members', '#4CAF50', 1),
(3, 'Engineering', 'Software development and technical team', '#1976D2', 1),
(4, 'Marketing', 'Marketing and communications team', '#FF9800', 1),
(5, 'Sales', 'Sales and business development team', '#9C27B0', 1),
(6, 'Support', 'Customer support and service team', '#00BCD4', 1),
(7, 'Management', 'Executive and management team', '#F44336', 1);

-- STEP 5: Add foreign key constraints (optional - for referential integrity)
-- =====================================================
-- Uncomment these lines if you want strict referential integrity:
-- ALTER TABLE `opm_team_member_job_info`
-- ADD CONSTRAINT `fk_tmji_department` FOREIGN KEY (`department_id`) 
-- REFERENCES `opm_departments`(`id`) ON DELETE SET NULL;

-- ALTER TABLE `opm_projects`
-- ADD CONSTRAINT `fk_projects_department` FOREIGN KEY (`department_id`) 
-- REFERENCES `opm_departments`(`id`) ON DELETE SET NULL;

-- =====================================================
-- Migration Complete
-- =====================================================
