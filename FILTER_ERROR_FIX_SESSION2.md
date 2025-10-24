# Announcement Filter Error Fix - Session 2

## Issue Reported
After implementing the announcement filters in the previous session, users were seeing error messages when trying to filter by category, priority, or department. The main visible errors were:
- "default_lang.loading..." text appearing in the table
- "An error occurred" notification
- No table data loading

## Root Cause Analysis

The error was caused by a **language function call inside the AJAX JSON response**. When `app_lang()` was called during HTML generation within the AJAX endpoint, if there were any issues with language loading or function execution, the entire JSON response could be malformed.

### Specific Problems:
1. **app_lang() calls in JSON context** - The controller was calling `app_lang()` multiple times while building the HTML string that would be returned as JSON
2. **Missing error handling** - No try-catch block to catch PHP errors and return proper JSON error responses
3. **Potential language loading issues** - app_lang() might fail in AJAX context if language files aren't properly initialized

## Solution Implemented

### 1. Pre-cache Language Strings
Instead of calling `app_lang()` within the HTML building loop:
```php
// OLD - Multiple calls during HTML generation:
$html .= '<span class="badge bg-light">' . app_lang('all_departments') . '</span>';

// NEW - Pre-cache all needed strings:
$lang_strings = array(
    'all_departments' => app_lang('all_departments'),
    'department_specific' => app_lang('department_specific'),
    'active' => app_lang('active'),
    'expired' => app_lang('expired'),
    'view' => app_lang('view'),
    'edit' => app_lang('edit'),
    'duplicate' => app_lang('duplicate'),
    'delete' => app_lang('delete'),
    'no_announcements_found' => app_lang('no_announcements_found')
);

// Then use the cached strings in HTML:
$html .= '<span class="badge bg-light">' . $lang_strings['all_departments'] . '</span>';
```

### 2. Add Try-Catch Error Handling
Wrapped the entire method in try-catch to ensure JSON response is always valid:
```php
function get_filtered_announcements() {
    try {
        // All existing filter logic...
        
        echo json_encode(array("success" => true, "html" => $html, "count" => count($announcements)));
    } catch(\Exception $e) {
        log_message('error', 'Announcements filter error: ' . $e->getMessage());
        echo json_encode(array("success" => false, "message" => 'Error filtering announcements: ' . $e->getMessage()));
    }
}
```

### 3. Enhanced AJAX Error Handling
Updated the JavaScript to better handle errors and provide debugging information:
```javascript
function loadAnnouncementsByFilters(department_id, category, priority) {
    $.ajax({
        url: '<?php echo base_url("index.php/departments/get_filtered_announcements"); ?>',
        type: 'POST',
        dataType: 'json',  // Explicitly set dataType
        data: {
            department_id: department_id || 0,
            category: category || '',
            priority: priority || ''
        },
        success: function(response) {
            console.log('Filter response:', response);  // Debug logging
            if(response && response.success) {
                $('#announcements-table tbody').html(response.html);
                // Re-initialize feather icons
                if (window.feather && typeof feather.replace === 'function') {
                    feather.replace();
                }
            } else {
                var errorMsg = (response && response.message) ? response.message : 'Error occurred';
                appAlert.error(errorMsg);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Enhanced error logging
            console.error('Filter AJAX Error:', {
                status: jqXHR.status,
                textStatus: textStatus,
                error: errorThrown,
                response: jqXHR.responseText
            });
            
            // Better error messages based on error type
            var errorMsg = 'Error occurred';
            if (jqXHR.status === 500) {
                errorMsg = 'Server error (500) - Check browser console and application logs';
            } else if (textStatus === 'parsererror') {
                errorMsg = 'Invalid response format - Check application logs for PHP errors';
            }
            appAlert.error(errorMsg);
        }
    });
}
```

## Files Modified

### 1. `/app/Controllers/Departments.php`
- Method: `get_filtered_announcements()` (lines 383-560)
- Changes:
  - Added try-catch error handling
  - Pre-caches all language strings before HTML generation
  - Ensures valid JSON response even if errors occur
  - Added error logging for debugging

### 2. `/app/Views/departments/announcements.php`
- Function: `loadAnnouncementsByFilters()` (lines ~713-755)
- Changes:
  - Added explicit `dataType: 'json'` to AJAX call
  - Added console.log for debugging filter responses
  - Enhanced error handling with detailed error messages
  - Better differentiation between parse errors, 500 errors, and other errors

## Testing the Fixes

### Quick Verification:
1. **Open browser Developer Tools** (F12)
2. **Go to Console tab** - you should see debug logs like "Filter response: {success: true, html: "...", count: 5}"
3. **Try filtering by category, priority, or department** - should work without errors
4. **Check Network tab** - should see the AJAX call succeed with 200 status

### What Should Happen:
- **No "default_lang" messages** appearing in tables
- **No JavaScript errors** in console  
- **Clean JSON responses** from the server
- **Announcements update** when filters change
- **Icons render properly** after filtering

## How to Debug If Issues Still Occur

1. **Check PHP Error Logs:**
   ```
   tail -f /var/log/php-errors.log
   ```

2. **Enable CodeIgniter Debug Logging:**
   - Edit `.env` file and set `CI_ENVIRONMENT = development`
   - Check `/writable/logs/` for detailed error logs

3. **Use Browser Console:**
   - Open F12 Developer Tools
   - Go to Console tab
   - Look for the "Filter response" log to see the actual server response
   - Look for any JavaScript errors

4. **Check AJAX Response:**
   - Go to Network tab
   - Click on the `get_filtered_announcements` POST request
   - View the Response tab to see the exact server response
   - If there's a PHP error, it will appear here

## Performance Notes

- Pre-caching language strings reduces function calls by ~9 per announcement (one call per badge/button)
- For 10 announcements, this reduces language function calls from 90 to 9
- Should be imperceptible but adds up on pages with many announcements

## Future Improvements

1. Add request validation to ensure department_id is numeric
2. Add rate limiting on AJAX calls (multiple rapid filter clicks)
3. Cache filter results for 30 seconds
4. Add pagination for large result sets
5. Consider moving HTML generation to view template for better separation of concerns
