<?php echo form_open(get_uri("departments/save_announcement"), array("id" => "announcement-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo isset($model_info) ? $model_info->id : ''; ?>" />
        
        <div class="form-group">
            <div class="row">
                <label for="title" class="col-md-3"><?php echo app_lang('title'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => isset($model_info) ? $model_info->title : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('title'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="content" class="col-md-3"><?php echo app_lang('content'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "content",
                        "name" => "content",
                        "value" => isset($model_info) ? $model_info->description : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('content'),
                        "rows" => 5,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="category" class="col-md-3"><?php echo app_lang('category'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("category", $announcement_categories, isset($model_info) ? $model_info->category : "", "class='select2 form-control' id='category'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="priority" class="col-md-3"><?php echo app_lang('priority'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_dropdown("priority", $priority_levels, isset($model_info) ? $model_info->priority : "", "class='select2 form-control' id='priority'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="target_departments" class="col-md-3"><?php echo app_lang('target_departments'); ?></label>
                <div class="col-md-9">
                    <?php
                    $departments_dropdown = array();
                    foreach ($departments as $department) {
                        $departments_dropdown[$department->id] = $department->title;
                    }
                    
                    $selected_departments = array();
                    if (isset($model_info) && $model_info->share_with) {
                        if ($model_info->share_with !== 'all_members') {
                            $share_with_parts = explode(',', $model_info->share_with);
                            foreach ($share_with_parts as $part) {
                                if (strpos($part, 'dept:') === 0) {
                                    $selected_departments[] = str_replace('dept:', '', $part);
                                }
                            }
                        }
                    }
                    
                    echo form_multiselect("target_departments[]", $departments_dropdown, $selected_departments, "class='select2 form-control' id='target_departments' data-placeholder='" . app_lang('all_departments') . "'");
                    ?>
                    <small class="form-text text-muted"><?php echo app_lang('leave_empty_for_all_departments'); ?></small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="start_date" class="col-md-3"><?php echo app_lang('start_date'); ?></label>
                <div class="col-md-9">
                    <?php
                    $start_date = isset($model_info) && $model_info->start_date ? $model_info->start_date : date('Y-m-d H:i:s');
                    echo form_input(array(
                        "id" => "start_date",
                        "name" => "start_date",
                        "value" => format_to_datetime($start_date),
                        "class" => "form-control",
                        "placeholder" => app_lang('start_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => false,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="end_date" class="col-md-3"><?php echo app_lang('end_date'); ?></label>
                <div class="col-md-9">
                    <?php
                    $end_date = isset($model_info) && $model_info->end_date ? $model_info->end_date : "";
                    echo form_input(array(
                        "id" => "end_date",
                        "name" => "end_date",
                        "value" => $end_date ? format_to_datetime($end_date) : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('end_date'),
                        "autocomplete" => "off",
                    ));
                    ?>
                    <small class="form-text text-muted"><?php echo app_lang('leave_empty_for_never_expire'); ?></small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="send_email" class="col-md-3"><?php echo app_lang('notifications'); ?></label>
                <div class="col-md-9">
                    <div class="form-check">
                        <?php
                        echo form_checkbox("send_email", "1", true, "id='send_email' class='form-check-input'");
                        ?>
                        <label class="form-check-label" for="send_email"><?php echo app_lang('send_email_notification'); ?></label>
                    </div>
                    <div class="form-check">
                        <?php
                        echo form_checkbox("send_push", "1", false, "id='send_push' class='form-check-input'");
                        ?>
                        <label class="form-check-label" for="send_push"><?php echo app_lang('send_push_notification'); ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#announcement-form").appForm({
            onSuccess: function (result) {
                // Close the modal
                $("#ajaxModal").modal('hide');
                
                // Show success message
                appAlert.success(result.message, {duration: 10000});
                
                // Reload the page to show changes
                setTimeout(function () {
                    location.reload();
                }, 500);
            }
        });
        
        // Initialize Select2 dropdowns
        $("#category, #priority").select2();
        $("#target_departments").select2({
            placeholder: "<?php echo app_lang('all_departments'); ?>",
            allowClear: true
        });

        // Initialize date pickers
        setDatePicker("#start_date, #end_date");
        
        // Focus on title field
        $("#title").focus();
    });
</script>