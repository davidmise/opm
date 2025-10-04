<?php if ($project_info->department_title) { ?>
<div class="card">
    <div class="card-header">
        <h6 class="float-start"><?php echo app_lang('department'); ?></h6>
    </div>
    <div class="card-body">
        <?php 
        $color = $project_info->department_color ? $project_info->department_color : "#6c757d";
        ?>
        <div class="d-flex align-items-center">
            <span class="badge me-2" style="background-color: <?php echo $color; ?>; color: white; font-size: 14px; padding: 8px 12px;">
                <i data-feather='grid' class='icon-16'></i> <?php echo $project_info->department_title; ?>
            </span>
            <small class="text-muted"><?php echo app_lang('project_department'); ?></small>
        </div>
    </div>
</div>
<?php } ?>