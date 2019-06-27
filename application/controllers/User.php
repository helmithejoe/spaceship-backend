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
        $raw_input = $this->input->raw_input_stream;
        
        if ($raw_input)
        {
            // decode json as array
            $input = json_decode($raw_input, TRUE);
        }
        else
        {
            $error = array('Empty input');
            $this->output->set_status_header(400);
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
            
            $this->output->set_status_header(200);
            $this->load->view('json_success', array('data' => '[]'));
        }
        else
        {
            $this->output->set_status_header(400);
            $this->load->view('json_error', array('error' => json_encode($result['error'])));
        }
    }
    
    public function activate($user_id = '', $activation_key = '')
    {
        $result = $this->user_model->activate_user($user_id, $activation_key);
        
        // if user activated successfully
        if ($result)
        {
            $this->load->view('activate_success');
        }
        else
        {
            $this->load->view('activate_error');
        }
    }
    
    public function login()
    {
        $raw_input = $this->input->raw_input_stream;
        
        if ($raw_input)
        {
            // decode json as array
            $input = json_decode($raw_input, TRUE);
        }
        else
        {
            $error = array('Empty input');
            $this->output->set_status_header(400);
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        
        $result = $this->user_model->login_user($input);
        $this->output->set_status_header(200);
        
        // if user login successfully
        if ($result['success'])
        {
            $data = $result['data'];
            $jwt_token = jwt_get_token($data['user_id']);
            $data['jwt_token'] = $jwt_token;
            $this->load->view('json_success', array('data' => json_encode($data)));
        }
        else
        {
            $error = $result['error'];
            $this->load->view('json_error', array('error' => json_encode($error)));
        }
    }
    
    public function profile()
    {
        $payload = $this->_auth();
        if ( ! $payload)
        {
            $this->output->set_status_header(403);
            $error = array('Access denied');
            return $this->load->view('json_error', array('error' => json_encode($error)));
        }
        $user_id = $payload['user_id'];
        $data = $this->user_model->get_profile($user_id);
        $this->load->view('json_success', array('data' => json_encode($data)));
    }
}