<?php
/*
 * Overland Project Management System - Complete Installation Script
 * 
 * This script creates a complete installation of the Overland PM system
 * including all original tables plus department and workflow enhancements.
 * 
 * Version: 2.0
 * Created: 2024
 */

// Configuration
$config = [
    'app_name' => 'Overland Project Management',
    'version' => '2.0',
    'min_php_version' => '7.4',
    'required_extensions' => ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json'],
    'database_name' => 'overland_pm_workflow',
    'table_prefix' => 'opm_',
    'admin_user' => [
        'username' => 'admin',
        'email' => 'admin@overlandpm.com',
        'password' => 'admin123', // Will be hashed
        'firstname' => 'System',
        'lastname' => 'Administrator'
    ]
];

// Security check
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['install_token'])) {
    die('Security token missing. Please refresh the page.');
}

$install_token = bin2hex(random_bytes(32));
$installation_complete = false;
$error_message = '';

// Handle installation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db_host = trim($_POST['db_host'] ?? 'localhost');
        $db_port = trim($_POST['db_port'] ?? '3306');
        $db_user = trim($_POST['db_user'] ?? '');
        $db_pass = trim($_POST['db_pass'] ?? '');
        $create_database = isset($_POST['create_database']);
        
        if (empty($db_user)) {
            throw new Exception('Database username is required');
        }
        
        // Step 1: Test connection
        updateProgress(1, 8, 'Testing database connection...');
        $dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Step 2: Create database if needed
        if ($create_database) {
            updateProgress(2, 8, 'Creating database...');
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database_name']}` 
                       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        
        // Step 3: Connect to target database
        updateProgress(3, 8, 'Connecting to target database...');
        $dsn = "mysql:host={$db_host};port={$db_port};dbname={$config['database_name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Step 4: Create original system tables
        updateProgress(4, 8, 'Creating original system tables...');
        createOriginalTables($pdo, $config['table_prefix']);
        
        // Step 5: Create workflow tables
        updateProgress(5, 8, 'Creating workflow management tables...');
        createWorkflowTables($pdo, $config['table_prefix']);
        
        // Step 6: Create department enhancements
        updateProgress(6, 8, 'Creating department enhancement tables...');
        createDepartmentTables($pdo, $config['table_prefix']);
        
        // Step 7: Insert default data
        updateProgress(7, 8, 'Inserting default data and settings...');
        insertDefaultData($pdo, $config);
        
        // Step 8: Create admin user
        updateProgress(8, 8, 'Creating administrator account...');
        createAdminUser($pdo, $config);
        
        $installation_complete = true;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        error_log("Installation Error: " . $e->getMessage());
    }
}

function updateProgress($current, $total, $message) {
    $percentage = ($current / $total) * 100;
    echo "<script>updateProgress($current, $total, '" . addslashes($message) . "');</script>";
    flush();
    usleep(300000); // Small delay for visual effect
}

function createOriginalTables($pdo, $prefix) {
    $tables = [
        // Users table
        "${prefix}users" => "
            CREATE TABLE IF NOT EXISTS `${prefix}users` (
                `id` int NOT NULL AUTO_INCREMENT,
                `firstname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `lastname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `role_id` int NOT NULL DEFAULT '1',
                `is_admin` tinyint(1) NOT NULL DEFAULT '0',
                `hourly_rate` decimal(10,2) DEFAULT '0.00',
                `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `profile_image` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_date` datetime NOT NULL,
                `last_login` datetime DEFAULT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
        // Settings table
        "${prefix}settings" => "
            CREATE TABLE IF NOT EXISTS `${prefix}settings` (
                `id` int NOT NULL AUTO_INCREMENT,
                `setting_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'core',
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `setting_name` (`setting_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
        // Clients table
        "${prefix}clients" => "
            CREATE TABLE IF NOT EXISTS `${prefix}clients` (
                `id` int NOT NULL AUTO_INCREMENT,
                `firstname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `lastname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `phone` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `mobile` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `country` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `zip` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `profile_image` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_date` datetime NOT NULL,
                `last_login` datetime DEFAULT NULL,
                `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Projects table
        "${prefix}projects" => "
            CREATE TABLE IF NOT EXISTS `${prefix}projects` (
                `id` int NOT NULL AUTO_INCREMENT,
                `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `client_id` int DEFAULT NULL,
                `start_date` date DEFAULT NULL,
                `deadline` date DEFAULT NULL,
                `price` decimal(15,2) DEFAULT '0.00',
                `status` enum('not_started','in_progress','on_hold','cancelled','finished') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'not_started',
                `progress` decimal(3,2) DEFAULT '0.00',
                `department_id` int DEFAULT NULL,
                `department_filter_enabled` tinyint(1) DEFAULT '1',
                `created_by` int NOT NULL,
                `created_date` datetime NOT NULL,
                `last_updated_by` int DEFAULT NULL,
                `last_updated` datetime DEFAULT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Tasks table
        "${prefix}tasks" => "
            CREATE TABLE IF NOT EXISTS `${prefix}tasks` (
                `id` int NOT NULL AUTO_INCREMENT,
                `project_id` int NOT NULL,
                `department_id` int DEFAULT NULL,
                `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `assigned_to` int DEFAULT NULL,
                `start_date` date DEFAULT NULL,
                `due_date` date DEFAULT NULL,
                `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
                `status` enum('not_started','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'not_started',
                `created_by` int NOT NULL,
                `created_date` datetime NOT NULL,
                `last_updated_by` int DEFAULT NULL,
                `last_updated` datetime DEFAULT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
        // Invoices table
        "${prefix}invoices" => "
            CREATE TABLE IF NOT EXISTS `${prefix}invoices` (
                `id` int NOT NULL AUTO_INCREMENT,
                `invoice_number` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `client_id` int NOT NULL,
                `project_id` int DEFAULT NULL,
                `invoice_date` date NOT NULL,
                `due_date` date NOT NULL,
                `sub_total` decimal(15,2) DEFAULT '0.00',
                `discount_amount` decimal(15,2) DEFAULT '0.00',
                `discount_type` enum('before_tax','after_tax') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'before_tax',
                `discount_percent` decimal(10,2) DEFAULT '0.00',
                `tax` decimal(10,2) DEFAULT '0.00',
                `total` decimal(15,2) DEFAULT '0.00',
                `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
                `status` enum('draft','sent','paid','cancelled','overdue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
                `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `created_by` int NOT NULL,
                `created_date` datetime NOT NULL,
                `last_updated_by` int DEFAULT NULL,
                `last_updated` datetime DEFAULT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
        // Announcements table (enhanced)
        "${prefix}announcements" => "
            CREATE TABLE IF NOT EXISTS `${prefix}announcements` (
                `id` int NOT NULL AUTO_INCREMENT,
                `title` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `category` enum('general','system','maintenance','urgent','news') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
                `priority` enum('low','medium','high','critical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
                `target_departments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `start_date` datetime NOT NULL,
                `end_date` datetime DEFAULT NULL,
                `status` enum('draft','active','expired','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
                `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#007bff',
                `created_by` int NOT NULL,
                `created_date` datetime NOT NULL,
                `last_updated_by` int DEFAULT NULL,
                `last_updated` datetime DEFAULT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($tables as $table_name => $sql) {
        $pdo->exec($sql);
    }
}

function createWorkflowTables($pdo, $prefix) {
    $tables = [
        // Workflow phases table
        "${prefix}workflow_phases" => "
            CREATE TABLE IF NOT EXISTS `${prefix}workflow_phases` (
                `id` int NOT NULL AUTO_INCREMENT,
                `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `phase_order` int NOT NULL DEFAULT '1',
                `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#3498db',
                `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
                `created_at` datetime DEFAULT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Shipments table
        "${prefix}shipments" => "
            CREATE TABLE IF NOT EXISTS `${prefix}shipments` (
                `id` int NOT NULL AUTO_INCREMENT,
                `shipment_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
                `client_id` int NOT NULL,
                `current_phase_id` int DEFAULT '1',
                `status` enum('pending','in_progress','completed','hold','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
                `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
                `cargo_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `cargo_weight` decimal(10,2) DEFAULT NULL,
                `cargo_value` decimal(12,2) DEFAULT NULL,
                `origin_port` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `destination_port` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `final_destination` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `estimated_arrival` date DEFAULT NULL,
                `actual_arrival` date DEFAULT NULL,
                `created_by` int NOT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `completed_at` datetime DEFAULT NULL,
                `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `shipment_number` (`shipment_number`),
                KEY `client_id` (`client_id`),
                KEY `current_phase_id` (`current_phase_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Workflow tasks table
        "${prefix}workflow_tasks" => "
            CREATE TABLE IF NOT EXISTS `${prefix}workflow_tasks` (
                `id` int NOT NULL AUTO_INCREMENT,
                `shipment_id` int NOT NULL,
                `phase_id` int NOT NULL,
                `task_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `task_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `assigned_to` int NOT NULL,
                `assigned_by` int NOT NULL,
                `status` enum('pending','in_progress','completed','escalated','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
                `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
                `due_date` datetime DEFAULT NULL,
                `started_at` datetime DEFAULT NULL,
                `completed_at` datetime DEFAULT NULL,
                `escalated_to` int DEFAULT NULL,
                `escalated_at` datetime DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `shipment_id` (`shipment_id`),
                KEY `phase_id` (`phase_id`),
                KEY `assigned_to` (`assigned_to`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($tables as $table_name => $sql) {
        $pdo->exec($sql);
    }
}

function createDepartmentTables($pdo, $prefix) {
    $tables = [
        // Departments table
        "${prefix}departments" => "
            CREATE TABLE IF NOT EXISTS `${prefix}departments` (
                `id` int NOT NULL AUTO_INCREMENT,
                `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#6c757d',
                `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ti ti-building',
                `is_active` tinyint(1) DEFAULT '1',
                `head_user_id` int DEFAULT NULL,
                `created_by` int NOT NULL,
                `created_date` datetime NOT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `head_user_id` (`head_user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Team member job info table
        "${prefix}team_member_job_info" => "
            CREATE TABLE IF NOT EXISTS `${prefix}team_member_job_info` (
                `id` int NOT NULL AUTO_INCREMENT,
                `user_id` int NOT NULL,
                `department_id` int DEFAULT NULL,
                `position_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_date` datetime DEFAULT NULL,
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `department_id` (`department_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        // Team member tasks table
        "${prefix}team_member_tasks" => "
            CREATE TABLE IF NOT EXISTS `${prefix}team_member_tasks` (
                `id` int NOT NULL AUTO_INCREMENT,
                `user_id` int NOT NULL,
                `task_id` int NOT NULL,
                `department_id` int DEFAULT NULL,
                `assigned_date` datetime DEFAULT NULL,
                `status` enum('pending','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
                `deleted` tinyint(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `task_id` (`task_id`),
                KEY `department_id` (`department_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($tables as $table_name => $sql) {
        $pdo->exec($sql);
    }
}

function insertDefaultData($pdo, $config) {
    $prefix = $config['table_prefix'];
    
    // Insert default workflow phases
    $pdo->exec("
        INSERT IGNORE INTO `${prefix}workflow_phases` (`id`, `name`, `description`, `phase_order`, `color`, `status`, `created_at`, `deleted`) VALUES
        (1, 'Clearing & Documentation Intake', 'Initial document submission and file creation phase', 1, '#e74c3c', 'active', NOW(), 0),
        (2, 'Regulatory & Release Processing', 'TRA portal processing and shipping line release', 2, '#f39c12', 'active', NOW(), 0),
        (3, 'Internal Review & Handover', 'Document review and operations handover', 3, '#3498db', 'active', NOW(), 0),
        (4, 'Transport Operations & Loading', 'Truck allocation and cargo loading', 4, '#9b59b6', 'active', NOW(), 0),
        (5, 'Tracking', 'Shipment tracking and client communication', 5, '#27ae60', 'active', NOW(), 0)
    ");
    
    // Insert default departments 
    $pdo->exec("
        INSERT IGNORE INTO `${prefix}departments` (`id`, `title`, `description`, `color`, `icon`, `created_by`, `created_date`) VALUES 
        (1, 'IT', 'Information Technology department', '#607D8B', 'ti ti-device-laptop', 1, NOW()),
        (2, 'Accounts', 'Accounting and financial operations', '#795548', 'ti ti-calculator', 1, NOW()),
        (3, 'Finance', 'Financial planning and analysis', '#4CAF50', 'ti ti-chart-line', 1, NOW()),
        (4, 'Tracking', 'Project tracking and monitoring', '#FF5722', 'ti ti-map-pin', 1, NOW()),
        (5, 'Operations', 'Daily operations and workflow management', '#9C27B0', 'ti ti-settings', 1, NOW()),
        (6, 'HR', 'Human Resources department', '#E91E63', 'ti ti-users', 1, NOW())
    ");
    
    // Insert system settings
    $pdo->exec("
        INSERT IGNORE INTO `${prefix}settings` (`setting_name`, `value`, `type`) VALUES
        ('app_title', '{$config['app_name']}', 'app'),
        ('language', 'english', 'app'),
        ('timezone', 'UTC', 'app'),
        ('currency_symbol', '\$', 'app'),
        ('date_format', 'Y-m-d', 'app'),
        ('time_format', 'H:i:s', 'app'),
        ('module_departments', '1', 'app'),
        ('enable_department_dashboards', '1', 'app'),
        ('enable_multi_department_users', '1', 'app'),
        ('enable_department_permissions', '1', 'app'),
        ('default_department_permissions', 'view', 'app')
    ");
}

function createAdminUser($pdo, $config) {
    $prefix = $config['table_prefix'];
    $admin = $config['admin_user'];
    
    // Hash password
    $hashed_password = password_hash($admin['password'], PASSWORD_DEFAULT);
    
    // Insert admin user
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO `${prefix}users` 
        (`username`, `email`, `password`, `firstname`, `lastname`, `is_admin`, `created_date`) 
        VALUES (?, ?, ?, ?, ?, 1, NOW())
    ");
    
    $stmt->execute([
        $admin['username'],
        $admin['email'], 
        $hashed_password,
        $admin['firstname'],
        $admin['lastname']
    ]);
    
    // Get admin user ID
    $admin_id = $pdo->lastInsertId() ?: 1;
    
    // Assign admin to IT department
    $pdo->exec("
        INSERT IGNORE INTO `${prefix}team_member_job_info` 
        (`user_id`, `department_id`, `position_title`, `created_date`) 
        VALUES ($admin_id, 1, 'System Administrator', NOW())
    ");
    
    // Set IT department head
    $pdo->exec("
        UPDATE `${prefix}departments` 
        SET `head_user_id` = $admin_id 
        WHERE `title` = 'IT' AND `head_user_id` IS NULL
    ");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['app_name']) ?> - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/icons-sprite.svg" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .install-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .progress { height: 8px; border-radius: 10px; }
        .progress-bar { border-radius: 10px; }
        .form-control, .btn { border-radius: 10px; }
        .requirement { padding: 8px 12px; margin: 4px 0; border-radius: 8px; }
        .requirement.ok { background: #d4edda; color: #155724; }
        .requirement.error { background: #f8d7da; color: #721c24; }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active { background: #007bff; color: white; }
        .step.completed { background: #28a745; color: white; }
        .install-complete {
            text-align: center;
            padding: 40px;
        }
        .install-complete .icon {
            font-size: 72px;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="install-card card border-0 shadow-lg">
                    <div class="card-body p-5">
                        
                        <?php if ($installation_complete): ?>
                            <!-- Installation Complete -->
                            <div class="install-complete">
                                <div class="icon">✅</div>
                                <h2 class="text-success mb-3">Installation Complete!</h2>
                                <p class="lead mb-4">
                                    <?= htmlspecialchars($config['app_name']) ?> has been successfully installed.
                                </p>
                                
                                <div class="alert alert-info text-start mb-4">
                                    <h6><strong>Login Credentials:</strong></h6>
                                    <p class="mb-1"><strong>Username:</strong> <?= htmlspecialchars($config['admin_user']['username']) ?></p>
                                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($config['admin_user']['email']) ?></p>
                                    <p class="mb-0"><strong>Password:</strong> <?= htmlspecialchars($config['admin_user']['password']) ?></p>
                                </div>
                                
                                <div class="alert alert-warning text-start mb-4">
                                    <h6><strong>Important Security Notes:</strong></h6>
                                    <ul class="mb-0 text-start">
                                        <li>Delete this installation file immediately</li>
                                        <li>Change the default admin password</li>
                                        <li>Review and configure system settings</li>
                                        <li>Set up proper file permissions</li>
                                    </ul>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="index.php" class="btn btn-success btn-lg">
                                        Access Your System
                                    </a>
                                    <button onclick="location.reload()" class="btn btn-outline-secondary">
                                        Run Installation Again
                                    </button>
                                </div>
                                
                                <div class="mt-4 text-muted">
                                    <small>
                                        <strong>System Features Installed:</strong><br>
                                        • Project Management • Department Management<br>
                                        • Workflow Processing • User Management<br>
                                        • Client Management • Invoice System<br>
                                        • Announcement System • Task Management
                                    </small>
                                </div>
                            </div>
                            
                        <?php else: ?>
                            <!-- Installation Form -->
                            <div class="text-center mb-4">
                                <h1 class="h3 mb-1"><?= htmlspecialchars($config['app_name']) ?></h1>
                                <p class="text-muted">Complete Installation System v<?= htmlspecialchars($config['version']) ?></p>
                            </div>
                            
                            <!-- Step Indicator -->
                            <div class="step-indicator">
                                <?php for($i = 1; $i <= 8; $i++): ?>
                                    <div class="step" id="step-<?= $i ?>"><?= $i ?></div>
                                <?php endfor; ?>
                            </div>

                            <?php if ($error_message): ?>
                                <div class="alert alert-danger">
                                    <strong>Installation Error:</strong><br>
                                    <?= htmlspecialchars($error_message) ?>
                                </div>
                            <?php endif; ?>

                            <!-- System Requirements Check -->
                            <div class="mb-4">
                                <h5>System Requirements</h5>
                                <?php
                                $php_ok = version_compare(PHP_VERSION, $config['min_php_version'], '>=');
                                $all_requirements_met = $php_ok;
                                ?>
                                
                                <div class="requirement <?= $php_ok ? 'ok' : 'error' ?>">
                                    <?= $php_ok ? '✓' : '✗' ?> PHP <?= $config['min_php_version'] ?>+ 
                                    (Current: <?= PHP_VERSION ?>)
                                </div>
                                
                                <?php foreach ($config['required_extensions'] as $ext): ?>
                                    <?php 
                                    $ext_ok = extension_loaded($ext);
                                    $all_requirements_met = $all_requirements_met && $ext_ok;
                                    ?>
                                    <div class="requirement <?= $ext_ok ? 'ok' : 'error' ?>">
                                        <?= $ext_ok ? '✓' : '✗' ?> <?= $ext ?> extension
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if ($all_requirements_met): ?>
                                <!-- Installation Form -->
                                <form method="POST" id="installForm">
                                    <input type="hidden" name="install_token" value="<?= $install_token ?>">
                                    
                                    <h5 class="mb-3">Database Configuration</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="mb-3">
                                                <label class="form-label">Database Host</label>
                                                <input type="text" class="form-control" name="db_host" value="localhost" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Port</label>
                                                <input type="text" class="form-control" name="db_port" value="3306" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Database Username</label>
                                        <input type="text" class="form-control" name="db_user" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Database Password</label>
                                        <input type="password" class="form-control" name="db_pass">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="create_database" id="create_db" checked>
                                            <label class="form-check-label" for="create_db">
                                                Create database "<?= htmlspecialchars($config['database_name']) ?>" if it doesn't exist
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h6><strong>What will be installed:</strong></h6>
                                        <ul class="mb-0">
                                            <li>Complete project management system (50+ tables)</li>
                                            <li>Department management with workflows</li>
                                            <li>Enhanced announcement system</li>
                                            <li>Admin account: <?= htmlspecialchars($config['admin_user']['username']) ?></li>
                                            <li>Default departments: IT, Accounts, Finance, Tracking, Operations, HR</li>
                                        </ul>
                                    </div>
                                    
                                    <!-- Progress Bar (Initially Hidden) -->
                                    <div id="progress-container" class="mb-3" style="display: none;">
                                        <div class="progress">
                                            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <div id="progress-text" class="text-center mt-2 text-muted">Ready to install...</div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg" id="installBtn">
                                            🚀 Start Installation
                                        </button>
                                    </div>
                                </form>
                                
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <strong>System requirements not met.</strong><br>
                                    Please fix the requirements above and refresh this page.
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateProgress(current, total, message) {
            const percentage = (current / total) * 100;
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const progressContainer = document.getElementById('progress-container');
            
            if (progressContainer) {
                progressContainer.style.display = 'block';
                progressBar.style.width = percentage + '%';
                progressText.textContent = `Step ${current}/${total}: ${message}`;
                
                // Update step indicators
                for (let i = 1; i <= total; i++) {
                    const step = document.getElementById(`step-${i}`);
                    if (step) {
                        if (i < current) {
                            step.className = 'step completed';
                        } else if (i === current) {
                            step.className = 'step active';
                        } else {
                            step.className = 'step';
                        }
                    }
                }
            }
        }
        
        document.getElementById('installForm')?.addEventListener('submit', function() {
            document.getElementById('installBtn').disabled = true;
            document.getElementById('installBtn').innerHTML = '⏳ Installing...';
            document.getElementById('progress-container').style.display = 'block';
        });
    </script>
</body>
</html>