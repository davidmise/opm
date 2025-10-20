-- Add department_id column to tasks table
ALTER TABLE `opm_tasks` 
ADD COLUMN `department_id` INT NOT NULL DEFAULT '0' AFTER `client_id`,
ADD KEY `department_id` (`department_id`);

-- Add 'department' to the context enum in tasks table
ALTER TABLE `opm_tasks` 
MODIFY COLUMN `context` ENUM(
    'project',
    'client',
    'lead',
    'invoice',
    'estimate',
    'order',
    'contract',
    'proposal',
    'subscription',
    'ticket',
    'expense',
    'department',
    'general'
) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'general';
