<?php
// Test if modal_form receives department_id
$_POST['department_id'] = 17;  // Clearing department

require_once('system/bootstrap.php');
$app = \Config\Services::codeigniter();
$app->initialize();

$controller = new \App\Controllers\Tasks();

// Use reflection to test the private method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('_get_task_related_dropdowns');
$method->setAccessible(true);

echo "Testing _get_task_related_dropdowns with department context\n\n";

// Test with department context and ID
$result = $method->invoke($controller, 'department', 17, false);

echo "Collaborators dropdown:\n";
if (isset($result['collaborators_dropdown']) && count($result['collaborators_dropdown']) > 0) {
    echo "Found " . count($result['collaborators_dropdown']) . " members:\n";
    foreach ($result['collaborators_dropdown'] as $member) {
        echo "  - ID: {$member['id']}, Name: {$member['text']}\n";
    }
} else {
    echo "ERROR: No collaborators found!\n";
}

echo "\n\nAssign to dropdown:\n";
if (isset($result['assign_to_dropdown']) && count($result['assign_to_dropdown']) > 0) {
    echo "Found " . count($result['assign_to_dropdown']) . " members:\n";
    foreach ($result['assign_to_dropdown'] as $member) {
        echo "  - ID: {$member['id']}, Name: {$member['text']}\n";
    }
} else {
    echo "ERROR: No assignees found!\n";
}
?>
