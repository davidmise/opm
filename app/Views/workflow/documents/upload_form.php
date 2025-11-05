<?php echo form_open_multipart(get_uri("workflow/upload_document"), array("id" => "document-upload-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="document_type" class="col-md-3"><?php echo app_lang('document_type'); ?></label>
                <div class="col-md-9">
                    <?php
                    $document_types = array(
                        'client_documents' => app_lang('client_documents'),
                        'bill_of_lading' => app_lang('bill_of_lading'),
                        'customs_declaration' => app_lang('customs_declaration'),
                        'customs_release' => app_lang('customs_release_order'),
                        'loading_order' => app_lang('loading_order'),
                        't1_form' => app_lang('t1_form'),
                        'tracking_report' => app_lang('tracking_report'),
                        'pod' => app_lang('proof_of_delivery'),
                        'other' => app_lang('other')
                    );
                    echo form_dropdown("document_type", $document_types, "", "class='select2 form-control' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'");
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="document_name" class="col-md-3"><?php echo app_lang('document_name'); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "document_name",
                        "name" => "document_name",
                        "class" => "form-control",
                        "placeholder" => app_lang('document_name'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="file" class="col-md-3"><?php echo app_lang('file'); ?></label>
                <div class="col-md-9">
                    <input type="file" name="file" id="file" class="form-control" 
                           data-rule-required="true" 
                           data-msg-required="<?php echo app_lang('field_required'); ?>" />
                    <small class="form-text text-muted"><?php echo app_lang('max_file_size_20mb'); ?></small>
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
                        "placeholder" => app_lang('document_notes'),
                        "rows" => 3
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div id="pod-warning" class="alert alert-warning" style="display: none;">
            <i data-feather="alert-triangle" class="icon-16"></i>
            <strong><?php echo app_lang('pod_upload_warning'); ?></strong><br>
            <?php echo app_lang('pod_will_trigger_shipment_closure_approval'); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="upload" class="icon-16"></span> <?php echo app_lang('upload'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#document-upload-form").appForm({
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

        // Show POD warning
        $('#document_type').change(function() {
            if ($(this).val() === 'pod') {
                $('#pod-warning').slideDown();
            } else {
                $('#pod-warning').slideUp();
            }
        });
    });
</script>
