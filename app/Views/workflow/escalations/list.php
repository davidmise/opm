<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('escalations'); ?></h1>
            <div class="title-button-group">
                <button class="btn btn-default" id="filter-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i data-feather="filter" class="icon-16"></i> <?php echo app_lang('filter'); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" id="escalation-filter-dropdown">
                    <li><a class="dropdown-item" href="#" data-filter="all"><?php echo app_lang('all'); ?></a></li>
                    <li><a class="dropdown-item" href="#" data-filter="pending"><?php echo app_lang('pending'); ?></a></li>
                    <li><a class="dropdown-item" href="#" data-filter="acknowledged"><?php echo app_lang('acknowledged'); ?></a></li>
                    <li><a class="dropdown-item" href="#" data-filter="resolved"><?php echo app_lang('resolved'); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive">
            <table id="escalation-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var currentFilter = 'all';

        $("#escalation-table").appTable({
            source: '<?php echo_uri("workflow/list_escalations") ?>',
            filterParams: {status: currentFilter},
            columns: [
                {title: '<?php echo app_lang("id") ?>', "class": "text-center w50"},
                {title: '<?php echo app_lang("type") ?>'},
                {title: '<?php echo app_lang("reference") ?>'},
                {title: '<?php echo app_lang("reason") ?>'},
                {title: '<?php echo app_lang("escalated_by") ?>'},
                {title: '<?php echo app_lang("escalated_to") ?>'},
                {title: '<?php echo app_lang("level") ?>'},
                {title: '<?php echo app_lang("status") ?>', "class": "text-center"},
                {title: '<?php echo app_lang("priority") ?>', "class": "text-center"},
                {title: '<?php echo app_lang("created_date") ?>', "class": "text-center"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
        });

        // Filter dropdown handling
        $('#escalation-filter-dropdown').on('click', 'a', function(e) {
            e.preventDefault();
            currentFilter = $(this).data('filter');
            $("#escalation-table").appTable({
                newData: {status: currentFilter},
                dataOnly: true
            });
        });
    });
</script>
