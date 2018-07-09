<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function parse_custom_post($json)
{
    $data = json_decode($json,true);

    if(json_last_error()!==JSON_ERROR_NONE){
        custom_response(false,'Invalid JSON');
    }
    return $data;
}

function custom_response($isSuccessful,$msg,$custom=array()){
    $response = array(
		'success'=>$isSuccessful,
		'msg'=>$msg
	);
    foreach($custom as $key=>$c){
        $response[$key]=$c;
    }
	echo json_encode($response,JSON_NUMERIC_CHECK);
	die();
}
