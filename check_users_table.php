<?php
// Check opm_users table structure
$connection = mysqli_connect('localhost', 'root', '', 'overland_pm');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Checking opm_users table structure:\n\n";

// Check if department_id column exists in opm_users
$result = mysqli_query($connection, "DESCRIBE opm_users");
$has_dept_column = false;

echo "Columns in opm_users table:\n";
while ($row = mysqli_fetch_array($result)) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    if ($row['Field'] === 'department_id') {
        $has_dept_column = true;
    }
}

echo "\nDepartment_id column exists in opm_users: " . ($has_dept_column ? "YES" : "NO") . "\n";

if (!$has_dept_column) {
    echo "\n❌ PROBLEM CONFIRMED: opm_users table is missing department_id column!\n";
    echo "The controller is trying to update users.department_id but the column doesn't exist.\n";
}

mysqli_close($connection);
?>