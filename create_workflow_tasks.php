<?php
/**
 * Create workflow-linked tasks
 */

$pdo = new PDO('mysql:host=localhost;dbname=overland_pm', 'root', '');

// Get shipments and users
$shipments = $pdo->query("SELECT id, shipment_number, cargo_type, client_id FROM opm_workflow_shipments ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, first_name, last_name FROM opm_users WHERE deleted = 0 AND user_type = 'staff' LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    die("No staff users found for task assignment\n");
}

echo "Creating workflow tasks for " . count($shipments) . " shipments...\n";

$task_templates = [
    'Customs clearance for {cargo_type}',
    'Documentation review - {shipment_number}',
    'Cargo inspection and verification',
    'Transport coordination for {cargo_type}',
    'Delivery arrangement - {shipment_number}',
    'Insurance claim processing',
    'Port handling supervision',
    'Export permit verification'
];

$task_descriptions = [
    'Complete customs clearance procedures for shipment {shipment_number}',
    'Review and verify all documentation for {cargo_type} cargo',
    'Conduct thorough inspection of cargo and verify against shipping manifest',
    'Coordinate transportation logistics from port to final destination',
    'Arrange delivery schedule and customer notification for {shipment_number}',
    'Process insurance documentation and claims if necessary',
    'Supervise cargo handling at port and ensure proper loading procedures',
    'Verify and obtain all necessary export permits and certificates'
];

$tasks_created = 0;

foreach ($shipments as $shipment) {
    // Create 1-2 tasks per shipment
    $num_tasks = rand(1, 2);
    
    for ($i = 0; $i < $num_tasks; $i++) {
        $template_index = array_rand($task_templates);
        $user = $users[array_rand($users)];
        
        $task_title = str_replace(['{cargo_type}', '{shipment_number}'], 
                                 [$shipment['cargo_type'], $shipment['shipment_number']], 
                                 $task_templates[$template_index]);
        
        $task_description = str_replace(['{cargo_type}', '{shipment_number}'], 
                                       [$shipment['cargo_type'], $shipment['shipment_number']], 
                                       $task_descriptions[$template_index]);
        
        $start_date = date('Y-m-d', strtotime('-' . rand(1, 15) . ' days'));
        $deadline = date('Y-m-d', strtotime($start_date . ' +' . rand(5, 20) . ' days'));
        
        $statuses = ['to_do', 'in_progress', 'done'];
        $task_status = $statuses[array_rand($statuses)];
        
        $priorities = [1, 2, 3, 4]; // 1=Low, 2=Medium, 3=High, 4=Urgent
        $priority_id = $priorities[array_rand($priorities)];
        
        try {
            // Insert task
            $stmt = $pdo->prepare("
                INSERT INTO opm_tasks (
                    title, description, project_id, assigned_to, deadline, start_date, 
                    status, priority_id, created_date, created_by, department_id, 
                    sort, recurring, deleted, context, status_id, collaborators,
                    labels, points, milestone_id, client_id, blocking, blocked_by
                ) VALUES (?, ?, 0, ?, ?, ?, ?, ?, NOW(), 1, 1, 0, 0, 0, 'general', 1, '', '', 0, 0, ?, '', '')
            ");
            
            $stmt->execute([
                $task_title, $task_description, $user['id'], $deadline, $start_date,
                $task_status, $priority_id, $shipment['client_id']
            ]);
            
            $task_id = $pdo->lastInsertId();
            
            // Create workflow task reference
            $stmt = $pdo->prepare("
                INSERT INTO opm_workflow_tasks (
                    task_id, workflow_type, reference_id, shipment_id, 
                    task_type, assigned_to, status, created_at, updated_at, deleted
                ) VALUES (?, 'shipment', ?, ?, 'operational', ?, ?, NOW(), NOW(), 0)
            ");
            
            $task_types = ['documentation', 'customs', 'transportation', 'delivery'];
            $task_type = $task_types[array_rand($task_types)];
            
            $stmt->execute([
                $task_id, $shipment['id'], $shipment['id'], $user['id'], $task_status
            ]);
            
            // Update shipment with task reference if first task
            if ($i == 0) {
                $pdo->prepare("
                    UPDATE opm_workflow_shipments 
                    SET assigned_to = ?, task_id = ? 
                    WHERE id = ?
                ")->execute([$user['id'], $task_id, $shipment['id']]);
            }
            
            $tasks_created++;
            echo "Created task: $task_title (assigned to {$user['first_name']} {$user['last_name']})\n";
            
        } catch (Exception $e) {
            echo "Failed to create task for shipment {$shipment['shipment_number']}: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n✅ Created $tasks_created workflow tasks successfully!\n";
echo "Tasks are now integrated with the main task system and workflow shipments.\n";

?>