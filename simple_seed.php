<?php
/**
 * Simple Workflow System Seed Data
 */

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'overland_pm_workflow';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully\n";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Get existing clients
$clients = $pdo->query("SELECT id, company_name FROM opm_clients WHERE deleted = 0 LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
if (empty($clients)) {
    die("No clients found. Please create some clients first.\n");
}

echo "Found " . count($clients) . " clients to use for shipments.\n";

// Tanzanian data
$cargo_types = [
    'Coffee Beans', 'Gold Ore', 'Cement', 'Cotton Textiles', 'Cashew Nuts',
    'Sisal Fiber', 'Spices', 'Steel Products', 'Tobacco', 'Tea',
    'Copper Concentrate', 'Diamonds', 'Sunflower Oil', 'Maize', 'Rice'
];

$tanzanian_ports = [
    'Dar es Salaam Port', 'Tanga Port', 'Mtwara Port', 'Kilindoni Port', 
    'Stone Town Port (Zanzibar)', 'Mwanza Port', 'Bukoba Port', 'Musoma Port'
];

$international_ports = [
    'Mombasa (Kenya)', 'Dubai (UAE)', 'Rotterdam (Netherlands)', 'Hamburg (Germany)', 
    'Shanghai (China)', 'Mumbai (India)', 'Cape Town (South Africa)', 'London (UK)'
];

echo "Starting to seed workflow data...\n";

// Clear existing workflow data
$pdo->exec("DELETE FROM opm_workflow_shipments WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_tasks WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_documents WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_tracking WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_trucks WHERE 1");

echo "Cleared existing workflow data.\n";

// Create shipments
$shipments_created = 0;
for ($i = 1; $i <= 15; $i++) {
    $client = $clients[array_rand($clients)];
    $cargo_type = $cargo_types[array_rand($cargo_types)];
    $origin_port = $tanzanian_ports[array_rand($tanzanian_ports)];
    $destination_port = $international_ports[array_rand($international_ports)];
    
    $created_date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 90) . ' days'));
    $estimated_arrival = date('Y-m-d H:i:s', strtotime($created_date . ' +' . rand(15, 45) . ' days'));
    
    $statuses = ['active', 'completed', 'cancelled'];
    $phases = ['clearing_intake', 'regulatory_processing', 'internal_review', 'transport_loading', 'tracking'];
    
    $status = $statuses[array_rand($statuses)];
    $phase = $phases[array_rand($phases)];
    
    $shipment_number = 'SH-' . date('Y') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
    $cargo_weight = rand(5, 1000) . '.' . rand(0, 99);
    $cargo_value = rand(10000, 500000);
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO opm_workflow_shipments (
                shipment_number, client_id, cargo_type, cargo_weight, cargo_value,
                origin_port, destination_port, final_destination, estimated_arrival,
                current_phase, status, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $shipment_number, $client['id'], $cargo_type, $cargo_weight, $cargo_value,
            $origin_port, $destination_port, $destination_port, $estimated_arrival,
            $phase, $status, $created_date, $created_date
        ]);
        
        $shipments_created++;
        echo "Created shipment: $shipment_number - {$client['company_name']} - $cargo_type\n";
        
    } catch (Exception $e) {
        echo "Failed to create shipment $shipment_number: " . $e->getMessage() . "\n";
    }
}

echo "\n" . "=".str_repeat("=", 60) . "=\n";
echo "Simple Workflow Data Seeding Complete!\n";
echo "=".str_repeat("=", 60) . "=\n";
echo "Created $shipments_created shipments with realistic Tanzanian data.\n";
echo "You can now access the workflow system to see the data.\n";

?>