-- Department Permissions Table for RBAC
CREATE TABLE IF NOT EXISTS `opm_department_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `permission` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `role` (`role`),
  KEY `permission` (`permission`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Insert default permissions for admin role
INSERT INTO `opm_department_permissions` (`role`, `permission`, `value`, `created_at`, `deleted`) VALUES
('admin', 'can_view_all_departments', 1, NOW(), 0),
('admin', 'can_create_departments', 1, NOW(), 0),
('admin', 'can_edit_departments', 1, NOW(), 0),
('admin', 'can_delete_departments', 1, NOW(), 0),
('admin', 'can_manage_department_users', 1, NOW(), 0),
('admin', 'can_view_department_reports', 1, NOW(), 0),
('admin', 'can_export_department_data', 1, NOW(), 0),
('admin', 'can_manage_department_settings', 1, NOW(), 0);

-- Insert default permissions for manager role
INSERT INTO `opm_department_permissions` (`role`, `permission`, `value`, `created_at`, `deleted`) VALUES
('manager', 'can_view_all_departments', 1, NOW(), 0),
('manager', 'can_create_departments', 0, NOW(), 0),
('manager', 'can_edit_departments', 1, NOW(), 0),
('manager', 'can_delete_departments', 0, NOW(), 0),
('manager', 'can_manage_department_users', 1, NOW(), 0),
('manager', 'can_view_department_reports', 1, NOW(), 0),
('manager', 'can_export_department_data', 1, NOW(), 0),
('manager', 'can_manage_department_settings', 0, NOW(), 0);

-- Insert default permissions for member role  
INSERT INTO `opm_department_permissions` (`role`, `permission`, `value`, `created_at`, `deleted`) VALUES
('member', 'can_view_all_departments', 0, NOW(), 0),
('member', 'can_create_departments', 0, NOW(), 0),
('member', 'can_edit_departments', 0, NOW(), 0),
('member', 'can_delete_departments', 0, NOW(), 0),
('member', 'can_manage_department_users', 0, NOW(), 0),
('member', 'can_view_department_reports', 0, NOW(), 0),
('member', 'can_export_department_data', 0, NOW(), 0),
('member', 'can_manage_department_settings', 0, NOW(), 0);

-- Insert default permissions for client role
INSERT INTO `opm_department_permissions` (`role`, `permission`, `value`, `created_at`, `deleted`) VALUES
('client', 'can_view_all_departments', 0, NOW(), 0),
('client', 'can_create_departments', 0, NOW(), 0),
('client', 'can_edit_departments', 0, NOW(), 0),
('client', 'can_delete_departments', 0, NOW(), 0),
('client', 'can_manage_department_users', 0, NOW(), 0),
('client', 'can_view_department_reports', 0, NOW(), 0),
('client', 'can_export_department_data', 0, NOW(), 0),
('client', 'can_manage_department_settings', 0, NOW(), 0);