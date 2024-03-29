<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* a base class for all models.
* This class provide common functions for all models
*/
class Base_model extends CI_Model {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
        
        // load database class
        $this->load->database();
        
        // load validation library
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters(',', ',');
    }
    
    /**
    * transforms error string collection into array
    */
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
        $where = $this->db->escape_str($where);
        return $this->db->update($table, $data, $where);
    }
    
    protected function get_data($table, $where, $single = TRUE)
    {
        $query = $this->db->get_where($table, $where);
        $where = $this->db->escape_str($where);
        if ( ! $single)
        {
            return $query->result();
        }
        else
        {
            return $query->row();
        }
    }
}