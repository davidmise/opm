<?php
// Test job info save operation
$connection = mysqli_connect('localhost', 'root', '', 'overland_pm');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Testing job info save operation:\n\n";

// Test 1: Try to update existing job info (user_id = 1)
echo "Test 1: Update existing job info for user_id = 1\n";
$user_id = 1;
$department_id = 2; // General
$salary = 50000;
$salary_term = 'per_year';
$date_of_hire = '2024-01-01';

$sql = "UPDATE opm_team_member_job_info 
        SET department_id = $department_id, 
            salary = $salary, 
            salary_term = '$salary_term', 
            date_of_hire = '$date_of_hire'
        WHERE user_id = $user_id";

echo "SQL: $sql\n";
$result = mysqli_query($connection, $sql);

if ($result) {
    echo "✅ UPDATE successful! Affected rows: " . mysqli_affected_rows($connection) . "\n";
} else {
    echo "❌ UPDATE failed: " . mysqli_error($connection) . "\n";
}

// Test 2: Try to insert new job info for a user without one (user_id = 7)
echo "\nTest 2: Insert/Update job info for user_id = 7\n";
$user_id = 7;
$department_id = 6; // Tracking

// First check if user 7 has job info
$check_sql = "SELECT COUNT(*) as count FROM opm_team_member_job_info WHERE user_id = $user_id";
$check_result = mysqli_query($connection, $check_sql);
$count_row = mysqli_fetch_array($check_result);

if ($count_row['count'] > 0) {
    echo "User $user_id already has job info, updating...\n";
    $sql = "UPDATE opm_team_member_job_info 
            SET department_id = $department_id, 
                salary = 45000, 
                salary_term = 'per_year'
            WHERE user_id = $user_id";
} else {
    echo "User $user_id has no job info, inserting...\n";
    $sql = "INSERT INTO opm_team_member_job_info (user_id, department_id, salary, salary_term) 
            VALUES ($user_id, $department_id, 45000, 'per_year')";
}

echo "SQL: $sql\n";
$result = mysqli_query($connection, $sql);

if ($result) {
    echo "✅ Operation successful! Affected rows: " . mysqli_affected_rows($connection) . "\n";
} else {
    echo "❌ Operation failed: " . mysqli_error($connection) . "\n";
}

// Test 3: Verify the data
echo "\nTest 3: Verify current job info data:\n";
$result = mysqli_query($connection, "
    SELECT 
        tmji.id, 
        tmji.user_id, 
        tmji.department_id, 
        tmji.salary,
        tmji.salary_term,
        u.first_name, 
        u.last_name,
        d.title as dept_name
    FROM opm_team_member_job_info tmji
    LEFT JOIN opm_users u ON tmji.user_id = u.id
    LEFT JOIN opm_departments d ON tmji.department_id = d.id
    ORDER BY tmji.id
");

while ($row = mysqli_fetch_array($result)) {
    echo "- User: " . $row['first_name'] . " " . $row['last_name'] . 
         " (ID: " . $row['user_id'] . "), Dept: " . ($row['dept_name'] ? $row['dept_name'] : 'NULL') . 
         ", Salary: " . $row['salary'] . " " . $row['salary_term'] . "\n";
}

mysqli_close($connection);
?>