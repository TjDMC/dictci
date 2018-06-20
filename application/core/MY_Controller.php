<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->add_package_path(APPPATH.'third_party/ion_auth/');
        $this->load->model('ion_auth_init');
        $this->load->library('ion_auth');

        $this->load->helper('custom_routing');

        $this->checkLogin();
    }

    protected function checkLogin(){
        //Check if user is logged in and is admin, if not, display login page
        if(!$this->ion_auth->logged_in()||!$this->ion_auth->is_admin()){
            $this->html(function(){
                $this->load->view("login");
            });
            die($this->output->get_output());
        }
    }


    public function index(){
        $this->load->view("header");
        $this->body();
        $this->load->view("footer");
    }

    protected function html($lambda){
        $this->load->view("header");
        $lambda();
        $this->load->view("footer");
    }

    protected function body(){
        //To Override
    }
}
