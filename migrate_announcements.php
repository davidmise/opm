<?php
/**
 * Complete Database Migration Script for Announcements System
 * This script creates all required tables with the opm_ prefix if they don't exist
 */

$host = 'localhost';
$dbname = 'overland_pm_workflow';
$username = 'root';
$password = '';
$prefix = 'opm_';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Database Migration - Announcements System</h1>";
    echo "<p>Database: <strong>$dbname</strong> | Prefix: <strong>$prefix</strong></p>";
    echo "<hr>";
    
    $created = [];
    $existed = [];
    $modified = [];
    $errors = [];
    
    // ========================================
    // 1. CREATE ANNOUNCEMENTS TABLE
    // ========================================
    $table = $prefix . 'announcements';
    $check = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount();
    
    if ($check == 0) {
        $sql = "CREATE TABLE `{$table}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` text NOT NULL,
            `description` mediumtext NOT NULL,
            `category` varchar(50) DEFAULT 'general',
            `priority` varchar(20) DEFAULT 'normal',
            `start_date` datetime NOT NULL,
            `end_date` datetime DEFAULT NULL,
            `created_by` int(11) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `share_with` mediumtext DEFAULT NULL,
            `files` text DEFAULT NULL,
            `read_by` mediumtext DEFAULT NULL,
            `deleted` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `created_by` (`created_by`),
            KEY `deleted` (`deleted`),
            KEY `start_date` (`start_date`),
            KEY `end_date` (`end_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        $created[] = $table;
        echo "<p style='color: green;'>✓ Created table: <strong>$table</strong></p>";
    } else {
        $existed[] = $table;
        echo "<p style='color: blue;'>→ Table exists: <strong>$table</strong></p>";
        
        // Check and add missing columns
        $columns = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('category', $columns)) {
            $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `category` VARCHAR(50) DEFAULT 'general' AFTER `description`");
            $modified[] = "$table (added category)";
            echo "<p style='color: orange;'>  ↳ Added column: <strong>category</strong></p>";
        }
        
        if (!in_array('priority', $columns)) {
            $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `priority` VARCHAR(20) DEFAULT 'normal' AFTER `category`");
            $modified[] = "$table (added priority)";
            echo "<p style='color: orange;'>  ↳ Added column: <strong>priority</strong></p>";
        }
        
        // Fix end_date to allow NULL
        $col_info = $pdo->query("SHOW COLUMNS FROM `{$table}` WHERE Field = 'end_date'")->fetch(PDO::FETCH_ASSOC);
        if ($col_info && strpos($col_info['Null'], 'NO') !== false) {
            $pdo->exec("ALTER TABLE `{$table}` MODIFY COLUMN `end_date` DATETIME DEFAULT NULL");
            $modified[] = "$table (end_date now nullable)";
            echo "<p style='color: orange;'>  ↳ Modified column: <strong>end_date</strong> (now allows NULL)</p>";
        }
    }
    
    // ========================================
    // 2. CREATE DEPARTMENTS TABLE (if not exists)
    // ========================================
    $table = $prefix . 'team';
    $check = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount();
    
    if ($check == 0) {
        $sql = "CREATE TABLE `{$table}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` text NOT NULL,
            `description` mediumtext DEFAULT NULL,
            `members` mediumtext DEFAULT NULL,
            `created_date` date NOT NULL,
            `deleted` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `deleted` (`deleted`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        $created[] = $table;
        echo "<p style='color: green;'>✓ Created table: <strong>$table</strong></p>";
    } else {
        $existed[] = $table;
        echo "<p style='color: blue;'>→ Table exists: <strong>$table</strong></p>";
    }
    
    // ========================================
    // 3. FIX EXISTING DATA
    // ========================================
    echo "<hr><h3>Data Cleanup</h3>";
    
    $announcements_table = $prefix . 'announcements';
    
    // Fix invalid created_at dates FIRST (this is causing the error)
    $fixed_created = $pdo->exec("UPDATE `{$announcements_table}` SET created_at = NOW() WHERE created_at IN ('0000-00-00 00:00:00', '0000-00-00', '') OR created_at IS NULL");
    if ($fixed_created > 0) {
        echo "<p style='color: green;'>✓ Fixed $fixed_created invalid created_at values</p>";
    }
    
    // Fix empty end_dates
    $pdo->exec("UPDATE `{$announcements_table}` SET end_date = NULL WHERE end_date = ''");
    $fixed = $pdo->exec("UPDATE `{$announcements_table}` SET end_date = NULL WHERE end_date IN ('0000-00-00', '0000-00-00 00:00:00')");
    echo "<p style='color: green;'>✓ Fixed invalid end_date values in existing announcements</p>";
    
    // Fix invalid start_dates
    $fixed_start = $pdo->exec("UPDATE `{$announcements_table}` SET start_date = NOW() WHERE start_date IN ('0000-00-00 00:00:00', '0000-00-00', '') OR start_date IS NULL");
    if ($fixed_start > 0) {
        echo "<p style='color: green;'>✓ Fixed $fixed_start invalid start_date values</p>";
    }
    
    // Set default category/priority for existing records
    $updated_cat = $pdo->exec("UPDATE `{$announcements_table}` SET category = 'general' WHERE category IS NULL OR category = ''");
    if ($updated_cat > 0) {
        echo "<p style='color: green;'>✓ Set default category for $updated_cat announcements</p>";
    }
    
    $updated_pri = $pdo->exec("UPDATE `{$announcements_table}` SET priority = 'normal' WHERE priority IS NULL OR priority = ''");
    if ($updated_pri > 0) {
        echo "<p style='color: green;'>✓ Set default priority for $updated_pri announcements</p>";
    }
    
    // ========================================
    // 4. SUMMARY
    // ========================================
    echo "<hr><h2>Migration Summary</h2>";
    
    if (!empty($created)) {
        echo "<h3 style='color: green;'>✓ Tables Created:</h3><ul>";
        foreach ($created as $t) {
            echo "<li>$t</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($existed)) {
        echo "<h3 style='color: blue;'>→ Tables Already Existed:</h3><ul>";
        foreach ($existed as $t) {
            echo "<li>$t</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($modified)) {
        echo "<h3 style='color: orange;'>⚠ Tables Modified:</h3><ul>";
        foreach ($modified as $m) {
            echo "<li>$m</li>";
        }
        echo "</ul>";
    }
    
    // Show current announcements status
    echo "<hr><h3>Current Announcements Status</h3>";
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN deleted = 0 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN end_date IS NULL THEN 1 ELSE 0 END) as never_expire,
            SUM(CASE WHEN end_date >= CURDATE() THEN 1 ELSE 0 END) as currently_active
        FROM `{$announcements_table}`
    ");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Total Announcements</th><td>{$stats['total']}</td></tr>";
    echo "<tr><th>Not Deleted</th><td>{$stats['active']}</td></tr>";
    echo "<tr><th>Never Expire</th><td>{$stats['never_expire']}</td></tr>";
    echo "<tr><th>Currently Active</th><td>{$stats['currently_active']}</td></tr>";
    echo "</table>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✓ Migration Complete!</h2>";
    echo "<p><strong>All tables are ready. You can now use the announcements system.</strong></p>";
    echo "<p><a href='index.php/departments/announcements' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Go to Announcements</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>❌ Error:</h3>";
    echo "<p style='color: red; padding: 10px; background: #ffe6e6; border: 1px solid red;'>";
    echo "<strong>Error Code:</strong> {$e->getCode()}<br>";
    echo "<strong>Message:</strong> {$e->getMessage()}<br>";
    echo "<strong>File:</strong> {$e->getFile()}<br>";
    echo "<strong>Line:</strong> {$e->getLine()}";
    echo "</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    h1 { color: #333; }
    h2 { color: #555; margin-top: 30px; }
    h3 { color: #666; margin-top: 20px; }
    p { line-height: 1.6; }
    ul { line-height: 1.8; }
    hr { margin: 30px 0; border: none; border-top: 2px solid #eee; }
</style>
