<?php
// Simple test to check list_shipments endpoint response
session_start();

// Simulate being logged in (for testing purposes)
$_SESSION['user_id'] = 1;  // Assuming admin user ID is 1

// Test the endpoint directly
$url = 'http://localhost/overland_pm/index.php/workflow/list_shipments';

// Use file_get_contents with context for POST request
$postdata = http_build_query(array());
$context = stream_context_create(array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $postdata,
        'timeout' => 30
    )
));

echo "Testing endpoint: $url\n";
echo "========================\n";

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Failed to get response\n";
} else {
    echo "Response received (" . strlen($response) . " bytes):\n";
    echo "First 500 characters:\n";
    echo substr($response, 0, 500) . "\n";
    echo "...\n";
    
    // Check if it's JSON
    $json = @json_decode($response, true);
    if ($json !== null) {
        echo "\nParsed JSON:\n";
        echo "- Data count: " . (isset($json['data']) ? count($json['data']) : 'No data key') . "\n";
        echo "- Has debug: " . (isset($json['debug']) ? 'Yes' : 'No') . "\n";
        if (isset($json['data']) && count($json['data']) > 0) {
            echo "- First record columns: " . count($json['data'][0]) . "\n";
            echo "- Sample data: " . print_r(array_slice($json['data'], 0, 1), true);
        }
    } else {
        echo "\nNOT valid JSON - this is the problem!\n";
        echo "Response looks like HTML (login page)\n";
    }
}
?>