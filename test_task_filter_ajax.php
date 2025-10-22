<?php
require_once('system/bootstrap.php');

$app = \Config\Services::codeigniter();
$app->initialize();

// Simulate POST request
$_POST['context'] = 'project';  // Test project context

$controller = new \App\Controllers\My_department();

// Test with Clearing department (ID 17)
$_SESSION['user_department_preference_1'] = 17;  // Assuming user ID 1

echo "Testing department_tasks_list_data with context='project'\n";
echo "Department ID: 17 (Clearing)\n\n";

ob_start();
$controller->department_tasks_list_data();
$output = ob_get_clean();

$data = json_decode($output, true);

if (isset($data['data'])) {
    echo "Found " . count($data['data']) . " tasks\n\n";
    foreach ($data['data'] as $task) {
        echo "Task: " . strip_tags($task[1]) . "\n";  // Title is column 1
    }
} else {
    echo "Error or no data\n";
    print_r($data);
}

// Test department context
echo "\n\n---Testing with context='department'---\n\n";
$_POST['context'] = 'department';

ob_start();
$controller->department_tasks_list_data();
$output = ob_get_clean();

$data = json_decode($output, true);

if (isset($data['data'])) {
    echo "Found " . count($data['data']) . " tasks\n\n";
    foreach ($data['data'] as $task) {
        echo "Task: " . strip_tags($task[1]) . "\n";
    }
} else {
    echo "Error or no data\n";
    print_r($data);
}
?>
