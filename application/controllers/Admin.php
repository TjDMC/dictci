<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller{

    public function body(){
        $this->load->view('admin');
    }

    public function changeLoginCredentials($credential=null){
        $data = parse_custom_post($this->input->post('data'));

        if($data==null||!isset($data['password'])||!isset($data['username'])||!isset($data['new_password1'])||!isset($data['new_password2'])){
            custom_response(false,'Missing data.');
        }

        if(!$this->ion_auth->hash_password_db($this->ion_auth->user()->row()->id, $data['password'])){
            custom_response(false,'Incorrect Password');
        }
        switch($credential){
            //Default admin id is 1
            case 'password':
                if($data['new_password1']!=$data['new_password2']){
                    custom_response(false,'Passwords do not match');
                }
                $this->ion_auth->update(1,array(
                    'password'=>$data['new_password1']
                ));
                break;
            case 'username':
                $this->ion_auth->update(1,array(
                    'username'=>$data['username']
                ));
                break;
            default:
                show_404();
                return;
        }
        custom_response(true,ucfirst($credential).' successfully changed.');
    }

}
