<?php

/**
 * Schema Migration Script
 * Updates opm_workflow_shipments table from old schema to new schema
 */

$db_host = 'localhost';
$db_name = 'overland_pm';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n\n";
    
    // Check if migration needed
    $check_sql = "SHOW COLUMNS FROM opm_workflow_shipments LIKE 'current_phase_id'";
    $result = $pdo->query($check_sql);
    
    if ($result->rowCount() > 0) {
        echo "Migration already applied. current_phase_id column exists.\n";
        exit;
    }
    
    echo "Starting migration...\n\n";
    
    // Backup data first
    echo "Step 1: Backing up existing shipment data...\n";
    $backup_sql = "CREATE TABLE IF NOT EXISTS opm_workflow_shipments_backup_" . date('Ymd_His') . " AS SELECT * FROM opm_workflow_shipments";
    $pdo->exec($backup_sql);
    echo "✓ Backup created\n\n";
    
    // Add new columns
    echo "Step 2: Adding new columns...\n";
    
    $alterations = array(
        "ADD COLUMN current_phase_id INT DEFAULT 1 AFTER cargo_value",
        "ADD COLUMN phase_locked TINYINT(1) DEFAULT 0 AFTER current_phase_id",
        "ADD COLUMN costs_cleared TINYINT(1) DEFAULT 0 AFTER phase_locked",
        "ADD COLUMN phase_transitioned_at TIMESTAMP NULL AFTER costs_cleared",
        "ADD COLUMN completed_at TIMESTAMP NULL AFTER status",
        "ADD COLUMN completed_by INT NULL AFTER completed_at",
        "ADD COLUMN created_by INT NULL AFTER completed_by",
        "ADD COLUMN deleted TINYINT(1) DEFAULT 0 AFTER created_by",
        "MODIFY COLUMN status VARCHAR(50) DEFAULT 'active'"
    );
    
    foreach ($alterations as $alter) {
        try {
            $sql = "ALTER TABLE opm_workflow_shipments $alter";
            $pdo->exec($sql);
            echo "✓ $alter\n";
        } catch (PDOException $e) {
            // Column might already exist
            if (strpos($e->getMessage(), 'Duplicate column') === false) {
                echo "⚠ $alter - " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nStep 3: Migrating current_phase enum to current_phase_id...\n";
    
    // Map old enum values to new phase IDs
    $phase_mapping = array(
        'clearing_intake' => 1,
        'regulatory_processing' => 2,
        'internal_review' => 3,
        'transport_loading' => 4,
        'tracking' => 5
    );
    
    foreach ($phase_mapping as $old_value => $new_id) {
        $sql = "UPDATE opm_workflow_shipments SET current_phase_id = $new_id WHERE current_phase = '$old_value'";
        $pdo->exec($sql);
        echo "✓ Migrated $old_value → Phase $new_id\n";
    }
    
    echo "\nStep 4: Migrating status enum...\n";
    // Status is now VARCHAR, no need to change values
    echo "✓ Status column type updated\n";
    
    echo "\nStep 5: Creating indexes for performance...\n";
    
    $indexes = array(
        "CREATE INDEX idx_shipment_phase ON opm_workflow_shipments(current_phase_id)",
        "CREATE INDEX idx_shipment_status ON opm_workflow_shipments(status)",
        "CREATE INDEX idx_shipment_client ON opm_workflow_shipments(client_id)",
        "CREATE INDEX idx_shipment_created ON opm_workflow_shipments(created_at)"
    );
    
    foreach ($indexes as $index_sql) {
        try {
            $pdo->exec($index_sql);
            echo "✓ Index created\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate key') === false) {
                echo "⚠ Index creation: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n========================================\n";
    echo "Migration completed successfully!\n";
    echo "========================================\n\n";
    
    // Display final structure
    echo "Updated table structure:\n";
    $columns = $pdo->query("SHOW COLUMNS FROM opm_workflow_shipments");
    foreach ($columns as $column) {
        echo "  - {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\nNote: Old 'current_phase' enum column still exists for reference.\n";
    echo "You can drop it manually when ready: ALTER TABLE opm_workflow_shipments DROP COLUMN current_phase;\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>
