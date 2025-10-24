# Announcement Management UI Reference

## Tabs Overview

### 1. All Announcements Tab
- **Purpose**: Display all announcements the user has access to
- **For Admin**: Shows ALL announcements in the system
- **For Staff**: Shows only announcements targeted to their departments
- **Features**:
  - Search by title/description
  - Filter by category
  - Filter by priority
  - Filter by department
  - Table with columns: Title, Category, Priority, Target Departments, Status, Created Date, Actions

### 2. Department Targeted Tab
- **Purpose**: View announcements grouped by department
- **Layout**: Card-based layout showing each department
- **For Each Department Card**:
  - Department name with color indicator
  - Number of targeted announcements
  - List of up to 3 announcements with quick info
  - "Add Announcement" button
  - "View All" button for department

### 3. Templates Tab
- **Purpose**: Manage announcement templates for quick reuse
- **Features**:
  - Create new template
  - Use template to create announcement
  - Edit existing templates
  - Delete templates

### 4. Analytics Tab (Future)
- **Purpose**: Track announcement engagement
- **Planned Features**:
  - Read statistics
  - Department-wise distribution
  - User engagement metrics
  - Announcement performance charts

## Action Buttons

### View (Eye Icon)
```
Icon: Eye
Color: Primary Blue
Function: View announcement details
Modal: Shows full announcement content
```

### Edit (Pencil Icon)
```
Icon: Edit-2 (pencil)
Color: Secondary Gray
Function: Edit announcement
Modal: Opens form with pre-filled data
  - Title
  - Description/Content
  - Category
  - Priority
  - Start Date
  - End Date
  - Target Departments
Action: Updates announcement in database
```

### Duplicate (Copy Icon)
```
Icon: Copy
Color: Success Green
Function: Create copy of announcement
Behavior: 
  - Copies all properties
  - Adds " (Copy)" to title
  - Creates new record with current user as creator
  - New announcement date is today
  - All other properties remain the same
```

### Delete (Trash Icon)
```
Icon: Trash-2
Color: Danger Red
Function: Delete announcement
Behavior:
  - Shows confirmation dialog
  - Soft deletes from database (marks as deleted)
  - Cannot be undone from UI (admin can restore from DB)
  - Page reloads showing updated list
```

## Statistics Cards

Located at top of page:

1. **Total Announcements**: Count of all announcements
2. **Active Announcements**: Count of announcements with end_date >= today
3. **Department Specific**: Count of announcements targeted to specific departments
4. **Global Announcements**: Count of announcements visible to all members

## Filters & Search

### Category Filter Dropdown
Options:
- All Categories (default)
- General
- Urgent
- Policy
- Event
- Training
- Maintenance
- Celebration

### Priority Filter Dropdown
Options:
- All Priorities (default)
- Low
- Normal
- High
- Urgent

### Department Filter Dropdown
Options:
- All Departments (default) - Shows announcements user has access to
- Global Announcements Only - Shows only announcements with no department targeting
- [Individual Department Names] - Shows announcements targeted to specific department

### Search Bar
- Real-time search as you type
- Searches in announcement title and description

## Badge Colors

### Category Badges
- General: Blue (bg-info)
- Urgent: Red (bg-danger)
- Policy: Yellow (bg-warning)
- Event/Other: Gray (bg-secondary)

### Priority Badges
- Low: Gray (bg-secondary)
- Normal: Blue (bg-info)
- High: Yellow (bg-warning)
- Urgent: Red (bg-danger)

### Target Badges
- All Departments: Light Gray
- Department Specific: Primary Blue

### Status Badges
- Active: Green (bg-success)
- Expired: Gray (bg-secondary)

## Keyboard Shortcuts (Future)
- `Ctrl+N`: Create new announcement
- `Ctrl+S`: Save announcement
- `Esc`: Close modal/dialog
- `Ctrl+D`: Duplicate selected announcement
- `Del`: Delete selected announcement

## Bulk Actions (Future)
- Select multiple announcements with checkboxes
- Bulk delete
- Bulk update (category, priority, dates)
- Bulk export to CSV

## Mobile Responsiveness
- Table converts to card layout on mobile
- Action buttons stack vertically
- Filters collapse into dropdown menu
- Search bar remains prominent
