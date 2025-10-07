<?php
/**
 * Department Title Action Buttons - Matches Project Design
 */

if (can_access_reminders_module()) {
    echo modal_anchor(get_uri("events/reminders"), "<i data-feather='clock' class='icon-16 mr5'></i> " . app_lang('reminders'), array("class" => "btn btn-default hidden-sm", "id" => "reminder-icon", "data-post-department_id" => $department_info->id, "data-post-reminder_view_type" => "department", "title" => app_lang('reminders') . " (" . app_lang('private') . ")"));
}
?>

<?php
// Settings button for admins
if ($login_user->is_admin || get_array_value($login_user->permissions, "can_manage_departments")) {
    echo modal_anchor(get_uri("departments/modal_form"), "<i data-feather='settings' class='icon-16 mr5'></i> " . app_lang('settings'), array("class" => "btn btn-default", "title" => app_lang('edit_department'), "data-post-id" => $department_info->id));
}
?>

<?php 
// Actions dropdown for department management
$show_actions_dropdown = $login_user->is_admin || get_array_value($login_user->permissions, "can_manage_departments");
if ($show_actions_dropdown) { ?>
    <div class="dropdown btn-group">
        <button class="btn btn-primary dropdown-toggle caret" type="button" data-bs-toggle="dropdown" aria-expanded="true">
            <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
        </button>
        <ul class="dropdown-menu" role="menu">
            <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "can_manage_departments")) { ?>
                <li role="presentation">
                    <?php echo modal_anchor(get_uri("departments/modal_form"), "<i data-feather='edit' class='icon-16 mr5'></i> " . app_lang('edit_department'), array("class" => "dropdown-item", "data-post-id" => $department_info->id, "title" => app_lang('edit_department'))); ?>
                </li>
                
                <li role="presentation">
                    <?php echo ajax_anchor(get_uri("departments/toggle_status"), "<i data-feather='" . ($department_info->is_active ? 'pause' : 'play') . "' class='icon-16 mr5'></i> " . ($department_info->is_active ? app_lang('deactivate_department') : app_lang('activate_department')), array("data-reload-on-success" => true, "class" => "dropdown-item", "data-post-id" => $department_info->id)); ?>
                </li>
                
                <li role="presentation" class="dropdown-divider"></li>
                
                <li role="presentation">
                    <?php echo anchor(get_uri("departments/export"), "<i data-feather='download' class='icon-16 mr5'></i> " . app_lang('export_departments'), array("class" => "dropdown-item", "title" => app_lang('export_departments'))); ?>
                </li>
                
                <?php if ($login_user->is_admin) { ?>
                    <li role="presentation" class="dropdown-divider"></li>
                    <li role="presentation">
                        <?php echo js_anchor("<i data-feather='x' class='icon-16 mr5'></i> " . app_lang('delete_department'), array('title' => app_lang('delete_department'), "class" => "dropdown-item text-danger", "data-id" => $department_info->id, "data-action-url" => get_uri("departments/delete"), "data-action" => "delete-confirmation")); ?>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php
// Back to departments list button
echo anchor(get_uri("departments"), "<i data-feather='list' class='icon-16 mr5'></i> " . app_lang('back_to_departments_list'), array("class" => "btn btn-default", "title" => app_lang('back_to_departments_list')));
?>