<!-- DEPARTMENTS LIST VIEW -->
<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4><i data-feather="grid" class="icon-16"></i> <?php echo app_lang('departments'); ?> <?php echo app_lang('overview'); ?></h4>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm active" id="btn-grid">
                                <i data-feather="grid" class="icon-14"></i> Grid
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-table">
                                <i data-feather="list" class="icon-14"></i> List
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="departments-grid" style="display: block;">
                        <div class="text-center text-muted p-5">
                            <i data-feather="loader" class="icon-48"></i>
                            <h6>Loading departments...</h6>
                        </div>
                    </div>
                    <div id="departments-table-wrapper" style="display:none;">
                        <table id="departments-table" class="display" width="100%"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
$(function(){
    console.log('Departments list script loaded');
    
    // Get department data from controller
    var gridData = [];
    
    // Load grid data via AJAX
    $.get('<?php echo base_url("index.php/departments/get_grid_data"); ?>', function(response) {
        console.log('AJAX Success:', response);
        if(response && response.success && response.data) {
            gridData = response.data;
            renderGrid();
        } else {
            console.log('AJAX returned invalid response:', response);
            renderGrid(); // Show empty state
        }
    }).fail(function(xhr, status, error) {
        console.log('AJAX Failed:', status, error);
        console.log('Response:', xhr.responseText);
        // Show error message in grid
        $('#departments-grid').html('<div class="text-center text-muted p-5"><i data-feather="alert-circle" class="icon-48 text-danger"></i><h6>Failed to load departments</h6><p>Error: ' + error + '</p><button class="btn btn-sm btn-outline-primary" onclick="location.reload()">Retry</button></div>');
        // Safely replace feather icons
        if (window.feather && typeof feather.replace === 'function') { 
            feather.replace(); 
        }
    });
    
    function renderGrid(){
        var $grid = $('#departments-grid');
        $grid.empty();
        if(!gridData || !gridData.length){
            $grid.html('<div class="text-center text-muted p-5"><i data-feather="folder" class="icon-48"></i><h6>No departments found</h6><p>Start by creating your first department</p></div>');
            // Safely replace feather icons
            if (window.feather && typeof feather.replace === 'function') { 
                feather.replace(); 
            }
            return;
        }
        
        var gridHTML = '<div class="row">';
        gridData.forEach(function(d){
            var statusText = d.is_active ? '<?php echo app_lang('active'); ?>' : '<?php echo app_lang('inactive'); ?>';
            var statusClass = d.is_active ? 'bg-success' : 'bg-secondary';
            
            gridHTML += '<div class="col-xl-4 col-lg-6 col-md-6 mb-4">\
                <div class="card h-100">\
                    <div class="card-header">\
                        <div class="d-flex align-items-center">\
                            <div class="color-tag me-2" style="background-color: '+ (d.color || '#6c757d') +';"></div>\
                            <h6 class="card-title mb-0">\
                                <a href="<?php echo base_url('index.php/departments/view/'); ?>'+d.id+'">'+ d.title +'</a>\
                            </h6>\
                            <span class="badge '+ statusClass +' ms-auto">'+ statusText +'</span>\
                        </div>\
                    </div>\
                    <div class="card-body">\
                        <p class="text-muted small">'+ (d.description || 'No description available') +'</p>\
                        <div class="row text-center">\
                            <div class="col-4">\
                                <div class="border-end">\
                                    <i data-feather="users" class="icon-16 text-primary mb-1"></i>\
                                    <h6 class="mb-0">'+ d.total_members +'</h6>\
                                    <small class="text-muted">Members</small>\
                                </div>\
                            </div>\
                            <div class="col-4">\
                                <div class="border-end">\
                                    <i data-feather="briefcase" class="icon-16 text-info mb-1"></i>\
                                    <h6 class="mb-0">'+ d.total_projects +'</h6>\
                                    <small class="text-muted">Projects</small>\
                                </div>\
                            </div>\
                            <div class="col-4">\
                                <i data-feather="clipboard" class="icon-16 text-warning mb-1"></i>\
                                <h6 class="mb-0">'+ d.total_tasks +'</h6>\
                                <small class="text-muted">Tasks</small>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="card-footer">\
                        <a href="<?php echo get_uri('departments/view/'); ?>'+d.id+'" class="btn btn-outline-primary btn-sm w-100">\
                            <i data-feather="arrow-right" class="icon-14"></i> View Details\
                        </a>\
                    </div>\
                </div>\
            </div>';
        });
        gridHTML += '</div>';
        
        $grid.html(gridHTML);
        // Safely replace feather icons
        if (window.feather && typeof feather.replace === 'function') { 
            feather.replace(); 
        }
    }

    function renderTable(){
        $('#departments-table').appTable({
            source: '<?php echo_uri("departments/list_data"); ?>',
            columns: [
                {title: '<?php echo app_lang('department'); ?>'},
                {title: '<?php echo app_lang('description'); ?>'},
                {title: '<?php echo app_lang('statistics'); ?>'},
                {title: '<?php echo app_lang('created_by'); ?>'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0,1,2,3],
            xlsColumns: [0,1,2,3]
        });
    }

    // Toggle functionality
    $('#btn-grid').on('click', function(){
        if (!$(this).hasClass('active')) {
            $('#btn-table').removeClass('active');
            $(this).addClass('active');
            $('#departments-table-wrapper').hide();
            $('#departments-grid').show();
        }
    });
    
    $('#btn-table').on('click', function(){
        if (!$(this).hasClass('active')) {
            $('#btn-grid').removeClass('active');
            $(this).addClass('active');
            $('#departments-grid').hide();
            $('#departments-table-wrapper').show();
            if (!$.fn.DataTable.isDataTable('#departments-table')) {
                renderTable();
            }
        }
    });
});
</script>

<style>
/* Simple system-consistent styling */
.color-tag {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

/* Grid card improvements */
.border-end {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem;
}

/* Ensure icons display properly in grid cards */
#departments-grid .card-body i[data-feather] {
    display: block;
    margin: 0 auto;
}

#departments-grid .card {
    transition: transform 0.2s ease;
}

#departments-grid .card:hover {
    transform: translateY(-2px);
}
</style>
