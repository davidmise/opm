<?php echo form_open(get_uri("workflow/initiate_handover"), array("id" => "handover-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>" />

        <div class="alert alert-info">
            <i data-feather="info" class="icon-16"></i>
            <?php echo app_lang('handover_will_lock_shipment'); ?>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="from_department" class="col-md-3"><?php echo app_lang('from_department'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("from_department", $departments_dropdown, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="to_department" class="col-md-3"><?php echo app_lang('to_department'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("to_department", $departments_dropdown, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="from_phase" class="col-md-3"><?php echo app_lang('from_phase'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("from_phase", $phases, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="to_phase" class="col-md-3"><?php echo app_lang('to_phase'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("to_phase", $phases, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="handover_notes" class="col-md-3"><?php echo app_lang('handover_notes'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "handover_notes",
                        "name" => "handover_notes",
                        "class" => "form-control",
                        "placeholder" => app_lang('handover_instructions_and_notes'),
                        "rows" => 4
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="share" class="icon-16"></span> <?php echo app_lang('initiate_handover'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#handover-form").appForm({
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
