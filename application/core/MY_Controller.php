<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->add_package_path(APPPATH.'third_party/ion_auth/');
        $this->load->model('ion_auth_init');
        $this->load->library('ion_auth');

        $this->load->helper('custom_routing');
    }

    public function index(){
        $this->load->view("header");
        $this->body();
        $this->load->view("footer");
    }

    public function html($lambda){
        $this->load->view("header");
        $lambda();
        $this->load->view("footer");
    }

}
