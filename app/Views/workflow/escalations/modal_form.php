<?php echo form_open(get_uri("workflow/escalate_task"), array("id" => "escalation-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>" />
        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
        <input type="hidden" name="escalation_type" value="<?php echo $task_id ? 'task' : 'shipment'; ?>" />
        <input type="hidden" name="reference_id" value="<?php echo $task_id ?: $shipment_id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="escalation_reason" class="col-md-3"><?php echo app_lang('escalation_reason'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "escalation_reason",
                        "name" => "escalation_reason",
                        "class" => "form-control",
                        "placeholder" => app_lang('describe_reason_for_escalation'),
                        "rows" => 4,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="escalation_level" class="col-md-3"><?php echo app_lang('escalation_level'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("escalation_level", $escalation_levels, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="escalated_to" class="col-md-3"><?php echo app_lang('escalate_to'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("escalated_to", $users_dropdown, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="priority" class="col-md-3"><?php echo app_lang('priority'); ?></label>
                <div class="col-md-9">
                    <?php
                    $priorities = array(
                        "low" => app_lang('low'),
                        "medium" => app_lang('medium'),
                        "high" => app_lang('high'),
                        "urgent" => app_lang('urgent')
                    );
                    echo form_dropdown("priority", $priorities, "medium", "class='select2 form-control'");
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="alert-triangle" class="icon-16"></span> <?php echo app_lang('escalate'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#escalation-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                    location.reload();
                } else {
                    appAlert.error(result.message);
                }
            }
        });
        
        $('.select2').select2();
    });
</script>
