<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'overland_pm_workflow');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT id, title, context, department_id, project_id, client_id, assigned_to, collaborators FROM opm_tasks WHERE deleted=0 LIMIT 10");

echo "Task ID | Title | Context | Department ID | Project ID | Collaborators\n";
echo str_repeat("=", 100) . "\n";

while ($row = $result->fetch_assoc()) {
    printf(
        "%d | %s | %s | %s | %s | %s\n",
        $row['id'],
        substr($row['title'], 0, 30),
        $row['context'] ?? 'NULL',
        $row['department_id'] ?? 'NULL',
        $row['project_id'] ?? 'NULL',
        $row['collaborators'] ?? 'NULL'
    );
}

$mysqli->close();
