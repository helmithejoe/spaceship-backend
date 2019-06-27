<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use ReallySimpleJWT\Token;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Jwt;
use ReallySimpleJWT\Validate;
use ReallySimpleJWT\Encode;


function jwt_get_token($user_id)
{
    $CI =& get_instance();
    $CI->load->config('jwt');
    
    $secret = $CI->config->item('jwt_secret');
    $expiration = time() + $CI->config->item('jwt_lifetime');
    $issuer = $CI->input->server('SERVER_NAME');

    $token = Token::create($user_id, $secret, $expiration, $issuer);
    
    return $token;
}

function jwt_validate_token()
{
    $CI =& get_instance();
    $CI->load->config('jwt');
    
    $auth = $CI->input->get_request_header('Authorization', TRUE);
    $token = str_replace('Bearer ', '', $auth);
    $secret = $CI->config->item('jwt_secret');
    
    try
    {
        $jwt = new Jwt($token, $secret);
        $parse = new Parse($jwt, new Validate(), new Encode());
        $parsed = $parse->validate()
            ->validateExpiration()
            ->parse();
    }
    catch (Exception $e)
    {
        return FALSE;
    }

    // Return the token header claims as an associative array.
    $header = $parsed->getHeader();

    // Return the token payload claims as an associative array.
    $payload = $parsed->getPayload();

    return array('header' => $header, 'payload' => $payload);
}


