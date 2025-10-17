<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i data-feather="file-text" class="icon-16"></i>
            <?php echo app_lang('documents'); ?>
        </h5>
        <?php if (get_array_value($permissions, "can_manage_documents")) { ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal">
            <i data-feather="upload" class="icon-16"></i>
            <?php echo app_lang('upload_document'); ?>
        </button>
        <?php } ?>
    </div>
    <div class="card-body">
        <!-- Document Filter -->
        <div class="row mb-3">
            <div class="col-md-4">
                <select id="shipment-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_shipments'); ?></option>
                </select>
            </div>
            <div class="col-md-4">
                <select id="document-type-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_document_types'); ?></option>
                    <option value="bill_of_lading"><?php echo app_lang('bill_of_lading'); ?></option>
                    <option value="invoice"><?php echo app_lang('invoice'); ?></option>
                    <option value="customs_declaration"><?php echo app_lang('customs_declaration'); ?></option>
                    <option value="insurance"><?php echo app_lang('insurance'); ?></option>
                    <option value="contract"><?php echo app_lang('contract'); ?></option>
                    <option value="other"><?php echo app_lang('other'); ?></option>
                </select>
            </div>
            <div class="col-md-4">
                <select id="status-filter" class="form-select">
                    <option value=""><?php echo app_lang('all_statuses'); ?></option>
                    <option value="pending"><?php echo app_lang('pending'); ?></option>
                    <option value="approved"><?php echo app_lang('approved'); ?></option>
                    <option value="rejected"><?php echo app_lang('rejected'); ?></option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="documents-table" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?php echo app_lang('document_name'); ?></th>
                        <th><?php echo app_lang('shipment'); ?></th>
                        <th><?php echo app_lang('document_type'); ?></th>
                        <th><?php echo app_lang('uploaded_by'); ?></th>
                        <th><?php echo app_lang('upload_date'); ?></th>
                        <th><?php echo app_lang('file_size'); ?></th>
                        <th><?php echo app_lang('status'); ?></th>
                        <th class="w100"><?php echo app_lang('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel"><?php echo app_lang('upload_document'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="document-form" action="<?php echo get_uri("workflow/upload_document"); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document_shipment_id" class="form-label"><?php echo app_lang('shipment'); ?> *</label>
                                <select name="shipment_id" id="document_shipment_id" class="form-select select2" required>
                                    <option value=""><?php echo app_lang('select_shipment'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document_type" class="form-label"><?php echo app_lang('document_type'); ?> *</label>
                                <select name="document_type" id="document_type" class="form-select" required>
                                    <option value=""><?php echo app_lang('select_document_type'); ?></option>
                                    <option value="bill_of_lading"><?php echo app_lang('bill_of_lading'); ?></option>
                                    <option value="invoice"><?php echo app_lang('invoice'); ?></option>
                                    <option value="customs_declaration"><?php echo app_lang('customs_declaration'); ?></option>
                                    <option value="insurance"><?php echo app_lang('insurance'); ?></option>
                                    <option value="contract"><?php echo app_lang('contract'); ?></option>
                                    <option value="other"><?php echo app_lang('other'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="document_title" class="form-label"><?php echo app_lang('document_title'); ?> *</label>
                                <input type="text" class="form-control" name="title" id="document_title" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="document_file" class="form-label"><?php echo app_lang('select_file'); ?> *</label>
                                <input type="file" class="form-control" name="document_file" id="document_file" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required>
                                <div class="form-text">
                                    <?php echo app_lang('allowed_file_formats'); ?>: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="document_description" class="form-label"><?php echo app_lang('description'); ?></label>
                                <textarea class="form-control" name="description" id="document_description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document_version" class="form-label"><?php echo app_lang('version'); ?></label>
                                <input type="text" class="form-control" name="version" id="document_version" value="1.0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="document_expiry_date" class="form-label"><?php echo app_lang('expiry_date'); ?></label>
                                <input type="date" class="form-control" name="expiry_date" id="document_expiry_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="upload" class="icon-16"></i>
                        <?php echo app_lang('upload'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize DataTable
    var documentsTable = $("#documents-table").appTable({
        source: '<?php echo_uri("workflow/list_documents") ?>',
        order: [[4, "desc"]],
        columns: [
            {title: '<?php echo app_lang("document_name") ?>'},
            {title: '<?php echo app_lang("shipment") ?>'},
            {title: '<?php echo app_lang("document_type") ?>'},
            {title: '<?php echo app_lang("uploaded_by") ?>'},
            {title: '<?php echo app_lang("upload_date") ?>', "iDataSort": 4},
            {title: '<?php echo app_lang("file_size") ?>'},
            {title: '<?php echo app_lang("status") ?>'},
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
        ],
        printColumns: [0, 1, 2, 3, 4, 5, 6],
        xlsColumns: [0, 1, 2, 3, 4, 5, 6]
    });

    // Filter handlers
    $("#shipment-filter, #document-type-filter, #status-filter").on('change', function() {
        var shipmentFilter = $("#shipment-filter").val();
        var typeFilter = $("#document-type-filter").val();
        var statusFilter = $("#status-filter").val();
        
        var source = '<?php echo_uri("workflow/list_documents") ?>';
        var params = [];
        
        if (shipmentFilter) params.push('shipment_id=' + shipmentFilter);
        if (typeFilter) params.push('document_type=' + typeFilter);
        if (statusFilter) params.push('status=' + statusFilter);
        
        if (params.length > 0) {
            source += '?' + params.join('&');
        }
        
        $("#documents-table").DataTable().ajax.url(source).load();
    });

    // Initialize select2 for shipment dropdown
    $("#document_shipment_id, #shipment-filter").select2({
        data: <?php echo json_encode([]) ?>, // Load shipments via AJAX
        placeholder: "<?php echo app_lang('select_shipment'); ?>",
        allowClear: true
    });

    // Handle form submission
    $("#document-form").appForm({
        onSuccess: function (result) {
            if (result.success) {
                $("#documentModal").modal('hide');
                $("#documents-table").appTable({newData: result.data, dataId: result.id});
                appAlert.success(result.message);
                // Reset form
                $("#document-form")[0].reset();
                $("#document_shipment_id").val(null).trigger('change');
            } else {
                appAlert.error(result.message);
            }
        },
        onError: function () {
            appAlert.error("<?php echo app_lang('file_upload_failed'); ?>");
        }
    });

    // File upload progress
    $("#document-form").on('submit', function() {
        var fileInput = $("#document_file")[0];
        if (fileInput.files.length > 0) {
            var fileSize = fileInput.files[0].size;
            var maxSize = 10 * 1024 * 1024; // 10MB
            
            if (fileSize > maxSize) {
                appAlert.error("<?php echo app_lang('file_too_large'); ?>");
                return false;
            }
        }
    });

    // Auto-generate document title from filename
    $("#document_file").on('change', function() {
        var filename = $(this).val().split('\\').pop();
        if (filename && !$("#document_title").val()) {
            var title = filename.replace(/\.[^/.]+$/, ""); // Remove extension
            $("#document_title").val(title);
        }
    });
});
</script>