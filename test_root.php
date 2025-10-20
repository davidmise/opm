<?php
// Test root URL
echo "Testing root endpoint...\n";

$url = "http://localhost/overland_pm/";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access root URL\n";
} else {
    echo "SUCCESS: Root endpoint is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
    echo "Response preview (first 200 chars): " . substr($response, 0, 200) . "...\n";
}

// Test index.php directly
echo "\nTesting index.php endpoint...\n";
$url = "http://localhost/overland_pm/index.php";
$response = file_get_contents($url);

if ($response === false) {
    echo "ERROR: Could not access index.php URL\n";
} else {
    echo "SUCCESS: index.php endpoint is accessible\n";
    echo "Response length: " . strlen($response) . " characters\n";
}
?>