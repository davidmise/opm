<?php
// Test if any controllers work
echo "Testing various controllers...\n";

$controllers = [
    'dashboard',
    'projects', 
    'clients',
    'users',
    'announcements'
];

foreach ($controllers as $controller) {
    $url = "http://localhost/overland_pm/index.php/$controller";
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        echo "$controller: ERROR\n";
    } else {
        echo "$controller: SUCCESS (Length: " . strlen($response) . ")\n";
    }
}
?>