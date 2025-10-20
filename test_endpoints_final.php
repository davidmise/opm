<?php
// Test endpoints with correct index.php format
echo "Testing departments via index.php...\n";

$url = "http://localhost/overland_pm/index.php/departments";
$context = stream_context_create(['http' => ['timeout' => 10]]);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Could not access departments via index.php\n";
    $error = error_get_last();
    echo "Last error: " . ($error['message'] ?? 'unknown') . "\n";
} else {
    echo "SUCCESS: Departments via index.php is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}

// Test departments_list via index.php
echo "\nTesting departments_list via index.php...\n";
$url = "http://localhost/overland_pm/index.php/departments/departments_list";
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Could not access departments_list via index.php\n";
    $error = error_get_last();
    echo "Last error: " . ($error['message'] ?? 'unknown') . "\n";
} else {
    echo "SUCCESS: departments_list via index.php is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}

// Test settings via index.php
echo "\nTesting settings via index.php...\n";
$url = "http://localhost/overland_pm/index.php/departments/settings";
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Could not access settings via index.php\n";
    $error = error_get_last();
    echo "Last error: " . ($error['message'] ?? 'unknown') . "\n";
} else {
    echo "SUCCESS: settings via index.php is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}
?>