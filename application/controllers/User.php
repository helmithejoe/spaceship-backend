<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use ReallySimpleJWT\Token;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Encode;

class User extends CI_Controller {
    
    public function __construct()
    {
        // construct the parent class
        parent::__construct();
        
        $this->load->helper('email');
        $this->load->helper('url');
        $this->load->model('user_model');
    }
    
    public function test()
    {
        $user_id = 12;
        $secret = 'sec!ReT423*&';
        $expiration = time() + 3600;
        $issuer = 'localhost';
        
        $token = Token::create($user_id, $secret, $expiration, $issuer);
        
        echo $token;
    }
    
    public function validate()
    {
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMiwiaXNzIjoibG9jYWxob3N0IiwiZXhwIjoiMjAxOS0wNi0yNyAwMzoxMjoxOCIsInN1YiI6bnVsbCwiYXVkIjpudWxsfQ.9XEmSxbVY6HfMjKctS5TL7eFgJmU2v3HaRZtEkkq6VY';
        $secret = 'sec!ReT423*&';

        $jwt = new Jwt($token, $secret);

        $parse = new Parse($jwt, new Validate(), new Encode());

        $parsed = $parse->validate()
            ->validateExpiration()
            ->validateNotBefore()
            ->parse();

        // Return the token header claims as an associative array.
        $header = $parsed->getHeader();

        // Return the token payload claims as an associative array.
        $payload = $parsed->getPayload();
        
        print_r($header);
        print_r($payload);
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