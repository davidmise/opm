<?php
// Test if any controllers work
echo "Testing dashboard endpoint...\n";

$url = "http://localhost/overland_pm/dashboard";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access dashboard URL\n";
} else {
    echo "SUCCESS: Dashboard endpoint is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}

// Test if departments index works
echo "\nTesting departments index endpoint...\n";
$url = "http://localhost/overland_pm/departments";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access departments index URL\n";
} else {
    echo "SUCCESS: Departments index endpoint is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}
?>