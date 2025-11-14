<?php echo form_open(get_uri("workflow/update_shipment_status"), array("id" => "status-update-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="status" class="col-md-3"><?php echo app_lang('status'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("status", $status_options, $current_status, "class='select2' id='status'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="phase" class="col-md-3"><?php echo app_lang('current_phase'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("phase", $phase_options, $current_phase, "class='select2' id='phase'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="notes" class="col-md-3"><?php echo app_lang('notes'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "notes",
                        "name" => "notes",
                        "class" => "form-control",
                        "placeholder" => app_lang('add_notes_optional'),
                        "rows" => 3
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('update_status'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#status-update-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message);
                    location.reload();
                } else {
                    appAlert.error(result.message);
                }
            }
        });
        $("#status-update-form .select2").select2();
    });
</script>