<?php echo form_open(get_uri("workflow/add_shipment_cost"), array("id" => "cost-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="cost_type" class="col-md-3"><?php echo app_lang('cost_type'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("cost_type", $cost_types, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="cost_description" class="col-md-3"><?php echo app_lang('description'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "cost_description",
                        "name" => "cost_description",
                        "class" => "form-control",
                        "placeholder" => app_lang('cost_description')
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="amount" class="col-md-3"><?php echo app_lang('amount'); ?></label>
                <div class="col-md-9">
                    <div class="input-group">
                        <?php
                        echo form_input(array(
                            "id" => "amount",
                            "name" => "amount",
                            "class" => "form-control",
                            "type" => "number",
                            "step" => "0.01",
                            "placeholder" => "0.00",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        <?php
                        echo form_dropdown("currency", $currencies, "USD", "class='form-select' style='max-width: 100px;'");
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i data-feather="info" class="icon-16"></i>
            <?php echo app_lang('cost_will_require_payment_verification'); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="plus" class="icon-16"></span> <?php echo app_lang('add_cost'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#cost-form").appForm({
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
