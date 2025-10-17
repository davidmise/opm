<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowTables extends Migration
{
    public function up()
    {
        // Workflow phases table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'phase_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#3498db',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('omp_workflow_phases');

        // Shipments table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shipment_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'client_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'current_phase_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'hold', 'cancelled'],
                'default' => 'pending',
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'urgent'],
                'default' => 'medium',
            ],
            'cargo_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'cargo_weight' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'cargo_value' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'origin_port' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'destination_port' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'final_destination' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'estimated_arrival' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'actual_arrival' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('shipment_number');
        $this->forge->addKey('client_id');
        $this->forge->addKey('current_phase_id');
        $this->forge->createTable('omp_shipments');

        // Documents table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shipment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'document_type' => [
                'type' => 'ENUM',
                'constraint' => [
                    'bill_of_lading',
                    'commercial_invoice',
                    'packing_list',
                    'declaration_document',
                    'customs_release_order',
                    't1_form',
                    'shipping_order',
                    'custom_pre_alert_document',
                    'proof_of_delivery',
                    'other'
                ],
                'null' => false,
            ],
            'document_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'received', 'processed', 'archived'],
                'default' => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('shipment_id');
        $this->forge->addKey('document_type');
        $this->forge->createTable('omp_documents');

        // Workflow tasks table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shipment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'phase_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'task_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'task_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'assigned_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'escalated', 'cancelled'],
                'default' => 'pending',
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'urgent'],
                'default' => 'medium',
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'escalated_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'escalated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('shipment_id');
        $this->forge->addKey('phase_id');
        $this->forge->addKey('assigned_to');
        $this->forge->createTable('omp_workflow_tasks');

        // Trucks table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'truck_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'driver_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'driver_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'driver_license' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'truck_capacity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'truck_type' => [
                'type' => 'ENUM',
                'constraint' => ['container', 'flatbed', 'tanker', 'refrigerated', 'general'],
                'default' => 'general',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['available', 'assigned', 'in_transit', 'maintenance', 'inactive'],
                'default' => 'available',
            ],
            'current_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('truck_number');
        $this->forge->createTable('omp_trucks');

        // Truck allocations table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shipment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'truck_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'allocated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'allocation_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'loading_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'departure_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'estimated_delivery' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'actual_delivery' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['allocated', 'loading', 'in_transit', 'delivered', 'cancelled'],
                'default' => 'allocated',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('shipment_id');
        $this->forge->addKey('truck_id');
        $this->forge->createTable('omp_truck_allocations');

        // Tracking reports table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shipment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'truck_allocation_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'bl_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'current_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status_update' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'border_entry_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'border_exit_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'border_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'client_notified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('shipment_id');
        $this->forge->addKey('truck_allocation_id');
        $this->forge->createTable('omp_tracking_reports');
    }

    public function down()
    {
        $this->forge->dropTable('omp_tracking_reports');
        $this->forge->dropTable('omp_truck_allocations');
        $this->forge->dropTable('omp_trucks');
        $this->forge->dropTable('omp_workflow_tasks');
        $this->forge->dropTable('omp_documents');
        $this->forge->dropTable('omp_shipments');
        $this->forge->dropTable('omp_workflow_phases');
    }
}