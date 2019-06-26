<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = 'smtp';
$config['smtp_host'] = 'ssl://smtp.gmail.com';
$config['smtp_user'] = 'helmi.mailer@gmail.com';
$config['smtp_pass'] = 'ilxbkdetosrmjnjd';
$config['smtp_port'] = 465;
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['mailtype'] = 'html'; // or html
$config['from_email'] = 'helmi.mailer@gmail.com';
$config['from_name'] = 'Spaceship Registration';