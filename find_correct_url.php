<?php
// Try different URL patterns to find the correct one
echo "Testing different URL patterns...\n";

$urls = [
    "http://localhost/overland_pm/public/index.php",
    "http://localhost/overland_pm/public/",
    "http://overland_pm.test/",
    "http://overland_pm.test/index.php",
    "http://localhost:80/overland_pm/index.php",
    "http://127.0.0.1/overland_pm/index.php"
];

foreach ($urls as $url) {
    echo "\nTesting: $url\n";
    $context = stream_context_create(['http' => ['timeout' => 3]]);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "ERROR: Not accessible\n";
    } else {
        echo "SUCCESS: Accessible (Length: " . strlen($response) . ")\n";
        
        // Check if it's CodeIgniter
        if (strpos($response, 'CodeIgniter') !== false || strpos($response, 'signin') !== false || strpos($response, 'login') !== false) {
            echo "✓ Looks like the CodeIgniter application!\n";
            echo "Preview: " . substr(strip_tags($response), 0, 200) . "\n";
        } else {
            echo "✗ Not the CodeIgniter application\n";
        }
    }
}
?>