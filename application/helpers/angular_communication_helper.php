<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function parse_custom_post($json)
{
    $data = json_decode($json,true);

    if(json_last_error()!==JSON_ERROR_NONE){
        custom_response(false,'Invalid JSON');
    }
    return $data;
}

function custom_response($isSuccessful,$msg){
	echo json_encode(array(
		'success'=>$isSuccessful,
		'msg'=>$msg
	));
	die();
}
