<?php
// Simple test to verify departments_list endpoint
echo "Testing departments/departments_list endpoint...\n";

$url = "http://localhost/overland_pm/departments/departments_list";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access the URL\n";
} else {
    echo "SUCCESS: Endpoint is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}

// Also test settings endpoint
echo "\nTesting departments/settings endpoint...\n";
$url = "http://localhost/overland_pm/departments/settings";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access the settings URL\n";
} else {
    echo "SUCCESS: Settings endpoint is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}
?>