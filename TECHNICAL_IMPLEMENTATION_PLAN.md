# Technical Implementation Plan - Workflow System

## 🎯 Immediate Action Items (Critical Path)

### **PRIORITY 1: Fix Quick Actions (Issue 5) - URGENT**

#### Problem Analysis
Quick actions in overview are leading to blank pages. This suggests:
1. Missing route definitions
2. Controller methods not implemented
3. JavaScript errors
4. Permission validation issues

#### Immediate Fixes Required

**1. Check Routes Configuration**
```php
// File: app/Config/Routes.php
// Add these routes if missing:
$routes->post('workflow/bulk_assign_department', 'Workflow::bulk_assign_department');
$routes->post('workflow/update_status_bulk', 'Workflow::update_status_bulk');
$routes->post('workflow/create_task_bulk', 'Workflow::create_task_bulk');
```

**2. Implement Missing Controller Methods**
```php
// File: app/Controllers/Workflow.php
// These methods need to be implemented:

function bulk_assign_department() {
    // Implementation needed
}

function update_status_bulk() {
    // Implementation needed  
}

function create_task_bulk() {
    // Implementation needed
}
```

**3. Fix JavaScript Functions**
```javascript
// File: app/Views/workflow/overview/index.php
// Add these JavaScript functions:

function bulkAssignDepartment() {
    // Implementation needed
}

function updateStatusBulk() {
    // Implementation needed
}

function createTaskBulk() {
    // Implementation needed
}
```

---

### **PRIORITY 2: Complete CRUD Operations (Issue 1) - HIGH**

#### Missing Controller Methods
```php
// File: app/Controllers/Workflow.php
// Add these methods:

function edit_shipment($id = 0) {
    $this->access_only_allowed_workflow_members();
    
    if (!$this->can_edit_shipments()) {
        show_404();
    }
    
    $shipment_info = $this->_get_shipment_info($id);
    if (!$shipment_info) {
        show_404();
    }
    
    $view_data['model_info'] = $shipment_info;
    $view_data['clients_dropdown'] = $this->_get_clients_dropdown();
    
    return $this->template->view("workflow/shipments/modal_form", $view_data);
}

function delete_shipment() {
    $this->access_only_allowed_workflow_members();
    
    if (!$this->can_delete_shipments()) {
        echo json_encode(array("success" => false, 'message' => app_lang('access_denied')));
        return;
    }
    
    $id = $this->request->getPost('id');
    
    if ($this->_delete_shipment($id)) {
        echo json_encode(array("success" => true, "message" => app_lang('record_deleted')));
    } else {
        echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
    }
}
```

---

### **PRIORITY 3: Action Dropdown Implementation (Issue 2) - MEDIUM**

#### Replace Action Buttons with Dropdown
```php
// File: app/Views/workflow/shipments/list.php
// Replace the actions column in _make_shipment_row():

private function _make_shipment_row($data) {
    // ... existing code ...
    
    $actions = '<div class="dropdown table-actions">
        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i data-feather="more-horizontal" class="icon-16"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">';
    
    if ($this->can_edit_shipments()) {
        $actions .= '<li><a class="dropdown-item" href="#" onclick="editShipment(' . $data->id . ')">
            <i data-feather="edit" class="icon-16"></i> ' . app_lang('edit') . '</a></li>';
    }
    
    $actions .= '<li><a class="dropdown-item" href="#" onclick="viewShipmentDetails(' . $data->id . ')">
        <i data-feather="eye" class="icon-16"></i> ' . app_lang('view_details') . '</a></li>';
    
    if ($this->can_delete_shipments()) {
        $actions .= '<li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="deleteShipment(' . $data->id . ')">
            <i data-feather="trash-2" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';
    }
    
    $actions .= '</ul></div>';
    
    return array(
        // ... existing columns ...
        $actions
    );
}
```

---

### **PRIORITY 4: Shipment Details Page (Issue 3) - HIGH**

#### Create New Controller Method
```php
// File: app/Controllers/Workflow.php

function shipment_details($id = 0) {
    $this->access_only_allowed_workflow_members();
    
    if (!$id) {
        show_404();
    }
    
    $shipment_info = $this->_get_shipment_info($id);
    if (!$shipment_info) {
        show_404();
    }
    
    $view_data['shipment_info'] = $shipment_info;
    $view_data['permissions'] = $this->_get_workflow_permissions();
    $view_data['documents'] = $this->_get_shipment_documents($id);
    $view_data['tasks'] = $this->_get_shipment_tasks($id);
    $view_data['timeline'] = $this->_get_shipment_timeline($id);
    
    return $this->template->render("workflow/shipments/details", $view_data);
}
```

#### Create Details View Template
```php
// File: app/Views/workflow/shipments/details.php
<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <div class="page-title clearfix">
                <h4><?php echo app_lang('shipment_details'); ?> - <?php echo $shipment_info->shipment_number; ?></h4>
            </div>
        </div>
    </div>
    
    <!-- Shipment Header -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><?php echo app_lang('shipment_information'); ?></h5>
                </div>
                <div class="card-body">
                    <!-- Shipment details form/display -->
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><?php echo app_lang('status_and_actions'); ?></h5>
                </div>
                <div class="card-body">
                    <!-- Status update and actions -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabbed Content -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#documents">
                                <?php echo app_lang('documents'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tasks">
                                <?php echo app_lang('tasks'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#timeline">
                                <?php echo app_lang('timeline'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

### **PRIORITY 5: Complete Translations (Issues 6, 7, 8) - MEDIUM**

#### Add Missing English Translations
```php
// File: app/Language/english/custom_lang.php
// Add these translations:

// Navigation tabs
$lang["tasks"] = "Tasks";
$lang["tracking"] = "Tracking";
$lang["trucks"] = "Trucks";
$lang["documents"] = "Documents";

// Actions
$lang["view_details"] = "View Details";
$lang["edit_shipment"] = "Edit Shipment";
$lang["delete_shipment"] = "Delete Shipment";
$lang["assign_task"] = "Assign Task";
$lang["update_status"] = "Update Status";
$lang["quick_actions"] = "Quick Actions";
$lang["more_actions"] = "More Actions";

// Status and workflow
$lang["shipment_information"] = "Shipment Information";
$lang["status_and_actions"] = "Status & Actions";
$lang["assign_to_department"] = "Assign to Department";
$lang["create_tasks"] = "Create Tasks";
$lang["timeline"] = "Timeline";
```

#### Add Missing Swahili Translations
```php
// File: app/Language/swahili/custom_lang.php
// Add these translations:

// Navigation tabs
$lang["tasks"] = "Kazi";
$lang["tracking"] = "Ufuatiliaji";
$lang["trucks"] = "Malori";
$lang["documents"] = "Hati";

// Actions
$lang["view_details"] = "Tazama Maelezo";
$lang["edit_shipment"] = "Hariri Mizigo";
$lang["delete_shipment"] = "Futa Mizigo";
$lang["assign_task"] = "Kabidhi Kazi";
$lang["update_status"] = "Sasisha Hali";
$lang["quick_actions"] = "Vitendo vya Haraka";
$lang["more_actions"] = "Vitendo Zaidi";

// Status and workflow
$lang["shipment_information"] = "Taarifa za Mizigo";
$lang["status_and_actions"] = "Hali na Vitendo";
$lang["assign_to_department"] = "Kabidhi kwa Idara";
$lang["create_tasks"] = "Unda Kazi";
$lang["timeline"] = "Mstari wa Muda";
```

---

## 🚀 Quick Implementation Script

### Step 1: Immediate Bug Fixes
```bash
# 1. Test current quick actions
# 2. Check JavaScript console for errors
# 3. Verify route definitions
# 4. Implement missing controller methods
```

### Step 2: Database Validation
```sql
-- Check if workflow shipments table exists
DESCRIBE opm_workflow_shipments;

-- Verify sample data exists
SELECT COUNT(*) FROM opm_workflow_shipments WHERE deleted = 0;

-- Add sample data if needed
INSERT INTO opm_workflow_shipments (...) VALUES (...);
```

### Step 3: File Creation Order
1. Create missing controller methods
2. Create shipment details view
3. Update language files
4. Implement JavaScript functions
5. Test all functionality

---

## 🔍 Testing Protocol

### Manual Testing Checklist
- [ ] Quick actions work without errors
- [ ] Shipment CRUD operations complete successfully
- [ ] Action dropdown displays correctly
- [ ] Shipment details page loads properly
- [ ] Language switching works
- [ ] Mobile responsiveness verified

### Browser Console Check
- [ ] No JavaScript errors
- [ ] No 404 network errors
- [ ] AJAX calls respond correctly
- [ ] Form submissions work

### Database Validation
- [ ] Data saves correctly
- [ ] Soft deletes work
- [ ] Foreign keys maintained
- [ ] Performance acceptable

---

**Ready for immediate implementation with this technical roadmap!** ⚡