<?php
// Simple database check without CodeIgniter
$host = 'localhost';
$dbname = 'overland_pm_workflow';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Check if shipments table exists and has data
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM opm_workflow_shipments");
    $stmt->execute();
    $count = $stmt->fetch()['count'];
    
    echo "Total shipments: $count\n";
    
    if ($count > 0) {
        $stmt = $pdo->prepare("SELECT id, shipment_number, status FROM opm_workflow_shipments LIMIT 5");
        $stmt->execute();
        $shipments = $stmt->fetchAll();
        
        echo "Sample shipments:\n";
        foreach ($shipments as $shipment) {
            echo "- ID: {$shipment['id']}, Number: {$shipment['shipment_number']}, Status: {$shipment['status']}\n";
        }
    } else {
        echo "No shipments found. Creating sample data...\n";
        
        $stmt = $pdo->prepare("INSERT INTO opm_workflow_shipments 
            (shipment_number, client_id, cargo_type, cargo_weight, status, current_phase, origin_port, destination_port, final_destination, estimated_arrival, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $test_data = [
            'SHP-TEST-001',
            1, // client_id
            'Electronics',
            '2.5',
            'active',
            'clearing_intake',
            'Dar es Salaam',
            'Mombasa',
            'Nairobi',
            date('Y-m-d', strtotime('+7 days')),
            date('Y-m-d H:i:s')
        ];
        
        if ($stmt->execute($test_data)) {
            $new_id = $pdo->lastInsertId();
            echo "Created test shipment with ID: $new_id\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>