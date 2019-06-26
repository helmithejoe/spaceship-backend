<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
        
        $this->load->model('user_model');
    }
    
    private function _send_email()
    {
        $this->load->library('email');
        $config = array();
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_user'] = 'helmi.mailer@gmail.com';
        $config['smtp_pass'] = 'ilxbkdetosrmjnjd';
        $config['smtp_port'] = 465;
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";
        $config['mailtype'] = 'text'; // or html
        $this->email->initialize($config);

        $this->email->from('helmi.mailer@gmail.com', 'Spaceship');
        $this->email->to('helmi.informatika@gmail.com');

        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');

        $this->email->send();
    }
    
    public function signup()
    {
        //$this->_send_email();
        $raw_input = $this->input->raw_input_stream;
        
        // decode json as array
        $input = json_decode($raw_input, TRUE);
        
        $result = $this->user_model->create_user($input);
        
        // if user created successfully
        if ($result['success'])
        {
            $data = array('user_id' => $result);
            $this->output->set_status_header(200);
            $this->load->view('json_success');
        }
        else
        {
            $this->output->set_status_header(400);
            $this->load->view('json_error', array('error' => json_encode($result)));
        }
    }
    
    public function login()
    {
        //$this->_send_email();
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