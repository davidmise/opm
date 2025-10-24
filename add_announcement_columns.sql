-- Add missing category and priority columns to announcements table

USE overland_pm_workflow;

-- Check current structure
DESCRIBE opm_announcements;

-- Add category column if it doesn't exist
ALTER TABLE opm_announcements 
ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'general' AFTER description;

-- Add priority column if it doesn't exist
ALTER TABLE opm_announcements 
ADD COLUMN IF NOT EXISTS priority VARCHAR(20) DEFAULT 'normal' AFTER category;

-- Verify the changes
DESCRIBE opm_announcements;

-- Show current data
SELECT id, title, category, priority, share_with, created_by 
FROM opm_announcements 
WHERE deleted = 0 
ORDER BY id DESC 
LIMIT 10;
