<?php echo form_open(get_uri("departments/save"), array("id" => "department-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        
        <div class="form-group">
            <div class="row">
                <label for="title" class="col-md-3"><?php echo app_lang('title'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => $model_info->title,
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
                <label for="description" class="col-md-3"><?php echo app_lang('description'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "description",
                        "name" => "description",
                        "value" => $model_info->description,
                        "class" => "form-control",
                        "placeholder" => app_lang('description'),
                        "style" => "height:100px;",
                    ));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">
                <label for="color" class="col-md-3"><?php echo app_lang('color'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "color",
                        "name" => "color",
                        "value" => $model_info->color ? $model_info->color : "#4CAF50",
                        "class" => "form-control",
                        "type" => "color",
                        "placeholder" => app_lang('color')
                    ));
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
        $("#department-form").appForm({
            onSuccess: function (result) {
                $("#department-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        
        $("#title").focus();
    });
</script>
