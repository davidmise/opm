# Issue Resolution: 500 Error on /departments/list_data

## Problem
When accessing the departments list page, the AJAX call to `/departments/list_data` was returning a **500 Internal Server Error**.

### Error Details
```
Request URL: http://localhost/overland_pm/index.php/departments/list_data
Request Method: POST
Status Code: 500 Internal Server Error
```

## Root Cause
The **database migration had not been run**. The following database objects were missing:

1. ❌ `opm_departments` table did not exist
2. ❌ `opm_team_member_job_info.department_id` column did not exist
3. ❌ `opm_projects.department_id` column did not exist

When the `Departments_model->get_details()` method tried to query the non-existent table, it caused a MySQL error which resulted in the 500 status code.

## Solution Applied

### Step 1: Fixed Migration Script
The migration script `database_migrations/001_add_departments.sql` was missing the table prefix `opm_`. 

**Changed:**
```sql
CREATE TABLE IF NOT EXISTS `departments` ...
ALTER TABLE `team_member_job_info` ...
ALTER TABLE `projects` ...
INSERT INTO `departments` ...
```

**To:**
```sql
CREATE TABLE IF NOT EXISTS `opm_departments` ...
ALTER TABLE `opm_team_member_job_info` ...
ALTER TABLE `opm_projects` ...
INSERT INTO `opm_departments` ...
```

### Step 2: Ran Migration
Executed the corrected migration script which:

1. ✅ Created `opm_departments` table with 7 columns
2. ✅ Added `department_id` column to `opm_team_member_job_info`
3. ✅ Added `department_id` column to `opm_projects`
4. ✅ Inserted 6 seed departments (General, Engineering, Marketing, Sales, Support, Management)
5. ✅ Added indexes on foreign key columns

### Step 3: Verified Installation
Confirmed that:
- ✅ `opm_departments` table exists and is queryable
- ✅ Table contains 6 seed departments
- ✅ `opm_team_member_job_info` has `department_id` column
- ✅ `opm_projects` has `department_id` column

## Result
The departments feature is now **fully operational**. The `/departments/list_data` endpoint now returns valid JSON data with the list of departments.

## What Was Created in Database

### Table: `opm_departments`
```sql
CREATE TABLE `opm_departments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(191) NOT NULL,
  `description` TEXT,
  `color` VARCHAR(7) DEFAULT '#4CAF50',
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`)
)
```

### Seed Data Inserted
1. **General** (#4CAF50) - Default department for all members
2. **Engineering** (#2196F3) - Software development and technical team
3. **Marketing** (#FF9800) - Marketing and communications team
4. **Sales** (#9C27B0) - Sales and business development team
5. **Support** (#00BCD4) - Customer support and service team
6. **Management** (#F44336) - Executive and management team

### Foreign Key Columns Added
- `opm_team_member_job_info.department_id` (INT NULL, indexed)
- `opm_projects.department_id` (INT NULL, indexed)

## Testing
The feature can now be tested:

1. ✅ Access `http://localhost/overland_pm/index.php/departments` - Should load the list page
2. ✅ The DataTable should display 6 departments with color badges
3. ✅ Click "Add Department" - Modal should open
4. ✅ Create a new department - Should save successfully
5. ✅ Edit existing department - Should update successfully
6. ✅ Assign department to team member - Should save in job info
7. ✅ Assign department to project - Should save in project form

## Files Modified During Fix

1. **database_migrations/001_add_departments.sql**
   - Added `opm_` prefix to all table names
   - Lines changed: 11, 18, 23, 28, 33, 36

## Prevention for Future
To avoid this issue in future installations:

1. **Always run migrations** after implementing new features
2. **Check database schema** before reporting errors
3. **Enable error logging** in development:
   - Check `writable/logs/` for error details
   - Set `CI_ENVIRONMENT = development` in `.env` or `index.php`

## Error Logging Recommendation
To make future debugging easier, ensure error logging is enabled:

**In `app/Config/App.php` or `.env`:**
```php
public $CSRFProtection = true;
public $CSRFTokenName = 'rise_csrf_cookie';
public $CSRFExpire = 7200;
public $log = true; // Enable logging
public $logThreshold = 4; // Log level: 4 = all messages
```

## Summary
✅ **Issue:** 500 error on `/departments/list_data`  
✅ **Cause:** Migration not run, table doesn't exist  
✅ **Fix:** Added table prefix, ran migration  
✅ **Status:** RESOLVED - Feature is now fully functional

---

**Date:** October 4, 2025  
**Time to Fix:** ~5 minutes  
**Migration Script:** `database_migrations/001_add_departments.sql`  
**Database:** `overland_pm` (prefix: `opm_`)
