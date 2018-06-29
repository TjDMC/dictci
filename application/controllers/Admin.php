<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller{

    public function body(){
        $this->load->view('admin');
    }

    public function changeLoginCredentials($credential=null){
        $data = parse_custom_post($this->input->post('data'));
        if($data==null){
            custom_response(false,'Missing data.');
        }
        if(!isset($data['password'])){
            custom_response(false,'Missing password.');
        }

        if($this->ion_auth->hash_password_db($this->ion_auth->user()->row()->id, $data['password'])){
            custom_response(true,'correct password lol');
        }
        switch($credential){
            case 'password':
                break;
            case 'username':
                break;
            default:
                show_404();
                return;
        }
    }

}
