<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
    }
}

class Flags{
	const DEF = 0;
	const DELETED = 1;
}
