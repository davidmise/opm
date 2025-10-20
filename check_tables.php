<?php
$pdo = new PDO('mysql:host=localhost;dbname=overland_pm_workflow', 'root', '');

echo "Checking opm_clients table structure:\n";
$result = $pdo->query('DESCRIBE opm_clients');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . "\n";
}

echo "\nChecking opm_workflow_shipments table structure:\n";
$result = $pdo->query('DESCRIBE opm_workflow_shipments');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . "\n";
}

echo "\nChecking if workflow tables exist:\n";
$tables = ['opm_workflow_shipments', 'opm_workflow_tasks', 'opm_workflow_documents', 'opm_workflow_tracking', 'opm_workflow_trucks'];
foreach ($tables as $table) {
    try {
        $result = $pdo->query("SELECT COUNT(*) FROM $table");
        echo "$table: EXISTS (rows: " . $result->fetchColumn() . ")\n";
    } catch (Exception $e) {
        echo "$table: NOT EXISTS\n";
    }
}
?>