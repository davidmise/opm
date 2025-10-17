-- Dummy Data for Clearing & Transport Workflow System
-- Based on realistic Tanzanian operations

-- First, let's add the workflow team members
INSERT INTO `users` (`id`, `first_name`, `last_name`, `user_type`, `is_admin`, `role_id`, `email`, `password`, `status`, `job_title`, `phone`, `created_at`, `deleted`) VALUES
(2, 'Imran', 'Hassan', 'staff', 1, 0, 'imran.hassan@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Top Boss / Managing Director', '+255 754 123 456', NOW(), 0),
(3, 'Zakayo', 'Mwalimu', 'staff', 0, 0, 'zakayo.mwalimu@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Supervisor', '+255 755 234 567', NOW(), 0),
(4, 'Miriam', 'Kimario', 'staff', 0, 0, 'miriam.kimario@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Assistant / File Manager', '+255 756 345 678', NOW(), 0),
(5, 'Pendo', 'Lugano', 'staff', 0, 0, 'pendo.lugano@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Customs Specialist', '+255 757 456 789', NOW(), 0),
(6, 'Edson', 'Mwakalinga', 'staff', 0, 0, 'edson.mwakalinga@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Shipping Line Coordinator', '+255 758 567 890', NOW(), 0),
(7, 'Husein', 'Omari', 'staff', 0, 0, 'husein.omari@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Transport Manager', '+255 759 678 901', NOW(), 0),
(8, 'Robert', 'Shayo', 'staff', 0, 0, 'robert.shayo@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Operations Coordinator', '+255 765 789 012', NOW(), 0),
(9, 'Grace', 'Mwakasege', 'staff', 0, 0, 'grace.mwakasege@overland.co.tz', '$2y$10$defaultpassword', 'active', 'General Manager', '+255 766 890 123', NOW(), 0),
(10, 'Daniel', 'Lyimo', 'staff', 0, 0, 'daniel.lyimo@overland.co.tz', '$2y$10$defaultpassword', 'active', 'Tracking Coordinator', '+255 767 901 234', NOW(), 0);

-- Add realistic Tanzanian clients
INSERT INTO `clients` (`id`, `company_name`, `type`, `address`, `city`, `state`, `country`, `created_date`, `phone`, `group_ids`, `deleted`, `is_lead`, `lead_status_id`, `owner_id`, `created_by`, `sort`, `lead_source_id`) VALUES
(1, 'Simba Cement Company Ltd', 'organization', 'Plot 123, Nyerere Road', 'Dar es Salaam', 'Dar es Salaam', 'Tanzania', NOW(), '+255 22 212 3456', '', 0, 0, 0, 2, 2, 0, 1),
(2, 'East African Steel Mills', 'organization', 'Plot 456, Morogoro Road', 'Dar es Salaam', 'Dar es Salaam', 'Tanzania', NOW(), '+255 22 286 7890', '', 0, 0, 0, 2, 2, 0, 1),
(3, 'Tanzania Coffee Board', 'organization', 'Kilimanjaro Wing, P.O. Box 732', 'Moshi', 'Kilimanjaro', 'Tanzania', NOW(), '+255 27 275 4321', '', 0, 0, 0, 2, 2, 0, 1),
(4, 'Mwanza Cotton Traders Ltd', 'organization', 'Plot 789, Kenyatta Road', 'Mwanza', 'Mwanza', 'Tanzania', NOW(), '+255 28 250 1234', '', 0, 0, 0, 2, 2, 0, 1),
(5, 'Dodoma Agri-Processing Co.', 'organization', 'Plot 321, Independence Avenue', 'Dodoma', 'Dodoma', 'Tanzania', NOW(), '+255 26 232 5678', '', 0, 0, 0, 2, 2, 0, 1),
(6, 'Arusha Mining Corporation', 'organization', 'Sokoine Road, Industrial Area', 'Arusha', 'Arusha', 'Tanzania', NOW(), '+255 27 250 9876', '', 0, 0, 0, 2, 2, 0, 1);

-- Add client contacts
INSERT INTO `users` (`id`, `first_name`, `last_name`, `user_type`, `is_admin`, `role_id`, `email`, `password`, `status`, `client_id`, `is_primary_contact`, `job_title`, `phone`, `created_at`, `deleted`) VALUES
(11, 'Joseph', 'Mwamba', 'client', 0, 0, 'joseph.mwamba@simbacement.co.tz', '$2y$10$defaultpassword', 'active', 1, 1, 'Logistics Manager', '+255 754 111 222', NOW(), 0),
(12, 'Sarah', 'Kimweri', 'client', 0, 0, 'sarah.kimweri@eastafrican.co.tz', '$2y$10$defaultpassword', 'active', 2, 1, 'Supply Chain Director', '+255 755 333 444', NOW(), 0),
(13, 'Hassan', 'Mwinyi', 'client', 0, 0, 'hassan.mwinyi@coffeeboard.go.tz', '$2y$10$defaultpassword', 'active', 3, 1, 'Export Manager', '+255 756 555 666', NOW(), 0),
(14, 'Fatma', 'Selemani', 'client', 0, 0, 'fatma.selemani@mwanzacotton.co.tz', '$2y$10$defaultpassword', 'active', 4, 1, 'Trading Manager', '+255 757 777 888', NOW(), 0),
(15, 'Peter', 'Macha', 'client', 0, 0, 'peter.macha@dodomaagri.co.tz', '$2y$10$defaultpassword', 'active', 5, 1, 'Operations Head', '+255 758 999 000', NOW(), 0),
(16, 'Mary', 'Mollel', 'client', 0, 0, 'mary.mollel@arushamining.co.tz', '$2y$10$defaultpassword', 'active', 6, 1, 'Logistics Coordinator', '+255 759 111 333', NOW(), 0);

-- Add trucks with Tanzanian registration numbers
INSERT INTO `omp_trucks` (`id`, `truck_number`, `driver_name`, `driver_phone`, `driver_license`, `truck_capacity`, `truck_type`, `status`, `current_location`, `created_at`, `deleted`) VALUES
(1, 'T 123 ABC', 'Juma Kassim', '+255 763 456 789', 'DL001234', 30.00, 'container', 'available', 'Dar es Salaam Port', NOW(), 0),
(2, 'T 456 DEF', 'Mohamed Ali', '+255 764 567 890', 'DL002345', 25.00, 'flatbed', 'available', 'Dar es Salaam Port', NOW(), 0),
(3, 'T 789 GHI', 'Hamisi Juma', '+255 765 678 901', 'DL003456', 40.00, 'container', 'available', 'Mwanza', NOW(), 0),
(4, 'T 321 JKL', 'Rashid Omary', '+255 766 789 012', 'DL004567', 35.00, 'general', 'available', 'Dar es Salaam Port', NOW(), 0),
(5, 'T 654 MNO', 'Salim Hassan', '+255 767 890 123', 'DL005678', 20.00, 'refrigerated', 'available', 'Arusha', NOW(), 0),
(6, 'T 987 PQR', 'Ibrahim Mwalimu', '+255 768 901 234', 'DL006789', 45.00, 'container', 'available', 'Dar es Salaam Port', NOW(), 0),
(7, 'T 147 STU', 'Ally Khamis', '+255 769 012 345', 'DL007890', 30.00, 'flatbed', 'available', 'Mbeya', NOW(), 0),
(8, 'T 258 VWX', 'Bakari Selemani', '+255 761 123 456', 'DL008901', 38.00, 'general', 'available', 'Dar es Salaam Port', NOW(), 0);

-- Add realistic shipments with Tanzanian context
INSERT INTO `omp_shipments` (`id`, `shipment_number`, `client_id`, `current_phase_id`, `status`, `priority`, `cargo_type`, `cargo_weight`, `cargo_value`, `origin_port`, `destination_port`, `final_destination`, `estimated_arrival`, `actual_arrival`, `created_by`, `created_at`, `updated_at`, `notes`, `deleted`) VALUES
(1, 'SHM-2024-001', 1, 3, 'in_progress', 'high', 'Cement Manufacturing Equipment', 25000.00, 850000.00, 'Shanghai Port', 'Dar es Salaam Port', 'Mbeya Industrial Area', '2024-10-20', '2024-10-18', 2, '2024-10-15 08:30:00', '2024-10-18 14:20:00', 'Heavy machinery for new cement plant expansion', 0),
(2, 'SHM-2024-002', 2, 4, 'in_progress', 'urgent', 'Steel Raw Materials', 40000.00, 1200000.00, 'Mumbai Port', 'Dar es Salaam Port', 'Mwanza Steel Mills', '2024-10-22', '2024-10-21', 2, '2024-10-16 09:15:00', '2024-10-21 11:45:00', 'Critical steel coils for production line', 0),
(3, 'SHM-2024-003', 3, 2, 'in_progress', 'medium', 'Coffee Processing Machinery', 15000.00, 675000.00, 'Hamburg Port', 'Dar es Salaam Port', 'Kilimanjaro Coffee Estate', '2024-10-25', NULL, 2, '2024-10-17 10:00:00', '2024-10-17 10:00:00', 'New coffee roasting and packaging equipment', 0),
(4, 'SHM-2024-004', 4, 1, 'pending', 'medium', 'Cotton Ginning Equipment', 20000.00, 450000.00, 'Karachi Port', 'Dar es Salaam Port', 'Mwanza Cotton Processing', '2024-10-28', NULL, 2, '2024-10-18 11:30:00', '2024-10-18 11:30:00', 'Cotton processing machinery for harvest season', 0),
(5, 'SHM-2024-005', 5, 5, 'completed', 'low', 'Agricultural Machinery', 12000.00, 320000.00, 'Durban Port', 'Dar es Salaam Port', 'Dodoma Agricultural Center', '2024-10-10', '2024-10-08', 2, '2024-10-05 07:45:00', '2024-10-16 16:30:00', 'Tractors and farming equipment delivered successfully', 0),
(6, 'SHM-2024-006', 6, 4, 'in_progress', 'high', 'Mining Equipment', 35000.00, 1500000.00, 'Cape Town Port', 'Dar es Salaam Port', 'Mererani Tanzanite Mines', '2024-10-24', '2024-10-22', 2, '2024-10-14 13:20:00', '2024-10-22 09:15:00', 'Specialized mining equipment for tanzanite extraction', 0);

-- Add documents for shipments
INSERT INTO `omp_documents` (`id`, `shipment_id`, `document_type`, `document_name`, `file_path`, `file_size`, `uploaded_by`, `uploaded_at`, `status`, `notes`, `deleted`) VALUES
-- Shipment 1 documents
(1, 1, 'bill_of_lading', 'BL-SHM-2024-001-Shanghai.pdf', 'documents/bl/BL-SHM-2024-001.pdf', 245678, 11, '2024-10-15 08:45:00', 'processed', 'Original BL from Shanghai shipping line', 0),
(2, 1, 'commercial_invoice', 'CI-SHM-2024-001-Cement-Equipment.pdf', 'documents/ci/CI-SHM-2024-001.pdf', 189432, 11, '2024-10-15 08:50:00', 'processed', 'Commercial invoice for cement machinery', 0),
(3, 1, 'packing_list', 'PL-SHM-2024-001-Detailed.pdf', 'documents/pl/PL-SHM-2024-001.pdf', 156789, 11, '2024-10-15 08:55:00', 'processed', 'Detailed packing list with specifications', 0),
(4, 1, 'declaration_document', 'DD-SHM-2024-001-TRA.pdf', 'documents/dd/DD-SHM-2024-001.pdf', 198765, 5, '2024-10-16 14:30:00', 'processed', 'TRA declaration completed by Pendo', 0),
(5, 1, 'customs_release_order', 'CRO-SHM-2024-001-Release.pdf', 'documents/cro/CRO-SHM-2024-001.pdf', 145632, 6, '2024-10-17 10:15:00', 'processed', 'Customs release order from shipping line', 0),

-- Shipment 2 documents
(6, 2, 'bill_of_lading', 'BL-SHM-2024-002-Mumbai.pdf', 'documents/bl/BL-SHM-2024-002.pdf', 234567, 12, '2024-10-16 09:30:00', 'processed', 'Steel materials BL from Mumbai', 0),
(7, 2, 'commercial_invoice', 'CI-SHM-2024-002-Steel.pdf', 'documents/ci/CI-SHM-2024-002.pdf', 167890, 12, '2024-10-16 09:35:00', 'processed', 'Steel raw materials commercial invoice', 0),
(8, 2, 'packing_list', 'PL-SHM-2024-002-Steel-Coils.pdf', 'documents/pl/PL-SHM-2024-002.pdf', 178901, 12, '2024-10-16 09:40:00', 'processed', 'Steel coils packing specifications', 0),
(9, 2, 'declaration_document', 'DD-SHM-2024-002-TRA.pdf', 'documents/dd/DD-SHM-2024-002.pdf', 189012, 5, '2024-10-18 11:20:00', 'processed', 'TRA processing for steel imports', 0),
(10, 2, 'customs_release_order', 'CRO-SHM-2024-002-Release.pdf', 'documents/cro/CRO-SHM-2024-002.pdf', 156789, 6, '2024-10-19 15:45:00', 'processed', 'Shipping line release for steel', 0),
(11, 2, 't1_form', 'T1-SHM-2024-002-Transport.pdf', 'documents/t1/T1-SHM-2024-002.pdf', 134567, 8, '2024-10-21 08:30:00', 'processed', 'T1 form for Mwanza transport', 0),
(12, 2, 'shipping_order', 'SO-SHM-2024-002-Loading.pdf', 'documents/so/SO-SHM-2024-002.pdf', 145678, 8, '2024-10-21 09:00:00', 'processed', 'Shipping order for truck loading', 0),

-- Shipment 3 documents
(13, 3, 'bill_of_lading', 'BL-SHM-2024-003-Hamburg.pdf', 'documents/bl/BL-SHM-2024-003.pdf', 267890, 13, '2024-10-17 10:15:00', 'received', 'Coffee machinery BL from Hamburg', 0),
(14, 3, 'commercial_invoice', 'CI-SHM-2024-003-Coffee.pdf', 'documents/ci/CI-SHM-2024-003.pdf', 189123, 13, '2024-10-17 10:20:00', 'received', 'Coffee processing equipment invoice', 0),
(15, 3, 'packing_list', 'PL-SHM-2024-003-Machinery.pdf', 'documents/pl/PL-SHM-2024-003.pdf', 198234, 13, '2024-10-17 10:25:00', 'received', 'Coffee machinery components list', 0);

-- Add workflow tasks
INSERT INTO `omp_workflow_tasks` (`id`, `shipment_id`, `phase_id`, `task_name`, `task_description`, `assigned_to`, `assigned_by`, `status`, `priority`, `due_date`, `started_at`, `completed_at`, `created_at`, `notes`, `deleted`) VALUES
-- Phase 1 tasks for shipment 1
(1, 1, 1, 'Document Review and File Creation', 'Review BL, CI, and PL documents. Create master file with all details.', 4, 3, 'completed', 'high', '2024-10-15 18:00:00', '2024-10-15 09:00:00', '2024-10-15 16:30:00', '2024-10-15 08:30:00', 'File created with all document details. Ready for regulatory processing.', 0),
(2, 1, 1, 'TRA Portal Documentation', 'Process documents through TRA portal for customs declaration.', 5, 3, 'completed', 'high', '2024-10-16 17:00:00', '2024-10-16 08:00:00', '2024-10-16 15:45:00', '2024-10-15 16:35:00', 'TRA declaration submitted and approved.', 0),
(3, 1, 1, 'Shipping Line Release Processing', 'Coordinate with shipping line for customs release order.', 6, 3, 'completed', 'high', '2024-10-17 16:00:00', '2024-10-17 08:30:00', '2024-10-17 11:20:00', '2024-10-16 15:50:00', 'Release order obtained from Maersk shipping line.', 0),

-- Phase 3 tasks for shipment 1 (current phase)
(4, 1, 3, 'Document Review and Verification', 'Review all documents against master file for accuracy.', 3, 3, 'completed', 'medium', '2024-10-18 12:00:00', '2024-10-18 08:00:00', '2024-10-18 11:30:00', '2024-10-17 11:25:00', 'All documents verified and approved for operations handover.', 0),
(5, 1, 3, 'Operations Payment Verification', 'Verify all port charges and fees are cleared.', 8, 3, 'in_progress', 'high', '2024-10-19 15:00:00', '2024-10-18 14:00:00', NULL, '2024-10-18 11:35:00', 'Checking final port charges and storage fees.', 0),

-- Phase 2 tasks for shipment 2
(6, 2, 2, 'TRA Portal Processing', 'Submit steel import documentation through TRA portal.', 5, 3, 'completed', 'urgent', '2024-10-18 16:00:00', '2024-10-18 09:00:00', '2024-10-18 14:30:00', '2024-10-16 12:00:00', 'TRA approval received for steel imports.', 0),
(7, 2, 2, 'Shipping Line Coordination', 'Obtain release order from shipping line.', 6, 3, 'completed', 'urgent', '2024-10-19 17:00:00', '2024-10-19 08:00:00', '2024-10-19 16:45:00', '2024-10-18 14:35:00', 'Release order secured from MSC shipping line.', 0),

-- Phase 4 tasks for shipment 2 (current phase)
(8, 2, 4, 'Truck Allocation for Steel Transport', 'Allocate suitable trucks for steel coil transport to Mwanza.', 7, 3, 'completed', 'urgent', '2024-10-21 10:00:00', '2024-10-20 08:00:00', '2024-10-20 16:20:00', '2024-10-19 16:50:00', 'Allocated 2 container trucks for steel transport.', 0),
(9, 2, 4, 'Transport Documentation Preparation', 'Prepare T1 forms and shipping orders for road transport.', 8, 7, 'completed', 'urgent', '2024-10-21 12:00:00', '2024-10-21 07:00:00', '2024-10-21 10:45:00', '2024-10-20 16:25:00', 'All transport documents prepared and verified.', 0),
(10, 2, 4, 'Loading Operations Oversight', 'Supervise loading of steel coils onto allocated trucks.', 8, 7, 'in_progress', 'urgent', '2024-10-22 14:00:00', '2024-10-21 11:00:00', NULL, '2024-10-21 10:50:00', 'Loading operations in progress at port.', 0),

-- Phase 2 tasks for shipment 3 (current phase)
(11, 3, 2, 'Coffee Equipment TRA Processing', 'Process coffee machinery through TRA customs.', 5, 3, 'in_progress', 'medium', '2024-10-24 16:00:00', '2024-10-22 09:00:00', NULL, '2024-10-17 15:00:00', 'TRA processing started for coffee equipment imports.', 0),
(12, 3, 2, 'Shipping Line Release for Coffee Equipment', 'Coordinate release with Hapag-Lloyd for coffee machinery.', 6, 3, 'pending', 'medium', '2024-10-25 17:00:00', NULL, NULL, '2024-10-17 15:05:00', 'Waiting for TRA completion before shipping line coordination.', 0);

-- Add truck allocations
INSERT INTO `omp_truck_allocations` (`id`, `shipment_id`, `truck_id`, `allocated_by`, `allocation_date`, `loading_date`, `departure_date`, `estimated_delivery`, `actual_delivery`, `status`, `notes`, `deleted`) VALUES
(1, 2, 1, 7, '2024-10-20 16:20:00', '2024-10-21 11:00:00', '2024-10-21 15:30:00', '2024-10-23 18:00:00', NULL, 'in_transit', 'Steel coils loaded on T 123 ABC, en route to Mwanza', 0),
(2, 2, 4, 7, '2024-10-20 16:25:00', '2024-10-21 13:30:00', '2024-10-21 17:00:00', '2024-10-24 12:00:00', NULL, 'in_transit', 'Additional steel materials on T 321 JKL, following main convoy', 0),
(3, 5, 5, 7, '2024-10-08 10:00:00', '2024-10-09 08:00:00', '2024-10-09 14:00:00', '2024-10-11 16:00:00', '2024-10-11 14:30:00', 'delivered', 'Agricultural machinery successfully delivered to Dodoma', 0),
(4, 6, 6, 7, '2024-10-22 11:00:00', '2024-10-23 09:30:00', '2024-10-23 15:00:00', '2024-10-25 20:00:00', NULL, 'in_transit', 'Mining equipment loaded on T 987 PQR, heading to Arusha', 0);

-- Add tracking reports
INSERT INTO `omp_tracking_reports` (`id`, `shipment_id`, `truck_allocation_id`, `bl_number`, `current_location`, `status_update`, `border_entry_date`, `border_exit_date`, `border_location`, `updated_by`, `updated_at`, `client_notified`, `deleted`) VALUES
-- Tracking for shipment 2 (steel to Mwanza)
(1, 2, 1, 'MSC123456789', 'Morogoro Rest Stop', 'Truck convoy departed Dar es Salaam port successfully. Currently at Morogoro for mandatory rest. All steel coils secured properly.', NULL, NULL, NULL, 10, '2024-10-22 08:30:00', 1, 0),
(2, 2, 1, 'MSC123456789', 'Dodoma Checkpoint', 'Passed through Dodoma checkpoint. All documentation verified. Expected to reach Mwanza by tomorrow evening.', NULL, NULL, NULL, 10, '2024-10-22 18:45:00', 1, 0),
(3, 2, 2, 'MSC123456789', 'Singida Border Control', 'Second truck cleared Singida control point. Both trucks maintaining schedule. No delays reported.', NULL, NULL, NULL, 10, '2024-10-23 06:15:00', 1, 0),

-- Tracking for delivered shipment 5
(4, 5, 3, 'SAF987654321', 'Dodoma Agricultural Center', 'Agricultural machinery successfully delivered and handed over to client. POD obtained and filed.', NULL, NULL, NULL, 10, '2024-10-11 14:30:00', 1, 0),

-- Tracking for shipment 6 (mining equipment)
(5, 6, 4, 'CPT456789123', 'Moshi Industrial Area', 'Mining equipment convoy passed through Moshi. All specialized equipment secured. Expected arrival at Mererani mines tomorrow.', NULL, NULL, NULL, 10, '2024-10-24 16:20:00', 1, 0),
(6, 6, 4, 'CPT456789123', 'Arusha City Center', 'Reached Arusha city center. Final leg to Mererani tanzanite mines. Mining company representatives notified for reception.', NULL, NULL, NULL, 10, '2024-10-25 09:30:00', 1, 0);