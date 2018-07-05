<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
    }

    protected function checkFields($checker,$checkee){
        $result = array();
        foreach($checker as $field){
            if($field['required']){
                if(!isset($checkee[$field['field_name']])){
                    return "Please fill-in the required field ".$field['field_title'];
                }else{
                    $result[$field['field_name']]=$checkee[$field['field_name']];
                }
            }else{
                if(isset($checkee[$field['field_name']])){
                    $result[$field['field_name']]=$checkee[$field['field_name']];
                }
            }
        }
        return $result;
    }

}

class Flags{
	const DEF = 0;
	const DELETED = 1;
}
