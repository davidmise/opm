<?php
// Direct database check for announcements
$host = 'localhost';
$dbname = 'overland_pm_workflow';  // CORRECT DATABASE!
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Checking announcement table in database: <strong>$dbname</strong></h2>";
    
    // Check opm_announcements table (correct prefix)
    echo "<h3>Table: opm_announcements</h3>";
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM opm_announcements WHERE deleted = 0")->fetchColumn();
        echo "<p>Active Records: <strong>$count</strong></p>";
        
        $stmt = $pdo->query("
            SELECT id, title, category, priority, share_with, start_date, end_date, created_by, created_at 
            FROM opm_announcements 
            WHERE deleted = 0
            ORDER BY id DESC 
            LIMIT 10
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($data)) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th>ID</th><th>Title</th><th>Category</th><th>Priority</th><th>Share With</th>";
            echo "<th>Start Date</th><th>End Date</th><th>Created By</th><th>Created At</th>";
            echo "</tr>";
            
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['category'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['priority'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['share_with'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['start_date'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['end_date'] ?? 'NULL') . "</td>";
                echo "<td>{$row['created_by']}</td>";
                echo "<td>{$row['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'><strong>No records found!</strong></p>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<p>The table might not exist. Checking what tables DO exist...</p>";
        
        $tables = $pdo->query("SHOW TABLES LIKE '%announcement%'")->fetchAll(PDO::FETCH_COLUMN);
        if (!empty($tables)) {
            echo "<p>Found tables:</p><ul>";
            foreach ($tables as $table) {
                echo "<li><strong>$table</strong></li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>No announcement tables found in this database!</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h3>Database Connection Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
