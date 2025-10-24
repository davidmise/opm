<?php
// Add missing columns to announcements table
$host = 'localhost';
$dbname = 'overland_pm_workflow';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Adding missing columns to opm_announcements table...</h2>";
    
    // Check current structure
    echo "<h3>Current table structure:</h3>";
    $columns = $pdo->query("SHOW COLUMNS FROM opm_announcements")->fetchAll(PDO::FETCH_ASSOC);
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li><strong>{$col['Field']}</strong> - {$col['Type']}</li>";
    }
    echo "</ul>";
    
    // Check if category column exists
    $has_category = false;
    $has_priority = false;
    foreach ($columns as $col) {
        if ($col['Field'] == 'category') $has_category = true;
        if ($col['Field'] == 'priority') $has_priority = true;
    }
    
    // Add category column
    if (!$has_category) {
        echo "<p style='color: blue;'>Adding <strong>category</strong> column...</p>";
        $pdo->exec("ALTER TABLE opm_announcements ADD COLUMN category VARCHAR(50) DEFAULT 'general' AFTER description");
        echo "<p style='color: green;'>✓ Category column added successfully!</p>";
    } else {
        echo "<p style='color: orange;'>Category column already exists.</p>";
    }
    
    // Add priority column
    if (!$has_priority) {
        echo "<p style='color: blue;'>Adding <strong>priority</strong> column...</p>";
        $pdo->exec("ALTER TABLE opm_announcements ADD COLUMN priority VARCHAR(20) DEFAULT 'normal' AFTER category");
        echo "<p style='color: green;'>✓ Priority column added successfully!</p>";
    } else {
        echo "<p style='color: orange;'>Priority column already exists.</p>";
    }
    
    // Show updated structure
    echo "<h3>Updated table structure:</h3>";
    $columns = $pdo->query("SHOW COLUMNS FROM opm_announcements")->fetchAll(PDO::FETCH_ASSOC);
    echo "<ul>";
    foreach ($columns as $col) {
        $highlight = ($col['Field'] == 'category' || $col['Field'] == 'priority') ? 'style="color: green; font-weight: bold;"' : '';
        echo "<li $highlight><strong>{$col['Field']}</strong> - {$col['Type']}</li>";
    }
    echo "</ul>";
    
    echo "<h3 style='color: green;'>✓ Done! You can now create announcements with category and priority.</h3>";
    echo "<p><a href='index.php/departments/announcements'>Go to Announcements Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
