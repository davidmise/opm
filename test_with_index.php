<?php
// Test endpoints with index.php in the path
echo "Testing departments via index.php...\n";

$url = "http://localhost/overland_pm/index.php/departments";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access departments via index.php\n";
} else {
    echo "SUCCESS: Departments via index.php is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}

// Test departments_list via index.php
echo "\nTesting departments_list via index.php...\n";
$url = "http://localhost/overland_pm/index.php/departments/departments_list";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access departments_list via index.php\n";
} else {
    echo "SUCCESS: departments_list via index.php is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}

// Test settings via index.php
echo "\nTesting settings via index.php...\n";
$url = "http://localhost/overland_pm/index.php/departments/settings";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access settings via index.php\n";
} else {
    echo "SUCCESS: settings via index.php is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}
?>