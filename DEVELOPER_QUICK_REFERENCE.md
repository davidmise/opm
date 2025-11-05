# QUICK IMPLEMENTATION GUIDE
## Workflow Features - Developer Reference

---

## DATABASE TABLES REFERENCE

### Core Workflow Tables
```
opm_workflow_phases (5 rows)           - Workflow phases definition
opm_workflow_shipments                 - Shipment tracking
opm_workflow_tasks                     - Task management
opm_workflow_documents                 - Document storage
```

### New Feature Tables
```
opm_workflow_escalations               - Escalation system
opm_workflow_handovers                 - Department handovers
opm_workflow_approvals                 - Approval gates
opm_workflow_task_assignees            - Parallel assignment
opm_shipment_costs                     - Cost tracking
opm_trucks                             - Truck management
opm_truck_allocations                  - Truck assignments
opm_tracking_reports                   - GPS tracking
```

---

## MODEL USAGE EXAMPLES

### 1. ESCALATION WORKFLOW

#### Create Escalation
```php
$Escalations_model = model('App\Models\Workflow_escalations_model');

$data = array(
    'shipment_id' => 123,
    'task_id' => 456,                    // optional
    'escalated_by' => get_user_id(),
    'escalated_to' => 5,                 // supervisor user_id
    'escalation_level' => 1,             // 1=Supervisor, 2=GM, 3=Management
    'escalation_reason' => 'urgent',     // urgent|delayed|quality|other
    'description' => 'Customs clearance delayed by 3 days',
    'priority' => 'high'                 // high|medium|low
);

$escalation_id = $Escalations_model->create_escalation($data);
```

#### Update Escalation Status
```php
// Acknowledge
$Escalations_model->update_status($escalation_id, 'acknowledged', $user_id, 'Working on it');

// Resolve
$Escalations_model->update_status($escalation_id, 'resolved', $user_id, 'Issue resolved');
```

#### Re-escalate to Higher Level
```php
$Escalations_model->re_escalate($escalation_id, $gm_user_id, 'Still not resolved after 24h');
```

#### Get My Escalations
```php
$my_escalations = $Escalations_model->get_my_pending_escalations(get_user_id());
```

---

### 2. HANDOVER WORKFLOW

#### Initiate Handover
```php
$Handovers_model = model('App\Models\Workflow_handovers_model');

$data = array(
    'shipment_id' => 123,
    'from_phase_id' => 1,
    'to_phase_id' => 2,
    'from_department_id' => 1,
    'to_department_id' => 2,
    'initiated_by' => get_user_id(),
    'notes' => 'All documents verified and ready'
);

$handover_id = $Handovers_model->initiate_handover($data);
// Auto-generates checklist based on phase transition
// Auto-locks shipment phase
```

#### Approve Handover
```php
$Handovers_model->approve_handover($handover_id, $approved_by_user_id);
// Auto-transitions shipment to new phase
// Auto-unlocks shipment
```

#### Reject Handover
```php
$Handovers_model->reject_handover($handover_id, $rejected_by_user_id, 'Missing customs release order');
// Auto-unlocks shipment
```

#### Update Checklist
```php
$checklist = array(
    array('item' => 'All documents received', 'completed' => true),
    array('item' => 'Master file created', 'completed' => true),
    array('item' => 'Tasks delegated', 'completed' => false)
);

$Handovers_model->update_checklist($handover_id, json_encode($checklist));
```

---

### 3. APPROVAL WORKFLOW

#### Request Approval
```php
$Approvals_model = model('App\Models\Workflow_approvals_model');

// Phase Transition Approval
$data = array(
    'shipment_id' => 123,
    'approval_type' => 'phase_transition',
    'requested_by' => get_user_id(),
    'description' => 'Request approval to move to Transport phase',
    'metadata_json' => json_encode(array('target_phase_id' => 4))
);

$approval_id = $Approvals_model->request_approval($data);
```

#### Approve Step
```php
$Approvals_model->approve($approval_id, $current_approver_id, 'Approved - all checks passed');
// If multi-step: moves to next approver
// If final step: triggers post-approval action
```

#### Reject Approval
```php
$Approvals_model->reject($approval_id, $current_approver_id, 'Payment verification incomplete');
```

#### Get My Pending Approvals
```php
$my_approvals = $Approvals_model->get_my_pending_approvals(get_user_id());
```

---

### 4. COST TRACKING

#### Add Cost
```php
$Costs_model = model('App\Models\Shipment_costs_model');

$data = array(
    'shipment_id' => 123,
    'cost_type' => 'customs',        // customs|port|transport|storage|handling|documentation
    'description' => 'Customs duty payment',
    'amount' => 5000.00,
    'currency' => 'USD',
    'added_by' => get_user_id()
);

$cost_id = $Costs_model->add_cost($data);
```

#### Mark as Paid
```php
$Costs_model->update_payment_status($cost_id, 'paid', $user_id);
```

#### Verify Payment (Critical for Task 10)
```php
$Costs_model->verify_payment($cost_id, $verified_by_user_id, 'Payment confirmed via bank statement');
// Auto-checks if ALL costs verified
// If yes: sets shipment.costs_cleared = 1
```

#### Check Transport Clearance
```php
if ($Costs_model->is_cleared_for_transport($shipment_id)) {
    // Can proceed to Phase 4 (Transport Loading)
} else {
    // Block phase transition
}
```

---

### 5. PARALLEL TASK ASSIGNMENT

#### Create Task with Multiple Assignees
```php
$Tasks_model = model('App\Models\Workflow_tasks_model');

$task_data = array(
    'shipment_id' => 123,
    'phase_id' => 1,
    'task_name' => 'Prepare declaration document',
    'task_order' => 4,
    'department_id' => 2,
    'assigned_to' => 10,                 // Pendo (primary)
    'created_by' => get_user_id()
);

$additional_assignees = array(11);       // Edson (parallel)

$task_id = $Tasks_model->create_task($task_data, $additional_assignees);
// Auto-sets assignment_type = 'parallel'
```

#### Update Individual Assignee Status
```php
$Tasks_model->update_assignee_status($task_id, $pendo_user_id, 'completed');
$Tasks_model->update_assignee_status($task_id, $edson_user_id, 'in_progress');
// Auto-checks if ALL assignees completed
// If yes: marks task as 'completed'
```

#### Get Task Assignees
```php
$assignees = $Tasks_model->get_task_assignees($task_id);
foreach ($assignees->getResult() as $assignee) {
    echo "{$assignee->user_name}: {$assignee->assignee_status}\n";
}
```

---

### 6. SHIPMENT MANAGEMENT

#### Create Shipment
```php
$Shipments_model = model('App\Models\Workflow_shipments_model');

$data = array(
    'client_id' => 45,
    'cargo_type' => 'Electronics',
    'cargo_weight' => 5000.00,
    'cargo_value' => 150000.00,
    'origin_port' => 'Dar es Salaam',
    'destination_port' => 'Mombasa',
    'created_by' => get_user_id()
);

$shipment_id = $Shipments_model->create_shipment($data);
// Auto-generates shipment_number: SHP202412200001
// Auto-sets current_phase_id = 1
// Auto-creates Phase 1 tasks (Tasks 1-4)
```

#### Transition to Next Phase
```php
// Check if current phase tasks complete
if ($Tasks_model->is_phase_complete($shipment_id, 1)) {
    $Shipments_model->transition_to_phase($shipment_id, 2);
    // Auto-creates Phase 2 tasks (Tasks 5-8)
}
```

#### Complete Shipment
```php
$Shipments_model->complete_shipment($shipment_id, $completed_by_user_id);
```

---

### 7. DOCUMENT MANAGEMENT

#### Upload Document
```php
$Documents_model = model('App\Models\Workflow_documents_model');

$data = array(
    'shipment_id' => 123,
    'document_type' => 'customs_declaration',
    'uploaded_by' => get_user_id(),
    'description' => 'Customs declaration form'
);

$file_info = array(
    'file_name' => 'customs_dec_123.pdf',
    'file_path' => 'files/shipment_files/123/customs_dec_123.pdf',
    'file_size' => 245678
);

$document_id = $Documents_model->upload_document($data, $file_info);
```

#### Upload POD (Auto-closure - Task 22)
```php
$data = array(
    'shipment_id' => 123,
    'document_type' => 'POD',
    'uploaded_by' => get_user_id()
);

$document_id = $Documents_model->upload_document($data, $file_info);
// Auto-creates shipment_closure approval request
// Approval chain: Operations Manager → General Manager
// When approved: Auto-closes shipment
```

#### Generate Loading Order (Task 18)
```php
$template_data = array(
    'loading_date' => '2024-12-25',
    'trucks' => array(
        array('truck_number' => 'T1234', 'driver' => 'John Doe'),
        array('truck_number' => 'T5678', 'driver' => 'Jane Smith')
    ),
    'items' => array(
        array('description' => 'Electronics', 'quantity' => 50, 'weight' => 2500)
    ),
    'total_weight' => 5000,
    'destination' => 'Client Warehouse, Mombasa'
);

$document_id = $Documents_model->generate_from_template('loading_order', $shipment_id, $template_data);
```

#### Generate Tracking Report (Task 20)
```php
$template_data = array(
    'current_location' => 'Nairobi Highway, KM 45',
    'status' => 'in_transit',
    'eta' => '2024-12-26 15:00:00',
    'tracking_history' => array(
        array('timestamp' => '2024-12-25 10:00', 'location' => 'Dar es Salaam Port'),
        array('timestamp' => '2024-12-25 14:00', 'location' => 'Border Checkpoint')
    )
);

$document_id = $Documents_model->generate_from_template('tracking_report', $shipment_id, $template_data);
```

---

## WORKFLOW STATUS ENUMS

### Escalation Status
- `pending` - Awaiting response
- `acknowledged` - Escalation acknowledged
- `resolved` - Issue resolved

### Handover Status
- `pending` - Awaiting approval
- `accepted` - Handover approved
- `rejected` - Handover rejected

### Approval Status
- `pending` - Awaiting approval
- `approved` - Approved
- `rejected` - Rejected

### Payment Status
- `unpaid` - Not yet paid
- `paid` - Payment made
- `verified` - Payment verified (clearance)

### Task Status
- `pending` - Not started
- `in_progress` - Work in progress
- `completed` - Task complete
- `cancelled` - Task cancelled

### Assignee Status
- `pending` - Not started
- `in_progress` - Working
- `completed` - Done

### Shipment Status
- `active` - In progress
- `completed` - Closed
- `cancelled` - Cancelled

### Document Status
- `pending` - Awaiting verification
- `approved` - Verified
- `rejected` - Rejected
- `generated` - Auto-generated
- `released` - Released to client

---

## APPROVAL TYPES

| Type | Description | Default Chain |
|------|-------------|---------------|
| `phase_transition` | Phase change approval | Supervisor → Manager |
| `document_approval` | Document verification | Reviewer |
| `cost_approval` | Financial approval | Finance Manager → GM |
| `task_completion` | Task sign-off | Supervisor |
| `handover_approval` | Department handover | Department Head |
| `shipment_closure` | Shipment completion | Operations Manager → GM |
| `exception_approval` | Exception handling | Supervisor → GM |
| `document_release` | Document release | Document Controller |

---

## COST TYPES

- `customs` - Customs duties and fees
- `port` - Port charges
- `transport` - Transportation costs
- `storage` - Storage/warehousing fees
- `handling` - Handling charges
- `documentation` - Documentation fees
- `other` - Other costs

---

## ESCALATION REASONS

- `urgent` - Urgent matter
- `delayed` - Delayed process
- `quality` - Quality issue
- `missing_info` - Missing information
- `blocked` - Process blocked
- `other` - Other reason

---

## PRIORITY LEVELS

- `low` - Low priority
- `medium` - Medium priority
- `high` - High priority
- `urgent` - Urgent/critical

---

## DOCUMENT TYPES

| Type | Required Phase | Auto-generated | Purpose |
|------|----------------|----------------|---------|
| `client_documents` | 1 | No | Initial docs from client |
| `bill_of_lading` | 1 | No | Shipping bill |
| `customs_declaration` | 2 | No | Customs filing |
| `customs_release_order` | 2 | No | Customs clearance |
| `loading_order` | 4 | Yes (Task 18) | Loading instructions |
| `T1_form` | 4 | No | Transit form |
| `tracking_report` | 5 | Yes (Task 20) | Tracking updates |
| `POD` | 5 | No | Proof of delivery (triggers closure) |

---

## PHASE TRANSITIONS

```
Phase 1: Clearing Intake (Tasks 1-4)
    ↓ Handover with checklist (3 items)
Phase 2: Regulatory Processing (Tasks 5-8)
    ↓ Handover with checklist (3 items)
Phase 3: Internal Review (Tasks 9-11)
    ↓ Handover with checklist (5 items) + Payment verification gate
Phase 4: Transport Loading (Tasks 12-18)
    ↓ Handover with checklist (4 items)
Phase 5: Tracking (Tasks 19-22)
    ↓ POD upload → Approval → Auto-close
COMPLETED
```

---

## STATISTICS METHODS

All models include statistics methods:

```php
// Escalations
$stats = $Escalations_model->get_statistics($user_id);
// Returns: total, pending, acknowledged, resolved, urgent_count

// Handovers
$stats = $Handovers_model->get_statistics($department_id);
// Returns: total, pending, accepted, rejected

// Approvals
$stats = $Approvals_model->get_statistics($user_id);
// Returns: total, pending, approved, rejected, pending_phase, pending_cost

// Costs
$stats = $Costs_model->get_statistics($shipment_id);
// Returns: total_items, total_amount, unpaid_count, paid_count, verified_count, by_type

// Tasks
$stats = $Tasks_model->get_shipment_statistics($shipment_id);
// Returns: total, pending, in_progress, completed, cancelled, completion_percentage
```

---

## ERROR HANDLING

All methods return:
- **Success:** ID (int) or true (bool)
- **Failure:** false (bool)

Always check return values:

```php
$result = $Escalations_model->create_escalation($data);
if ($result) {
    // Success
    echo json_encode(array('success' => true, 'id' => $result));
} else {
    // Failure
    echo json_encode(array('success' => false, 'message' => 'Failed to create escalation'));
}
```

---

## NOTIFICATION HOOKS

All models call `log_notification()` for key events:
- `escalation_created`
- `escalation_acknowledged`
- `escalation_resolved`
- `handover_initiated`
- `handover_approved`
- `handover_rejected`
- `approval_requested`
- `approval_completed`
- `approval_rejected`
- `cost_added`
- `cost_payment_updated`
- `shipment_costs_cleared`
- `task_created`
- `task_status_changed`
- `document_uploaded`
- `pod_uploaded`
- `shipment_auto_closed`

**TODO:** Implement actual email/push notifications

---

## PERMISSIONS (To Be Implemented)

### Department Access Control

| Department | Phases | Description |
|------------|--------|-------------|
| Management | 1 | Intake only |
| Clearing & Documentation | 1, 2, 3 | Intake + Regulatory + Review |
| Operations | 3, 4 | Review + Transport |
| Tracking | 5 | Tracking only |

### Action Permissions

| Action | Required Role |
|--------|---------------|
| Create escalation | Any user |
| Resolve escalation | Supervisor+ |
| Initiate handover | Department member |
| Approve handover | Department head |
| Request approval | Task assignee |
| Approve request | Designated approver |
| Verify payment | Finance team |
| Generate documents | Assigned user |
| Upload POD | Tracking team |

---

## TESTING CHECKLIST

### Escalation ✓
- [ ] Create escalation
- [ ] Acknowledge escalation
- [ ] Resolve escalation
- [ ] Re-escalate to higher level
- [ ] View my escalations
- [ ] View escalation statistics

### Handover ✓
- [ ] Initiate handover (auto-generates checklist)
- [ ] Update checklist items
- [ ] Approve handover (phase transitions)
- [ ] Reject handover (unlocks phase)
- [ ] View handover history

### Approval ✓
- [ ] Request approval (auto-creates chain)
- [ ] Approve step 1 (moves to step 2)
- [ ] Approve step 2 (final - triggers action)
- [ ] Reject approval
- [ ] View my pending approvals

### Cost Tracking ✓
- [ ] Add cost
- [ ] Mark as paid
- [ ] Verify payment
- [ ] Check transport clearance gate
- [ ] View cost summary

### Parallel Assignment ✓
- [ ] Create task with multiple assignees
- [ ] Update individual assignee status
- [ ] Auto-complete when all done
- [ ] View assignee progress

### Documents ✓
- [ ] Upload document
- [ ] Generate Loading Order
- [ ] Generate Tracking Report
- [ ] Upload POD (triggers approval)
- [ ] Approve POD (auto-closes shipment)

---

## QUICK REFERENCE: All Models

```php
model('App\Models\Workflow_escalations_model')
model('App\Models\Workflow_handovers_model')
model('App\Models\Workflow_approvals_model')
model('App\Models\Shipment_costs_model')
model('App\Models\Workflow_tasks_model')
model('App\Models\Workflow_task_assignees_model')
model('App\Models\Workflow_shipments_model')
model('App\Models\Workflow_documents_model')
```

---

**For full implementation details, see:**
- `IMPLEMENTATION_PROGRESS_REPORT.md`
- `SYSTEM_ANALYSIS_FOR_CLEARING_TRANSPORT.md`

**Model files location:** `app/Models/`

**Next steps:** Controller integration → UI views → Testing

