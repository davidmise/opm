<?php
// Simple database check without CodeIgniter
$connection = mysqli_connect('localhost', 'root', '', 'overland_pm_workflow');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected to database successfully!\n\n";

// Get Clearing department
$result = mysqli_query($connection, "SELECT id, title FROM opm_departments WHERE title = 'Clearing'");
$department = mysqli_fetch_assoc($result);

if (!$department) {
    echo "Clearing department not found!\n";
    mysqli_close($connection);
    exit;
}

echo "Department: {$department['title']} (ID: {$department['id']})\n\n";

// Check all recent tasks
echo "=== Recent Tasks (all) ===\n";
$result = mysqli_query($connection, "SELECT id, title, department_id, project_id, context, deleted FROM opm_tasks ORDER BY id DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($result)) {
    echo "Task #{$row['id']}: {$row['title']}\n";
    echo "  - department_id: " . ($row['department_id'] ?: 'NULL') . "\n";
    echo "  - project_id: {$row['project_id']}\n";
    echo "  - context: {$row['context']}\n";
    echo "  - deleted: {$row['deleted']}\n\n";
}

// Check tasks belonging to Clearing department
echo "=== Tasks for Clearing Department ===\n";
$dept_id = $department['id'];
$query = "SELECT t.id, t.title, t.department_id, t.project_id, p.department_id as project_dept_id, t.context
          FROM opm_tasks t 
          LEFT JOIN opm_projects p ON t.project_id = p.id
          WHERE t.deleted = 0 
          AND (t.department_id = $dept_id OR p.department_id = $dept_id)
          ORDER BY t.id DESC";

$result = mysqli_query($connection, $query);
$count = mysqli_num_rows($result);

echo "Found $count tasks:\n\n";

while ($row = mysqli_fetch_assoc($result)) {
    echo "Task #{$row['id']}: {$row['title']}\n";
    echo "  - task department_id: " . ($row['department_id'] ?: 'NULL') . "\n";
    echo "  - project department_id: " . ($row['project_dept_id'] ?: 'NULL') . "\n";
    echo "  - context: {$row['context']}\n\n";
}

mysqli_close($connection);
?>
