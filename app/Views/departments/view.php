<?php
/**
 * Department Details View - Matches Project View Design
 */

if (!function_exists("make_department_tabs_data")) {
    function make_department_tabs_data($default_department_tabs = array())
    {
        $final_department_tabs = $default_department_tabs;

        foreach ($final_department_tabs as $key => $value) {
            echo "<li class='nav-item' role='presentation'><a class='nav-link' data-bs-toggle='tab' href='" . get_uri($value) . "' data-bs-target='#department-$key-section'>" . app_lang($key) . "</a></li>";
        }
    }
}
?>

<div class="page-content department-details-view clearfix">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="department-title-section">
                    <div class="page-title no-bg clearfix mb5 no-border">
                        <div>
                            <h1 class="pl0">
                                <span class="color-tag me-2" style="background-color: <?php echo $department_info->color; ?>; width: 16px; height: 16px; display: inline-block; border-radius: 3px; border: 1px solid rgba(0,0,0,0.1);"></span>
                                <?php echo $department_info->title; ?>
                            </h1>
                        </div>

                        <div class="department-title-button-group-section">
                            <div class="title-button-group mr0" id="department-action-box">
                                <?php echo view("departments/department_title_buttons"); ?>
                            </div>
                        </div>
                    </div>
                    
                    <ul id="department-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs rounded classic mb20 scrollable-tabs" role="tablist">
                        <?php
                        // Department tabs
                        $department_tabs = array(
                            "overview" => "departments/overview/" . $department_info->id,
                            "team_members" => "departments/team/" . $department_info->id,
                            "projects" => "departments/projects/" . $department_info->id,
                            "tasks" => "departments/tasks/" . $department_info->id,
                        );

                        make_department_tabs_data($department_tabs);
                        ?>
                    </ul>
                </div>
                
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active" id="department-overview-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-team_members-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-projects-section"></div>
                    <div role="tabpanel" class="tab-pane fade" id="department-tasks-section"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.department-details-view .page-title h1 {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.color-tag {
    vertical-align: middle;
}

.department-title-section .page-title {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px 0;
}

.department-title-button-group-section {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.nav-tabs.rounded.classic {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 5px;
    border: 1px solid #dee2e6;
}

.nav-tabs.rounded.classic .nav-link {
    border: none;
    border-radius: 6px;
    color: #6c757d;
    padding: 10px 16px;
    font-weight: 500;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.nav-tabs.rounded.classic .nav-link:hover {
    background-color: #e9ecef;
    color: #495057;
}

.nav-tabs.rounded.classic .nav-link.active {
    background-color: #fff;
    color: <?php echo $department_info->color; ?>;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid <?php echo $department_info->color; ?>20;
}

.scrollable-tabs {
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
}

.scrollable-tabs::-webkit-scrollbar {
    display: none;
}

/* Theme the page based on department color */
.department-details-view {
    --dept-color: <?php echo $department_info->color; ?>;
    --dept-color-light: <?php echo $department_info->color; ?>15;
    --dept-color-ultra-light: <?php echo $department_info->color; ?>05;
}

.department-details-view .btn-primary {
    background-color: var(--dept-color);
    border-color: var(--dept-color);
}

.department-details-view .btn-primary:hover {
    background-color: var(--dept-color);
    border-color: var(--dept-color);
    filter: brightness(0.9);
}

.department-details-view .text-primary {
    color: var(--dept-color) !important;
}
</style>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize the first tab
    setTimeout(function () {
        var hash = window.location.hash;
        if (hash && hash.indexOf("department-") !== -1) {
            $('a[data-bs-target="' + hash + '"]').tab('show');
        } else {
            $('a[data-bs-target="#department-overview-section"]').tab('show');
        }
    }, 50);
});
</script>