-- Dummy data for Clearing & Transport Workflow System
-- Context: Tanzanian clearing and transport operations

-- Insert workflow trucks
INSERT INTO opm_workflow_trucks (truck_number, driver_name, driver_phone, truck_capacity, truck_type, status, current_location, notes) VALUES
('TZ-001-DAR', 'John Mwamba', '+255 754 123 456', 40.00, 'container', 'available', 'Dar es Salaam Port', 'Heavy duty container truck, good condition'),
('TZ-002-DAR', 'Peter Kimaro', '+255 756 234 567', 35.00, 'flatbed', 'assigned', 'Dar es Salaam Port', 'Flatbed for construction materials'),
('TZ-003-DSM', 'Hassan Ali', '+255 758 345 678', 25.00, 'container', 'in_transit', 'En route to Arusha', 'Currently delivering to northern region'),
('TZ-004-PWN', 'Grace Makena', '+255 759 456 789', 30.00, 'refrigerated', 'available', 'Mtwara Port', 'Refrigerated truck for perishables'),
('TZ-005-TNG', 'David Msigwa', '+255 761 567 890', 45.00, 'container', 'maintenance', 'Tanga Garage', 'Scheduled maintenance until next week'),
('TZ-006-DAR', 'Mary Juma', '+255 762 678 901', 35.00, 'flatbed', 'available', 'Dar es Salaam Depot', 'Recently serviced, ready for dispatch'),
('TZ-007-ARU', 'Emmanuel Mollel', '+255 763 789 012', 40.00, 'container', 'in_transit', 'Namanga Border', 'Cross-border delivery to Kenya'),
('TZ-008-MBY', 'Fatuma Hassan', '+255 764 890 123', 28.00, 'tanker', 'available', 'Mbeya Terminal', 'Specialized for liquid cargo transport');

-- Insert dummy shipments
INSERT INTO opm_workflow_shipments (shipment_number, client_id, cargo_type, cargo_weight, cargo_value, origin_port, destination_port, final_destination, estimated_arrival, actual_arrival, current_phase, status) VALUES
('SH-2025-001', 1, 'Electronics & Machinery', 15.50, 125000.00, 'Shanghai Port, China', 'Dar es Salaam Port', 'Arusha Industrial Area', '2025-01-15 14:30:00', '2025-01-15 16:20:00', 'regulatory_processing', 'active'),
('SH-2025-002', 2, 'Medical Equipment', 8.25, 89000.00, 'Mumbai Port, India', 'Dar es Salaam Port', 'Mwanza Hospital', '2025-01-18 09:15:00', NULL, 'clearing_intake', 'active'),
('SH-2025-003', 3, 'Construction Materials', 42.75, 78000.00, 'Durban Port, South Africa', 'Mtwara Port', 'Songea Construction Site', '2025-01-20 11:00:00', NULL, 'clearing_intake', 'active'),
('SH-2025-004', 1, 'Agricultural Machinery', 25.30, 156000.00, 'Hamburg Port, Germany', 'Dar es Salaam Port', 'Dodoma Agricultural Zone', '2025-01-12 13:45:00', '2025-01-12 15:10:00', 'transport_loading', 'active'),
('SH-2025-005', 4, 'Pharmaceutical Products', 3.80, 245000.00, 'Rotterdam Port, Netherlands', 'Dar es Salaam Port', 'Kilimanjaro Medical Center', '2025-01-22 08:30:00', NULL, 'regulatory_processing', 'active'),
('SH-2025-006', 2, 'Textile & Clothing', 18.90, 67000.00, 'Guangzhou Port, China', 'Tanga Port', 'Moshi Market', '2025-01-10 16:00:00', '2025-01-10 17:30:00', 'completed', 'completed');

-- Insert workflow tasks
INSERT INTO opm_workflow_tasks (shipment_id, task_name, task_description, assigned_to, phase, status, priority, due_date, completed_at) VALUES
(1, 'Process Bill of Lading', 'Review and process the bill of lading for electronics shipment', 2, 'clearing_intake', 'completed', 'high', '2025-01-15 12:00:00', '2025-01-15 11:45:00'),
(1, 'Customs Declaration Review', 'Verify customs declaration forms and calculate duties', 3, 'regulatory_processing', 'in_progress', 'urgent', '2025-01-16 10:00:00', NULL),
(1, 'Tax Certificate Verification', 'Confirm tax exemption certificate for machinery import', 3, 'regulatory_processing', 'pending', 'high', '2025-01-16 14:00:00', NULL),
(2, 'Medical Equipment Inspection', 'Conduct mandatory inspection for medical equipment import', 4, 'regulatory_processing', 'pending', 'urgent', '2025-01-19 09:00:00', NULL),
(2, 'Import Permit Validation', 'Validate import permits for medical devices', 2, 'clearing_intake', 'in_progress', 'high', '2025-01-18 15:00:00', NULL),
(3, 'Construction Material Assessment', 'Assess construction materials for quality standards', 5, 'clearing_intake', 'pending', 'medium', '2025-01-21 10:00:00', NULL),
(4, 'Agricultural Machinery Clearance', 'Complete clearance process for farming equipment', 2, 'clearing_intake', 'completed', 'high', '2025-01-13 09:00:00', '2025-01-13 08:30:00'),
(4, 'Load Agricultural Equipment', 'Load machinery onto designated transport truck', 6, 'transport_loading', 'in_progress', 'urgent', '2025-01-17 07:00:00', NULL),
(4, 'Transport Coordination', 'Coordinate transport to Dodoma with local agent', 6, 'transport_loading', 'pending', 'high', '2025-01-17 12:00:00', NULL),
(5, 'Pharmaceutical Cold Chain Verification', 'Verify cold chain requirements for pharmaceutical products', 4, 'regulatory_processing', 'pending', 'urgent', '2025-01-23 08:00:00', NULL),
(5, 'Special Handling Permits', 'Obtain special handling permits for controlled substances', 3, 'regulatory_processing', 'pending', 'urgent', '2025-01-23 12:00:00', NULL),
(6, 'Final Delivery Confirmation', 'Confirm successful delivery to Moshi Market', 6, 'tracking', 'completed', 'medium', '2025-01-11 16:00:00', '2025-01-11 15:45:00');

-- Insert workflow documents
INSERT INTO opm_workflow_documents (shipment_id, document_name, document_type, file_path, uploaded_by, upload_date, status, notes) VALUES
(1, 'Bill of Lading - Electronics SH-2025-001', 'bill_of_lading', '/files/workflow/SH-2025-001/bill_of_lading.pdf', 2, '2025-01-15 10:30:00', 'approved', 'Document verified and approved by customs'),
(1, 'Commercial Invoice - Electronics', 'commercial_invoice', '/files/workflow/SH-2025-001/commercial_invoice.pdf', 2, '2025-01-15 10:45:00', 'approved', 'Invoice amount verified against declared value'),
(1, 'Packing List - Electronics Shipment', 'packing_list', '/files/workflow/SH-2025-001/packing_list.pdf', 2, '2025-01-15 11:00:00', 'approved', 'All items listed and verified'),
(2, 'Medical Equipment Import Permit', 'permits', '/files/workflow/SH-2025-002/import_permit.pdf', 4, '2025-01-18 09:30:00', 'pending', 'Awaiting ministry approval for medical devices'),
(2, 'Bill of Lading - Medical Equipment', 'bill_of_lading', '/files/workflow/SH-2025-002/bill_of_lading.pdf', 2, '2025-01-18 10:00:00', 'approved', 'Document received and verified'),
(3, 'Construction Materials Certificate', 'inspection_report', '/files/workflow/SH-2025-003/quality_certificate.pdf', 5, '2025-01-20 14:00:00', 'pending', 'Quality inspection scheduled for next week'),
(4, 'Agricultural Machinery Tax Exemption', 'tax_certificate', '/files/workflow/SH-2025-004/tax_exemption.pdf', 3, '2025-01-12 16:30:00', 'approved', 'Tax exemption approved for agricultural equipment'),
(4, 'Customs Declaration - Agricultural', 'customs_declaration', '/files/workflow/SH-2025-004/customs_declaration.pdf', 3, '2025-01-12 17:00:00', 'approved', 'Customs clearance completed successfully'),
(5, 'Pharmaceutical Import License', 'permits', '/files/workflow/SH-2025-005/pharma_license.pdf', 4, '2025-01-22 11:00:00', 'pending', 'License renewal in progress with health ministry'),
(5, 'Cold Chain Compliance Certificate', 'inspection_report', '/files/workflow/SH-2025-005/cold_chain_cert.pdf', 4, '2025-01-22 11:30:00', 'pending', 'Temperature monitoring compliance verification needed'),
(6, 'Textile Import Declaration', 'customs_declaration', '/files/workflow/SH-2025-006/customs_declaration.pdf', 3, '2025-01-10 14:00:00', 'approved', 'Completed shipment - all documentation approved'),
(1, 'Electronics Safety Inspection Report', 'inspection_report', '/files/workflow/SH-2025-001/safety_inspection.pdf', 4, '2025-01-15 15:00:00', 'approved', 'Electronics meet Tanzanian safety standards'),
(2, 'Medical Device Registration', 'other', '/files/workflow/SH-2025-002/device_registration.pdf', 4, '2025-01-18 11:30:00', 'pending', 'Registration with Tanzania Medical Devices Authority pending'),
(3, 'Environmental Impact Assessment', 'other', '/files/workflow/SH-2025-003/environmental_assessment.pdf', 5, '2025-01-20 15:30:00', 'pending', 'Environmental clearance required for construction materials'),
(4, 'Agricultural Equipment Installation Manual', 'other', '/files/workflow/SH-2025-004/installation_manual.pdf', 6, '2025-01-13 10:00:00', 'approved', 'Technical documentation for equipment setup');

-- Insert truck allocations
INSERT INTO opm_workflow_truck_allocations (shipment_id, truck_id, allocated_at, departure_time, arrival_time, status, mileage_start, mileage_end, fuel_cost, notes) VALUES
(4, 1, '2025-01-16 08:00:00', '2025-01-17 06:00:00', NULL, 'allocated', 125430.5, NULL, NULL, 'Truck allocated for agricultural machinery transport to Dodoma'),
(6, 2, '2025-01-10 12:00:00', '2025-01-10 18:00:00', '2025-01-11 15:30:00', 'delivered', 98765.2, 99127.8, 185000.00, 'Completed delivery of textiles to Moshi Market'),
(3, 4, '2025-01-20 16:00:00', NULL, NULL, 'allocated', 67890.1, NULL, NULL, 'Refrigerated truck allocated for construction materials - departure pending'),
(1, 7, '2025-01-15 20:00:00', '2025-01-16 05:00:00', NULL, 'in_transit', 156789.3, NULL, NULL, 'Electronics shipment en route to Arusha via cross-border truck');

-- Insert tracking information
INSERT INTO omp_workflow_tracking (shipment_id, location, status_update, latitude, longitude, timestamp, updated_by) VALUES
(1, 'Dar es Salaam Port - Container Terminal', 'Shipment arrived and unloaded from vessel', -6.8296, 39.2830, '2025-01-15 16:20:00', 2),
(1, 'Dar es Salaam Customs Office', 'Customs inspection completed, proceeding to regulatory review', -6.8156, 39.2884, '2025-01-15 18:30:00', 3),
(1, 'Transport Loading Bay', 'Shipment loaded onto truck TZ-007-ARU for Arusha delivery', -6.8200, 39.2700, '2025-01-16 07:15:00', 6),
(2, 'Dar es Salaam Port - Medical Cargo Terminal', 'Medical equipment arrived, awaiting specialized inspection', -6.8310, 39.2845, '2025-01-18 10:45:00', 4),
(3, 'Mtwara Port - Cargo Handling Area', 'Construction materials unloaded, quality assessment in progress', -10.2736, 40.1796, '2025-01-20 12:30:00', 5),
(4, 'Dodoma Agricultural Zone - Delivery Point', 'Agricultural machinery successfully delivered to final destination', -6.1833, 35.7500, '2025-01-13 14:20:00', 6),
(6, 'Moshi Market - Textile Section', 'Textile shipment delivered and received by client', -3.3397, 37.3407, '2025-01-11 15:45:00', 6),
(1, 'Chalinze Weighbridge', 'Truck passed weighbridge inspection, continuing to Arusha', -6.6833, 38.3000, '2025-01-16 09:30:00', 6),
(4, 'Dodoma Regional Office', 'Final documentation handover completed for agricultural project', -6.1629, 35.7516, '2025-01-13 16:00:00', 2),
(5, 'Dar es Salaam Cold Storage Facility', 'Pharmaceutical products transferred to temperature-controlled storage', -6.8100, 39.2650, '2025-01-22 14:00:00', 4);