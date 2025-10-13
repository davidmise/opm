<?php
// Test CodeIgniter model directly
require_once __DIR__ . '/system/bootstrap.php';

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "Testing CodeIgniter Users_model save_job_info method:\n\n";

try {
    // Load the Users model
    $Users_model = model('App\Models\Users_model');
    
    echo "✅ Users_model loaded successfully\n";
    
    // Test data - update job info for user_id = 1
    $job_data = array(
        "user_id" => 1,
        "department_id" => 2,  // General department
        "salary" => 55000,
        "salary_term" => "per_year",
        "date_of_hire" => "2024-01-01"
    );
    
    echo "Test data: " . print_r($job_data, true) . "\n";
    
    // Call the save_job_info method
    echo "Calling Users_model->save_job_info()...\n";
    $result = $Users_model->save_job_info($job_data);
    
    if ($result) {
        echo "✅ save_job_info() returned success: $result\n";
    } else {
        echo "❌ save_job_info() returned failure\n";
    }
    
    // Get database instance to check for errors
    $db = \Config\Database::connect();
    $error = $db->error();
    if ($error['code'] !== 0) {
        echo "Database error: " . print_r($error, true) . "\n";
    }
    
    // Check if the data was actually saved
    echo "\nVerifying saved data:\n";
    $query = $db->query("SELECT * FROM opm_team_member_job_info WHERE user_id = 1");
    $result_data = $query->getRow();
    
    if ($result_data) {
        echo "✅ Data found in database:\n";
        echo "- User ID: " . $result_data->user_id . "\n";
        echo "- Department ID: " . $result_data->department_id . "\n";
        echo "- Salary: " . $result_data->salary . "\n";
        echo "- Salary Term: " . $result_data->salary_term . "\n";
        echo "- Date of Hire: " . $result_data->date_of_hire . "\n";
    } else {
        echo "❌ No data found in database for user_id = 1\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>