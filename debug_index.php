<?php
// Check what happens when we access index.php with PATH_INFO
echo "Testing index.php with PATH_INFO...\n";

// Simulate PATH_INFO
$_SERVER['REQUEST_URI'] = '/overland_pm/index.php/dashboard';
$_SERVER['PATH_INFO'] = '/dashboard';
$_SERVER['SCRIPT_NAME'] = '/overland_pm/index.php';

$url = "http://localhost/overland_pm/index.php";
$context = stream_context_create(['http' => ['timeout' => 5]]);
$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Could not access index.php\n";
} else {
    echo "SUCCESS: index.php accessible\n";
    echo "Response length: " . strlen($response) . "\n";
    echo "Response preview (first 500 chars):\n";
    echo substr($response, 0, 500) . "\n...\n";
}

// Test if we can detect CodeIgniter
if (strpos($response, 'CodeIgniter') !== false) {
    echo "✓ CodeIgniter detected in response\n";
} else {
    echo "✗ CodeIgniter NOT detected in response\n";
}

if (strpos($response, 'login') !== false || strpos($response, 'signin') !== false) {
    echo "✓ Login page detected - application is working\n";
} else {
    echo "? No obvious login page detected\n";
}
?>