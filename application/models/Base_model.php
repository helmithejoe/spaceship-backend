<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_model extends CI_Model {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
        
        // load database class
        $this->load->database();
    }
    
    private function _insert_data($table, $data)
    {
        $data = $this->db->escape($data);
        $this->db->insert($table, $data);
    }
}