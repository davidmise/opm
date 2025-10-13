<?php
// Check foreign key constraints and department data
$connection = mysqli_connect('localhost', 'root', '', 'overland_pm');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Checking foreign key constraints and department data:\n\n";

// Check foreign key constraints on opm_team_member_job_info
echo "Foreign key constraints on opm_team_member_job_info:\n";
$result = mysqli_query($connection, "
    SELECT 
        CONSTRAINT_NAME,
        COLUMN_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME = 'opm_team_member_job_info' 
    AND TABLE_SCHEMA = 'overland_pm'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");

if (mysqli_num_rows($result) == 0) {
    echo "No foreign key constraints found.\n";
} else {
    while ($row = mysqli_fetch_array($result)) {
        echo "- " . $row['CONSTRAINT_NAME'] . ": " . $row['COLUMN_NAME'] . 
             " -> " . $row['REFERENCED_TABLE_NAME'] . "." . $row['REFERENCED_COLUMN_NAME'] . "\n";
    }
}

// Check opm_departments table
echo "\nDepartments in opm_departments:\n";
$result = mysqli_query($connection, "SELECT id, title, deleted FROM opm_departments ORDER BY id");
while ($row = mysqli_fetch_array($result)) {
    $status = $row['deleted'] == 1 ? " (DELETED)" : "";
    echo "- ID: " . $row['id'] . ", Title: " . $row['title'] . $status . "\n";
}

// Check opm_users (staff only)
echo "\nStaff users in opm_users:\n";
$result = mysqli_query($connection, "SELECT id, first_name, last_name, email, deleted FROM opm_users WHERE user_type = 'staff' ORDER BY id LIMIT 10");
while ($row = mysqli_fetch_array($result)) {
    $status = $row['deleted'] == 1 ? " (DELETED)" : "";
    echo "- ID: " . $row['id'] . ", Name: " . $row['first_name'] . " " . $row['last_name'] . 
         ", Email: " . $row['email'] . $status . "\n";
}

// Check current job info data
echo "\nCurrent data in opm_team_member_job_info:\n";
$result = mysqli_query($connection, "
    SELECT 
        tmji.id, 
        tmji.user_id, 
        tmji.department_id, 
        u.first_name, 
        u.last_name,
        d.title as dept_name
    FROM opm_team_member_job_info tmji
    LEFT JOIN opm_users u ON tmji.user_id = u.id
    LEFT JOIN opm_departments d ON tmji.department_id = d.id
    ORDER BY tmji.id
");

while ($row = mysqli_fetch_array($result)) {
    echo "- ID: " . $row['id'] . ", User: " . $row['first_name'] . " " . $row['last_name'] . 
         " (ID: " . $row['user_id'] . "), Dept: " . ($row['dept_name'] ? $row['dept_name'] : 'NULL') . 
         " (ID: " . $row['department_id'] . ")\n";
}

mysqli_close($connection);
?>