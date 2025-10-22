<?php
$connection = mysqli_connect('localhost', 'root', '', 'overland_pm_workflow');

$clearing_dept_id = 17;

echo "=== CLEARING DEPARTMENT (ID: $clearing_dept_id) ===\n\n";

// Department Tasks (context='department')
echo "1. DEPARTMENT TASKS (context='department'):\n";
$query = "SELECT id, title, department_id, context 
          FROM opm_tasks 
          WHERE deleted=0 
          AND department_id = $clearing_dept_id 
          AND context = 'department'";
$result = mysqli_query($connection, $query);
echo "   Found: " . mysqli_num_rows($result) . " tasks\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "   - Task #{$row['id']}: {$row['title']}\n";
}

// Project Tasks (context='project' from projects in this department)
echo "\n2. PROJECT TASKS (context='project' from department projects):\n";
$query = "SELECT t.id, t.title, t.project_id, p.title as project_title, p.department_id
          FROM opm_tasks t
          INNER JOIN opm_projects p ON t.project_id = p.id
          WHERE t.deleted=0 
          AND p.department_id = $clearing_dept_id
          AND t.context = 'project'";
$result = mysqli_query($connection, $query);
echo "   Found: " . mysqli_num_rows($result) . " tasks\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo "   - Task #{$row['id']}: {$row['title']} (Project: {$row['project_title']})\n";
}

echo "\n=== SUMMARY ===\n";
echo "This is what will be shown in each filter for Clearing department.\n";

mysqli_close($connection);
?>
