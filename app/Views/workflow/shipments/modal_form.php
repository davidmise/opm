<?php echo form_open(get_uri("workflow/save_shipment"), array("id" => "shipment-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        
        <div class="form-group">
            <div class="row">
                <label for="client_id" class="col-md-3"><?php echo app_lang('client'); ?> <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("client_id", $clients_dropdown, array($model_info->client_id), "class='select2 form-control' id='client_id' required");
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="cargo_type" class="col-md-3"><?php echo app_lang('cargo_type'); ?> <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "cargo_type",
                        "name" => "cargo_type",
                        "value" => $model_info->cargo_type,
                        "class" => "form-control",
                        "placeholder" => app_lang('cargo_type'),
                        "required" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="cargo_weight" class="col-md-3"><?php echo app_lang('cargo_weight'); ?> (tons)</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "cargo_weight",
                        "name" => "cargo_weight",
                        "value" => $model_info->cargo_weight,
                        "class" => "form-control",
                        "placeholder" => app_lang('cargo_weight'),
                        "type" => "number",
                        "step" => "0.01"
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="cargo_value" class="col-md-3"><?php echo app_lang('cargo_value'); ?> (USD)</label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "cargo_value",
                        "name" => "cargo_value",
                        "value" => $model_info->cargo_value,
                        "class" => "form-control",
                        "placeholder" => app_lang('cargo_value'),
                        "type" => "number",
                        "step" => "0.01"
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="origin_port" class="col-md-3"><?php echo app_lang('origin_port'); ?> <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "origin_port",
                        "name" => "origin_port",
                        "value" => $model_info->origin_port,
                        "class" => "form-control",
                        "placeholder" => app_lang('origin_port'),
                        "required" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="destination_port" class="col-md-3"><?php echo app_lang('destination_port'); ?> <span class="text-danger">*</span></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "destination_port",
                        "name" => "destination_port",
                        "value" => $model_info->destination_port,
                        "class" => "form-control",
                        "placeholder" => app_lang('destination_port'),
                        "required" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="final_destination" class="col-md-3"><?php echo app_lang('final_destination'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "final_destination",
                        "name" => "final_destination",
                        "value" => $model_info->final_destination,
                        "class" => "form-control",
                        "placeholder" => app_lang('final_destination'),
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="estimated_arrival" class="col-md-3"><?php echo app_lang('estimated_arrival'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "estimated_arrival",
                        "name" => "estimated_arrival",
                        "value" => $model_info->estimated_arrival ? get_my_local_time($model_info->estimated_arrival, "Y-m-d") : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('estimated_arrival'),
                        "type" => "date"
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="assigned_to" class="col-md-3"><?php echo app_lang('assign_to'); ?></label>
                <div class="col-md-9">
                    <?php
                    $selected_assigned_to = isset($model_info->assigned_to) ? $model_info->assigned_to : "";
                    echo form_dropdown("assigned_to", $users_dropdown, array($selected_assigned_to), "class='select2 form-control' id='assigned_to'");
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        // Use appForm for proper modal handling
        $("#shipment-form").appForm({
            onSuccess: function (result) {
                // Reload the shipments tab content without full page refresh
                if ($("[data-bs-target='#shipments_list']").length) {
                    $("[data-bs-target='#shipments_list']").trigger("click");
                }
                // Show success message
                if (typeof appAlert !== 'undefined') {
                    appAlert.success(result.message);
                }
            }
        });
        
        $("#shipment-form .select2").select2();
        
        // Populate Tanzanian ports as suggestions
        var tanzanianPorts = [
            'Dar es Salaam Port',
            'Tanga Port', 
            'Mtwara Port',
            'Kilindoni Port',
            'Stone Town Port (Zanzibar)',
            'Mwanza Port',
            'Bukoba Port',
            'Musoma Port'
        ];
        
        $("#origin_port").autocomplete({
            source: tanzanianPorts,
            minLength: 0
        }).focus(function() {
            $(this).autocomplete("search", "");
        });
        
        var internationalPorts = [
            'Mombasa (Kenya)',
            'Dubai (UAE)', 
            'Rotterdam (Netherlands)',
            'Hamburg (Germany)',
            'Shanghai (China)',
            'Mumbai (India)',
            'Cape Town (South Africa)',
            'London (UK)',
            'Antwerp (Belgium)',
            'Singapore',
            'Jeddah (Saudi Arabia)',
            'Alexandria (Egypt)'
        ];
        
        $("#destination_port, #final_destination").autocomplete({
            source: internationalPorts,
            minLength: 0
        }).focus(function() {
            $(this).autocomplete("search", "");
        });
    });
</script>