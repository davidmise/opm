<?php
require_once('app/Config/Database.php');

$db = \Config\Database::connect();

// Get department ID for "Clearing"
$dept_result = $db->query("SELECT id, name FROM departments WHERE name = 'Clearing'");
$department = $dept_result->getRow();

echo "Department: " . ($department ? $department->name . " (ID: " . $department->id . ")" : "Not found") . "\n\n";

if ($department) {
    // Check tasks with this department_id
    $tasks_result = $db->query("SELECT id, title, department_id, project_id, context FROM tasks WHERE deleted = 0 ORDER BY id DESC LIMIT 10");
    echo "Recent tasks:\n";
    foreach($tasks_result->getResultArray() as $task) {
        echo "  Task #{$task['id']}: {$task['title']} (dept_id: " . ($task['department_id'] ?: 'NULL') . ", project: {$task['project_id']}, context: {$task['context']})\n";
    }
    
    echo "\n\nTasks for department {$department->id}:\n";
    $dept_tasks = $db->query("SELECT t.id, t.title, t.department_id, t.project_id, p.department_id as project_dept_id 
                              FROM tasks t 
                              LEFT JOIN projects p ON t.project_id = p.id
                              WHERE t.deleted = 0 
                              AND (t.department_id = {$department->id} OR p.department_id = {$department->id})");
    
    foreach($dept_tasks->getResultArray() as $task) {
        echo "  Task #{$task['id']}: {$task['title']} (task_dept: " . ($task['department_id'] ?: 'NULL') . ", project_dept: " . ($task['project_dept_id'] ?: 'NULL') . ")\n";
    }
    
    echo "\nTotal tasks for department: " . $dept_tasks->getNumRows() . "\n";
}
