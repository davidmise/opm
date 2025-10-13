<?php
// Simple database check script
require_once __DIR__ . '/app/Config/Database.php';

$config = new \Config\Database();
$db_config = $config->default;

echo "Database: " . $db_config['database'] . "\n";
echo "Prefix: " . $db_config['DBPrefix'] . "\n";

// Connect to MySQL
$connection = mysqli_connect(
    $db_config['hostname'], 
    $db_config['username'], 
    $db_config['password'], 
    $db_config['database']
);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check tables related to team_member_job_info
echo "\nTables matching '%team_member_job_info%':\n";
$result = mysqli_query($connection, "SHOW TABLES LIKE '%team_member_job_info%'");
while ($row = mysqli_fetch_array($result)) {
    echo "- " . $row[0] . "\n";
}

// Check tables related to departments
echo "\nTables matching '%departments%':\n";
$result = mysqli_query($connection, "SHOW TABLES LIKE '%departments%'");
while ($row = mysqli_fetch_array($result)) {
    echo "- " . $row[0] . "\n";
}

// Check if opm_team_member_job_info exists and show structure
$result = mysqli_query($connection, "SHOW TABLES LIKE 'opm_team_member_job_info'");
if (mysqli_num_rows($result) > 0) {
    echo "\nStructure of opm_team_member_job_info:\n";
    $result = mysqli_query($connection, "DESCRIBE opm_team_member_job_info");
    while ($row = mysqli_fetch_array($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "\nopm_team_member_job_info table does NOT exist!\n";
}

mysqli_close($connection);
?>