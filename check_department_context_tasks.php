<?php
$connection = mysqli_connect('localhost', 'root', '', 'overland_pm_workflow');

echo "=== Tasks with context='department' ===\n\n";
$result = mysqli_query($connection, "SELECT id, title, context, department_id, deleted FROM opm_tasks WHERE deleted=0 AND context='department' ORDER BY id DESC");

$count = mysqli_num_rows($result);
echo "Found $count tasks:\n\n";

while ($row = mysqli_fetch_assoc($result)) {
    echo "Task #{$row['id']}: {$row['title']}\n";
    echo "  - department_id: " . ($row['department_id'] ?: 'NULL') . "\n";
    echo "  - context: {$row['context']}\n\n";
}

echo "\n=== All tasks by context ===\n";
$result = mysqli_query($connection, "SELECT context, COUNT(*) as count FROM opm_tasks WHERE deleted=0 GROUP BY context");
while ($row = mysqli_fetch_assoc($result)) {
    echo "{$row['context']}: {$row['count']} tasks\n";
}

mysqli_close($connection);
?>
