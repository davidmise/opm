<?php

namespace App\Models;

class Announcement_templates_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'announcement_templates';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $announcement_templates_table = $this->db->prefixTable('announcement_templates');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $announcement_templates_table.id=$id";
        }

        $created_by = $this->_get_clean_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $announcement_templates_table.created_by=$created_by";
        }

        $category = $this->_get_clean_value($options, "category");
        if ($category) {
            $where .= " AND $announcement_templates_table.category='$category'";
        }

        $priority = $this->_get_clean_value($options, "priority");
        if ($priority) {
            $where .= " AND $announcement_templates_table.priority='$priority'";
        }

        $sql = "SELECT $announcement_templates_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user
        FROM $announcement_templates_table
        LEFT JOIN $users_table ON $users_table.id = $announcement_templates_table.created_by
        WHERE $announcement_templates_table.deleted=0 $where";
        
        return $this->db->query($sql);
    }

}