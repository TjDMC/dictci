<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {
	
	public function body()
	{
		$this->load->view('welcome_message');
	}
}
