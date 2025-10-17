<?php
/**
 * Workflow Documents View
 */
?>

<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <div class="title-button-group">
            <h1 class="page-title"><?php echo app_lang('documents'); ?></h1>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="uploadDocument()">
                    <i data-feather="upload" class="icon-16"></i> <?php echo app_lang('upload_document'); ?>
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="generateReport()">
                    <i data-feather="file-text" class="icon-16"></i> <?php echo app_lang('generate_report'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang('workflow_documents'); ?></h4>
                    <div class="card-header-actions">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="<?php echo app_lang('search_documents'); ?>" id="document-search">
                            <button class="btn btn-outline-secondary" type="button">
                                <i data-feather="search" class="icon-16"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="documents-table">
                            <thead>
                                <tr>
                                    <th><?php echo app_lang('document_name'); ?></th>
                                    <th><?php echo app_lang('type'); ?></th>
                                    <th><?php echo app_lang('shipment'); ?></th>
                                    <th><?php echo app_lang('uploaded_by'); ?></th>
                                    <th><?php echo app_lang('upload_date'); ?></th>
                                    <th><?php echo app_lang('size'); ?></th>
                                    <th><?php echo app_lang('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample data -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="file-text" class="icon-16 text-primary me-2"></i>
                                            <span>Commercial_Invoice_SHP001.pdf</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary">Invoice</span></td>
                                    <td><a href="#" class="text-primary">SHP-2025-001</a></td>
                                    <td>John Doe</td>
                                    <td><?php echo format_to_date(date('Y-m-d'), false); ?></td>
                                    <td>2.4 MB</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument(1)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadDocument(1)">
                                                <i data-feather="download" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument(1)">
                                                <i data-feather="trash-2" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="file" class="icon-16 text-success me-2"></i>
                                            <span>Packing_List_SHP001.xlsx</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">Packing List</span></td>
                                    <td><a href="#" class="text-primary">SHP-2025-001</a></td>
                                    <td>Jane Smith</td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('-1 day')), false); ?></td>
                                    <td>1.2 MB</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument(2)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadDocument(2)">
                                                <i data-feather="download" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument(2)">
                                                <i data-feather="trash-2" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="image" class="icon-16 text-warning me-2"></i>
                                            <span>Customs_Form_SHP002.pdf</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning">Customs</span></td>
                                    <td><a href="#" class="text-primary">SHP-2025-002</a></td>
                                    <td>Mike Johnson</td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('-2 days')), false); ?></td>
                                    <td>856 KB</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument(3)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadDocument(3)">
                                                <i data-feather="download" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument(3)">
                                                <i data-feather="trash-2" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i data-feather="file-text" class="icon-16 text-info me-2"></i>
                                            <span>Bill_of_Lading_SHP003.pdf</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">Bill of Lading</span></td>
                                    <td><a href="#" class="text-primary">SHP-2025-003</a></td>
                                    <td>Sarah Wilson</td>
                                    <td><?php echo format_to_date(date('Y-m-d', strtotime('-3 days')), false); ?></td>
                                    <td>1.8 MB</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument(4)">
                                                <i data-feather="eye" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadDocument(4)">
                                                <i data-feather="download" class="icon-12"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDocument(4)">
                                                <i data-feather="trash-2" class="icon-12"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function uploadDocument() {
    alert('Upload Document feature coming soon!');
}

function generateReport() {
    alert('Generate Report feature coming soon!');
}

function viewDocument(id) {
    alert('View Document feature coming soon!');
}

function downloadDocument(id) {
    alert('Download Document feature coming soon!');
}

function deleteDocument(id) {
    if (confirm('Are you sure you want to delete this document?')) {
        alert('Delete Document feature coming soon!');
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('document-search');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#documents-table tbody tr');
        
        rows.forEach(row => {
            const documentName = row.querySelector('td:first-child span').textContent.toLowerCase();
            const shipment = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const type = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            
            if (documentName.includes(searchTerm) || shipment.includes(searchTerm) || type.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

<style>
.card-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

#documents-table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 11px;
    padding: 6px 12px;
}

.btn-group .btn {
    margin-right: 2px;
}

.input-group {
    width: 300px;
}
</style>