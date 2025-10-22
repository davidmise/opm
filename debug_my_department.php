<?php
// Direct check to see what's happening with my_department access

// Load CodeIgniter
require_once 'system/bootstrap.php';
$app = \Config\Services::codeigniter();
$app->initialize();

// Get current user
$session = \Config\Services::session();
$user_id = $session->get('user_id');

echo "<h1>Debug My Department Access</h1>";
echo "<h2>Session Info:</h2>";
echo "<pre>";
echo "User ID: " . ($user_id ?? 'Not logged in') . "\n";
echo "Session Data: ";
print_r($session->get());
echo "</pre>";

if ($user_id) {
    // Load models
    $db = \Config\Database::connect();
    
    // Get user info
    $user = $db->table('opm_users')->where('id', $user_id)->get()->getRow();
    echo "<h2>User Info:</h2>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
    // Get user departments
    $user_depts = $db->table('opm_user_departments')
        ->where('user_id', $user_id)
        ->where('deleted', 0)
        ->get()->getResult();
    
    echo "<h2>User Departments:</h2>";
    echo "<pre>";
    print_r($user_depts);
    echo "</pre>";
    
    // Check modules
    $module_setting = $db->table('opm_settings')
        ->where('setting_name', 'module_departments')
        ->get()->getRow();
    
    echo "<h2>Departments Module:</h2>";
    echo "<pre>";
    print_r($module_setting);
    echo "</pre>";
}
