<?php

namespace App\Controllers;

/**
 * My Department Controller
 * 
 * Provides department-focused dashboard and views for staff members
 * Shows only data relevant to user's department(s)
 * 
 * @package App\Controllers
 * @author O.P.M Development Team
 * @version 1.0.0
 */
class My_department extends Department_Access_Controller {

    public $Task_status_model;
    public $Milestones_model;
    public $Labels_model;
    public $Announcements_model;

    public function __construct() {
        parent::__construct();
        $this->check_module_availability("module_departments");
        
        // Initialize additional models
        $this->Task_status_model = model('App\Models\Task_status_model');
        $this->Milestones_model = model('App\Models\Milestones_model');  
        $this->Labels_model = model('App\Models\Labels_model');
        $this->Announcements_model = model('App\Models\Announcements_model');
        
        // Redirect clients to dashboard - this is staff-only feature
        if ($this->login_user->user_type === "client") {
            app_redirect("dashboard");
        }
    }

    /**
     * My Department Dashboard - Main entry point
     * Shows comprehensive department overview with KPIs and quick actions
     */
    function index() {
        // Get user's accessible departments
        $accessible_departments = $this->user_accessible_departments;
        
        if (empty($accessible_departments)) {
            // User has no department - show empty state
            $view_data = array(
                'has_department' => false,
                'message' => app_lang('no_department_assigned')
            );
            return $this->template->render("my_department/no_department", $view_data);
        }

        // Get primary department for default view
        $primary_department_id = $this->get_user_primary_department();
        if (!$primary_department_id && !empty($accessible_departments)) {
            $primary_department_id = $accessible_departments[0]; // Use first available
        }

        // Get department information
        $department_info = $this->Departments_model->get_details(array("id" => $primary_department_id))->getRow();
        
        if (!$department_info) {
            show_404();
        }

        // Gather comprehensive department statistics
        $stats = array();
        $recent_activities = array();
        $upcoming_deadlines = array();
        $announcements = array();
        $team_metrics = array();
        
        try {
            $stats = $this->_get_department_dashboard_stats($primary_department_id);
        } catch (\Exception $e) {
            log_message('error', 'Error loading department stats: ' . $e->getMessage());
        }
        
        try {
            $recent_activities = $this->_get_recent_department_activities($primary_department_id, 10);
        } catch (\Exception $e) {
            log_message('error', 'Error loading recent activities: ' . $e->getMessage());
        }
        
        try {
            $upcoming_deadlines = $this->_get_upcoming_deadlines($primary_department_id, 5);
        } catch (\Exception $e) {
            log_message('error', 'Error loading upcoming deadlines: ' . $e->getMessage());
        }
        
        try {
            $announcements = $this->_get_department_announcements($primary_department_id, 3);
        } catch (\Exception $e) {
            log_message('error', 'Error loading announcements: ' . $e->getMessage());
        }
        
        try {
            $team_metrics = $this->_get_team_performance_metrics($primary_department_id);
        } catch (\Exception $e) {
            log_message('error', 'Error loading team metrics: ' . $e->getMessage());
        }

        // Get dropdown data for tables with error handling
        $projects_dropdown = '[]';
        $team_members_dropdown = '[]';
        $task_statuses_dropdown = '[]';
        $milestones_dropdown = '[]';
        $project_labels_dropdown = '[]';
        
        try {
            $projects_dropdown = $this->_get_department_projects_dropdown($primary_department_id);
            $team_members_dropdown = $this->_get_department_team_members_dropdown($primary_department_id);
            $task_statuses_dropdown = $this->_get_department_task_statuses_dropdown();
            $milestones_dropdown = $this->_get_department_milestones_dropdown($primary_department_id);
            $project_labels_dropdown = $this->_get_department_project_labels_dropdown();
        } catch (Exception $e) {
            // Log the error but continue with empty dropdowns
            log_message('error', 'Error loading department dropdowns: ' . $e->getMessage());
        }

        // Prepare accessible departments with details
        $accessible_departments_details = array();
        foreach ($accessible_departments as $dept_id) {
            $dept_details = $this->Departments_model->get_one($dept_id);
            if ($dept_details) {
                $accessible_departments_details[] = $dept_details;
            }
        }

        // Get additional data needed for tables
        $project_statuses = array();
        $task_statuses = array();
        $quick_filters_dropdown = '[]';
        $contexts_dropdown = '[]';
        $priorities_dropdown = '[]';
        $labels_dropdown = '[]';
        
        try {
            // Get project statuses
            $project_status_model = model('App\Models\Project_status_model');
            $project_statuses = $project_status_model->get_details()->getResult();
            
            // Get task statuses  
            $task_status_results = $this->Task_status_model->get_details()->getResult();
            foreach ($task_status_results as $status) {
                $task_statuses[] = array("id" => $status->id, "text" => app_lang($status->key_name ? $status->key_name : $status->title));
            }
            
            // Additional dropdowns for advanced filtering
            $quick_filters_dropdown = json_encode(array(
                array("id" => "", "text" => "- " . app_lang("quick_filter") . " -"),
                array("id" => "recently_updated", "text" => app_lang("recently_updated")),
                array("id" => "recently_created", "text" => app_lang("recently_created"))
            ));
            
            $contexts_dropdown = json_encode(array(
                array("id" => "", "text" => "- " . app_lang("context") . " -"),
                array("id" => "project", "text" => app_lang("project")),
                array("id" => "general", "text" => app_lang("general"))
            ));
            
            $priorities_dropdown = json_encode(array(
                array("id" => "", "text" => "- " . app_lang("priority") . " -"),
                array("id" => "high", "text" => app_lang("high")),
                array("id" => "medium", "text" => app_lang("medium")), 
                array("id" => "low", "text" => app_lang("low"))
            ));
            
        } catch (Exception $e) {
            log_message('error', 'Error loading additional dropdown data: ' . $e->getMessage());
        }

        $view_data = array(
            'has_department' => true,
            'department_info' => $department_info,
            'accessible_departments' => $accessible_departments,
            'accessible_departments_details' => $accessible_departments_details,
            'stats' => $stats,
            'recent_activities' => $recent_activities,
            'upcoming_deadlines' => $upcoming_deadlines,
            'department_announcements' => $announcements,
            'team_metrics' => $team_metrics,
            'can_switch_department' => count($accessible_departments) > 1,
            'projects_dropdown' => $projects_dropdown,
            'team_members_dropdown' => $team_members_dropdown,
            'task_statuses_dropdown' => $task_statuses_dropdown,
            'milestones_dropdown' => $milestones_dropdown,
            'project_labels_dropdown' => $project_labels_dropdown,
            'project_statuses' => $project_statuses,
            'task_statuses' => $task_statuses,
            'quick_filters_dropdown' => $quick_filters_dropdown,
            'contexts_dropdown' => $contexts_dropdown,
            'priorities_dropdown' => $priorities_dropdown,
            'labels_dropdown' => $labels_dropdown,
            'deadline_expired' => "",
            'login_user' => $this->login_user,
            'tab' => "overview",
            'can_create_tasks' => ($this->login_user->user_type == "staff" || $this->login_user->is_admin)
        );

        return $this->template->render("my_department/index", $view_data);
    }

    /**
     * Switch active department via AJAX
     */
    function switch_department() {
        $this->validate_submitted_data(array(
            "department_id" => "required|numeric"
        ));

        $department_id = $this->request->getPost('department_id');
        
        // Verify user has access to this department
        if (!in_array($department_id, $this->user_accessible_departments)) {
            echo json_encode(array("success" => false, "message" => app_lang('access_denied')));
            return;
        }

        // Store preference in session
        $this->session->set('active_department_id', $department_id);
        
        echo json_encode(array(
            "success" => true, 
            "message" => app_lang('department_switched_successfully'),
            "refresh_page" => true
        ));
    }

    /**
     * Get department dashboard statistics
     */
    protected function _get_department_dashboard_stats($department_id) {
        // Projects statistics
        $projects_total = $this->Projects_model->get_details(array("department_id" => $department_id))->resultID->num_rows;
        $projects_active = $this->Projects_model->get_details(array("department_id" => $department_id, "status" => "open"))->resultID->num_rows;
        $projects_completed = $this->Projects_model->get_details(array("department_id" => $department_id, "status" => "completed"))->resultID->num_rows;

        // Tasks statistics  
        $tasks_options = array("department_ids" => array($department_id));
        $tasks_total = $this->Tasks_model->get_details($tasks_options)->resultID->num_rows;
        $tasks_completed = $this->Tasks_model->get_details(array_merge($tasks_options, array("status_ids" => "3")))->resultID->num_rows; // Assuming status 3 is completed
        $tasks_in_progress = $this->Tasks_model->get_details(array_merge($tasks_options, array("status_ids" => "2")))->resultID->num_rows; // Assuming status 2 is in progress
        $tasks_pending = $tasks_total - $tasks_completed - $tasks_in_progress;

        // Team statistics
        $team_options = array("department_id" => $department_id, "user_type" => "staff");
        $team_total = $this->Users_model->get_details($team_options)->resultID->num_rows;
        $team_active = $this->Users_model->get_details(array_merge($team_options, array("status" => "active")))->resultID->num_rows;

        // Calculate productivity metrics
        $completion_rate = $tasks_total > 0 ? round(($tasks_completed / $tasks_total) * 100, 1) : 0;
        $project_success_rate = $projects_total > 0 ? round(($projects_completed / $projects_total) * 100, 1) : 0;

        return array(
            'projects' => array(
                'total' => $projects_total,
                'active' => $projects_active, 
                'completed' => $projects_completed,
                'success_rate' => $project_success_rate
            ),
            'tasks' => array(
                'total' => $tasks_total,
                'completed' => $tasks_completed,
                'in_progress' => $tasks_in_progress,
                'pending' => $tasks_pending,
                'completion_rate' => $completion_rate
            ),
            'team' => array(
                'total' => $team_total,
                'active' => $team_active
            )
        );
    }

    /**
     * Get recent department activities
     */
    protected function _get_recent_department_activities($department_id, $limit = 10) {
        // This would typically come from an activity log table
        // For now, return empty array - to be implemented with actual activity tracking
        return array();
    }

    /**
     * Get upcoming deadlines for department
     */
    protected function _get_upcoming_deadlines($department_id, $limit = 5) {
        try {
            $options = array(
                "department_ids" => array($department_id),
                "deadline_between" => array(date('Y-m-d'), date('Y-m-d', strtotime('+30 days'))),
                "status_ids" => "1,2" // Not completed
            );

            $tasks_result = $this->Tasks_model->get_details($options);
            
            // Handle both array (when limit is used) and query result object
            if (is_array($tasks_result)) {
                $tasks = isset($tasks_result['data']) ? $tasks_result['data'] : $tasks_result;
            } else {
                $tasks = $tasks_result->getResult();
            }
            
            // Limit results manually if needed
            if ($limit && count($tasks) > $limit) {
                $tasks = array_slice($tasks, 0, $limit);
            }
            
            $deadlines = array();
            if ($tasks) {
                foreach ($tasks as $task) {
                    if (!$task->deadline) continue; // Skip tasks without deadlines
                    
                    $days_remaining = floor((strtotime($task->deadline) - time()) / (60 * 60 * 24));
                    $urgency = 'success';
                    if ($days_remaining <= 1) $urgency = 'danger';
                    elseif ($days_remaining <= 3) $urgency = 'warning';
                    elseif ($days_remaining <= 7) $urgency = 'info';

                    $deadlines[] = array(
                        'id' => $task->id,
                        'title' => $task->title,
                        'deadline' => $task->deadline,
                        'project' => isset($task->project_title) && $task->project_title ? $task->project_title : 'General',
                        'assigned_to' => isset($task->assigned_to_user) ? $task->assigned_to_user : '',
                        'days_remaining' => $days_remaining,
                        'urgency' => $urgency
                    );
                }
            }

            return $deadlines;
        } catch (\Exception $e) {
            log_message('error', 'Error loading upcoming deadlines: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get department announcements 
     */
    protected function _get_department_announcements($department_id, $limit = 3) {
        try {
            // Use proper announcement filtering with user context
            $options = array(
                "user_type" => "staff",
                "user_id" => $this->login_user->id,
                "department_id" => $department_id
            );

            $announcements = $this->Announcements_model->get_details($options)->getResult();
            
            // Filter for current/active announcements only
            $current_date = date('Y-m-d');
            $filtered_announcements = array();
            
            foreach ($announcements as $announcement) {
                // Only show announcements that are currently active
                if (!isset($announcement->end_date) || $announcement->end_date >= $current_date) {
                    $filtered_announcements[] = $announcement;
                }
            }
            
            // Limit results
            if ($limit && count($filtered_announcements) > $limit) {
                $filtered_announcements = array_slice($filtered_announcements, 0, $limit);
            }
            
            return $filtered_announcements;
        } catch (\Exception $e) {
            log_message('error', 'Error loading announcements: ' . $e->getMessage());
            return array(); // Return empty array on error
        }
    }

    /**
     * Get team performance metrics
     */
    protected function _get_team_performance_metrics($department_id) {
        try {
            // Get team members with their task completion stats
            $team_options = array("department_id" => $department_id, "user_type" => "staff");
            $team_members_result = $this->Users_model->get_details($team_options);
            
            // Handle both array and query result object
            if (is_array($team_members_result)) {
                $team_members = isset($team_members_result['data']) ? $team_members_result['data'] : $team_members_result;
            } else {
                $team_members = $team_members_result->getResult();
            }

            $team_performance = array();
            foreach ($team_members as $member) {
                // Count only tasks that belong to this department (matching Tasks tab filter)
                $completed_tasks = $this->Tasks_model->get_details(array(
                    "assigned_to" => $member->id,
                    "department_ids" => array($department_id), // Filter by department
                    "status_ids" => "3", // Completed
                    "created_date >=" => date('Y-m-d', strtotime('-30 days'))
                ))->resultID->num_rows;

                $total_tasks = $this->Tasks_model->get_details(array(
                    "assigned_to" => $member->id,
                    "department_ids" => array($department_id), // Filter by department
                    "created_date >=" => date('Y-m-d', strtotime('-30 days'))
                ))->resultID->num_rows;

                $completion_rate = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 1) : 0;

                // Only include team members who have tasks in this department
                if ($total_tasks > 0) {
                    $team_performance[] = array(
                        'name' => $member->first_name . ' ' . $member->last_name,
                        'completed_tasks' => $completed_tasks,
                        'total_tasks' => $total_tasks,
                        'completion_rate' => $completion_rate
                    );
                }
            }

            // Sort by completion rate descending
            usort($team_performance, function($a, $b) { 
                return $b['completion_rate'] <=> $a['completion_rate']; 
            });

            return array_slice($team_performance, 0, 5); // Top 5 performers
        } catch (\Exception $e) {
            log_message('error', 'Error loading team performance metrics: ' . $e->getMessage());
            return array(); // Return empty array on error
        }
    }

    /**
     * Get user's primary department ID
     */
    private function get_user_primary_department() {
        // Check session preference first
        $session_dept = $this->session->get('active_department_id');
        if ($session_dept && in_array($session_dept, $this->user_accessible_departments)) {
            return $session_dept;
        }

        // Get from user job info
        if (isset($this->login_user->job_info->department_id) && $this->login_user->job_info->department_id) {
            return $this->login_user->job_info->department_id;
        }

        // Fallback to first accessible department
        return !empty($this->user_accessible_departments) ? $this->user_accessible_departments[0] : null;
    }

    /**
     * Quick stats widget for topbar/sidebar
     */
    function quick_stats() {
        $department_id = $this->get_user_primary_department();
        
        if (!$department_id) {
            echo json_encode(array("success" => false, "message" => app_lang('no_department_assigned')));
            return;
        }

        $stats = $this->_get_department_dashboard_stats($department_id);
        $department_info = $this->Departments_model->get_one($department_id);

        echo json_encode(array(
            "success" => true,
            "data" => array(
                'department' => $department_info,
                'stats' => $stats,
                'pending_tasks' => $stats['tasks']['pending'],
                'active_projects' => $stats['projects']['active']
            )
        ));
    }

    /**
     * Get projects dropdown for department
     */
    protected function _get_department_projects_dropdown($department_id) {
        $dropdown = array();
        $dropdown[] = array("id" => "", "text" => "- " . app_lang("project") . " -");
        
        try {
            $projects_result = $this->Projects_model->get_details(array("department_id" => $department_id));
            if ($projects_result) {
                $projects = $projects_result->getResult();
                foreach ($projects as $project) {
                    $dropdown[] = array("id" => $project->id, "text" => $project->title);
                }
            }
        } catch (Exception $e) {
            // Log error but continue with empty dropdown
            log_message('error', 'Error getting department projects: ' . $e->getMessage());
        }
        
        return json_encode($dropdown);
    }

    /**
     * Get team members dropdown for department
     */
    protected function _get_department_team_members_dropdown($department_id) {
        $dropdown = array();
        $dropdown[] = array("id" => "", "text" => "- " . app_lang("team_member") . " -");
        
        try {
            $team_result = $this->Users_model->get_details(array(
                "department_id" => $department_id,
                "user_type" => "staff"
            ));
            
            if ($team_result) {
                $team_members = $team_result->getResult();
                foreach ($team_members as $member) {
                    $dropdown[] = array("id" => $member->id, "text" => $member->first_name . " " . $member->last_name);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting department team members: ' . $e->getMessage());
        }
        
        return json_encode($dropdown);
    }

    /**
     * Get task statuses dropdown
     */
    protected function _get_department_task_statuses_dropdown() {
        $dropdown = array();
        $dropdown[] = array("id" => "", "text" => "- " . app_lang("status") . " -");
        
        try {
            $statuses_result = $this->Task_status_model->get_details();
            if ($statuses_result) {
                $task_statuses = $statuses_result->getResult();
                foreach ($task_statuses as $status) {
                    $dropdown[] = array("id" => $status->id, "text" => $status->title);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting task statuses: ' . $e->getMessage());
        }
        
        return json_encode($dropdown);
    }

    /**
     * Get milestones dropdown for department projects
     */
    protected function _get_department_milestones_dropdown($department_id) {
        $dropdown = array();
        $dropdown[] = array("id" => "", "text" => "- " . app_lang("milestone") . " -");
        
        try {
            $projects_result = $this->Projects_model->get_details(array("department_id" => $department_id));
            if (!$projects_result) {
                return json_encode($dropdown);
            }
            
            $projects = $projects_result->getResult();
            $all_milestones = array();
            
            // Get milestones for each project individually since model doesn't support multiple project_ids
            foreach ($projects as $project) {
                $milestones_result = $this->Milestones_model->get_details(array("project_id" => $project->id));
                if ($milestones_result) {
                    $milestones = $milestones_result->getResult();
                    foreach ($milestones as $milestone) {
                        $all_milestones[] = $milestone;
                    }
                }
            }
            
            // Add unique milestones to dropdown
            $added_ids = array();
            foreach ($all_milestones as $milestone) {
                if (!in_array($milestone->id, $added_ids)) {
                    $dropdown[] = array("id" => $milestone->id, "text" => $milestone->title);
                    $added_ids[] = $milestone->id;
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error getting department milestones: ' . $e->getMessage());
        }
        
        return json_encode($dropdown);
    }

    /**
     * Get project labels dropdown
     */
    protected function _get_department_project_labels_dropdown() {
        $dropdown = array();
        $dropdown[] = array("id" => "", "text" => "- " . app_lang("label") . " -");
        
        try {
            $labels_result = $this->Labels_model->get_details(array("context" => "project"));
            if ($labels_result) {
                $labels = $labels_result->getResult();
                foreach ($labels as $label) {
                    $dropdown[] = array("id" => $label->id, "text" => $label->title);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error getting project labels: ' . $e->getMessage());
        }
        
        return json_encode($dropdown);
    }

    /**
     * Department Projects List Data - AJAX endpoint for Projects table
     */
    function department_projects_list_data() {
        $current_department_id = get_setting('user_department_preference_' . $this->login_user->id);
        if (!$current_department_id || !in_array($current_department_id, $this->user_accessible_departments)) {
            $current_department_id = $this->user_accessible_departments[0];
        }

        // Apply department filter to projects
        $options = array(
            "department_id" => $current_department_id,
            "status" => $this->request->getPost("status_id"),
            "project_label" => $this->request->getPost("project_label"),
            "start_date_from" => $this->request->getPost("start_date_from"),
            "start_date_to" => $this->request->getPost("start_date_to"),
            "deadline" => $this->request->getPost("deadline")
        );

        $list_data = $this->Projects_model->get_details($options)->getResult();
        
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_project_row($data);
        }
        
        echo json_encode(array("data" => $result));
    }

    /**
     * Department Tasks List Data - AJAX endpoint for Tasks table  
     */
    function department_tasks_list_data($tab = "", $mobile_view = "") {
        $current_department_id = get_setting('user_department_preference_' . $this->login_user->id);
        if (!$current_department_id || !in_array($current_department_id, $this->user_accessible_departments)) {
            $current_department_id = $this->user_accessible_departments[0];
        }

        // Get context filter from request (department or project tasks)
        $context_filter = $this->request->getPost("context");
        
        // Debug logging
        log_message('debug', 'Department Tasks List - Context filter received: ' . ($context_filter ?: 'NULL'));
        log_message('debug', 'Department Tasks List - Current department ID: ' . $current_department_id);
        
        if (!$context_filter) {
            $context_filter = "department"; // Default to department tasks only
        }
        
        // Build base options for task filtering
        $options = array(
            "status_ids" => $this->request->getPost("status_id"),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "project_id" => $this->request->getPost("project_id"),
            "milestone_id" => $this->request->getPost("milestone_id"),
            "specific_user_id" => $this->request->getPost("specific_user_id"),
            "priority_id" => $this->request->getPost("priority_id"),
            "label_id" => $this->request->getPost("label_id"),
            "deadline" => $this->request->getPost("deadline"),
            "context" => $context_filter
        );
        
        // Apply department filtering based on context
        if ($context_filter === "department") {
            // Show only tasks directly assigned to this department
            $options["department_id"] = $current_department_id;
            log_message('debug', 'Department Tasks List - Using department_id filter: ' . $current_department_id);
        } else {
            // For project or other contexts, use department_ids to check project association
            $options["department_ids"] = array($current_department_id);
            log_message('debug', 'Department Tasks List - Using department_ids filter with context: ' . $context_filter);
        }

        $list_data = $this->Tasks_model->get_details($options)->getResult();
        
        log_message('debug', 'Department Tasks List - Found ' . count($list_data) . ' tasks');
        
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_task_row($data, $mobile_view);
        }
        
        echo json_encode(array("data" => $result));
    }



    /**
     * Helper: Make project row for table
     */
    private function _make_project_row($data) {
        // Implementation similar to main Projects controller
        // This is a simplified version - you may need to expand based on your needs
        
        $progress = $data->project_progress ? $data->project_progress : 0;
        $class = "bg-primary";
        if ($progress == 100) {
            $class = "bg-success";
        }

        return array(
            $data->id,
            anchor(get_uri("projects/view/" . $data->id), $data->title),
            $data->company_name ? anchor(get_uri("clients/view/" . $data->client_id), $data->company_name) : "-",
            $data->department_name,
            to_currency($data->price),
            "",
            format_to_date($data->start_date, false),
            "",
            format_to_date($data->deadline, false),
            "<div class='progress' style='height:10px;'><div class='progress-bar $class' role='progressbar' style='width: $progress%'></div></div> $progress%",
            $data->status_title,
            modal_anchor(get_uri("projects/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_project'), "data-post-id" => $data->id))
        );
    }

    /**
     * Helper: Make task row for table
     */
    private function _make_task_row($data, $mobile_view = "") {
        // Build task row with proper column mapping
        $task_id = $data->id;
        $task_link = anchor(get_uri("tasks/view/" . $task_id), $task_id);
        $task_title = anchor(get_uri("tasks/view/" . $task_id), $data->title);
        $project = $data->project_title ? anchor(get_uri("projects/view/" . $data->project_id), $data->project_title) : "-";
        $assigned_to = isset($data->assigned_to_user) && $data->assigned_to_user ? get_team_member_profile_link($data->assigned_to_user, $data->assigned_to_name) : "-";
        
        // Format dates for display
        $start_date_display = format_to_date($data->start_date, false);
        $deadline_display = format_to_date($data->deadline, false);
        
        // Status badge
        $status_class = "bg-secondary";
        if (isset($data->status_key_name)) {
            if ($data->status_key_name == "done") {
                $status_class = "bg-success";
            } else if ($data->status_key_name == "to_do") {
                $status_class = "bg-info";
            } else if ($data->status_key_name == "in_progress") {
                $status_class = "bg-warning";
            }
        }
        $status = "<span class='badge $status_class'>" . $data->status_title . "</span>";
        
        // Return 9 columns: 7 visible + 2 hidden for sorting (columns 5 and 7)
        return array(
            $task_link,              // 0: ID
            $task_title,             // 1: Title  
            $project,                // 2: Project
            $assigned_to,            // 3: Assigned to
            $start_date_display,     // 4: Start date (displayed)
            $data->start_date,       // 5: Start date (hidden - for sorting)
            $deadline_display,       // 6: Deadline (displayed)
            $data->deadline,         // 7: Deadline (hidden - for sorting)
            $status                  // 8: Status
        );
    }

    /**
     * Helper: Make team member row for table
     */
    private function _make_team_member_row($data) {
        // Implementation similar to main Team_members controller
        
        $profile_image = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs mr10'><img src='$profile_image' alt='...'></span>";
        
        return array(
            $user_avatar,
            get_team_member_profile_link($data->id, $data->first_name . " " . $data->last_name),
            $data->job_title ? $data->job_title : "-",
            $data->department_name ? $data->department_name : "-",
            $data->email,
            $data->phone ? $data->phone : "-",
            modal_anchor(get_uri("team_members/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_team_member'), "data-post-id" => $data->id))
        );
    }

    function overview() {
        $view_data['tab'] = "overview";
        return $this->template->view("my_department/tabs/overview", $view_data);
    }

    function projects() {
        $view_data['tab'] = "projects";
        return $this->template->view("my_department/tabs/projects", $view_data);
    }

    function tasks() {
        // Get user's department ID
        $department_id = $this->get_user_primary_department();
        if (!$department_id) {
            show_404();
        }

        // Get dropdown data for tasks
        $team_members_dropdown = '[]';
        $task_statuses_dropdown = '[]';
        $milestones_dropdown = '[]';
        $projects_dropdown = '[]';
        
        try {
            $team_members_dropdown = $this->_get_department_team_members_dropdown($department_id);
            $task_statuses_dropdown = $this->_get_department_task_statuses_dropdown();
            $milestones_dropdown = $this->_get_department_milestones_dropdown($department_id);
            $projects_dropdown = $this->_get_department_projects_dropdown($department_id);
        } catch (\Exception $e) {
            log_message('error', 'Error loading tasks dropdown data: ' . $e->getMessage());
        }

        $view_data = array(
            'tab' => "tasks",
            'team_members_dropdown' => $team_members_dropdown,
            'task_statuses_dropdown' => $task_statuses_dropdown, 
            'milestones_dropdown' => $milestones_dropdown,
            'projects_dropdown' => $projects_dropdown
        );

        return $this->template->view("my_department/tabs/tasks", $view_data);
    }

    function team() {
        $view_data['tab'] = "team";
        return $this->template->view("my_department/tabs/team", $view_data);
    }

    function department_team_list_data() {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("team_members", $this->login_user->is_admin, $this->login_user->user_type);

        $department_id = $this->get_user_primary_department();
        $options = array(
            "department_id" => $department_id,
            "user_type" => "staff",
            "status" => $this->request->getPost("status") ? $this->request->getPost("status") : "active",
            "custom_fields" => $custom_fields,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("team_members", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $list_data = $this->Users_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_department_team_row($data, $custom_fields);
        }

        echo json_encode(array("data" => $result));
    }

    private function _make_department_team_row($data, $custom_fields) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $link = anchor(get_uri("team_members/view/" . $data->id), $user_avatar . $full_name);

        $row_data = array(
            get_team_member_profile_link($data->id, $user_avatar),
            get_team_member_profile_link($data->id, $full_name),
            $data->job_title,
            $data->department_title ? $data->department_title : "-",
            $data->email,
            $data->phone ? $data->phone : "-",
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $options = modal_anchor(get_uri("team_members/view/" . $data->id), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-default btn-sm", "title" => app_lang('view_details')));

        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_edit_team_members_info")) {
            $options .= modal_anchor(get_uri("team_members/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "btn btn-default btn-sm", "title" => app_lang('edit'), "data-post-id" => $data->id));
        }

        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_team_members")) {
            $user_full_name = trim($data->first_name . " " . $data->last_name);
            $department_name = $data->department_title ? $data->department_title : "this department";
            
            $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array(
                'title' => app_lang('remove_from_department'), 
                "class" => "btn btn-default btn-sm delete", 
                "data-id" => $data->id, 
                "data-action-url" => get_uri("my_department/remove_user_from_department"), 
                "data-action" => "delete-confirmation",
                "data-confirmation-title" => app_lang('remove_user_confirmation_title'),
                "data-confirmation-message" => app_lang('confirm_remove_user_from_department'),
                "data-user-name" => $user_full_name,
                "data-department-name" => $department_name
            ));
        }

        $row_data[] = $options;

        return $row_data;
    }

    /**
     * Overview Tab Data - AJAX endpoint (keeping for compatibility)
     */
    function overview_old($department_id = 0) {
        if (!$department_id) {
            show_404();
        }

        // Verify user has access to this department
        if (!in_array($department_id, $this->user_accessible_departments)) {
            show_404();
        }

        $department_info = $this->Departments_model->get_details(array("id" => $department_id))->getRow();
        if (!$department_info) {
            show_404();
        }

        // Get all the data needed for overview
        $stats = $this->_get_department_dashboard_stats($department_id);
        $recent_activities = $this->_get_recent_department_activities($department_id, 10);
        $upcoming_deadlines = $this->_get_upcoming_deadlines($department_id, 5);
        $announcements = $this->_get_department_announcements($department_id, 3);
        $team_metrics = $this->_get_team_performance_metrics($department_id);

        $view_data = array(
            'department_info' => $department_info,
            'stats' => $stats,
            'recent_activities' => $recent_activities,
            'upcoming_deadlines' => $upcoming_deadlines,
            'department_announcements' => $announcements,
            'team_metrics' => $team_metrics
        );

        return $this->template->view("my_department/tabs/overview", $view_data);
    }

    /**
     * Remove user from current department
     */
    function remove_user_from_department() {
        $user_id = $this->request->getPost('id');
        $department_id = $this->get_user_primary_department();

        if (!$user_id || !$department_id) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            exit();
        }

        // Check if user has permission to remove team members
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_team_members"))) {
            echo json_encode(array("success" => false, 'message' => app_lang('access_denied')));
            exit();
        }

        // Don't allow removing the last user from department
        $department_users_result = $this->Departments_model->get_department_users($department_id);
        $department_users_count = $department_users_result->getNumRows();
        if ($department_users_count <= 1) {
            echo json_encode(array("success" => false, 'message' => app_lang('cannot_remove_last_user_from_department')));
            exit();
        }

        // Don't allow removing self if admin
        if ($user_id == $this->login_user->id) {
            echo json_encode(array("success" => false, 'message' => app_lang('cannot_remove_yourself_from_department')));
            exit();
        }

        $save_id = $this->Departments_model->remove_user_from_department($user_id, $department_id);

        if ($save_id) {
            echo json_encode(array("success" => true, 'message' => app_lang('user_removed_from_department_successfully')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

}