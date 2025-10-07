-- Enable departments module
-- Run this SQL directly in your database

INSERT INTO overland_pm.opm_settings (setting_name, setting_value, type, deleted) 
SELECT 'module_departments', '1', 'app', '0'
WHERE NOT EXISTS (
    SELECT 1 FROM overland_pm.opm_settings 
    WHERE setting_name = 'module_departments' AND deleted = 0
);

-- Check if the module was added
SELECT * FROM overland_pm.opm_settings WHERE setting_name = 'module_departments' AND deleted = 0;