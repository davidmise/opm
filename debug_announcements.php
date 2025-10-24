<?php
// Debug script to check announcement data
try {
    $pdo = new PDO('mysql:host=localhost;dbname=overland_pm_workflow', 'root', '');
    $stmt = $pdo->query('SELECT id, title, share_with, category, priority, created_at FROM opm_announcements ORDER BY id DESC LIMIT 5');
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Recent Announcements Debug</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Share With</th><th>Category</th><th>Priority</th><th>Created</th></tr>";
    
    foreach ($announcements as $ann) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($ann['id']) . "</td>";
        echo "<td>" . htmlspecialchars($ann['title']) . "</td>";
        echo "<td style='color: red; font-weight: bold;'>" . htmlspecialchars($ann['share_with']) . "</td>";
        echo "<td>" . htmlspecialchars($ann['category']) . "</td>";
        echo "<td>" . htmlspecialchars($ann['priority']) . "</td>";
        echo "<td>" . htmlspecialchars($ann['created_at']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Also check if departments table exists and has data
    echo "<h3>Departments</h3>";
    $stmt2 = $pdo->query('SELECT id, title FROM opm_departments ORDER BY id');
    $departments = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($departments as $dept) {
        echo "Department ID: " . $dept['id'] . ", Title: " . $dept['title'] . "<br>";
    }
    
} catch (Exception $e) {
    echo 'Database Error: ' . $e->getMessage();
}
?>