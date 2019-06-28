<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
        
        $this->load->helper('email');
        $this->load->helper('url');
        $this->load->helper('jwt');
        $this->load->model('user_model');
    }
    
    private function _auth()
    {
        $result = jwt_validate_token();
        if ( ! $result)
        {
            return FALSE;
        }
        return $result['payload'];
    }
    
    public function signup()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $raw_input = $this->input->raw_input_stream;
        
        if ($raw_input)
        {
            // decode json as array
            $input = json_decode($raw_input, TRUE);
        }
        else
        {
            $error = array('Empty input');
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        
        
        $result = $this->user_model->create_user($input);
        
        // if user created successfully
        if ($result['success'])
        {
            $url = base_url("user/activate/{$result['user_id']}/{$result['activation_key']}");
            $message = $this->load->view('emails/activate', array('url' => $url), TRUE);
            $subject = 'Spaceship Registration';
            $recipient = $result['email'];
            send_email($recipient, $subject, $message);
            
            return $this->load->view('json_success', array('data' => '[]'));
        }
        else
        {
            return $this->load->view('json_error', array('error' => json_encode($result['error'])));
        }
    }
    
    public function activate($user_id = '', $activation_key = '')
    {
        $result = $this->user_model->activate_user($user_id, $activation_key);
        
        // if user activated successfully
        if ($result)
        {
            return $this->load->view('activate_success');
        }
        else
        {
            return $this->load->view('activate_error');
        }
    }
    
    public function login()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $raw_input = $this->input->raw_input_stream;
        
        if ($raw_input)
        {
            // decode json as array
            $input = json_decode($raw_input, TRUE);
        }
        else
        {
            $error = array('Empty input');
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        
        $result = $this->user_model->login_user($input);
        
        // if user login successfully
        if ($result['success'])
        {
            $data = $result['data'];
            $jwt_token = jwt_get_token($data['user_id']);
            $data['jwt_token'] = $jwt_token;
            return $this->load->view('json_success', array('data' => json_encode($data)));
        }
        else
        {
            $error = $result['error'];
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
    }
    
    public function profile()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $payload = $this->_auth();
        
        if ( ! $payload)
        {
            $error = array('Unauthorized access');
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        $user_id = $payload['user_id'];
        $result = $this->user_model->get_profile($user_id);
        //$this->user_model->update_last_activity($user_id);
        if ( ! $result)
        {
            $error = array('User not found');
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        return $this->load->view('json_success', array('data' => json_encode($result)));
    }
    
    public function online_users()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $payload = $this->_auth();
        
        if ( ! $payload)
        {
            $error = array('Unauthorized access');
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        $user_id = $payload['user_id'];
        $result = $this->user_model->get_online_users();
        //$this->user_model->update_last_activity($user_id);
        return $this->load->view('json_success', array('data' => json_encode($result)));
    }
    
    public function update_last_activity()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        
        $payload = $this->_auth();
        
        if ( ! $payload)
        {
            $error = array('Unauthorized access');
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        $user_id = $payload['user_id'];
        $result = array();
        $this->user_model->update_last_activity($user_id);
        return $this->load->view('json_success', array('data' => json_encode($result)));
    }
}