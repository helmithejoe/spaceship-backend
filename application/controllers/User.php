<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
        
        $this->load->helper('email');
        $this->load->helper('url');
        $this->load->model('user_model');
    }
    
    public function signup()
    {
        $raw_input = $this->input->raw_input_stream;
        
        // decode json as array
        $input = json_decode($raw_input, TRUE);
        
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
            $this->load->view('json_success');
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
        
        // decode json as array
        $input = json_decode($raw_input, TRUE);
        
        $result = $this->user_model->login($input);
        
        // if user created successfully
        if (is_int($result))
        {
            $data = array('user_id' => $result);
            $this->output->set_status_header(200);
            $this->load->view('json_success', array('data' => json_encode($data)));
        }
        else
        {
            $this->output->set_status_header(400);
            $this->load->view('json_error', array('error' => json_encode($result)));
        }
    }
}