<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <ul id="workflow-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#overview"><?php echo app_lang('overview'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/shipments_list/"); ?>" data-bs-target="#shipments_list"><?php echo app_lang('shipments'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/tasks_list/"); ?>" data-bs-target="#tasks_list"><?php echo app_lang('tasks'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/documents_list/"); ?>" data-bs-target="#documents_list"><?php echo app_lang('documents'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/trucks_list/"); ?>" data-bs-target="#trucks_list"><?php echo app_lang('trucks'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/tracking_list/"); ?>" data-bs-target="#tracking_list"><?php echo app_lang('tracking'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/escalations_list/"); ?>" data-bs-target="#escalations_list"><i data-feather="alert-triangle" class="icon-14"></i> Escalations</a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/handovers_list/"); ?>" data-bs-target="#handovers_list"><i data-feather="refresh-cw" class="icon-14"></i> Handovers</a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/approvals_list/"); ?>" data-bs-target="#approvals_list"><i data-feather="check-circle" class="icon-14"></i> Approvals</a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("workflow/costs_list/"); ?>" data-bs-target="#costs_list"><i data-feather="dollar-sign" class="icon-14"></i> Costs</a></li>
            
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php if ($permissions['can_manage_workflow']) { ?>
                        <button type="button" class="btn btn-default" onclick="showWorkflowAnalytics()">
                            <i data-feather="bar-chart-2" class="icon-16"></i> <?php echo app_lang('analytics'); ?>
                        </button>
                        <?php echo modal_anchor(get_uri("workflow/import_shipments_modal"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_shipments'), array("class" => "btn btn-default", "title" => app_lang('import_shipments'))); ?>
                        <?php echo modal_anchor(get_uri("workflow/shipment_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_shipment'), array("class" => "btn btn-default", "title" => app_lang('add_shipment'))); ?>
                    <?php } ?>
                </div>
            </div>
        </ul>
        
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="overview">
                <?php echo view("workflow/overview/index"); ?>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="shipments_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="tasks_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="documents_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="trucks_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="tracking_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="escalations_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="handovers_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="approvals_list"></div>
            <div role="tabpanel" class="tab-pane fade" id="costs_list"></div>
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div class="modal fade" id="workflow-analytics-modal" tabindex="-1" role="dialog" aria-labelledby="workflow-analytics-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workflow-analytics-title"><?php echo app_lang('workflow_analytics'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="workflow-analytics-content">
                <!-- Analytics content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(function() {
            var tab = "<?php echo $tab; ?>";
            if (tab === "shipments_list") {
                $("[data-bs-target='#shipments_list']").trigger("click");
            } else if (tab === "tasks_list") {
                $("[data-bs-target='#tasks_list']").trigger("click");
            } else if (tab === "documents_list") {
                $("[data-bs-target='#documents_list']").trigger("click");
            } else if (tab === "tracking_list") {
                $("[data-bs-target='#tracking_list']").trigger("click");
            }
        }, 210);
    });

    function showWorkflowAnalytics() {
        $("#workflow-analytics-modal").modal('show');
        
        // Load analytics content
        $.ajax({
            url: "<?php echo get_uri('workflow/analytics'); ?>",
            type: 'POST',
            success: function(response) {
                $("#workflow-analytics-content").html(response);
            }
        });
    }
</script>
           
        </div>
    </div>
</div>

<style>
.workflow-phase {
    padding: 15px;
    margin-bottom: 10px;
}

.phase-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}

.urgent-task-item {
    margin-bottom: 10px;
}

.task-meta strong {
    display: block;
    margin-bottom: 5px;
}

.task-assignee {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.vertical-tabs {
    border-right: 1px solid #dee2e6;
    padding-right: 0;
}

.vertical-tabs a {
    display: block;
    padding: 12px 15px;
    color: #6c757d;
    text-decoration: none;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s;
}

.vertical-tabs a:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.vertical-tabs a.active {
    background-color: #007bff;
    color: white;
    border-left: 4px solid #0056b3;
}

.vertical-tabs a i {
    margin-right: 8px;
}
</style>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize any dashboard-specific functionality
});
</script>