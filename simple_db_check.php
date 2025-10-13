<?php
// Simple database check without CodeIgniter
$connection = mysqli_connect('localhost', 'root', '', 'overland_pm');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected to database successfully!\n\n";

// Check tables related to team_member_job_info
echo "Tables matching '%team_member_job_info%':\n";
$result = mysqli_query($connection, "SHOW TABLES LIKE '%team_member_job_info%'");
if (mysqli_num_rows($result) == 0) {
    echo "No team_member_job_info tables found!\n";
} else {
    while ($row = mysqli_fetch_array($result)) {
        echo "- " . $row[0] . "\n";
    }
}

// Check tables related to departments  
echo "\nTables matching '%departments%':\n";
$result = mysqli_query($connection, "SHOW TABLES LIKE '%departments%'");
while ($row = mysqli_fetch_array($result)) {
    echo "- " . $row[0] . "\n";
}

// Check if specific tables exist
$tables_to_check = ['opm_team_member_job_info', 'omp_team_member_job_info', 'team_member_job_info'];

echo "\nChecking specific table existence:\n";
foreach ($tables_to_check as $table) {
    $result = mysqli_query($connection, "SHOW TABLES LIKE '$table'");
    $exists = mysqli_num_rows($result) > 0 ? "EXISTS" : "NOT EXISTS";
    echo "- $table: $exists\n";
}

// If opm_team_member_job_info exists, show structure
$result = mysqli_query($connection, "SHOW TABLES LIKE 'opm_team_member_job_info'");
if (mysqli_num_rows($result) > 0) {
    echo "\nStructure of opm_team_member_job_info:\n";
    $result = mysqli_query($connection, "DESCRIBE opm_team_member_job_info");
    while ($row = mysqli_fetch_array($result)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\nCount of records in opm_team_member_job_info:\n";
    $result = mysqli_query($connection, "SELECT COUNT(*) as count FROM opm_team_member_job_info");
    $row = mysqli_fetch_array($result);
    echo "Records: " . $row['count'] . "\n";
}

mysqli_close($connection);
?>