<?php
// Test URL generation
require_once 'app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/bootstrap.php';

// Load the framework
$app = \Config\Services::codeigniter();
$app->initialize();

echo "Testing URL generation:\n";
echo "Base URL: " . base_url() . "\n";
echo "Config indexPage: " . config("App")->indexPage . "\n";
echo "get_uri('departments'): " . get_uri('departments') . "\n";
echo "get_uri('departments/departments_list'): " . get_uri('departments/departments_list') . "\n";
echo "get_uri('departments/settings'): " . get_uri('departments/settings') . "\n";
?>