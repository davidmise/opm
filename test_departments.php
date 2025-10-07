<?php

/**
 * Department Enhancement Testing Script
 * 
 * This script tests the enhanced department functionality
 * Run this after migration to verify everything works correctly
 * 
 * Usage: Place in root directory and access via browser
 * URL: http://localhost/overland_pm/test_departments.php
 */

// Include CodeIgniter bootstrap
require_once 'app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/bootstrap.php';

// Load the framework
$app = \Config\Services::codeigniter();
$app->initialize();

echo "<h1>Department Enhancement Test Results</h1>";
echo "<style>
.test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
</style>";

// Test 1: Database Schema
echo "<div class='test-section'>";
echo "<h3>1. Database Schema Tests</h3>";

$db = \Config\Database::connect();

// Check if new columns exist in departments table
$query = $db->query("SHOW COLUMNS FROM `departments` LIKE 'icon'");
if ($query->getNumRows() > 0) {
    echo "<p class='success'>✓ Departments.icon column exists</p>";
} else {
    echo "<p class='error'>✗ Departments.icon column missing</p>";
}

$query = $db->query("SHOW COLUMNS FROM `departments` LIKE 'is_active'");
if ($query->getNumRows() > 0) {
    echo "<p class='success'>✓ Departments.is_active column exists</p>";
} else {
    echo "<p class='error'>✗ Departments.is_active column missing</p>";
}

$query = $db->query("SHOW COLUMNS FROM `departments` LIKE 'head_user_id'");
if ($query->getNumRows() > 0) {
    echo "<p class='success'>✓ Departments.head_user_id column exists</p>";
} else {
    echo "<p class='error'>✗ Departments.head_user_id column missing</p>";
}

// Check if user_departments table exists
$query = $db->query("SHOW TABLES LIKE 'user_departments'");
if ($query->getNumRows() > 0) {
    echo "<p class='success'>✓ user_departments table exists</p>";
} else {
    echo "<p class='error'>✗ user_departments table missing</p>";
}

// Check if department_permissions table exists
$query = $db->query("SHOW TABLES LIKE 'department_permissions'");
if ($query->getNumRows() > 0) {
    echo "<p class='success'>✓ department_permissions table exists</p>";
} else {
    echo "<p class='error'>✗ department_permissions table missing</p>";
}

// Check if tasks.department_id column exists
$query = $db->query("SHOW COLUMNS FROM `tasks` LIKE 'department_id'");
if ($query->getNumRows() > 0) {
    echo "<p class='success'>✓ tasks.department_id column exists</p>";
} else {
    echo "<p class='error'>✗ tasks.department_id column missing</p>";
}

echo "</div>";

// Test 2: Model Functionality
echo "<div class='test-section'>";
echo "<h3>2. Model Functionality Tests</h3>";

try {
    $departmentsModel = model('App\Models\Departments_model');
    $departments = $departmentsModel->get_details()->getResult();
    echo "<p class='success'>✓ Departments_model loads successfully</p>";
    echo "<p>Found " . count($departments) . " departments</p>";
    
    foreach ($departments as $dept) {
        echo "<p>- " . $dept->title . " (ID: " . $dept->id . ")</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Departments_model error: " . $e->getMessage() . "</p>";
}

try {
    $userDepartmentsModel = model('App\Models\User_departments_model');
    echo "<p class='success'>✓ User_departments_model loads successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ User_departments_model error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test 3: Settings and Module Status
echo "<div class='test-section'>";
echo "<h3>3. Settings and Module Status</h3>";

$query = $db->query("SELECT setting_value FROM `settings` WHERE setting_name='module_departments'");
if ($query->getNumRows() > 0) {
    $setting = $query->getRow();
    if ($setting->setting_value == '1') {
        echo "<p class='success'>✓ Departments module is enabled</p>";
    } else {
        echo "<p class='warning'>⚠ Departments module is disabled</p>";
    }
} else {
    echo "<p class='error'>✗ module_departments setting not found</p>";
}

// Check new settings
$new_settings = [
    'enable_department_dashboards',
    'enable_multi_department_users',
    'enable_department_permissions'
];

foreach ($new_settings as $setting) {
    $query = $db->query("SELECT setting_value FROM `settings` WHERE setting_name='$setting'");
    if ($query->getNumRows() > 0) {
        $row = $query->getRow();
        echo "<p class='success'>✓ $setting = " . $row->setting_value . "</p>";
    } else {
        echo "<p class='error'>✗ $setting setting missing</p>";
    }
}

echo "</div>";

// Test 4: Controller Access
echo "<div class='test-section'>";
echo "<h3>4. Controller Access Tests</h3>";

$baseUrl = base_url();
$testUrls = [
    'departments' => 'departments',
    'departments/modal_form' => 'departments/modal_form',
    'departments/list_data' => 'departments/list_data'
];

foreach ($testUrls as $name => $url) {
    $fullUrl = $baseUrl . $url;
    echo "<p>Testing: <a href='$fullUrl' target='_blank'>$fullUrl</a></p>";
}

echo "</div>";

// Test 5: File Structure
echo "<div class='test-section'>";
echo "<h3>5. File Structure Tests</h3>";

$requiredFiles = [
    'app/Models/Departments_model.php' => 'Departments Model',
    'app/Models/User_departments_model.php' => 'User Departments Model',
    'app/Controllers/Departments.php' => 'Departments Controller',
    'app/Controllers/Department_Access_Controller.php' => 'Department Access Controller',
    'app/Views/departments/dashboard.php' => 'Department Dashboard View',
    'app/Views/departments/manage_users.php' => 'Manage Users View',
    'app/Views/departments/add_user_modal.php' => 'Add User Modal View',
    'app/Views/includes/department_filter_widget.php' => 'Department Filter Widget',
    'install/department_enhancements_migration.sql' => 'Migration Script'
];

foreach ($requiredFiles as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ $description ($file)</p>";
    } else {
        echo "<p class='error'>✗ $description missing ($file)</p>";
    }
}

echo "</div>";

// Test 6: Sample Data Test
echo "<div class='test-section'>";
echo "<h3>6. Sample Data Tests</h3>";

// Check if sample departments were created
$sampleDepts = ['IT', 'Accounts', 'Finance', 'Tracking', 'Operations', 'HR'];
foreach ($sampleDepts as $deptName) {
    $query = $db->query("SELECT id FROM `departments` WHERE title='$deptName' AND deleted=0");
    if ($query->getNumRows() > 0) {
        echo "<p class='success'>✓ $deptName department exists</p>";
    } else {
        echo "<p class='warning'>⚠ $deptName department not found (may need to run migration)</p>";
    }
}

echo "</div>";

// Test 7: Integration Points
echo "<div class='test-section'>";
echo "<h3>7. Integration Points Tests</h3>";

// Check if Projects controller extends Department_Access_Controller
$projectsContent = file_get_contents('app/Controllers/Projects.php');
if (strpos($projectsContent, 'extends Department_Access_Controller') !== false) {
    echo "<p class='success'>✓ Projects controller extends Department_Access_Controller</p>";
} else {
    echo "<p class='warning'>⚠ Projects controller may need department access integration</p>";
}

// Check if Tasks controller extends Department_Access_Controller
$tasksContent = file_get_contents('app/Controllers/Tasks.php');
if (strpos($tasksContent, 'extends Department_Access_Controller') !== false) {
    echo "<p class='success'>✓ Tasks controller extends Department_Access_Controller</p>";
} else {
    echo "<p class='warning'>⚠ Tasks controller may need department access integration</p>";
}

echo "</div>";

// Summary
echo "<div class='test-section'>";
echo "<h3>Summary</h3>";
echo "<p><strong>To complete the implementation:</strong></p>";
echo "<ol>";
echo "<li>Run the migration script: <code>install/department_enhancements_migration.sql</code></li>";
echo "<li>Ensure all controllers that need department filtering extend Department_Access_Controller</li>";
echo "<li>Add department filter widgets to relevant views</li>";
echo "<li>Test department dashboard and user management features</li>";
echo "<li>Configure department permissions for users</li>";
echo "</ol>";

echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Access the departments dashboard at: <a href='" . base_url('departments') . "' target='_blank'>" . base_url('departments') . "</a></li>";
echo "<li>Configure department settings in admin panel</li>";
echo "<li>Assign users to departments</li>";
echo "<li>Test department-filtered project and task views</li>";
echo "</ul>";
echo "</div>";

?>

<script>
// Auto-refresh every 30 seconds during testing
setTimeout(function() {
    console.log('Auto-refreshing test results...');
    location.reload();
}, 30000);
</script>