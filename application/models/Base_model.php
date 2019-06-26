<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_model extends CI_Model {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
        
        // load database class
        $this->load->database();
        
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters(',', ',');
    }
    
    protected function parse_errors($errors)
    {
        $error_list = explode(',', $errors);
        $error_filtered = array_filter(
            $error_list,
            function($element)
            {
                if(trim($element) != '') return $element;
            }
        );
        return array_values($error_filtered);
    }
    
    protected function insert_data($table, $data)
    {
        $data = $this->db->escape_str($data);
        if ($this->db->insert($table, $data))
        {
            return $this->db->insert_id();
        }
        return FALSE;
    }
    
    protected function update_data($table, $data, $where)
    {
        $data = $this->db->escape_str($data);
        return $this->db->update($table, $data, $where);
    }
    
    protected function get_data($table, $where)
    {
        $query = $this->db->get_where($table, $where);
        return $query->row();
    }
}