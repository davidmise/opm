<?php echo form_open(get_uri("departments/add_user_to_department"), array("id" => "add-user-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="department_id" value="<?php echo $department_info->id; ?>" />
        
        <div class="form-group">
            <div class="row">
                <label for="user_id" class="col-md-3"><?php echo app_lang('team_member'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("user_id", $users_dropdown, "", "class='select2 form-control' id='user_id' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="is_primary" class="col-md-3"><?php echo app_lang('set_as_primary'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_checkbox("is_primary", "1", false, "id='is_primary' class='form-check-input'");
                    ?>
                    <label for="is_primary" class="form-check-label"><?php echo app_lang('make_this_primary_department'); ?></label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i>
                        <strong><?php echo app_lang('note'); ?>:</strong>
                        <?php echo app_lang('user_can_belong_to_multiple_departments'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span class="fa fa-close"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo app_lang('add_user'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#add-user-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                    location.reload();
                } else {
                    appAlert.error(result.message);
                }
            }
        });
        
        $("#user_id").select2();
        setDatePicker("#date_of_hire");
    });
</script>