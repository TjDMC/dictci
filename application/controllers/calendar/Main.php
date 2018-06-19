<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller{

    public function body(){
		echo "<div>";
        $this->load->library('calendar');
		echo $this->calendar->generate();
		echo "</div>";
    }

}
