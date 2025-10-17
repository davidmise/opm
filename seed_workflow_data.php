<?php
/**
 * Workflow System Seed Data - Tanzanian Context
 * This script seeds the workflow system with realistic Tanzanian data
 */

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'overland_pm';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to database successfully\n";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Tanzanian companies and locations
$tanzanian_companies = [
    ['company_name' => 'Kilimanjaro Coffee Exporters Ltd', 'contact_person' => 'John Mwalimu', 'email' => 'john@kilicoffee.co.tz', 'phone' => '+255 27 275 4821'],
    ['company_name' => 'Dar es Salaam Port Authority', 'contact_person' => 'Fatma Rashid', 'email' => 'fatma@dsm-port.co.tz', 'phone' => '+255 22 211 7474'],
    ['company_name' => 'Tanzania Mining Corporation', 'contact_person' => 'Peter Mbwambo', 'email' => 'peter@tanmining.co.tz', 'phone' => '+255 25 260 4125'],
    ['company_name' => 'Simba Cement Limited', 'contact_person' => 'Mary Kiondo', 'email' => 'mary@simbacement.co.tz', 'phone' => '+255 22 286 1064'],
    ['company_name' => 'East African Textiles Ltd', 'contact_person' => 'Ahmed Hassan', 'email' => 'ahmed@eatextiles.co.tz', 'phone' => '+255 22 215 0088'],
    ['company_name' => 'Mbeya Agricultural Exports', 'contact_person' => 'Grace Mwamba', 'email' => 'grace@mbeyaagri.co.tz', 'phone' => '+255 25 250 2764'],
    ['company_name' => 'Zanzibar Spice Company', 'contact_person' => 'Salim Omar', 'email' => 'salim@zanzibarspice.co.tz', 'phone' => '+255 24 223 3456'],
    ['company_name' => 'Tanga Steel Mills', 'contact_person' => 'Robert Kimaro', 'email' => 'robert@tangasteel.co.tz', 'phone' => '+255 27 264 8910']
];

$tanzanian_ports = [
    'Dar es Salaam Port', 'Tanga Port', 'Mtwara Port', 'Kilindoni Port', 
    'Stone Town Port (Zanzibar)', 'Mwanza Port', 'Bukoba Port', 'Musoma Port'
];

$international_ports = [
    'Mombasa (Kenya)', 'Dubai (UAE)', 'Rotterdam (Netherlands)', 'Hamburg (Germany)', 
    'Shanghai (China)', 'Mumbai (India)', 'Cape Town (South Africa)', 'London (UK)',
    'Antwerp (Belgium)', 'Singapore', 'Jeddah (Saudi Arabia)', 'Alexandria (Egypt)'
];

$cargo_types = [
    'Coffee Beans', 'Gold Ore', 'Cement', 'Cotton Textiles', 'Cashew Nuts',
    'Sisal Fiber', 'Spices', 'Steel Products', 'Tobacco', 'Tea',
    'Copper Concentrate', 'Diamonds', 'Sunflower Oil', 'Maize', 'Rice'
];

echo "Starting to seed workflow data...\n";

// 1. First, let's check if clients exist, if not create some
echo "Checking existing clients...\n";
$existing_clients = $pdo->query("SELECT COUNT(*) FROM opm_clients WHERE deleted = 0")->fetchColumn();

if ($existing_clients < 5) {
    echo "Need to create more clients. Using simplified approach...\n";
    
    // Try to create clients with minimal required fields
    foreach (array_slice($tanzanian_companies, 0, 3) as $company) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO opm_clients (
                    company_name, phone, country, city, created_date, is_lead, type,
                    starred_by, group_ids, deleted, currency, lead_status_id, 
                    owner_id, created_by, sort, lead_source_id, disable_online_payment,
                    last_lead_status
                ) VALUES (?, ?, ?, ?, NOW(), 0, 'organization', '', '', 0, 'USD', 1, 1, 1, 0, 1, 0, '')
            ");
            
            $stmt->execute([
                $company['company_name'],
                $company['phone'],
                'Tanzania',
                'Dar es Salaam'
            ]);
            
            echo "Created client: {$company['company_name']}\n";
        } catch (Exception $e) {
            echo "Failed to create client {$company['company_name']}: " . $e->getMessage() . "\n";
            echo "Will use existing clients instead...\n";
            break;
        }
    }
} else {
    echo "Found $existing_clients existing clients. Will use existing data.\n";
}

// Get client IDs for shipments
$clients = $pdo->query("SELECT id, company_name FROM opm_clients WHERE deleted = 0 LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
if (empty($clients)) {
    die("No clients found. Please ensure clients table has data.\n");
}

// 2. Create realistic workflow shipments
echo "Creating workflow shipments...\n";

// Clear existing workflow data
$pdo->exec("DELETE FROM opm_workflow_shipments WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_tasks WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_documents WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_tracking WHERE 1");
$pdo->exec("DELETE FROM opm_workflow_trucks WHERE 1");

$shipments_data = [];
$current_year = date('Y');

for ($i = 1; $i <= 15; $i++) {
    $client = $clients[array_rand($clients)];
    $cargo_type = $cargo_types[array_rand($cargo_types)];
    $origin_port = $tanzanian_ports[array_rand($tanzanian_ports)];
    $destination_port = $international_ports[array_rand($international_ports)];
    
    // Random dates within last 3 months
    $created_date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 90) . ' days'));
    $estimated_arrival = date('Y-m-d', strtotime($created_date . ' +' . rand(15, 45) . ' days'));
    
    $statuses = ['active', 'completed', 'cancelled'];
    $phases = ['clearing_intake', 'regulatory_processing', 'internal_review', 'transport_loading', 'tracking'];
    
    $status = $statuses[array_rand($statuses)];
    $phase = $phases[array_rand($phases)];
    
    $shipment_number = 'SH-' . $current_year . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
    $cargo_weight = rand(5, 1000) . '.' . rand(0, 99);
    $cargo_value = rand(10000, 500000);
    
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
    
    $shipment_id = $pdo->lastInsertId();
    $shipments_data[] = [
        'id' => $shipment_id,
        'shipment_number' => $shipment_number,
        'client_name' => $client['company_name'],
        'cargo_type' => $cargo_type
    ];
    
    echo "Created shipment: $shipment_number - {$client['company_name']} - $cargo_type\n";
}

// 3. Create associated tasks in the main tasks table
echo "Creating associated tasks in main tasks table...\n";

// Get some users for task assignment
$users = $pdo->query("SELECT id, first_name, last_name FROM opm_users WHERE deleted = 0 AND user_type = 'staff' LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    echo "Warning: No staff users found for task assignment\n";
} else {
    foreach ($shipments_data as $shipment) {
        // Create 1-3 tasks per shipment
        $num_tasks = rand(1, 3);
        
        for ($t = 1; $t <= $num_tasks; $t++) {
            $user = $users[array_rand($users)];
            
            $task_titles = [
                "Customs clearance for {$shipment['cargo_type']}",
                "Documentation review - {$shipment['shipment_number']}",
                "Cargo inspection and verification",
                "Transport coordination for {$shipment['cargo_type']}",
                "Delivery arrangement - {$shipment['client_name']}",
                "Insurance claim processing",
                "Port handling supervision",
                "Export permit verification"
            ];
            
            $task_title = $task_titles[array_rand($task_titles)];
            $task_description = "Workflow task for shipment {$shipment['shipment_number']} - {$shipment['cargo_type']} cargo handling and processing.";
            
            $start_date = date('Y-m-d', strtotime('-' . rand(1, 30) . ' days'));
            $deadline = date('Y-m-d', strtotime($start_date . ' +' . rand(3, 15) . ' days'));
            
            $task_statuses = ['to_do', 'in_progress', 'done'];
            $task_status = $task_statuses[array_rand($task_statuses)];
            
            $priorities = [1, 2, 3, 4]; // 1=Low, 2=Medium, 3=High, 4=Urgent
            $priority_id = $priorities[array_rand($priorities)];
            
            // Insert into main tasks table
            $stmt = $pdo->prepare("
                INSERT INTO opm_tasks (
                    title, description, project_id, assigned_to, deadline, start_date, 
                    status, priority_id, created_date, created_by, department_id, 
                    sort, recurring, deleted, context, status_id, collaborators,
                    labels, points, milestone_id
                ) VALUES (?, ?, 0, ?, ?, ?, ?, ?, NOW(), 1, 1, 0, 0, 0, 'general', 1, '', '', 0, 0)
            ");
            
            $stmt->execute([
                $task_title, $task_description, $user['id'], $deadline, $start_date,
                $task_status, $priority_id
            ]);
            
            $task_id = $pdo->lastInsertId();
            
            // Create workflow task reference
            $stmt = $pdo->prepare("
                INSERT INTO opm_workflow_tasks (
                    task_id, workflow_type, reference_id, shipment_id, 
                    task_type, assigned_to, status, created_at, updated_at, deleted
                ) VALUES (?, 'shipment', ?, ?, 'operational', ?, ?, NOW(), NOW(), 0)
            ");
            
            $workflow_task_types = ['documentation', 'customs', 'transportation', 'delivery'];
            $task_type = $workflow_task_types[array_rand($workflow_task_types)];
            
            $stmt->execute([
                $task_id, $shipment['id'], $shipment['id'], $user['id'], $task_status
            ]);
            
            // Update shipment with task reference
            if ($t == 1) { // First task becomes the primary assigned task
                $pdo->prepare("
                    UPDATE opm_workflow_shipments 
                    SET assigned_to = ?, task_id = ? 
                    WHERE id = ?
                ")->execute([$user['id'], $task_id, $shipment['id']]);
            }
            
            echo "Created task: $task_title (assigned to {$user['first_name']} {$user['last_name']})\n";
        }
    }
}

// 4. Create workflow documents
echo "Creating workflow documents...\n";

$document_types = [
    'bill_of_lading', 'packing_list', 'commercial_invoice', 'insurance_certificate',
    'customs_declaration', 'certificate_of_origin', 'inspection_certificate'
];

foreach ($shipments_data as $shipment) {
    // Create 2-4 documents per shipment
    $num_docs = rand(2, 4);
    
    for ($d = 1; $d <= $num_docs; $d++) {
        $doc_type = $document_types[array_rand($document_types)];
        $user = $users[array_rand($users)];
        
        $doc_names = [
            'bill_of_lading' => 'Bill of Lading - ' . $shipment['shipment_number'],
            'packing_list' => 'Packing List - ' . $shipment['cargo_type'],
            'commercial_invoice' => 'Commercial Invoice - ' . $shipment['client_name'],
            'insurance_certificate' => 'Insurance Certificate - ' . $shipment['shipment_number'],
            'customs_declaration' => 'Customs Declaration Form',
            'certificate_of_origin' => 'Certificate of Origin - Tanzania',
            'inspection_certificate' => 'Cargo Inspection Certificate'
        ];
        
        $document_name = $doc_names[$doc_type];
        $file_name = strtolower(str_replace([' ', '-'], '_', $document_name)) . '.pdf';
        
        $statuses = ['pending', 'approved', 'rejected'];
        $doc_status = $statuses[array_rand($statuses)];
        
        $stmt = $pdo->prepare("
            INSERT INTO opm_workflow_documents (
                shipment_id, document_name, document_type, file_name, file_path,
                uploaded_by, status, assigned_to, created_at, updated_at, deleted
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 0)
        ");
        
        $file_path = 'files/workflow_docs/' . $file_name;
        
        $stmt->execute([
            $shipment['id'], $document_name, $doc_type, $file_name, $file_path,
            $user['id'], $doc_status, $user['id']
        ]);
        
        echo "Created document: $document_name ($doc_status)\n";
    }
}

// 5. Create workflow tracking entries
echo "Creating workflow tracking entries...\n";

$tracking_statuses = ['in_transit', 'at_port', 'customs_clearance', 'delivered', 'delayed'];
$locations = [
    'Dar es Salaam Port - Container Terminal',
    'Tanga Port - Cargo Yard',
    'Customs Office - Dar es Salaam',
    'TICTS - Container Terminal',
    'Mwanza Port - Lake Victoria',
    'Kilimanjaro International Airport',
    'Border Post - Namanga',
    'Warehouse - Industrial Area'
];

foreach ($shipments_data as $shipment) {
    // Create 3-6 tracking entries per shipment
    $num_tracking = rand(3, 6);
    
    for ($tr = 1; $tr <= $num_tracking; $tr++) {
        $location = $locations[array_rand($locations)];
        $tracking_status = $tracking_statuses[array_rand($tracking_statuses)];
        
        $tracking_notes = [
            "Cargo received at $location",
            "Customs documentation under review",
            "Container inspection completed",
            "Clearance documentation submitted",
            "Cargo loaded for transport",
            "Delivery scheduled for tomorrow",
            "Awaiting customer collection",
            "Transport delayed due to weather conditions"
        ];
        
        $notes = $tracking_notes[array_rand($tracking_notes)];
        $tracking_date = date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days'));
        
        $stmt = $pdo->prepare("
            INSERT INTO opm_workflow_tracking (
                shipment_id, status, location, notes, tracking_date,
                assigned_to, created_at, updated_at, deleted
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), 0)
        ");
        
        $user = $users[array_rand($users)];
        
        $stmt->execute([
            $shipment['id'], $tracking_status, $location, $notes, $tracking_date, $user['id']
        ]);
        
        echo "Created tracking: {$shipment['shipment_number']} - $tracking_status at $location\n";
    }
}

// 6. Create trucks and allocations
echo "Creating trucks and allocations...\n";

$truck_models = [
    'Mercedes-Benz Actros', 'Volvo FH', 'Scania R-Series', 'MAN TGX',
    'DAF XF', 'Isuzu NPR', 'Mitsubishi Fuso', 'Hino Ranger'
];

$tanzanian_plates = ['T', 'DA', 'MB', 'MZ', 'TG', 'MT', 'DO', 'KI', 'AR'];

// Create some trucks
for ($truck = 1; $truck <= 8; $truck++) {
    $model = $truck_models[array_rand($truck_models)];
    $plate_prefix = $tanzanian_plates[array_rand($tanzanian_plates)];
    $plate_number = $plate_prefix . ' ' . rand(100, 999) . ' ' . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25));
    
    $capacity = rand(5, 40) . ' tons';
    $truck_statuses = ['available', 'in_use', 'maintenance', 'out_of_service'];
    $truck_status = $truck_statuses[array_rand($truck_statuses)];
    
    $stmt = $pdo->prepare("
        INSERT INTO opm_workflow_trucks (
            truck_number, model, capacity, status, driver_id,
            created_at, updated_at, deleted
        ) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), 0)
    ");
    
    $driver = $users[array_rand($users)];
    
    $stmt->execute([
        $plate_number, $model, $capacity, $truck_status, $driver['id']
    ]);
    
    $truck_id = $pdo->lastInsertId();
    
    echo "Created truck: $plate_number - $model (Driver: {$driver['first_name']} {$driver['last_name']})\n";
    
    // Allocate some trucks to shipments
    if ($truck_status == 'in_use' && $truck <= count($shipments_data)) {
        $shipment = $shipments_data[$truck - 1];
        
        $stmt = $pdo->prepare("
            INSERT INTO opm_workflow_truck_allocations (
                truck_id, shipment_id, allocated_date, status,
                created_at, updated_at, deleted
            ) VALUES (?, ?, ?, 'active', NOW(), NOW(), 0)
        ");
        
        $allocation_date = date('Y-m-d', strtotime('-' . rand(1, 30) . ' days'));
        
        $stmt->execute([
            $truck_id, $shipment['id'], $allocation_date
        ]);
        
        echo "Allocated truck $plate_number to shipment {$shipment['shipment_number']}\n";
    }
}

echo "\n" . "=".str_repeat("=", 60) . "=\n";
echo "Workflow Data Seeding Complete!\n";
echo "=".str_repeat("=", 60) . "=\n";

// Summary
$shipment_count = $pdo->query("SELECT COUNT(*) FROM opm_workflow_shipments")->fetchColumn();
$task_count = $pdo->query("SELECT COUNT(*) FROM opm_tasks")->fetchColumn();
$workflow_task_count = $pdo->query("SELECT COUNT(*) FROM opm_workflow_tasks")->fetchColumn();
$document_count = $pdo->query("SELECT COUNT(*) FROM opm_workflow_documents")->fetchColumn();
$tracking_count = $pdo->query("SELECT COUNT(*) FROM opm_workflow_tracking")->fetchColumn();
$truck_count = $pdo->query("SELECT COUNT(*) FROM opm_workflow_trucks")->fetchColumn();

echo "Summary:\n";
echo "- Shipments: $shipment_count\n";
echo "- Main Tasks: $task_count\n";
echo "- Workflow Task References: $workflow_task_count\n";
echo "- Documents: $document_count\n";
echo "- Tracking Entries: $tracking_count\n";
echo "- Trucks: $truck_count\n";
echo "\nYou can now access the workflow system with realistic Tanzanian data!\n";
echo "Navigate to: your-site/workflow to see the data in action.\n";

?>