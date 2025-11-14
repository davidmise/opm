<?php echo form_open(get_uri("departments/add_user_to_department"), array("id" => "add-user-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="department_id" value="<?php echo $department_info->id; ?>" />
        
        <div class="form-group">
            <div class="row">
                <label for="user_ids" class="col-md-3"><?php echo app_lang('team_members'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("user_ids[]", $users_dropdown, "", "class='select2 form-control' id='user_ids' multiple='multiple' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i data-feather="info" class="icon-16"></i>
                        <strong><?php echo app_lang('note'); ?>:</strong>
                        <?php echo app_lang('user_can_belong_to_multiple_departments'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><i data-feather="check-circle" class="icon-16"></i> <?php echo app_lang('add_members'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    // Wait for jQuery to be available before executing
    function initAddUserModalScript() {
        if (typeof $ === 'undefined') {
            // jQuery not loaded yet, wait 100ms and try again
            setTimeout(initAddUserModalScript, 100);
            return;
        }
        
        $(document).ready(function () {
            $("#add-user-form").appForm({
                onSuccess: function (result) {
                    if (result.success) {
                        appAlert.success(result.message, {duration: 10000});
                        $("#department-team-table").appTable({reload: true});
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });
            
            $("#user_ids").select2({
                placeholder: "<?php echo app_lang('select_team_members'); ?>",
                allowClear: true
            });

            // Initialize feather icons
            setTimeout(function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }, 100);
        });
    }
    
    // Start the initialization
    initAddUserModalScript();
</script>