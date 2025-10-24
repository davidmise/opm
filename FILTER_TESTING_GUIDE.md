# Quick Testing Guide - Announcement Filters

## What Was Fixed

Two main issues have been resolved:

1. **Department Filter 500 Error** - Fixed by removing duplicate filtering logic
2. **Category/Priority Filters Not Working** - Fixed by creating unified filter function with proper AJAX handling

## How to Test

### Quick Test (5 minutes)

1. Navigate to: **Departments → Announcements** tab
2. Try filtering by **Category** - should show only announcements with that category
3. Try filtering by **Priority** - should show only announcements with that priority  
4. Try filtering by **Department** - should show only announcements for that department
5. **No errors** should appear in browser console (F12)
6. **Icons should render** properly after filtering

### Comprehensive Test (15 minutes)

#### Setup Test Data
```sql
-- Create test announcements with different categories and priorities
INSERT INTO ci_announcements (title, description, category, priority, share_with, start_date, end_date, is_active)
VALUES
('General Update', 'This is a general announcement', 'general', 'normal', 'all_members', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1),
('Urgent Policy Change', 'This is an urgent policy announcement', 'policy', 'urgent', 'dept:1,dept:2', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1),
('Event Invitation', 'Team building event coming up', 'event', 'high', 'all_members', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1),
('Maintenance Notice', 'Server maintenance scheduled', 'maintenance', 'high', 'all_members', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1),
('Training Program', 'New employee training available', 'training', 'normal', 'dept:3', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1);
```

#### Run Tests

**Test 1: Category Filtering**
- [ ] Clear all filters (set to blank/default)
- [ ] Select "General" category → should show only General Update
- [ ] Select "Policy" category → should show only Urgent Policy Change
- [ ] Select "Event" category → should show only Event Invitation
- [ ] Select "Maintenance" category → should show only Maintenance Notice
- [ ] Select "Training" category → should show only Training Program
- [ ] Clear category filter → should show all announcements again

**Test 2: Priority Filtering**
- [ ] Clear all filters
- [ ] Select "Normal" priority → should show General Update, Training Program
- [ ] Select "High" priority → should show Event Invitation, Maintenance Notice
- [ ] Select "Urgent" priority → should show Urgent Policy Change
- [ ] Select "Low" priority → should show nothing (none configured)
- [ ] Clear priority filter → should show all announcements again

**Test 3: Department Filtering**
- [ ] Clear all filters
- [ ] Select "All Departments" (default) → should show all announcements
- [ ] Select "Global Announcements Only" → should show General Update, Event Invitation, Maintenance Notice
- [ ] Select specific department (e.g., "IT") → should show only announcements targeting that department
- [ ] Verify department filter works for logged-in staff users (limited to their department)

**Test 4: Combined Filters**
- [ ] Select Category = "Policy" AND Priority = "Urgent" → should show Urgent Policy Change only
- [ ] Select Department = "Department 1" AND Category = "Policy" → should show Urgent Policy Change
- [ ] Select ALL THREE filters → results should respect all three conditions

**Test 5: Error Handling**
- [ ] Check browser console (F12) for any errors
- [ ] Look for "Cannot read properties of undefined (reading 'toSvg')" errors - should NOT appear
- [ ] Look for 500 errors in Network tab - should NOT appear
- [ ] Should see proper success/error messages for any issues

**Test 6: UI/UX**
- [ ] Feather icons should render properly after each filter
- [ ] Action buttons (View, Edit, Duplicate, Delete) should work after filtering
- [ ] Loading spinner should appear briefly while filtering
- [ ] Table should update smoothly without full page reload

### Expected Behaviors

| Filter | Before Fix | After Fix |
|--------|-----------|-----------|
| Category | No change | Filters to selected category |
| Priority | No change | Filters to selected priority |
| Department | 500 Error | Filters correctly |
| Combined | N/A | All three work together |

## Browser Console Commands

You can test the filter functions directly in console:

```javascript
// Test category filter
loadAnnouncementsByFilters(0, 'general', '');

// Test priority filter
loadAnnouncementsByFilters(0, '', 'high');

// Test department filter
loadAnnouncementsByFilters(1, '', '');

// Test combined filters
loadAnnouncementsByFilters(1, 'policy', 'urgent');
```

## Common Issues & Solutions

### Issue: Still getting 500 error on department filter
**Solution:** Clear browser cache (Ctrl+Shift+Del) and hard refresh (Ctrl+Shift+R)

### Issue: Icons not showing after filter
**Solution:** Feather icons should auto-initialize. If not, try opening browser console and running:
```javascript
if (window.feather) { feather.replace(); }
```

### Issue: No announcements showing when filter applied
**Solution:** Verify test data was created with correct category/priority/department values

### Issue: Filters changing but table not updating
**Solution:** Check Network tab (F12) for failed AJAX requests. Look for error responses.

## Files Changed

- `app/Controllers/Departments.php` - Fixed `get_filtered_announcements()` method
- `app/Views/departments/announcements.php` - Added unified filter functions

## Rollback Instructions

If you need to revert these changes:

```bash
# Using git
git checkout app/Controllers/Departments.php
git checkout app/Views/departments/announcements.php

# Or restore from backup
cp backup/Departments.php app/Controllers/Departments.php
cp backup/announcements.php app/Views/departments/announcements.php
```
