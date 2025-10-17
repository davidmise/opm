<?php
// Simple database test
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "overland_pm";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Testing workflow data...\n\n";
    
    // Test 1: Count total shipments
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM opm_workflow_shipments");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total shipments: " . $count['count'] . "\n";
    
    // Test 2: Get shipments with status breakdown
    echo "\nStatus breakdown:\n";
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM opm_workflow_shipments GROUP BY status");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['status']}: {$row['count']}\n";
    }
    
    // Test 3: Get phases breakdown
    echo "\nPhase breakdown:\n";
    $stmt = $pdo->query("SELECT current_phase, COUNT(*) as count FROM opm_workflow_shipments GROUP BY current_phase");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['current_phase']}: {$row['count']}\n";
    }
    
    // Test 4: Get recent shipments
    echo "\nRecent shipments:\n";
    $stmt = $pdo->query("SELECT s.shipment_number, s.status, s.current_phase, s.cargo_type, c.company_name 
                         FROM opm_workflow_shipments s 
                         LEFT JOIN opm_clients c ON s.client_id = c.id 
                         ORDER BY s.created_at DESC LIMIT 5");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $company = $row['company_name'] ?: 'Unknown Client';
        echo "- {$row['shipment_number']}: {$company} - {$row['status']} ({$row['current_phase']})\n";
    }
    
    echo "\nDone!\n";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>