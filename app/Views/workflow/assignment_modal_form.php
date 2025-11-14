<?php echo form_open(get_uri("workflow/save_assignment"), array("id" => "assignment-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="assigned_to" class="col-md-3"><?php echo app_lang('assign_to_user'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("assigned_to", $team_members_dropdown, $current_assigned_to, "class='select2' id='assigned_to'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="department_id" class="col-md-3"><?php echo app_lang('assign_to_department'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("department_id", $departments_dropdown, "", "class='select2' id='department_id'");
                    ?>
                    <div class="mt5">
                        <small class="text-muted"><?php echo app_lang('optional_department_assignment'); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="assignment_notes" class="col-md-3"><?php echo app_lang('notes'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "assignment_notes",
                        "name" => "assignment_notes",
                        "class" => "form-control",
                        "placeholder" => app_lang('assignment_instructions_optional'),
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
    <button type="submit" class="btn btn-primary"><span data-feather="user-plus" class="icon-16"></span> <?php echo app_lang('assign_shipment'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#assignment-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message);
                    location.reload();
                } else {
                    appAlert.error(result.message);
                }
            }
        });
        $("#assignment-form .select2").select2();
    });
</script>