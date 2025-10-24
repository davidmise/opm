# 🚀 Overland Project Management - Complete System Installation

## Overview

This installation script (`install_system.php`) provides a one-click deployment solution for the Overland Project Management system. Simply upload to your server and access via browser to automatically create all required database tables, including the new department/workflow features.

## Features

- ✅ **One-Click Installation**: Access via browser, no command line needed
- ✅ **Complete Database Setup**: Creates all required tables with proper prefixes
- ✅ **Default Data**: Includes sample departments, task statuses, and system settings
- ✅ **Admin User Creation**: Automatically creates default admin account
- ✅ **Progress Tracking**: Visual progress bar and detailed logging
- ✅ **Error Handling**: Comprehensive error reporting and recovery
- ✅ **Security Features**: Warnings and recommendations for production use

## Quick Start

### 1. Update Database Configuration

Edit the database configuration at the top of `install_system.php`:

```php
$db_config = array(
    'hostname' => 'localhost',        // Your database host
    'username' => 'your_db_user',     // Your database username  
    'password' => 'your_db_password', // Your database password
    'database' => 'overland_pm_workflow', // Your database name
    'DBPrefix' => 'opm_'              // Table prefix (recommended: opm_)
);
```

### 2. Upload and Access

1. Upload `install_system.php` to your web server root directory
2. Open your browser and navigate to: `http://yourdomain.com/overland_pm/install_system.php`
3. Click "🚀 Start Installation" 
4. Wait for completion (2-3 minutes)

### 3. Login to System

After successful installation:

- **URL**: `http://yourdomain.com/overland_pm/index.php`
- **Email**: `admin@overland.pm`
- **Password**: `admin123`

**🔐 IMPORTANT**: Change the admin password immediately after first login!

## What Gets Created

### Database Tables (with opm_ prefix)

| Table | Purpose |
|-------|---------|
| `opm_users` | User accounts and profiles |
| `opm_departments` | Department/team structure |
| `omp_department_members` | User-department relationships |
| `opm_projects` | Project management |
| `opm_project_members` | Project team assignments |
| `opm_tasks` | Task management |
| `opm_task_status` | Task status definitions |
| `opm_announcements` | Announcement system with department targeting |
| `opm_team_member_tasks` | Workflow task assignments |
| `opm_team_member_job_info` | Employee job information |
| `opm_settings` | System configuration |

### Default Data

- **Task Statuses**: To Do, In Progress, Done, Cancelled
- **Departments**: Administration, Development, Design, Marketing
- **Admin User**: Complete admin account with full permissions
- **System Settings**: Basic configuration for immediate use

### Default Admin Account

```
Email: admin@overland.pm
Password: admin123
Role: System Administrator
Department: Administration
```

## Installation Steps Details

1. **Database Connection Test** - Verifies database credentials
2. **Database Creation** - Creates database if it doesn't exist
3. **Table Creation** - Creates all 11 required tables
4. **Default Data Insert** - Adds essential system data
5. **Admin User Creation** - Sets up administrative account
6. **Final Verification** - Confirms successful installation

## Requirements

- **PHP**: 7.4 or higher
- **MySQL/MariaDB**: 5.7 or higher
- **Extensions**: PDO, PDO_MySQL
- **Permissions**: Web server write access to application directories

## Security Considerations

### For Production Deployment:

1. **Delete Installation File**: Remove `install_system.php` after successful installation
2. **Change Admin Password**: Immediately update default admin credentials
3. **Database Security**: Use strong database passwords and limited user privileges
4. **File Permissions**: Set appropriate file and directory permissions
5. **HTTPS**: Use SSL/TLS encryption for all communications

### Database User Permissions

The database user needs these minimum permissions:
```sql
CREATE, DROP, INSERT, UPDATE, DELETE, SELECT, ALTER, INDEX
```

## Troubleshooting

### Common Issues:

**Database Connection Failed**
- Verify database credentials in configuration
- Ensure MySQL/MariaDB service is running
- Check firewall settings and port access

**Permission Denied Errors**
- Verify web server has write permissions
- Check PHP execution permissions
- Ensure database user has sufficient privileges

**Table Creation Failed**
- Check available disk space
- Verify database user has CREATE privileges
- Look for character set/collation conflicts

**Installation Hangs**
- Increase PHP execution time limit
- Check server memory limits
- Monitor database connection timeouts

### Debug Mode

To enable detailed error reporting, add this at the top of the installation file:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Customization

### Adding Custom Tables

To add custom tables to the installation, modify the `getTableDefinitions()` function:

```php
function getTableDefinitions($prefix) {
    return array(
        // ... existing tables ...
        
        $prefix . 'your_custom_table' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}your_custom_table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        "
    );
}
```

### Custom Default Data

Modify the `insertDefaultData()` function to add your custom data:

```php
function insertDefaultData($pdo, $prefix) {
    // ... existing data ...
    
    $pdo->exec("
        INSERT IGNORE INTO `{$prefix}your_table` (`column1`, `column2`) VALUES
        ('value1', 'value2')
    ");
}
```

## File Structure After Installation

```
overland_pm/
├── install_system.php          # DELETE after installation
├── index.php                   # Main application entry
├── app/                        # Application files
│   ├── Controllers/
│   ├── Models/
│   ├── Views/
│   └── ...
├── assets/                     # CSS, JS, images
├── writable/                   # Logs, cache, uploads
└── system/                     # CodeIgniter framework
```

## Post-Installation Checklist

- [ ] Delete `install_system.php` file
- [ ] Login with admin credentials
- [ ] Change admin password
- [ ] Configure system settings
- [ ] Add your departments
- [ ] Create user accounts
- [ ] Test core functionality
- [ ] Set up email configuration
- [ ] Configure backups

## Support

For issues or questions:

1. Check the troubleshooting section above
2. Verify system requirements
3. Enable debug mode for detailed errors
4. Check server error logs
5. Review database permissions

## Version Information

- **Installation Script Version**: 1.0
- **Compatible with**: Overland PM v2.0+
- **Database Schema Version**: Latest
- **Last Updated**: October 2025

---

**🎉 Happy Project Managing!**

The Overland Project Management Team