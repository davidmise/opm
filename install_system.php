<?php
/**
 * Overland Project Management - Complete System Installation
 * 
 * This script creates all required database tables for the system to work properly.
 * Simply access this file via browser to install the complete system.
 * 
 * Usage: http://yourdomain.com/overland_pm/install_system.php
 */

// Database configuration - UPDATE THESE VALUES FOR YOUR SERVER
$db_config = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'overland_pm_workflow',
    'DBPrefix' => 'opm_'
);

// Set execution time limit for large installations
set_time_limit(300); // 5 minutes

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overland PM - System Installation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; }
        .step { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .progress { background: #e9ecef; height: 20px; border-radius: 10px; margin: 10px 0; }
        .progress-bar { background: #007bff; height: 100%; border-radius: 10px; transition: width 0.3s ease; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; font-size: 12px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #0056b3; }
        .config-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .status-created { color: #28a745; font-weight: bold; }
        .status-exists { color: #6c757d; }
        .status-error { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Overland Project Management - System Installation</h1>
        
        <?php if (!isset($_GET['install'])): ?>
            <div class="step info">
                <h3>📋 Pre-Installation Checklist</h3>
                <p>Before proceeding, please ensure:</p>
                <ul>
                    <li>✅ PHP 7.4+ is installed</li>
                    <li>✅ MySQL/MariaDB is running</li>
                    <li>✅ Database connection details are correct</li>
                    <li>✅ Web server has write permissions</li>
                </ul>
            </div>

            <div class="config-section">
                <h3>⚙️ Database Configuration</h3>
                <table>
                    <tr><th>Setting</th><th>Value</th></tr>
                    <tr><td>Host</td><td><?php echo htmlspecialchars($db_config['hostname']); ?></td></tr>
                    <tr><td>Database</td><td><?php echo htmlspecialchars($db_config['database']); ?></td></tr>
                    <tr><td>Username</td><td><?php echo htmlspecialchars($db_config['username']); ?></td></tr>
                    <tr><td>Password</td><td><?php echo str_repeat('*', strlen($db_config['password'])); ?></td></tr>
                    <tr><td>Table Prefix</td><td><?php echo htmlspecialchars($db_config['DBPrefix']); ?></td></tr>
                </table>
                <p><strong>⚠️ Important:</strong> Update the database configuration at the top of this file before installation.</p>
            </div>
            
            <div class="step warning">
                <h3>⚠️ Installation Warning</h3>
                <p><strong>This installation will:</strong></p>
                <ul>
                    <li>Create all required database tables</li>
                    <li>Set up department and workflow systems</li>
                    <li>Create default admin user and sample data</li>
                    <li>Configure system settings</li>
                </ul>
                <p><strong>🔥 CAUTION:</strong> If tables already exist, they may be dropped and recreated!</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="?install=start" class="btn">🚀 Start Installation</a>
            </div>
            
        <?php else: ?>
            
            <div class="progress">
                <div class="progress-bar" id="progress" style="width: 0%"></div>
            </div>
            <p id="progress-text">Initializing installation...</p>
            
            <?php
            $installation_log = array();
            $errors = array();
            $success_count = 0;
            $total_steps = 15;
            
            // Step 1: Test database connection
            updateProgress(1, $total_steps, "Testing database connection...");
            try {
                $pdo = new PDO(
                    "mysql:host={$db_config['hostname']};charset=utf8mb4", 
                    $db_config['username'], 
                    $db_config['password'],
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    )
                );
                $installation_log[] = "✅ Database connection successful";
                $success_count++;
            } catch (Exception $e) {
                $errors[] = "❌ Database connection failed: " . $e->getMessage();
                echo '<div class="step error"><h3>Database Connection Failed</h3><p>' . htmlspecialchars($e->getMessage()) . '</p></div>';
                exit;
            }
            
            // Step 2: Create database if not exists
            updateProgress(2, $total_steps, "Creating database...");
            try {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$db_config['database']}`");
                $installation_log[] = "✅ Database '{$db_config['database']}' ready";
                $success_count++;
            } catch (Exception $e) {
                $errors[] = "❌ Database creation failed: " . $e->getMessage();
            }
            
            // Define all tables with their SQL
            $tables = getTableDefinitions($db_config['DBPrefix']);
            
            // Step 3-12: Create tables
            $step = 3;
            foreach ($tables as $table_name => $sql) {
                updateProgress($step, $total_steps, "Creating table: {$table_name}...");
                try {
                    $pdo->exec($sql);
                    $installation_log[] = "✅ Table '{$table_name}' created successfully";
                    $success_count++;
                } catch (Exception $e) {
                    $errors[] = "❌ Failed to create table '{$table_name}': " . $e->getMessage();
                }
                $step++;
            }
            
            // Step 13: Insert default data
            updateProgress(13, $total_steps, "Inserting default data...");
            try {
                insertDefaultData($pdo, $db_config['DBPrefix']);
                $installation_log[] = "✅ Default data inserted successfully";
                $success_count++;
            } catch (Exception $e) {
                $errors[] = "❌ Failed to insert default data: " . $e->getMessage();
            }
            
            // Step 14: Create admin user
            updateProgress(14, $total_steps, "Creating admin user...");
            try {
                createAdminUser($pdo, $db_config['DBPrefix']);
                $installation_log[] = "✅ Admin user created successfully";
                $success_count++;
            } catch (Exception $e) {
                $errors[] = "❌ Failed to create admin user: " . $e->getMessage();
            }
            
            // Step 15: Final verification
            updateProgress(15, $total_steps, "Finalizing installation...");
            try {
                $table_count = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$db_config['database']}' AND table_name LIKE '{$db_config['DBPrefix']}%'")->fetch()['count'];
                $installation_log[] = "✅ Installation completed - {$table_count} tables created";
                $success_count++;
            } catch (Exception $e) {
                $errors[] = "❌ Final verification failed: " . $e->getMessage();
            }
            
            ?>
            
            <script>
                document.getElementById('progress').style.width = '100%';
                document.getElementById('progress-text').textContent = 'Installation completed!';
            </script>
            
            <!-- Installation Results -->
            <div class="step <?php echo empty($errors) ? 'success' : 'warning'; ?>">
                <h3>📊 Installation Results</h3>
                <p><strong>Success Rate:</strong> <?php echo $success_count; ?>/<?php echo $total_steps; ?> steps completed</p>
                
                <?php if (!empty($installation_log)): ?>
                    <h4>✅ Successful Operations:</h4>
                    <ul>
                        <?php foreach ($installation_log as $log): ?>
                            <li><?php echo htmlspecialchars($log); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="step error">
                        <h4>❌ Errors Encountered:</h4>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Admin Credentials -->
            <div class="step info">
                <h3>🔑 Admin Login Credentials</h3>
                <table>
                    <tr><th>Field</th><th>Value</th></tr>
                    <tr><td>Email</td><td><strong>admin@overland.pm</strong></td></tr>
                    <tr><td>Password</td><td><strong>admin123</strong></td></tr>
                    <tr><td>Login URL</td><td><a href="index.php" target="_blank">index.php</a></td></tr>
                </table>
                <p><strong>🔐 Security Note:</strong> Please change the admin password after first login!</p>
            </div>
            
            <!-- Next Steps -->
            <div class="step success">
                <h3>🎉 Installation Complete!</h3>
                <p><strong>Next Steps:</strong></p>
                <ol>
                    <li>Delete this installation file (install_system.php) for security</li>
                    <li>Login using the admin credentials above</li>
                    <li>Configure your system settings</li>
                    <li>Add departments and users</li>
                    <li>Start managing your projects!</li>
                </ol>
                <div style="text-align: center; margin: 20px 0;">
                    <a href="index.php" class="btn">🚀 Access System</a>
                    <a href="?view=tables" class="btn" style="background: #28a745;">📋 View Created Tables</a>
                </div>
            </div>
            
        <?php endif; ?>
        
        <?php if (isset($_GET['view']) && $_GET['view'] === 'tables'): ?>
            <div class="step info">
                <h3>📋 Created Database Tables</h3>
                <?php
                try {
                    $pdo = new PDO("mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset=utf8mb4", $db_config['username'], $db_config['password']);
                    $tables = $pdo->query("SHOW TABLES LIKE '{$db_config['DBPrefix']}%'")->fetchAll(PDO::FETCH_COLUMN);
                    
                    echo "<table>";
                    echo "<tr><th>#</th><th>Table Name</th><th>Status</th><th>Records</th></tr>";
                    
                    foreach ($tables as $index => $table) {
                        try {
                            $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                            echo "<tr>";
                            echo "<td>" . ($index + 1) . "</td>";
                            echo "<td>$table</td>";
                            echo "<td class='status-created'>✅ Created</td>";
                            echo "<td>$count</td>";
                            echo "</tr>";
                        } catch (Exception $e) {
                            echo "<tr>";
                            echo "<td>" . ($index + 1) . "</td>";
                            echo "<td>$table</td>";
                            echo "<td class='status-error'>❌ Error</td>";
                            echo "<td>-</td>";
                            echo "</tr>";
                        }
                    }
                    echo "</table>";
                } catch (Exception $e) {
                    echo "<p class='status-error'>Error loading table information: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
                ?>
            </div>
        <?php endif; ?>
        
    </div>
    
    <script>
        function updateProgress(current, total, message) {
            const percentage = (current / total) * 100;
            const progressBar = document.getElementById('progress');
            const progressText = document.getElementById('progress-text');
            
            if (progressBar) progressBar.style.width = percentage + '%';
            if (progressText) progressText.textContent = message;
        }
    </script>
</body>
</html>

<?php

function updateProgress($current, $total, $message) {
    $percentage = ($current / $total) * 100;
    echo "<script>updateProgress($current, $total, '" . addslashes($message) . "');</script>";
    flush();
    usleep(200000); // Small delay for visual effect
}

function getTableDefinitions($prefix) {
    return array(
        
        // Core Users Table
        $prefix . 'users' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `image` text COLLATE utf8_unicode_ci,
                `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
                `message_checked_at` datetime DEFAULT NULL,
                `notification_checked_at` datetime DEFAULT NULL,
                `is_admin` tinyint(1) NOT NULL DEFAULT '0',
                `role_id` int(11) NOT NULL DEFAULT '0',
                `note` mediumtext COLLATE utf8_unicode_ci,
                `created_at` datetime NOT NULL,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Departments Table
        $prefix . 'departments' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}departments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `description` text COLLATE utf8_unicode_ci,
                `color` varchar(7) COLLATE utf8_unicode_ci DEFAULT '#007bff',
                `status` enum('active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
                `manager_id` int(11) DEFAULT NULL,
                `created_by` int(11) NOT NULL,
                `created_at` datetime NOT NULL,
                `modified_at` datetime DEFAULT NULL,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `manager_id` (`manager_id`),
                KEY `created_by` (`created_by`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Department Members Table
        $prefix . 'department_members' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}department_members` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `department_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `is_primary` tinyint(1) NOT NULL DEFAULT '0',
                `role` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'member',
                `joined_at` datetime NOT NULL,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `dept_user_unique` (`department_id`, `user_id`),
                KEY `department_id` (`department_id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Projects Table
        $prefix . 'projects' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}projects` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` text COLLATE utf8_unicode_ci NOT NULL,
                `description` mediumtext COLLATE utf8_unicode_ci,
                `start_date` date NOT NULL,
                `deadline` date NOT NULL,
                `status` enum('open','completed','hold','cancelled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
                `priority` enum('low','normal','high','urgent') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
                `department_id` int(11) DEFAULT NULL,
                `created_by` int(11) NOT NULL,
                `created_date` datetime NOT NULL,
                `modified_by` int(11) DEFAULT NULL,
                `modified_date` datetime DEFAULT NULL,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `status` (`status`),
                KEY `department_id` (`department_id`),
                KEY `created_by` (`created_by`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Project Members Table
        $prefix . 'project_members' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}project_members` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `project_id` int(11) NOT NULL,
                `is_leader` tinyint(1) NOT NULL DEFAULT '0',
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `user_project` (`user_id`, `project_id`),
                KEY `project_id` (`project_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Tasks Table
        $prefix . 'tasks' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}tasks` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` text COLLATE utf8_unicode_ci NOT NULL,
                `description` mediumtext COLLATE utf8_unicode_ci,
                `project_id` int(11) NOT NULL,
                `milestone_id` int(11) NOT NULL DEFAULT '0',
                `assigned_to` int(11) NOT NULL DEFAULT '0',
                `collaborators` text COLLATE utf8_unicode_ci,
                `status_id` int(11) NOT NULL DEFAULT '1',
                `priority` enum('low','normal','high','urgent') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
                `start_date` date DEFAULT NULL,
                `deadline` date DEFAULT NULL,
                `created_date` datetime NOT NULL,
                `created_by` int(11) NOT NULL,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `assigned_to` (`assigned_to`),
                KEY `status_id` (`status_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Task Status Table
        $prefix . 'task_status' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}task_status` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `key_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
                `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#83c340',
                `sort` int(11) NOT NULL DEFAULT '0',
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Announcements Table
        $prefix . 'announcements' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}announcements` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` text COLLATE utf8_unicode_ci NOT NULL,
                `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
                `category` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'general',
                `priority` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'normal',
                `start_date` datetime NOT NULL,
                `end_date` datetime DEFAULT NULL,
                `created_by` int(11) NOT NULL,
                `created_at` datetime NOT NULL,
                `share_with` text COLLATE utf8_unicode_ci,
                `files` text COLLATE utf8_unicode_ci,
                `read_by` text COLLATE utf8_unicode_ci,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `created_by` (`created_by`),
                KEY `start_date` (`start_date`),
                KEY `category` (`category`),
                KEY `priority` (`priority`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Team Member Tasks (Workflow)
        $prefix . 'team_member_tasks' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}team_member_tasks` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `task_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `description` text COLLATE utf8_unicode_ci,
                `assigned_to` int(11) NOT NULL,
                `assigned_by` int(11) NOT NULL,
                `priority` enum('low','normal','high','urgent') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
                `status` enum('pending','in_progress','completed','cancelled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
                `department_id` int(11) DEFAULT NULL,
                `due_date` datetime DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                `completed_at` datetime DEFAULT NULL,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `assigned_to` (`assigned_to`),
                KEY `assigned_by` (`assigned_by`),
                KEY `department_id` (`department_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Team Member Job Info
        $prefix . 'team_member_job_info' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}team_member_job_info` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `job_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                `department_id` int(11) DEFAULT NULL,
                `salary` decimal(10,2) DEFAULT NULL,
                `salary_term` enum('monthly','yearly','hourly') COLLATE utf8_unicode_ci DEFAULT 'monthly',
                `date_of_hire` date DEFAULT NULL,
                `deleted` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `user_id` (`user_id`),
                KEY `department_id` (`department_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ",
        
        // Settings Table
        $prefix . 'settings' => "
            CREATE TABLE IF NOT EXISTS `{$prefix}settings` (
                `setting_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `setting_value` mediumtext COLLATE utf8_unicode_ci NOT NULL,
                `category` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'general',
                `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
                PRIMARY KEY (`setting_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        "
    );
}

function insertDefaultData($pdo, $prefix) {
    // Insert default task statuses
    $pdo->exec("
        INSERT IGNORE INTO `{$prefix}task_status` (`id`, `title`, `key_name`, `color`, `sort`) VALUES
        (1, 'To Do', 'to_do', '#6c757d', 1),
        (2, 'In Progress', 'in_progress', '#007bff', 2),
        (3, 'Done', 'done', '#28a745', 3),
        (4, 'Cancelled', 'cancelled', '#dc3545', 4)
    ");
    
    // Insert system settings
    $pdo->exec("
        INSERT IGNORE INTO `{$prefix}settings` (`setting_name`, `setting_value`, `category`, `type`) VALUES
        ('app_title', 'Overland Project Management', 'general', 'text'),
        ('language', 'english', 'general', 'text'),
        ('timezone', 'UTC', 'general', 'text'),
        ('currency_symbol', '$', 'general', 'text'),
        ('date_format', 'Y-m-d', 'general', 'text'),
        ('time_format', 'H:i:s', 'general', 'text'),
        ('first_day_of_week', '0', 'general', 'text'),
        ('enable_registration', '0', 'general', 'text'),
        ('email_protocol', 'mail', 'email', 'text'),
        ('email_smtp_host', '', 'email', 'text'),
        ('email_smtp_port', '587', 'email', 'text'),
        ('email_smtp_user', '', 'email', 'text'),
        ('email_smtp_pass', '', 'email', 'password')
    ");
    
    // Insert default departments
    $pdo->exec("
        INSERT IGNORE INTO `{$prefix}departments` (`id`, `title`, `description`, `color`, `status`, `created_by`, `created_at`) VALUES
        (1, 'Administration', 'Administrative and management tasks', '#007bff', 'active', 1, NOW()),
        (2, 'Development', 'Software development and technical tasks', '#28a745', 'active', 1, NOW()),
        (3, 'Design', 'UI/UX and graphic design', '#6f42c1', 'active', 1, NOW()),
        (4, 'Marketing', 'Marketing and promotional activities', '#fd7e14', 'active', 1, NOW())
    ");
}

function createAdminUser($pdo, $prefix) {
    // Check if admin user already exists
    $existing = $pdo->query("SELECT COUNT(*) FROM `{$prefix}users` WHERE email = 'admin@overland.pm'")->fetchColumn();
    
    if ($existing == 0) {
        // Create admin user
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO `{$prefix}users` 
            (`first_name`, `last_name`, `email`, `password`, `is_admin`, `status`, `created_at`) VALUES
            ('System', 'Administrator', 'admin@overland.pm', '$password_hash', 1, 'active', NOW())
        ");
        
        // Add admin to Administration department
        $pdo->exec("
            INSERT IGNORE INTO `{$prefix}department_members` 
            (`department_id`, `user_id`, `is_primary`, `role`, `joined_at`) VALUES
            (1, 1, 1, 'manager', NOW())
        ");
        
        // Add job info for admin
        $pdo->exec("
            INSERT IGNORE INTO `{$prefix}team_member_job_info` 
            (`user_id`, `job_title`, `department_id`, `date_of_hire`) VALUES
            (1, 'System Administrator', 1, CURDATE())
        ");
    }
}

?>