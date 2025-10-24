# Announcement Filters - Quick Troubleshooting Guide

## What Was Changed

**Problem:** Filters were showing "default_lang.loading..." errors and not working

**Solution:** 
- Pre-cache language strings in PHP before building HTML
- Add try-catch error handling to catch PHP errors
- Enhance JavaScript error reporting

**Files Changed:**
- `app/Controllers/Departments.php` - method `get_filtered_announcements()`
- `app/Views/departments/announcements.php` - function `loadAnnouncementsByFilters()`

---

## Testing The Fixes

### Step 1: Clear Cache
```
- Browser: Ctrl+Shift+Del (or Cmd+Shift+Del on Mac)
- Select all time, check all boxes
- Click Clear
```

### Step 2: Hard Refresh
```
Ctrl+Shift+R (or Cmd+Shift+R on Mac)
```

### Step 3: Open Developer Tools
```
Press F12
Go to Console tab
```

### Step 4: Try A Filter
- Select any category
- Select any priority
- Select any department

### Expected Results

✅ **Good Sign:**
- Announcements table updates
- No "default_lang..." text appears
- Console shows: `Filter response: {success: true, html: "...", count: X}`
- Icons render properly
- No red error messages in console

❌ **Bad Sign:**
- Table shows error message
- Console shows JavaScript errors
- "default_lang.loading..." text appears
- Network tab shows 500 error
- AJAX response contains HTML instead of JSON

---

## If You See Errors

### Error: "default_lang.loading..."
**Cause:** Language strings not caching properly
**Fix:** 
1. Clear cache and hard refresh
2. Check if Laragon/PHP is running properly
3. Check writable/logs/ for PHP errors

### Error: "parsererror" in console
**Cause:** Server returned HTML instead of JSON (likely PHP error)
**Fix:**
1. Check Application Error Logs:
   ```
   /var/log/php-errors.log
   writable/logs/log-*.log
   ```
2. Look for PHP fatal errors in the get_filtered_announcements() method
3. Check if Announcements_model is loading correctly

### Error: "500 Internal Server Error"
**Cause:** PHP error on server
**Fix:**
1. Check writable/logs/ for detailed error message
2. Check if all models are loaded correctly
3. Try enabling debug mode in .env

### Filters Don't Change Table
**Cause:** AJAX call failing silently
**Fix:**
1. Open Network tab (F12)
2. Click a filter dropdown
3. Look for POST to `get_filtered_announcements`
4. Click on it and check Response tab
5. If Response is not JSON, there's a PHP error

---

## Debug Console Commands

**Test Category Filter:**
```javascript
loadAnnouncementsByFilters(0, 'general', '');
```

**Test Priority Filter:**
```javascript
loadAnnouncementsByFilters(0, '', 'high');
```

**Test Department Filter:**
```javascript
loadAnnouncementsByFilters(1, '', '');
```

**Test All Three:**
```javascript
loadAnnouncementsByFilters(2, 'urgent', 'high');
```

**Force Icon Reload:**
```javascript
if (window.feather) { feather.replace(); }
```

---

## What Each Part Does

### Backend (PHP):
1. **Pre-cache language strings** - Get all translations needed BEFORE building HTML
2. **Filter announcements** - Apply department/category/priority filters
3. **Build HTML** - Create table rows using cached language strings
4. **Return JSON** - Send `{success: true, html: "...", count: X}`
5. **Catch errors** - If anything goes wrong, send `{success: false, message: "..."}`

### Frontend (JavaScript):
1. **Get filter values** - Read what user selected
2. **Make AJAX call** - Send to backend with POST data
3. **Wait for response** - Show spinner while loading
4. **Check if successful** - Test `response.success`
5. **Update table** - Insert HTML into tbody
6. **Re-init icons** - Call feather.replace() to render icons
7. **Show errors** - If failed, show user-friendly error message

---

## Key File Locations

**Backend Filter Logic:**
`app/Controllers/Departments.php` lines 383-560

**Frontend Filter Functions:**
`app/Views/departments/announcements.php` lines 713-755

**Language Strings Cache:**
`app/Controllers/Departments.php` lines 477-487

---

## Performance Tips

**If Filters Are Slow:**
1. Check how many announcements are loaded (huge dataset?)
2. Check Network tab - is AJAX call taking >1 second?
3. Check browser console for JavaScript errors
4. Consider pagination or loading fewer announcements initially

**If Icons Don't Appear:**
1. Check if feather.replace() is being called
2. Open Network tab and look for feather.min.js loading
3. Check if `<i data-feather="...">` tags are in the response HTML
4. Check browser console for feather errors

---

## Emergency Rollback

If filters break completely and you need to revert:

```bash
# Using git
git checkout app/Controllers/Departments.php
git checkout app/Views/departments/announcements.php

# Then hard refresh page (Ctrl+Shift+R)
```

---

## Next Steps After Fix

Once filters are working:

1. **Test with multiple announcements** (at least 10+)
2. **Test with different departments** (verify access control)
3. **Test combining filters** (category + priority + department)
4. **Test on different browsers** (Chrome, Firefox, Safari, Edge)
5. **Test on mobile** (if applicable)
6. **Monitor performance** (check if adding icons, buttons slows down)

---

## Important Notes

⚠️ **Language caching is only for AJAX responses** - regular page views still use normal app_lang() calls

⚠️ **Error handling catches ALL exceptions** - check logs if something unexpected happens

⚠️ **JSON response format is critical** - even extra whitespace can break parsing

✅ **Always check browser console** - shows JavaScript errors the server can't see

✅ **Always check Network tab** - shows if AJAX call succeeds (200) or fails (500)

✅ **Always check application logs** - shows PHP errors that won't appear in browser
