CREATE TABLE IF NOT EXISTS `department_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `template_description` text DEFAULT NULL,
  `template_color` varchar(7) DEFAULT '#6c757d',
  `template_permissions` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add settings for department management
INSERT IGNORE INTO `settings` (`setting_name`, `setting_value`, `type`, `created_by`, `created_date`) VALUES
('default_department_color', '#6c757d', 'general', 1, NOW()),
('auto_assign_new_users', '0', 'general', 1, NOW()),
('require_department_approval', '0', 'general', 1, NOW()),
('department_hierarchy_enabled', '0', 'general', 1, NOW()),
('enable_department_audit', '1', 'general', 1, NOW()),
('audit_retention_days', '365', 'general', 1, NOW());

-- Add RBAC settings for different roles
INSERT IGNORE INTO `settings` (`setting_name`, `setting_value`, `type`, `created_by`, `created_date`) VALUES
-- Admin permissions (always enabled)
('can_view_all_departments_admin', '1', 'rbac', 1, NOW()),
('can_create_departments_admin', '1', 'rbac', 1, NOW()),
('can_edit_departments_admin', '1', 'rbac', 1, NOW()),
('can_delete_departments_admin', '1', 'rbac', 1, NOW()),
('can_manage_department_members_admin', '1', 'rbac', 1, NOW()),
('can_view_department_projects_admin', '1', 'rbac', 1, NOW()),
('can_assign_projects_to_departments_admin', '1', 'rbac', 1, NOW()),
('can_view_department_tasks_admin', '1', 'rbac', 1, NOW()),
('can_assign_tasks_to_departments_admin', '1', 'rbac', 1, NOW()),
('can_view_department_analytics_admin', '1', 'rbac', 1, NOW()),
('can_export_department_data_admin', '1', 'rbac', 1, NOW()),

-- Department Manager permissions
('can_view_all_departments_department_manager', '1', 'rbac', 1, NOW()),
('can_create_departments_department_manager', '0', 'rbac', 1, NOW()),
('can_edit_departments_department_manager', '1', 'rbac', 1, NOW()),
('can_delete_departments_department_manager', '0', 'rbac', 1, NOW()),
('can_manage_department_members_department_manager', '1', 'rbac', 1, NOW()),
('can_view_department_projects_department_manager', '1', 'rbac', 1, NOW()),
('can_assign_projects_to_departments_department_manager', '1', 'rbac', 1, NOW()),
('can_view_department_tasks_department_manager', '1', 'rbac', 1, NOW()),
('can_assign_tasks_to_departments_department_manager', '1', 'rbac', 1, NOW()),
('can_view_department_analytics_department_manager', '1', 'rbac', 1, NOW()),
('can_export_department_data_department_manager', '1', 'rbac', 1, NOW()),

-- Team Member permissions  
('can_view_all_departments_team_member', '1', 'rbac', 1, NOW()),
('can_create_departments_team_member', '0', 'rbac', 1, NOW()),
('can_edit_departments_team_member', '0', 'rbac', 1, NOW()),
('can_delete_departments_team_member', '0', 'rbac', 1, NOW()),
('can_manage_department_members_team_member', '0', 'rbac', 1, NOW()),
('can_view_department_projects_team_member', '1', 'rbac', 1, NOW()),
('can_assign_projects_to_departments_team_member', '0', 'rbac', 1, NOW()),
('can_view_department_tasks_team_member', '1', 'rbac', 1, NOW()),
('can_assign_tasks_to_departments_team_member', '0', 'rbac', 1, NOW()),
('can_view_department_analytics_team_member', '0', 'rbac', 1, NOW()),
('can_export_department_data_team_member', '0', 'rbac', 1, NOW()),

-- Client permissions
('can_view_all_departments_client', '0', 'rbac', 1, NOW()),
('can_create_departments_client', '0', 'rbac', 1, NOW()),
('can_edit_departments_client', '0', 'rbac', 1, NOW()),
('can_delete_departments_client', '0', 'rbac', 1, NOW()),
('can_manage_department_members_client', '0', 'rbac', 1, NOW()),
('can_view_department_projects_client', '0', 'rbac', 1, NOW()),
('can_assign_projects_to_departments_client', '0', 'rbac', 1, NOW()),
('can_view_department_tasks_client', '0', 'rbac', 1, NOW()),
('can_assign_tasks_to_departments_client', '0', 'rbac', 1, NOW()),
('can_view_department_analytics_client', '0', 'rbac', 1, NOW()),
('can_export_department_data_client', '0', 'rbac', 1, NOW());