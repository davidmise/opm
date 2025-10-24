<?php
// Fix existing announcements with empty end_date
$host = 'localhost';
$dbname = 'overland_pm_workflow';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Fixing existing announcements...</h2>";
    
    // Count announcements with empty/invalid end_date
    $count_stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM opm_announcements 
        WHERE deleted = 0 
        AND (end_date IS NULL OR end_date = '' OR end_date = '0000-00-00' OR end_date = '0000-00-00 00:00:00')
    ");
    $count = $count_stmt->fetchColumn();
    
    echo "<p>Found <strong>$count</strong> announcements with empty/invalid end_date.</p>";
    
    if ($count > 0) {
        // First, check the column type
        $col_info = $pdo->query("SHOW COLUMNS FROM opm_announcements WHERE Field = 'end_date'")->fetch(PDO::FETCH_ASSOC);
        echo "<p>End_date column type: <strong>{$col_info['Type']}</strong></p>";
        
        // Update empty strings to NULL first
        $update1 = $pdo->exec("UPDATE opm_announcements SET end_date = NULL WHERE end_date = ''");
        echo "<p style='color: blue;'>✓ Converted <strong>$update1</strong> empty strings to NULL.</p>";
        
        // Update invalid dates
        $update2 = $pdo->exec("UPDATE opm_announcements SET end_date = NULL WHERE end_date IN ('0000-00-00', '0000-00-00 00:00:00')");
        echo "<p style='color: blue;'>✓ Converted <strong>$update2</strong> invalid dates to NULL.</p>";
        
        echo "<p style='color: green;'>✓ Total updated: <strong>" . ($update1 + $update2) . "</strong> announcements now never expire.</p>";
    }
    
    // Show current status
    echo "<h3>Current announcements status:</h3>";
    $stmt = $pdo->query("
        SELECT 
            id, 
            title, 
            category,
            priority,
            start_date, 
            end_date,
            CASE 
                WHEN end_date IS NULL THEN 'Active (Never Expires)'
                WHEN end_date >= CURDATE() THEN 'Active'
                ELSE 'Expired'
            END as status
        FROM opm_announcements 
        WHERE deleted = 0 
        ORDER BY id DESC
    ");
    
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>ID</th><th>Title</th><th>Category</th><th>Priority</th><th>Start Date</th><th>End Date</th><th>Status</th>";
    echo "</tr>";
    
    foreach ($announcements as $ann) {
        $status_color = ($ann['status'] == 'Expired') ? 'color: red;' : 'color: green;';
        echo "<tr>";
        echo "<td>{$ann['id']}</td>";
        echo "<td>" . htmlspecialchars($ann['title']) . "</td>";
        echo "<td>" . htmlspecialchars($ann['category'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($ann['priority'] ?? 'NULL') . "</td>";
        echo "<td>{$ann['start_date']}</td>";
        echo "<td>" . ($ann['end_date'] ?? '<em>Never Expires</em>') . "</td>";
        echo "<td style='$status_color'><strong>{$ann['status']}</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3 style='color: green;'>✓ Done! All announcements should now show as Active.</h3>";
    echo "<p><a href='index.php/departments/announcements'>Refresh Announcements Page</a></p>";
    
} catch (PDOException $e) {
    echo "<h3 style='color: red;'>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
