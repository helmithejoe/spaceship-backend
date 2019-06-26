<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Base_model.php');

class User_model extends Base_model {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
    }
    
    private function _set_validation_rules() {
        $this->form_validation->set_rules(
            'email',
            'Email',
            'required|valid_email|is_unique[users.email]',
            array('is_unique' => 'Email already registered.')
        );
        $this->form_validation->set_rules(
            'first_name',
            'First Name',
            'required|alpha|max_length[100]'
        );
        $this->form_validation->set_rules(
            'last_name',
            'Last Name',
            'required|alpha|max_length[100]'
        );
        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|max_length[100]|min_length[8]'
        );
    }
    
    private function _generate_activation_key($user_id, $data)
    {
        $key = $this->config->item('encryption_key');
        $str = $user_id.$data['email'].$data['first_name'].$data['last_name'].$key;
        $activation_key = base64_encode(md5($str));
        return $activation_key;
    }
    
    private function _verify_activation_key($user_id, $activation_key)
    {
        $where = array('id' => $user_id);
        $user = $this->get_data('users', $where);
        if( ! $user) return FALSE;
        
        $key = $this->config->item('encryption_key');
        $str = $user->id.$user->email.$user->first_name.$user->last_name.$key;
        $correct_key = base64_encode(md5($str));
        
        if ($correct_key == $activation_key) return TRUE;
        return FALSE;
    }
    
    public function create_user($data) {
        $data = (array) $data;
        $this->form_validation->set_data($data);
        $this->_set_validation_rules();
        
        if ($this->form_validation->run() == FALSE)
        {
            $errors = validation_errors();
            return array(
                'error' => $this->parse_errors($errors),
                'success' => FALSE
            );  
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        if ($user_id = $this->insert_data('users', $data))
        {
            return array(
                'success' => TRUE,
                'activation_key' => $this->_generate_activation_key($user_id, $data),
                'email' => $data['email'],
                'user_id' => $user_id
            );
        }
    }
    
    public function activate_user($user_id, $activation_key)
    {
        $check = $this->_verify_activation_key($user_id, $activation_key);
        if ($check)
        {
            $data = array('active_flag' => '1');
            $where = array('id' => $user_id);
            return $this->update_data('users', $data, $where);
        }
        return FALSE;
    }
    
}