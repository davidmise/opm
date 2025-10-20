<?php
$pdo = new PDO('mysql:host=localhost;dbname=overland_pm_workflow', 'root', '');

echo "Checking opm_tasks table structure:\n";
$result = $pdo->query('DESCRIBE opm_tasks');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' (' . $row['Type'] . ')' . "\n";
}
?>