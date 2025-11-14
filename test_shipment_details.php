<?php
// Simple test to check shipment details functionality
require_once 'app/Config/Autoload.php';
require_once 'system/bootstrap.php';

// Initialize database connection
$db = \Config\Database::connect();

// Test if we have any shipments
$shipments_count = $db->table('opm_workflow_shipments')->countAllResults();
echo "Total shipments in database: " . $shipments_count . "\n";

// Get a shipment ID to test with
$shipment = $db->table('opm_workflow_shipments')->select('id, shipment_number')->limit(1)->get()->getRow();

if ($shipment) {
    echo "Found shipment ID: " . $shipment->id . " with number: " . $shipment->shipment_number . "\n";
    echo "Test URL: http://localhost/overland_pm/index.php/workflow/shipment_details/" . $shipment->id . "\n";
} else {
    echo "No shipments found in database\n";
    echo "Creating test shipment...\n";
    
    // Create a test shipment
    $test_data = [
        'shipment_number' => 'SHP-TEST-001',
        'client_id' => 1,
        'cargo_type' => 'Electronics',
        'cargo_weight' => '2.5',
        'status' => 'active',
        'current_phase' => 'clearing_intake',
        'origin_port' => 'Dar es Salaam',
        'destination_port' => 'Mombasa',
        'final_destination' => 'Nairobi',
        'estimated_arrival' => date('Y-m-d', strtotime('+7 days')),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    try {
        $insert_result = $db->table('opm_workflow_shipments')->insert($test_data);
        if ($insert_result) {
            $new_id = $db->insertID();
            echo "Created test shipment with ID: " . $new_id . "\n";
            echo "Test URL: http://localhost/overland_pm/index.php/workflow/shipment_details/" . $new_id . "\n";
        } else {
            echo "Failed to create test shipment\n";
        }
    } catch (Exception $e) {
        echo "Error creating test shipment: " . $e->getMessage() . "\n";
    }
}

// Test the controller method directly
echo "\n=== Testing Controller Method ===\n";
try {
    // Instantiate the controller
    $workflow = new \App\Controllers\Workflow();
    
    // Test with shipment ID 1 (or the found shipment ID)
    $test_id = $shipment ? $shipment->id : 1;
    
    // Call the private method to get shipment details
    $reflection = new ReflectionClass($workflow);
    $method = $reflection->getMethod('_get_shipment_details');
    $method->setAccessible(true);
    
    $result = $method->invoke($workflow, $test_id);
    
    if ($result) {
        echo "Shipment details found:\n";
        echo "- ID: " . $result->id . "\n";
        echo "- Number: " . $result->shipment_number . "\n";
        echo "- Client: " . $result->company_name . "\n";
        echo "- Status: " . $result->status . "\n";
    } else {
        echo "No shipment details returned for ID: " . $test_id . "\n";
    }
    
} catch (Exception $e) {
    echo "Error testing controller: " . $e->getMessage() . "\n";
}