<?php

$db_host = 'localhost';
$db_name = 'overland_pm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Listing all workflow-related tables:\n\n";
    
    $sql = "SHOW TABLES LIKE '%workflow%'";
    $result = $pdo->query($sql);
    
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $table = $row[0];
        echo "Table: $table\n";
        
        // Get row count
        try {
            $count_sql = "SELECT COUNT(*) as count FROM `$table`";
            $count_result = $pdo->query($count_sql);
            $count = $count_result->fetch(PDO::FETCH_ASSOC)['count'];
            echo "  Rows: $count\n";
        } catch (PDOException $e) {
            echo "  Error counting: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    echo "\nListing escalation/handover/approval/cost tables:\n\n";
    $sql2 = "SHOW TABLES LIKE '%escalat%'";
    $result2 = $pdo->query($sql2);
    while ($row = $result2->fetch(PDO::FETCH_NUM)) {
        echo "Found: " . $row[0] . "\n";
    }
    
    $sql3 = "SHOW TABLES LIKE '%handover%'";
    $result3 = $pdo->query($sql3);
    while ($row = $result3->fetch(PDO::FETCH_NUM)) {
        echo "Found: " . $row[0] . "\n";
    }
    
    $sql4 = "SHOW TABLES LIKE '%approval%'";
    $result4 = $pdo->query($sql4);
    while ($row = $result4->fetch(PDO::FETCH_NUM)) {
        echo "Found: " . $row[0] . "\n";
    }
    
    $sql5 = "SHOW TABLES LIKE '%shipment%'";
    $result5 = $pdo->query($sql5);
    while ($row = $result5->fetch(PDO::FETCH_NUM)) {
        echo "Found: " . $row[0] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
