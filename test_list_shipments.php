<?php
// Simple test for the workflow list_shipments endpoint
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "overland_pm_workflow";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Testing list_shipments data format...\n\n";
    
    // Test the query that _get_shipments_list_data would use
    $stmt = $pdo->query("
        SELECT s.*, c.company_name 
        FROM opm_workflow_shipments s 
        LEFT JOIN opm_clients c ON s.client_id = c.id 
        ORDER BY s.created_at DESC 
        LIMIT 5
    ");
    
    $shipments = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo "Found " . count($shipments) . " shipments:\n";
    
    foreach ($shipments as $shipment) {
        echo "\nShipment: {$shipment->shipment_number}\n";
        echo "- Client ID: {$shipment->client_id}\n";
        echo "- Company: " . ($shipment->company_name ?: 'Unknown') . "\n";
        echo "- Status: {$shipment->status}\n";
        echo "- Phase: {$shipment->current_phase}\n";
        echo "- Cargo: {$shipment->cargo_type}\n";
        echo "- Weight: " . ($shipment->cargo_weight ?: 'N/A') . "\n";
    }
    
    echo "\nTesting simulated row format...\n";
    
    foreach ($shipments as $data) {
        $status_colors = [
            'active' => 'warning',
            'completed' => 'success', 
            'cancelled' => 'danger'
        ];
        
        $status_class = $status_colors[$data->status] ?? 'secondary';
        $phase_display = ucwords(str_replace('_', ' ', $data->current_phase));
        $status_badge = "<span class='badge bg-$status_class'>" . ucfirst($data->status) . "</span>";
        $phase_badge = "<span class='badge bg-info'>" . $phase_display . "</span>";

        $row = array(
            $data->shipment_number,
            $data->company_name ?: 'Unknown Client',
            $data->cargo_type,
            $data->cargo_weight ? $data->cargo_weight . " tons" : "-",
            $status_badge,
            "-", // Priority column - workflow shipments don't have priority
            $phase_badge,
            $data->origin_port,
            $data->destination_port,
            date('Y-m-d', strtotime($data->created_at)),
            "Actions" // Simplified actions
        );
        
        echo json_encode($row) . "\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>