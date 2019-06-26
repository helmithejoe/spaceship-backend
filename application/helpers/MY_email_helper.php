<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function send_email($recipient, $subject, $message)
{
    $CI =& get_instance();
    $CI->load->config('email');
    $CI->load->library('email');

    $CI->email->from($CI->config->item('from_email'), $CI->config->item('from_name'));
    $CI->email->to($recipient);

    $CI->email->subject($subject);
    $CI->email->message($message);

    return $CI->email->send();
}
