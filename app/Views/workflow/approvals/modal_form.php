<?php echo form_open(get_uri("workflow/request_approval"), array("id" => "approval-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>" />
        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
        <input type="hidden" name="document_id" value="<?php echo $document_id; ?>" />
        <input type="hidden" name="reference_id" value="<?php echo $shipment_id ?: ($task_id ?: $document_id); ?>" />

        <div class="form-group">
            <div class="row">
                <label for="approval_type" class="col-md-3"><?php echo app_lang('approval_type'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("approval_type", $approval_types, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="request_notes" class="col-md-3"><?php echo app_lang('request_notes'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "request_notes",
                        "name" => "request_notes",
                        "class" => "form-control",
                        "placeholder" => app_lang('describe_approval_request'),
                        "rows" => 4,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i data-feather="info" class="icon-16"></i>
            <?php echo app_lang('approval_chain_will_be_auto_generated'); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('request_approval'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#approval-form").appForm({
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
