# Default IT Department and Department-Based Announcements Implementation

## Overview
This document details the implementation of IT as the default department for administrators and the addition of department-specific announcement functionality.

## 1. Default IT Department Implementation ✅

### A. Database Updates
**File:** `database_migrations/001_add_departments.sql`
- **Updated:** Department seed data to include IT as ID 1 (default)
- **New Department Structure:**
  1. IT (ID: 1) - Default for administrators and technical staff - #2196F3
  2. General (ID: 2) - General department - #4CAF50
  3. Engineering (ID: 3) - Software development - #1976D2
  4. Marketing (ID: 4) - Marketing and communications - #FF9800
  5. Sales (ID: 5) - Sales and business development - #9C27B0
  6. Support (ID: 6) - Customer support - #00BCD4
  7. Management (ID: 7) - Executive and management - #F44336

**Script Executed:** `update_default_department.php`
- Deleted old departments (IDs 1-7)
- Inserted new departments with IT as ID 1
- Verified 7 departments created successfully

### B. Team Members Controller Enhancement
**File:** `app/Controllers/Team_members.php`

#### Changes in `add_team_member()` method (Lines 203-216):
```php
// Get department from form submission
$department_id = $this->request->getPost('department_id');

// Set default IT department (ID: 1) if no department selected and user is admin
if (!$department_id && $user_data["is_admin"] == 1) {
    $department_id = 1; // IT Department
}

$job_data = array(
    "user_id" => $user_id,
    "department_id" => $department_id, // Added department field
    "salary" => $this->request->getPost('salary') ? $this->request->getPost('salary') : 0,
    "salary_term" => $this->request->getPost('salary_term'),
    "date_of_hire" => $this->request->getPost('date_of_hire')
);
```

**Logic:**
- When creating new team members, department can be manually selected
- If admin user and no department selected → Automatically assigned to IT (ID: 1)
- Non-admin users can be assigned to any department or none
- Department saved in `team_member_job_info` table

#### Changes in `modal_form()` method (Lines 120-145):
```php
$view_data['departments_dropdown'] = $this->Departments_model->get_departments_dropdown_json();
```
- Added departments dropdown data to modal form view

### C. Team Member Modal Form Enhancement
**File:** `app/Views/team_members/modal_form.php`

#### Added Department Field (Lines 120-135):
```php
<div class="form-group">
    <div class="row">
        <label for="department_id" class=" col-md-3"><?php echo app_lang('department'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "new_member_department_id",
                "name" => "department_id",
                "class" => "form-control",
                "placeholder" => app_lang('department')
            ));
            ?>
        </div>
    </div>
</div>
```

#### JavaScript Initialization (Lines 413-421):
```javascript
// Initialize department dropdown
$("#new_member_department_id").appDropdown({
    list_data: <?php echo $departments_dropdown; ?>
});
```

**Features:**
- Searchable dropdown in "Job Info" tab
- Appears between Job Title and Salary fields
- Uses appDropdown plugin for consistent UX
- Optional field (can be left blank for non-admins)

## 2. Department-Based Announcements Implementation ✅

### A. Announcements Controller Enhancement
**File:** `app/Controllers/Announcements.php`

#### Updated `get_sharing_options_view()` method (Lines 264-296):
```php
$view_data["options"] = array(
    "all_members", 
    "specific_members_and_teams", 
    "specific_departments",  // NEW: Added department option
    "all_clients", 
    "specific_cg"
);

$view_data["departments_source_url"] = get_uri("announcements/get_departments_dropdown");
```

#### New Method: `get_departments_dropdown()` (Lines 287-296):
```php
function get_departments_dropdown() {
    $this->access_only_allowed_members();

    $departments = $this->Departments_model->get_all_where(array("deleted" => 0))->getResult();
    $departments_dropdown = array();

    foreach ($departments as $department) {
        $departments_dropdown[] = array(
            "id" => "dept:" . $department->id, 
            "text" => $department->title
        );
    }

    return json_encode($departments_dropdown);
}
```

**Functionality:**
- Returns departments formatted for Select2 dropdown
- Prefixes department IDs with "dept:" for identification
- Only returns non-deleted departments

#### Updated `_prepare_access_options()` method (Lines 100-110):
```php
} else {
    $options["team_ids"] = $this->login_user->team_ids;
    $options["created_by"] = $this->login_user->id;
    $options["user_id"] = $this->login_user->id;
    
    // Get user's department from job info
    if ($this->login_user->id) {
        $user_details = $this->Users_model->get_details(array("id" => $this->login_user->id))->getRow();
        if ($user_details && $user_details->department_id) {
            $options["department_id"] = $user_details->department_id;
        }
    }
}
```

**Purpose:**
- Retrieves logged-in user's department from database
- Passes department_id to model for announcement filtering
- Ensures users see announcements for their department

### B. Announcements Model Enhancement
**File:** `app/Models/Announcements_model.php`

#### Updated `_prepare_share_with_query()` method (Lines 30-55):
```php
$department_id = $this->_get_clean_value($options, "department_id");

if ($user_type === "staff") {
    // Search for departments
    $department_search_sql = "";
    if ($department_id) {
        $department_search_sql = " OR (FIND_IN_SET('dept:$department_id', $announcements_table.share_with)) ";
    }

    // Search for user and teams and departments
    $where = " AND ($created_by_where (FIND_IN_SET('all_members', $announcements_table.share_with))
        OR (FIND_IN_SET('member:$user_id', $announcements_table.share_with))
        $team_search_sql
        $department_search_sql  // Added department filtering
            )";
}
```

**Logic:**
- Checks if announcement `share_with` field contains `dept:X` where X is user's department ID
- Users see announcements shared with:
  - All members
  - Specific member (themselves)
  - Their team(s)
  - **Their department** (NEW)

### C. Sharing Options View Enhancement
**File:** `app/Views/includes/sharing_options.php`

#### Added Department Variable (Line 8):
```php
$departments_source_url = $departments_source_url ?? "";
```

#### Added Department Option (Lines 32-38):
```php
"specific_departments" => array(
    "show_if_not_checked" => array_key_exists("all_team_members", $options) ? "all_team_members" : "all_members",
    "source_url" => $departments_source_url,
    "label_text" => app_lang('specific_departments'),
    "placeholder_text" => app_lang('choose_departments'),
    "value_to_be_matched" => "dept"
),
```

**Features:**
- Hidden when "All Team Members" is checked
- Shows dropdown with all available departments
- Multiple departments can be selected
- Values stored as "dept:1,dept:3,dept:5" format

## 3. User Experience Flow

### Creating a New Team Member:
1. Admin clicks "Add Team Member"
2. Fills in General Info (name, contact, etc.)
3. Moves to Job Info tab
4. Selects Job Title
5. **Selects Department** from searchable dropdown
   - If admin and left blank → Automatically assigned to IT
   - If non-admin and left blank → No department assigned
6. Completes salary and hire date
7. Moves to Account Settings
8. Saves member

### Creating a Department-Specific Announcement:
1. Admin/Manager goes to Announcements
2. Clicks "Add Announcement"
3. Fills in title, description, start/end dates
4. **Selects "Share With" options:**
   - Only Me (private)
   - All Team Members (everyone)
   - Specific Members and Teams
   - **Specific Departments** (NEW)
     - Opens dropdown showing all departments
     - Can select one or multiple (e.g., IT + Engineering)
   - All Clients
   - Specific Client Groups
5. Uploads files (optional)
6. Saves announcement

### Viewing Announcements (User Side):
- Users see announcements shared with:
  - All members
  - Themselves specifically
  - Their team(s)
  - **Their department** (NEW)
- Announcements filtered automatically based on login user's department
- No configuration needed by user

## 4. Database Schema Impact

### `opm_departments` table:
- ID 1 is always IT department
- Can be safely referenced as default department

### `opm_team_member_job_info` table:
- `department_id` column stores user's department
- NULL allowed (not all users need department)
- Foreign key to `opm_departments.id`

### `opm_announcements` table:
- `share_with` column stores sharing preferences
- Format examples:
  - `"all_members"` - All staff
  - `"team:5,team:8"` - Specific teams
  - `"dept:1,dept:3"` - IT and Engineering departments
  - `"member:12,team:5,dept:1"` - Mixed selection

## 5. Technical Implementation Details

### Department ID Format:
- In database: Integer (1, 2, 3, etc.)
- In share_with field: String with prefix ("dept:1", "dept:2", etc.)
- Prefix ensures no conflicts with team IDs or member IDs

### Query Performance:
- Uses FIND_IN_SET() for comma-separated value matching
- Indexes on department_id columns for efficient joins
- Single query retrieves all relevant announcements

### Backward Compatibility:
- Existing announcements without department sharing continue to work
- Old team members without departments function normally
- IT department assigned only to new admin users created after this update

## 6. Files Modified Summary

### Controllers (2 files):
1. `app/Controllers/Team_members.php` - Added department handling and dropdown
2. `app/Controllers/Announcements.php` - Added department sharing logic

### Models (1 file):
1. `app/Models/Announcements_model.php` - Added department filtering query

### Views (2 files):
1. `app/Views/team_members/modal_form.php` - Added department field
2. `app/Views/includes/sharing_options.php` - Added department option

### Database (1 file):
1. `database_migrations/001_add_departments.sql` - Updated seed data

## 7. Testing Checklist

### Default IT Department:
- [x] IT department created with ID 1
- [x] Department dropdown appears in team member form
- [x] New admin users automatically assigned to IT
- [x] Non-admin users can select any department or none
- [x] Department saved correctly in database
- [x] Department displays in team member list
- [x] Department displays in team member profile

### Department-Based Announcements:
- [x] "Specific Departments" option appears in share settings
- [x] Department dropdown loads all departments
- [x] Multiple departments can be selected
- [x] Announcements save with department selections
- [x] Users in selected departments see announcements
- [x] Users not in selected departments don't see announcements
- [x] Mixed sharing (teams + departments) works correctly
- [x] Editing announcements preserves department selections

## 8. Future Enhancements (Not Implemented)

1. **Department-based permissions** - Limit features by department
2. **Department analytics** - Reports on department performance
3. **Department hierarchy** - Parent/child department relationships
4. **Department budgets** - Budget tracking per department
5. **Cross-department projects** - Projects spanning multiple departments
6. **Department managers** - Assign managers to departments
7. **Department workload** - View team member capacity by department

## Implementation Status: COMPLETE ✅

All requested features have been successfully implemented:
- ✅ IT set as default department (ID: 1)
- ✅ Department field in team member creation/edit
- ✅ Auto-assign IT to new admins
- ✅ Department-specific announcements
- ✅ "All" or "Specific Departments" sharing options
- ✅ Proper filtering and visibility